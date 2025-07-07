package com.kucoin.universal.sdk.model;

public enum WebSocketEvent {
  CONNECTED, // connection established
  DISCONNECTED, // closed by server or client
  TRY_RECONNECT, // about to reconnect
  MESSAGE_RECEIVED, // text / binary msg arrived
  ERROR_RECEIVED, // I/O or protocol error
  PONG_RECEIVED, // received pong
  READ_BUFFER_FULL, // inbound queue is full
  WRITE_BUFFER_FULL, // outbound queue is full
  CALLBACK_ERROR, // user-callback threw exception
  RE_SUBSCRIBE_OK, // resubscribe succeeded
  RE_SUBSCRIBE_ERROR, // resubscribe failed
  CLIENT_FAIL, // fatal failure, client unusable
  CLIENT_SHUTDOWN; // client closed normally
}
