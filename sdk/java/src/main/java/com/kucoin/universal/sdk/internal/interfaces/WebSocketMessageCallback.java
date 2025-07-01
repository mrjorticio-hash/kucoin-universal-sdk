package com.kucoin.universal.sdk.internal.interfaces;

import com.kucoin.universal.sdk.model.WsMessage;

public interface WebSocketMessageCallback<T> {

  /** Handles incoming WebSocket messages. */
  void onMessage(WsMessage<T> message);
}
