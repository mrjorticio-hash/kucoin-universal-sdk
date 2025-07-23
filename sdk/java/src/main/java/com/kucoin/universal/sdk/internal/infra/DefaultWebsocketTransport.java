package com.kucoin.universal.sdk.internal.infra;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.internal.interfaces.WebsocketTransport;
import com.kucoin.universal.sdk.internal.interfaces.WebsocketTransportListener;
import com.kucoin.universal.sdk.internal.interfaces.WsToken;
import com.kucoin.universal.sdk.internal.interfaces.WsTokenProvider;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.WebSocketClientOption;
import com.kucoin.universal.sdk.model.WebSocketEvent;
import com.kucoin.universal.sdk.model.WsMessage;
import java.net.URI;
import java.time.Duration;
import java.util.Date;
import java.util.List;
import java.util.Map;
import java.util.concurrent.*;
import java.util.concurrent.atomic.AtomicBoolean;
import lombok.extern.slf4j.Slf4j;
import okhttp3.*;

/** OkHttp-based WebSocket transport with */
@Slf4j
public final class DefaultWebsocketTransport implements WebsocketTransport {

  private final WsTokenProvider tokenProvider;
  private final WebSocketClientOption opt;
  private final WebsocketTransportListener listener;

  private final OkHttpClient http;
  private final ObjectMapper mapper = new ObjectMapper();
  private final AtomicBoolean connected = new AtomicBoolean(false);
  private final AtomicBoolean shutting = new AtomicBoolean(false);
  private final AtomicBoolean reconnecting = new AtomicBoolean(false);
  private final Map<String, CompletableFuture<Void>> ackMap = new ConcurrentHashMap<>();
  private final ScheduledExecutorService scheduler =
      Executors.newSingleThreadScheduledExecutor(
          r -> {
            Thread t = new Thread(r);
            t.setName("ws-scheduler-single-pool");
            t.setDaemon(true);
            return t;
          });
  private volatile WebSocket socket;
  private volatile WsToken token;

  public DefaultWebsocketTransport(
      WsTokenProvider tokenProvider,
      WebSocketClientOption option,
      WebsocketTransportListener listener) {

    this.tokenProvider = tokenProvider;
    this.opt = option;
    this.listener = listener;
    this.http =
        new OkHttpClient()
            .newBuilder()
            .connectTimeout(option.getDialTimeout())
            .writeTimeout(option.getWriteTimeout())
            .build();
  }

  private static WsToken pick(List<WsToken> list) {
    if (list == null || list.isEmpty()) {
      throw new IllegalArgumentException("empty token list");
    }
    return list.get(ThreadLocalRandom.current().nextInt(list.size()));
  }

  private static <T> CompletableFuture<T> failed(Throwable ex) {
    CompletableFuture<T> f = new CompletableFuture<>();
    f.completeExceptionally(ex);
    return f;
  }

  @Override
  public void start() {
    dial();
    schedulePing();
  }

  @Override
  public void stop() {
    shutting.set(true);
    safeClose("shutdown");
    scheduler.shutdownNow();
    tokenProvider.close();
    log.info("websocket closed");
    listener.onEvent(WebSocketEvent.CLIENT_SHUTDOWN, "");
  }

  @Override
  public CompletableFuture<Void> write(WsMessage m, Duration timeout) {
    if (!connected.get()) {
      return failed(new IllegalStateException("not connected"));
    }

    CompletableFuture<Void> fut = new CompletableFuture<>();
    ackMap.put(m.getId(), fut);

    try {
      boolean queued = socket.send(mapper.writeValueAsString(m));
      if (!queued) {
        throw new IllegalStateException("OkHttp buffer full");
      }
    } catch (Exception e) {
      ackMap.remove(m.getId());
      return failed(e);
    }

    scheduler.schedule(
        () -> {
          if (ackMap.remove(m.getId()) != null) {
            fut.completeExceptionally(new TimeoutException("ack timeout"));
          }
        },
        timeout.toMillis(),
        TimeUnit.MILLISECONDS);

    return fut;
  }

  private void dial() {
    try {
      token = pick(tokenProvider.getToken());

      URI uri =
          URI.create(
              token.getEndpoint()
                  + "?connectId="
                  + System.nanoTime()
                  + "&token="
                  + token.getToken());

      Request req = new Request.Builder().url(uri.toString()).build();
      CountDownLatch welcome = new CountDownLatch(1);

      socket =
          http.newWebSocket(
              req,
              new WebSocketListener() {
                @Override
                public void onMessage(WebSocket w, String txt) {
                  handle(txt, welcome);
                }

                @Override
                public void onClosed(WebSocket w, int c, String r) {
                  tryReconnect(r);
                }

                @Override
                public void onFailure(WebSocket w, Throwable t, Response r) {
                  if (!shutting.get()) {
                    log.error("websocket emits error events", t);
                    return;
                  }
                  tryReconnect(t.getMessage());
                }
              });

      if (!welcome.await(opt.getDialTimeout().toMillis(), TimeUnit.MILLISECONDS)) {
        throw new IllegalStateException("welcome not received");
      }

      connected.set(true);
      listener.onEvent(WebSocketEvent.CONNECTED, "");
      log.info("Websocket connected");
    } catch (Exception e) {
      safeClose("dial-error");
      throw new RuntimeException(e);
    }
  }

