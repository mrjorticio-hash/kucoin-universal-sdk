import threading
import time
from abc import ABC, abstractmethod
from typing import Any, Dict, List, Optional

from pydantic import BaseModel, Field

from kucoin_universal_sdk.model.common import WsMessage


class WebSocketMessageCallback(ABC):
    @abstractmethod
    def on_message(self, message: WsMessage):
        """
        Handles incoming WebSocket messages.

        :param message: The WsMessage object to process
        """
        pass


class WriteMsg:
    def __init__(self, msg: WsMessage, timeout: float):
        self.msg = msg
        self.ts = time.time()
        self.timeout = timeout
        self.exception = None
        self.event = threading.Event()

    def set_exception(self, exception: Exception):
        self.exception = exception
        self.event.set()


class WebsocketTransport(ABC):
    @abstractmethod
    def start(self):
        pass

    @abstractmethod
    def stop(self):
        pass

    @abstractmethod
    def write(self, ms: WsMessage, timeout: float) -> WriteMsg:
        pass


class WebSocketService(ABC):
    @abstractmethod
    def start(self):
        """
        Starts the WebSocket service and handles incoming WebSocket messages.

        :return: Exception if an error occurs, otherwise None.
        """
        pass

    @abstractmethod
    def stop(self):
        """
        Stops the WebSocket service.

        :return: Exception if an error occurs, otherwise None.
        """
        pass

    @abstractmethod
    def subscribe(self, topic_prefix: str, args: List[str], callback: WebSocketMessageCallback) -> str:
        """
        Subscribes to a topic with a provided message callback.

        :param topic_prefix: The topic to subscribe to
        :param args: Arguments for the subscription
        :param callback: A callback for handling messages on this topic
        :return: Subscription ID and an Exception if an error occurs, otherwise None.
        """
        pass

    @abstractmethod
    def unsubscribe(self, id: str):
        """
        Unsubscribes from a topic.

        :param id: The subscription ID
        :return: Exception if an error occurs, otherwise None.
        """
        pass


class WsToken(BaseModel):
    """
    WsToken holds the token and API endpoint for a WebSocket connection.
    """
    token: Optional[str] = Field(default=None, description="The token for authentication")
    ping_interval: Optional[int] = Field(default=None, alias="pingInterval",
                                         description="Interval between ping messages (in milliseconds)")
    endpoint: Optional[str] = Field(default=None, description="The WebSocket API endpoint")
    protocol: Optional[str] = Field(default=None, description="Protocol used for WebSocket connection")
    encrypt: Optional[bool] = Field(default=None, description="Indicates if the connection is encrypted")
    ping_timeout: Optional[int] = Field(default=None, alias="pingTimeout",
                                        description="Ping timeout duration (in milliseconds)")

    @classmethod
    def from_dict(cls, obj: Optional[Dict[str, Any]]):
        if obj is None:
            return None

        if not isinstance(obj, dict):
            return cls.model_validate(obj)

        _obj = cls.model_validate({
            "token":
                obj.get("token"),
            "pingInterval":
                obj.get("pingInterval"),
            "endpoint":
                obj.get("endpoint"),
            "protocol":
                obj.get("protocol"),
            "encrypt":
                obj.get("encrypt"),
            "pingTimeout":
                obj.get("pingTimeout")
        })
        return _obj


class WsTokenProvider(ABC):
    @abstractmethod
    def get_token(self) -> List[WsToken]:
        """
        GetToken retrieves a list of WebSocket tokens.

        :return: list of WsToken objects.
        """
        pass

    @abstractmethod
    def close(self):
        """
        Close WebSocket token provider http connect.
        """
        pass
