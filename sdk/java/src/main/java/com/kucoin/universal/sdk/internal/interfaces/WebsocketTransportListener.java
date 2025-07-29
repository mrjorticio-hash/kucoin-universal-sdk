package com.kucoin.universal.sdk.internal.interfaces;

import com.kucoin.universal.sdk.model.WebSocketEvent;
import com.kucoin.universal.sdk.model.WsMessage;

public interface WebsocketTransportListener {

  void onEvent(WebSocketEvent event, String message);

  void onMessage(WsMessage wsMessage);

  void onReconnected();
}
