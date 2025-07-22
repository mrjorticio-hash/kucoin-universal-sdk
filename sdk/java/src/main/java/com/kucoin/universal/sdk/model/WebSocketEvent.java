package com.kucoin.universal.sdk.model;

public enum WebSocketEvent {
  CONNECTED, // connection established
  DISCONNECTED, // closed by server or client
  TRY_RECONNECT, // about to reconnect
  ERROR_RECEIVED, // I/O or protocol error
  CALLBACK_ERROR, // user-callback threw exception
  RE_SUBSCRIBE_OK, // resubscribe succeeded
  RE_SUBSCRIBE_ERROR, // resubscribe failed
  CLIENT_FAIL, // fatal failure, client unusable
  CLIENT_SHUTDOWN; // client closed normally
}
