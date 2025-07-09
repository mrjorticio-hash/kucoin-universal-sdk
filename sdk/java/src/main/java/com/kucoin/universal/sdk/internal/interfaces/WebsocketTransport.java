package com.kucoin.universal.sdk.internal.interfaces;

import com.kucoin.universal.sdk.model.WsMessage;
import java.time.Duration;
import java.util.concurrent.CompletableFuture;

public interface WebsocketTransport {

  /** Establishes the connection and launches background loops. */
  void start();

  /** Closes the connection and stops all loops. */
  void stop();

  /** Enqueues a message for sending. */
  CompletableFuture<Void> write(WsMessage msg, Duration timeout);
}
