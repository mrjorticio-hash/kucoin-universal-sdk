package com.kucoin.universal.sdk.internal.interfaces;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.model.WsMessage;

public interface WebSocketMessageCallback {

  /** Handles incoming WebSocket messages. */
  void onMessage(WsMessage message, ObjectMapper objectMapper);
}
