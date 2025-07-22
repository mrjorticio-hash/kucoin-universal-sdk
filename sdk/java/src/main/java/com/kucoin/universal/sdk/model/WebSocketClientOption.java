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
     * @param message additional description / error detail
     */
    void onEvent(WebSocketEvent event, String message);
  }

  /** auto reconnect after disconnect */
  @Builder.Default private final boolean reconnect = true;

  /** max reconnect attempts; -1 = unlimited */
  @Builder.Default private final int reconnectAttempts = -1;

  /** interval between reconnect attempts */
  @Builder.Default private final Duration reconnectInterval = Duration.ofSeconds(5);

  /** dial (hand-shake) timeout */
  @Builder.Default private final Duration dialTimeout = Duration.ofSeconds(10);

  /** single send timeout */
  @Builder.Default private final Duration writeTimeout = Duration.ofSeconds(5);

  /** event dispatcher; may be {@code null} */
  @Builder.Default private final WebSocketCallback eventCallback = null;

  /* ---------------- helper ---------------- */

  /** no-op option with all defaults */
  public static WebSocketClientOption defaults() {
    return WebSocketClientOption.builder().build();
  }
}
