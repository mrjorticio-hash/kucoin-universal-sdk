import logging
import time
import uuid
from typing import List

from kucoin_universal_sdk.model.client_option import ClientOption
from kucoin_universal_sdk.model.common import WsMessage
from kucoin_universal_sdk.model.constants import DomainType
from kucoin_universal_sdk.model.constants import WsMessageType
from kucoin_universal_sdk.model.websocket_option import WebSocketEvent
from ..infra.default_transport import DefaultTransport
from ..infra.default_ws_callback import TopicManager, CallbackManager
from ..infra.default_ws_client import WebSocketClient, WriteMsg
from ..infra.default_ws_token_provider import DefaultWsTokenProvider
from ..interfaces.websocket import WebSocketService, WebSocketMessageCallback, WebsocketTransport
from ..util.sub import SubInfo


class DefaultWsService(WebSocketService):
    def __init__(self, client_option: ClientOption, domain: DomainType, private: bool, sdk_version: str):
        self.token_transport = DefaultTransport(client_option, sdk_version)
        ws_option = client_option.websocket_client_option

        self.client: WebsocketTransport = WebSocketClient(DefaultWsTokenProvider(self.token_transport, domain, private),
                                                          ws_option, self.on_reconnected, self.on_message,
                                                          self.notify_event)
        self.topic_manager = TopicManager()
        self.option = ws_option
        self.private = private

    def on_reconnected(self):
        logging.info("WebSocket client reconnected, resubscribe...")
        old_topic_manager = self.topic_manager
        self.topic_manager = TopicManager()

        pending = list()
        old_topic_manager.range(lambda _, value: pending.append(value))

        for attempt in range(0, self.option.auto_resubscribe_max_attempts):
            if len(pending) == 0:
                self.notify_event(WebSocketEvent.EVENT_RE_SUBSCRIBE_OK, "", "")
                break

            logging.info(f"[Attempt {attempt}] Resubscribing {len(pending)} items in 5 seconds...")
            time.sleep(5)

            failed = []
            for cm in pending:
                success = self._resubscribe(cm)
                if not success:
                    failed.append(cm)

            pending = failed
        if pending:
            self.notify_event(WebSocketEvent.EVENT_RE_SUBSCRIBE_ERROR, "", "")
            logging.info(
                f"Resubscribe failed after {self.option.auto_resubscribe_max_attempts} attempts")

    def _resubscribe(self, callback_manager: CallbackManager) -> bool:
        sub_info_list = callback_manager.get_sub_info()
        for sub in sub_info_list:
            try:
                self.subscribe(sub.prefix, sub.args, sub.callback)
            except Exception as err:
                return False
        return True

    def start(self):
        try:
            self.client.start()
        except Exception as err:
            logging.error(f"Failed to start client: {err}")
            raise

    def notify_event(self, event: WebSocketEvent, msg: str, error: str):
        try:
            if self.option.event_callback:
                self.option.event_callback(event, msg, error)
        except Exception as e:
            logging.error(f"Exception in notify_event: {e}")

    def on_message(self, msg: WsMessage):
        if msg is None:
            return
        if msg.type != WsMessageType.MESSAGE.value:
            return

        callback_manager = self.topic_manager.get_callback_manager(msg.topic)
        if callback_manager is None:
            logging.error(f"Cannot find callback manager, id: {msg.id}, topic: {msg.topic}")
            return

        cb = callback_manager.get(msg.topic)
        if cb is None:
            logging.error(f"Cannot find callback for id: {msg.id}, topic: {msg.topic}")
            return

        try:
            cb.on_message(msg)
        except Exception as e:
            logging.error(f"Exception in callback: {e}")
            self.notify_event(WebSocketEvent.EVENT_CALLBACK_ERROR, msg.id, str(e))

    def stop(self):
        logging.info("Closing WebSocket client")
        self.client.stop()

    def subscribe(self, prefix: str, args: List[str], callback: WebSocketMessageCallback) -> str:
        callback_manager = None
        sub_id = None
        try:
            if args is None:
                args = []
            sub_info = SubInfo(prefix=prefix, args=args, callback=callback)
            sub_id = sub_info.to_id()

            callback_manager = self.topic_manager.get_callback_manager(prefix)
            created = callback_manager.add(sub_info)
            if not created:
                logging.info(f"Already subscribed: {sub_id}")
                return sub_id

            sub_event = WsMessage(
                id=sub_id,
                type=WsMessageType.SUBSCRIBE.value,
                topic=sub_info.sub_topic(),
                private_channel=self.private,
                response=True
            )

            data: WriteMsg = self.client.write(sub_event, self.option.write_timeout)
            event_triggered = data.event.wait(timeout=data.timeout)
            if event_triggered:
                logging.info(f"ACK received for subscribe message, id: {data.msg.id}")
                data.event.clear()
            else:
                logging.warning(f"Timeout for subscribe, id: {data.msg.id}")
                raise TimeoutError(f"Timeout for subscribe, id: {data.msg.id}")
            if data.exception is not None:
                logging.error(f"ERROR received for subscribe, id: {data.msg.id}, exception: {data.exception}")
                raise data.exception
            return sub_id

        except Exception as err:
            if callback_manager is not None and sub_id is not None:
                callback_manager.remove(sub_id)
            logging.error(f"Subscribe error: {sub_id}, error: {err}")
            raise

    def unsubscribe(self, sub_id: str):
        try:
            sub_info = SubInfo.from_id(sub_id)
            callback_manager = self.topic_manager.get_callback_manager(sub_info.prefix)

            sub_event = WsMessage(
                id=str(uuid.uuid4()),
                type=WsMessageType.UNSUBSCRIBE.value,
                topic=sub_info.sub_topic(),
                private_channel=self.private,
                response=True
            )

            try:
                data: WriteMsg = self.client.write(sub_event, self.option.write_timeout)
                event_triggered = data.event.wait(timeout=data.timeout)
                if event_triggered:
                    logging.info(f"ACK received for unsubscribe, id: {sub_id}")
                    data.event.clear()
                else:
                    logging.warning(f"Timeout for unsubscribe, id: {sub_id}")
                    raise TimeoutError(f"Timeout for unsubscribe, id: {sub_id}")
                if data.exception is not None:
                    logging.error(f"Error received for unsubscribe, id: {sub_id}, exception: {data.exception}")
                    raise data.exception
                callback_manager.remove(sub_id)
                logging.info(f"Unsubscribe success: {sub_id}")
            except Exception as err:
                raise
        except Exception as e:
            logging.error(f"Unsubscribe error: {sub_id}, error: {e}")
            raise
