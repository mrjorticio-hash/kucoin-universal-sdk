from __future__ import annotations

from enum import Enum, auto
from typing import Callable, Optional


class WebSocketEvent(Enum):
    """
    WebSocketEvent defines the types of WebSocket events.

    Attributes:
        EVENT_CONNECTED: Connection established event.
        EVENT_DISCONNECTED: Connection closed event.
        EVENT_TRY_RECONNECT: Try to reconnect event.
        EVENT_MESSAGE_RECEIVED: Message received event.
        EVENT_ERROR_RECEIVED: Error occurred event.
        EVENT_PONG_RECEIVED: Pong message received event.
        EVENT_READ_BUFFER_FULL: The read buffer of WebSocket is full.
        EVENT_WRITE_BUFFER_FULL: The write buffer of WebSocket is full.
        EVENT_CALLBACK_ERROR: An event triggered when an error occurs during a callback operation.
        EVENT_RE_SUBSCRIBE_OK: ReSubscription success event.
        EVENT_RE_SUBSCRIBE_ERROR: ReSubscription error event.
        EVENT_CLIENT_FAIL: Client failure event.
        EVENT_CLIENT_SHUTDOWN: Client shutdown event.
    """

    EVENT_CONNECTED = auto()  # Connection established event
    EVENT_DISCONNECTED = auto()  # Connection closed event
    EVENT_TRY_RECONNECT = auto()  # Try to reconnect event
    EVENT_MESSAGE_RECEIVED = auto()  # Message received event
    EVENT_ERROR_RECEIVED = auto()  # Error occurred event
    EVENT_PONG_RECEIVED = auto()  # Pong message received event
    EVENT_READ_BUFFER_FULL = auto()  # The read buffer of WebSocket is full
    EVENT_WRITE_BUFFER_FULL = auto()  # The write buffer of WebSocket is full
    EVENT_CALLBACK_ERROR = auto()  # An event triggered when an error occurs during a callback operation
    EVENT_RE_SUBSCRIBE_OK = auto()  # ReSubscription success event
    EVENT_RE_SUBSCRIBE_ERROR = auto()  # ReSubscription error event
    EVENT_CLIENT_FAIL = auto()  # Client failure event
    EVENT_CLIENT_SHUTDOWN = auto()  # Client shutdown event

    def __str__(self):
        return self.name


WebSocketCallback = Callable[[WebSocketEvent, str, str], None]
"""
WebSocketCallback defines the signature for callback functions handling WebSocket events.

Parameters:
    event_type (WebSocketEvent): The type of WebSocket event (use WebSocketEvent for clarity).
    event_data (str): The primary data related to the event, such as the message received.
    event_message (str): Additional message for the event, which could include metadata or error details.
"""


class WebSocketClientOption:
    def __init__(self,
                 reconnect: bool = True,
                 reconnect_attempts: int = -1,
                 reconnect_interval: float = 5.0,
                 dial_timeout: float = 10.0,
                 read_message_buffer: int = 1024,
                 write_message_buffer: int = 256,
                 write_timeout: float = 5.0,
                 event_callback: Optional[WebSocketCallback] = None,
                 auto_resubscribe_max_attempts: int = 3):
        """
        Initializes WebSocket client options for managing connection behavior and settings.

        Parameters:
            reconnect (bool): Enables automatic reconnection if the connection is lost. Defaults to True.
            reconnect_attempts (int): Maximum number of reconnection attempts; -1 for unlimited attempts. Defaults to -1.
            reconnect_interval (int): Interval between reconnection attempts in seconds. Defaults to 5.0.
            dial_timeout (int): Timeout duration for establishing a WebSocket connection in seconds. Defaults to 10.0.
            read_message_buffer (int): Buffer size for reading messages in the queue. Defaults to 1024.
            write_message_buffer (int): Buffer size for writing messages in the queue. Defaults to 256.
            write_timeout (int): Timeout for sending messages in seconds. Defaults to 5.0.
            event_callback (Optional[WebSocketCallback]): A callback function to handle WebSocket events. Defaults to None.
            auto_resubscribe_max_attempts(int): Maximum number of retry attempts for automatic resubscription upon failure. After exceeding this limit, failed items will not be retried further.
        """

        self.reconnect = reconnect
        self.reconnect_attempts = reconnect_attempts
        self.reconnect_interval = reconnect_interval
        self.dial_timeout = dial_timeout
        self.read_message_buffer = read_message_buffer
        self.write_message_buffer = write_message_buffer
        self.write_timeout = write_timeout
        self.event_callback = event_callback
        self.auto_resubscribe_max_attempts = auto_resubscribe_max_attempts


class WebSocketClientOptionBuilder:
    def __init__(self):
        """
        WebSocketClientOptionBuilder is a builder for WebSocketClientOption
        """
        self.option = WebSocketClientOption()

    def with_reconnect(self, reconnect: bool) -> WebSocketClientOptionBuilder:
        self.option.reconnect = reconnect
        return self

    def with_reconnect_attempts(self, attempts: int) -> WebSocketClientOptionBuilder:
        self.option.reconnect_attempts = attempts
        return self

    def with_reconnect_interval(self, interval: int) -> WebSocketClientOptionBuilder:
        self.option.reconnect_interval = interval
        return self

    def with_dial_timeout(self, timeout: int) -> WebSocketClientOptionBuilder:
        self.option.dial_timeout = timeout
        return self

    def with_read_message_buffer(self, read_message_buffer: int) -> WebSocketClientOptionBuilder:
        self.option.read_message_buffer = read_message_buffer
        return self

    def with_write_message_buffer(self, write_message_buffer: int) -> WebSocketClientOptionBuilder:
        self.option.write_message_buffer = write_message_buffer
        return self

    def with_write_timeout(self, timeout: int) -> WebSocketClientOptionBuilder:
        self.option.write_timeout = timeout
        return self

    def with_event_callback(self, callback: WebSocketCallback) -> WebSocketClientOptionBuilder:
        self.option.event_callback = callback
        return self

    def with_auto_resubscribe_max_attempts(self, auto_resubscribe_max_attempts: int) -> WebSocketClientOptionBuilder:
        self.option.auto_resubscribe_max_attempts = auto_resubscribe_max_attempts
        return self

    def build(self) -> WebSocketClientOption:
        return self.option
