package com.kucoin.universal.sdk.internal.interfaces;

public interface WebSocketService {

  /** Starts the WebSocket service and handles incoming messages. */
  void start();

  /** Stops the WebSocket service. */
  void stop();

  /** Subscribes to a topic with a callback handler. */
  String subscribe(String prefix, String[] args, WebSocketMessageCallback<?> callback);

  /** Unsubscribes from a topic. */
  void unsubscribe(String id);
}
