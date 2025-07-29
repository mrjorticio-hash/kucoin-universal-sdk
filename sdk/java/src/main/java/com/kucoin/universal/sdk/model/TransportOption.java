package com.kucoin.universal.sdk.model;

import java.net.Proxy;
import java.time.Duration;
import java.util.Collections;
import java.util.List;
import java.util.Optional;
import java.util.concurrent.ExecutorService;
import lombok.*;
import okhttp3.EventListener;
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

  /** Global dispatcher limit (default 256). */
  @Builder.Default private final int maxRequests = 256;

  /** Per-host dispatcher limit (default 32). */
  @Builder.Default private final int maxRequestsPerHost = 32;

  /* ---------- timeout ---------- */

  @Builder.Default private final Duration connectTimeout = Duration.ofSeconds(10);
  @Builder.Default private final Duration readTimeout = Duration.ofSeconds(30);
  @Builder.Default private final Duration writeTimeout = Duration.ofSeconds(30);

  /** Hard deadline for the entire call; {@code 0} disables it. */
  @Builder.Default private final Duration callTimeout = Duration.ZERO;

  /** HTTP/2 ping interval; {@code 0} disables pings. */
  @Builder.Default private final Duration pingInterval = Duration.ZERO;

  /* ----------- proxy ------------ */

  /** Proxy; {@code null} means "no proxy". */
  @Builder.Default private final Proxy proxy = null;

  /* ---------- retry / redirect ---------- */
  @Builder.Default private final boolean retryOnConnectionFailure = true;

  /* ---------- interceptors&listener ---------- */

  /** Application interceptors – executed before routing / retries. */
  @Singular("interceptor")
  private final List<Interceptor> interceptors;

  /** Optional event listener for connection lifecycle logging, etc. */
  @Builder.Default private final EventListener eventListener = null;

  /** Custom executor for Dispatcher (call execution); null means use default */
  @Builder.Default private final ExecutorService dispatcherExecutor = null;

  public Optional<Proxy> proxy() {
    return Optional.ofNullable(proxy);
  }

  public Optional<EventListener> eventListener() {
    return Optional.ofNullable(eventListener);
  }

  public Optional<ExecutorService> dispatcherExecutor() {
    return Optional.ofNullable(dispatcherExecutor);
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