  private void handle(String json, CountDownLatch welcome) {
    try {
      WsMessage m = mapper.readValue(json, WsMessage.class);
      switch (m.getType()) {
        case Constants.WS_MESSAGE_TYPE_WELCOME:
          {
            welcome.countDown();
            break;
          }
        case Constants.WS_MESSAGE_TYPE_MESSAGE:
          {
            listener.onMessage(m);
            break;
          }

        case Constants.WS_MESSAGE_TYPE_PONG:
        case Constants.WS_MESSAGE_TYPE_ACK:
        case Constants.WS_MESSAGE_TYPE_ERROR:
          {
            CompletableFuture<Void> f = ackMap.remove(m.getId());
            if (f == null) {
              break;
            }
            if (m.getType().equalsIgnoreCase(Constants.WS_MESSAGE_TYPE_ERROR)) {
              f.completeExceptionally(new RuntimeException(String.valueOf(m.getData())));
            } else {
              f.complete(null);
            }
            break;
          }
        default:
          {
            log.warn("unknown ws type {}", m.getType());
          }
      }
    } catch (Exception e) {
      log.error("decode err", e);
    }
  }

  private void schedulePing() {
    long interval = token.getPingInterval();
    long timeout = token.getPingTimeout();
    scheduler.scheduleAtFixedRate(
        () -> {
          if (!connected.get()) {
            return;
          }
          WsMessage ping = new WsMessage();
          ping.setId(String.valueOf(System.nanoTime()));
          ping.setType(Constants.WS_MESSAGE_TYPE_PING);
          write(ping, Duration.ofMillis(timeout))
              .exceptionally(
                  ex -> {
                    log.error("Schedule ping error", ex);
                    listener.onEvent(WebSocketEvent.ERROR_RECEIVED, ex.getMessage());
                    return null;
                  });
        },
        interval,
        interval,
        TimeUnit.MILLISECONDS);
  }

  private void tryReconnect(String reason) {
    if (shutting.get()) {
      return;
    }
    log.info("Websocket disconnected due to {}, Reconnection...", reason);
    safeClose(reason);

    if (!opt.isReconnect()) {
      log.warn("Reconnect failed: auto-reconnect is disabled");
      return;
    }

    if (!reconnecting.compareAndSet(false, true)) {
      log.warn("Another thread is reconnecting, skip current attempt");
      return;
    }

    Thread reconnectThread =
        new Thread(
            () -> {
              int attempt = 0;

              try {
                while (true) {
                  if (shutting.get()) {
                    log.info("Reconnect failed: client is shutting down");
                    return;
                  }
                  if (opt.getReconnectAttempts() != -1 && attempt >= opt.getReconnectAttempts()) {
                    log.info("Reconnect failed: maximum number of attempts exceeded");
                    listener.onEvent(WebSocketEvent.CLIENT_FAIL, "");
                    return;
                  }

                  try {
                    listener.onEvent(WebSocketEvent.TRY_RECONNECT, "attempt " + attempt);
                    dial();
                    listener.onEvent(WebSocketEvent.CONNECTED, "");
                    listener.onReconnected();
                    log.info("Reconnect successful");
                    return;
                  } catch (Exception ex) {
                    attempt++;
                    log.info(
                        "Reconnect failed, retry:{}, max:{}, reason:{}",
                        attempt,
                        opt.getReconnectAttempts(),
                        ex.getMessage());
                    try {
                      Thread.sleep(opt.getReconnectInterval().toMillis());
                    } catch (InterruptedException e) {
                      Thread.currentThread().interrupt();
                      return;
                    }
                  }
                }
              } finally {
                reconnecting.set(false);
              }
            });
    reconnectThread.setName(String.format("Reconnect-Thread-%s", new Date()));
    reconnectThread.setDaemon(true);
    reconnectThread.start();
  }

  private void safeClose(String reason) {
    try {
      listener.onEvent(WebSocketEvent.DISCONNECTED, "");
      ackMap
          .values()
          .forEach(f -> f.completeExceptionally(new RuntimeException("connection closed")));
      ackMap.clear();
      connected.set(false);
      if (socket != null) {
        socket.close(1000, reason);
        socket.cancel();
        http.connectionPool().evictAll();
        http.dispatcher().executorService().shutdown();
      }
    } catch (Exception e) {
      log.error("exception when safe close", e);
    }
  }
}
