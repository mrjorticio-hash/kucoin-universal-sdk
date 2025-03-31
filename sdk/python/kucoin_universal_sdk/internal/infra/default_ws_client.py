import logging
import random
import threading
import time
import urllib.parse
from queue import Queue, Full, Empty
from typing import Dict, List, Optional, Callable

import websocket

from kucoin_universal_sdk.model.common import WsMessage
from kucoin_universal_sdk.model.constants import WsMessageType
from kucoin_universal_sdk.model.websocket_option import WebSocketClientOption
from kucoin_universal_sdk.model.websocket_option import WebSocketEvent
from ..interfaces.websocket import WsTokenProvider, WsToken, WebsocketTransport, WriteMsg


class WebSocketClient(WebsocketTransport):
    def __init__(self, token_provider: WsTokenProvider, options: WebSocketClientOption,
                 on_reconnected: Optional[Callable[[], None]],
                 on_message: Optional[Callable[[WsMessage], None]],
                 on_event: Callable[[WebSocketEvent, str, str], None]):
        self.options = options
        self.conn = None
        self.conn_lock = threading.Lock()
        self.connected = threading.Event()
        self.shutdown = threading.Event()
        self.token_provider = token_provider
        self.token_info = None
        self.close_event = threading.Event()
        self.reconnecting = threading.Event()
        # callbacks
        self.on_reconnected = on_reconnected
        self.on_message = on_message
        self.on_event = on_event
        # data queues
        self.read_msg_queue = Queue(maxsize=options.read_message_buffer)
        self.write_msg_queue = Queue(maxsize=options.write_message_buffer)
        # ack
        self.ack_event: Dict[str, WriteMsg] = {}
        self.ack_event_lock = threading.Lock()
        # looping thread
        self.keep_alive_thread = None
        self.write_thread = None
        self.read_thread = None
        self.reconnect_thread = None

        self.ws_thread = None
        self.welcome_received = threading.Event()

    def start(self):
        with self.conn_lock:
            if self.connected.is_set():
                logging.warning("WebSocket client is already connected.")
                return
            try:
                self.dial()
            except Exception as err:
                logging.error(f"Failed to start WebSocket client: {err}")
                raise
            self.connected.set()
            self.on_event(WebSocketEvent.EVENT_CONNECTED, "", "")
            self.run()
            logging.info("Websocket client started")
            if not self.reconnect_thread or not self.reconnect_thread.is_alive():
                self.reconnect_thread = threading.Thread(target=self.reconnect_loop, daemon=True)
                self.reconnect_thread.start()

    def stop(self):
        with self.conn_lock:
            self.on_event(WebSocketEvent.EVENT_CLIENT_SHUTDOWN, "", "")
            self.shutdown.set()
            self.close()
            self.reconnect_thread.join()
            self.token_provider.close()

    def run(self):
        if not self.keep_alive_thread or not self.keep_alive_thread.is_alive():
            self.keep_alive_thread = threading.Thread(target=self.keep_alive_loop, daemon=True)
            self.keep_alive_thread.start()

        if not self.write_thread or not self.write_thread.is_alive():
            self.write_thread = threading.Thread(target=self.write_loop, daemon=True)
            self.write_thread.start()

        if not self.read_thread or not self.read_thread.is_alive():
            self.read_thread = threading.Thread(target=self.read_loop, daemon=True)
            self.read_thread.start()

    def write_loop(self):
        while not self.close_event.is_set():
            data: WriteMsg = None
            try:
                data = self.write_msg_queue.get(timeout=1)
                self.conn.send(data.msg.to_json())
                logging.debug(f"Message sent: {data.msg}")
                data.ts = time.time()
            except Empty:
                continue
            except Exception as e:
                if data:
                    with self.ack_event_lock:
                        data: WriteMsg = self.ack_event.pop(data.msg.id)
                        data.set_exception(e)
                logging.error(f"Error sending message: {e}")
        logging.info("Exiting write loop...")

    def read_loop(self):
        while not self.close_event.is_set():
            try:
                data: WsMessage = self.read_msg_queue.get(timeout=1)
                self.on_message(data)
            except Empty:
                continue
            except Exception as e:
                logging.error(f"Error process callback message error: {e}")
        logging.info("Exiting read loop...")

    def reconnect_loop(self):
        logging.info("Reconnecting loop started")
        while not self.shutdown.is_set():
            if self.reconnecting.is_set():
                try:
                    logging.info("Broken WebSocket connection, starting reconnection")
                    self.on_event(WebSocketEvent.EVENT_TRY_RECONNECT, "", "")
                    attempt = 0

                    while True:
                        if not self.options.reconnect or (
                                self.options.reconnect_attempts != -1 and attempt >= self.options.reconnect_attempts):
                            logging.error("Max reconnect attempts reached or reconnect disabled")
                            self.on_event(WebSocketEvent.EVENT_CLIENT_FAIL, "", "")
                            break

                        logging.info(
                            f"Reconnecting in {self.options.reconnect_interval} seconds... (attempt {attempt})")
                        time.sleep(self.options.reconnect_interval)
                        try:
                            with self.conn_lock:
                                self.close()
                                self.dial()
                            self.connected.set()
                            self.on_event(WebSocketEvent.EVENT_CONNECTED, "", "")
                            self.run()
                            self.on_reconnected()
                            logging.info("Reconnect success")
                            break
                        except Exception as err:
                            logging.error(f"Reconnect attempt {attempt} failed: {err}")
                            attempt += 1


                finally:
                    self.reconnecting.clear()
            time.sleep(1)
        logging.info("Exiting read loop...")

    def keep_alive_loop(self):
        interval = self.token_info.ping_interval / 1000.0
        timeout = self.token_info.ping_timeout / 1000.0
        last_ping_time = time.time()

        while not self.close_event.is_set():
            current_time = time.time()
            if current_time - last_ping_time >= interval:
                ping_msg = self.new_ping_message()
                try:
                    self.write(ping_msg, timeout=timeout)
                except TimeoutError:
                    logging.error("Heartbeat ping timeout")
                except Exception as e:
                    logging.error(f"Exception in keep_alive: {e}")
                last_ping_time = current_time
            time.sleep(1)
        logging.info("Exiting keep alive loop...")

    def dial(self):
        try:
            self.welcome_received.clear()
            token_infos = self.token_provider.get_token()
            self.token_info = self.random_endpoint(token_infos)
            query_params = {
                "connectId": str(int(time.time() * 1e9)),
                "token": self.token_info.token,
            }
            url_str = f"{self.token_info.endpoint}?{urllib.parse.urlencode(query_params)}"
            self.conn = websocket.WebSocketApp(
                url_str,
                on_message=self.on_message_cb,
                on_error=self.on_error_cb,
                on_close=self.on_close_cb,
                on_open=self.on_open_cb,
            )
            if not self.ws_thread or not self.ws_thread.is_alive():
                self.ws_thread = threading.Thread(target=self.conn.run_forever, daemon=True)
                self.ws_thread.start()
            if not self.welcome_received.wait(timeout=5):
                self.close()
                raise Exception("Did not receive welcome message")
            self.close_event.clear()
            self.shutdown.clear()
        except Exception as err:
            self.connected.clear()
            logging.error(f"Failed to connect or validate welcome message: {err}")
            raise

    def on_open_cb(self, ws):
        logging.info("WebSocket connection opened.")

    def on_error_cb(self, ws, error):
        logging.error(f"WebSocket error: {error}")

    def on_close_cb(self, ws, close_status_code, close_msg):
        logging.info(f"WebSocket closed with status code {close_status_code}, message: {close_msg}")
        if self.shutdown.is_set():
            return
        self.reconnecting.set()

    def on_message_cb(self, ws, message):
        if logging.root.level <= logging.DEBUG:
            logging.debug(f"Received message: {message}")
            pass
        m = WsMessage.from_json(message)
        if m.type == WsMessageType.WELCOME.value:
            self.welcome_received.set()
            logging.info("Welcome message received.")

        elif m.type == WsMessageType.MESSAGE.value:
            self.on_event(WebSocketEvent.EVENT_MESSAGE_RECEIVED, "", "")
            try:
                logging.debug(f"queue size: {self.read_msg_queue.qsize()}, max size: {self.read_msg_queue.maxsize}")
                self.read_msg_queue.put(m, block=False)
            except Full:
                self.on_event(WebSocketEvent.EVENT_READ_BUFFER_FULL, "", "")
                logging.warning("Read buffer full")

        elif m.type == WsMessageType.PONG.value:
            self.on_event(WebSocketEvent.EVENT_PONG_RECEIVED, "", "")
            logging.debug("PONG received")
            self._handle_ack_event(m)

        elif m.type in [WsMessageType.ACK.value, WsMessageType.ERROR.value]:
            self._handle_ack_event(m)

        else:
            logging.warning(f"Unknown message type: {m.type}")

    def _handle_ack_event(self, m: WsMessage):
        with self.ack_event_lock:
            data: WriteMsg = self.ack_event.pop(m.id, None)
            if not data:
                logging.warning(f"Cannot find ack event, id: {m.id}, error message:{m}")
                return
            if m.type == WsMessageType.ERROR.value:
                error = m.raw_data
                self.on_event(WebSocketEvent.EVENT_ERROR_RECEIVED, "", error)
                data.set_exception(error)
            else:
                data.event.set()

    def write(self, ms: WsMessage, timeout: float) -> WriteMsg:
        logging.debug(f"Write message: {ms}")
        if not self.connected.is_set():
            raise Exception("Not connected")

        msg = WriteMsg(msg=ms, timeout=timeout)
        with self.ack_event_lock:
            self.ack_event[ms.id] = msg
            try:
                self.write_msg_queue.put(msg)
            except Full:
                logging.warning(f"Write buffer is full for message ID {ms.id}.")
                self.ack_event.pop(ms.id, None)
            except Exception as e:
                logging.error(f"Exception in write method: {e}")
                self.ack_event.pop(ms.id, None)
                raise
            finally:
                return msg

    def close(self):
        if self.connected.is_set():
            self.conn.close()
            self.conn = None
            logging.info("WebSocket connection closed.")
            logging.info("Waiting all threads close...")
            self.close_event.set()
            self.write_thread.join()
            self.read_thread.join()
            self.keep_alive_thread.join()
            self.ws_thread.join()

            with self.ack_event_lock:
                for msg in self.ack_event.values():
                    msg.set_exception(RuntimeError("connection closed"))
                self.ack_event.clear()

            while not self.read_msg_queue.empty():
                self.read_msg_queue.get_nowait()
            while not self.write_msg_queue.empty():
                self.write_msg_queue.get_nowait()

            self.on_event(WebSocketEvent.EVENT_DISCONNECTED, "", "")
            self.connected.clear()
        logging.info("WebSocket client closed.")

    def random_endpoint(self, tokens: List[WsToken]) -> Optional[WsToken]:
        if not tokens:
            raise ValueError("Tokens list is empty")
        return random.choice(tokens)

    def new_ping_message(self) -> WsMessage:
        return WsMessage(
            id=str(int(time.time() * 1e9)),
            type=WsMessageType.PING.value,
        )
