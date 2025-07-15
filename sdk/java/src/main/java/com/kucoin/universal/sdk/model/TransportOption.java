package com.kucoin.universal.sdk.model;

import java.net.Proxy;
import java.time.Duration;
import java.util.Collections;
import java.util.List;
import java.util.Optional;
import lombok.*;
import okhttp3.Interceptor;

/** TransportOption holds configurations for HTTP client behavior. */
@Getter
@Builder(toBuilder = true)
@AllArgsConstructor(access = AccessLevel.PRIVATE)
public final class TransportOption {

  /* ---------- connection & pooling ---------- */

  /** Enable connection pooling / HTTP keep-alive (OkHttp default: {@code true}). */
  @Builder.Default private final boolean keepAlive = true;

  /** Maximum idle connections kept in the pool (OkHttp default: {@code 5}). */
  @Builder.Default private final int maxIdleConnections = 5;

  /** Idle connection eviction threshold. */
  @Builder.Default private final Duration keepAliveDuration = Duration.ofSeconds(30);

  /** Global dispatcher limit (default 64). */
  @Builder.Default private final int maxRequests = 64;

  /** Per-host dispatcher limit (default 5). */
  @Builder.Default private final int maxRequestsPerHost = 5;

  /* ---------- timeout ---------- */

  @Builder.Default private final Duration connectTimeout = Duration.ofSeconds(10);
  @Builder.Default private final Duration readTimeout = Duration.ofSeconds(30);
  @Builder.Default private final Duration writeTimeout = Duration.ofSeconds(30);

  /** Hard deadline for the entire call; {@code 0} disables it. */
  @Builder.Default private final Duration callTimeout = Duration.ZERO;

  /** HTTP/2 ping interval; {@code 0} disables pings. */
  @Builder.Default private final Duration pingInterval = Duration.ZERO;

  /* ----------- proxy ------------ */

  /** Pre-configured proxy; {@code null} means “use JVM default”. */
  private final Proxy proxy;

  /* ---------- retry / redirect ---------- */
  @Builder.Default private final boolean retryOnConnectionFailure = true;

  /** SDK-level retry attempts for idempotent requests. */
  @Builder.Default private final int maxRetries = 3;

  /** Delay between retries. */
  @Builder.Default private final Duration retryDelay = Duration.ofSeconds(2);

  /* ---------- interceptors ---------- */

  /** Application interceptors – executed before routing / retries. */
  @Singular("interceptor")
  private final List<Interceptor> interceptors;

  /* ---------- convenience getters ---------- */

  public Optional<Proxy> proxy() {
    return Optional.ofNullable(proxy);
  }

  public List<Interceptor> interceptors() {
    return interceptors == null
        ? Collections.emptyList()
        : Collections.unmodifiableList(interceptors);
  }

  /** no-op option with all defaults */
  public static TransportOption defaults() {
    return TransportOption.builder().build();
  }
}
