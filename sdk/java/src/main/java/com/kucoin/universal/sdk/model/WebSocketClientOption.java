package com.kucoin.universal.sdk.model;

import java.time.Duration;
import lombok.*;

@Getter
@ToString
@Builder(toBuilder = true)
@AllArgsConstructor(access = AccessLevel.PRIVATE)
public final class WebSocketClientOption {

  @FunctionalInterface
  public interface WebSocketCallback {
    /**
     * @param event event type
     * @param data primary data (e.g. message payload)
     * @param message additional description / error detail
     */
    void onEvent(WebSocketEvent event, String data, String message);
  }

  /** auto reconnect after disconnect */
  @Builder.Default private final boolean reconnect = true;

  /** max reconnect attempts; -1 = unlimited */
  @Builder.Default private final int reconnectAttempts = -1;

  /** interval between reconnect attempts */
  @Builder.Default private final Duration reconnectInterval = Duration.ofSeconds(5);

  /** dial (hand-shake) timeout */
  @Builder.Default private final Duration dialTimeout = Duration.ofSeconds(10);

  /** inbound queue size (frames) */
  @Builder.Default private final int readMessageBuffer = 1024;

  /** outbound queue size (frames) */
  @Builder.Default private final int writeMessageBuffer = 256;

  /** single send timeout */
  @Builder.Default private final Duration writeTimeout = Duration.ofSeconds(5);

  /** event dispatcher; may be {@code null} */
  private final WebSocketCallback eventCallback;

  /** max retry for automatic resubscribe (per item) */
  @Builder.Default private final int autoResubscribeMaxAttempts = 3;

  /* ---------------- helper ---------------- */

  /** no-op option with all defaults */
  public static WebSocketClientOption defaults() {
    return WebSocketClientOption.builder().build();
  }

  /** apply event callback without touching other fields */
  public WebSocketClientOption withCallback(WebSocketCallback cb) {
    return toBuilder().eventCallback(cb).build();
  }
}
