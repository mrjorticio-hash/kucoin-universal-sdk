package com.kucoin.universal.sdk.test.e2e.ws.futures;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.futures.futurespublic.FuturesPublicWs;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import com.kucoin.universal.sdk.model.WebSocketClientOption;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.CountDownLatch;
import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.AfterAll;
import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.Test;

@Slf4j
public class PublicTest {

  private static FuturesPublicWs api;

  public static ObjectMapper mapper = new ObjectMapper();

  @BeforeAll
  public static void setUp() {

    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    WebSocketClientOption webSocketClientOption = WebSocketClientOption.defaults();

    ClientOption clientOpt =
        ClientOption.builder()
            .key(key)
            .secret(secret)
            .passphrase(passphrase)
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(TransportOption.defaults())
            .websocketClientOption(webSocketClientOption)
            .build();

    KucoinClient kucoinClient = new DefaultKucoinClient(clientOpt);
    api = kucoinClient.getWsService().newFuturesPublicWS();
    api.start();
  }

  @AfterAll
  public static void tearDown() {
    api.stop();
  }

  // TODO
  @Test
  public void testAnnouncement() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.announcement(
                    "XBTUSDTM",
                    (__, ___, event) -> {
                      Assertions.assertNotNull(event.getSymbol());
                      Assertions.assertNotNull(event.getFundingTime());
                      Assertions.assertNotNull(event.getFundingRate());
                      Assertions.assertNotNull(event.getTimestamp());
                      log.info("event: {}", event.toString());
                      gotEvent.countDown();
                    }))
        .thenApply(
            id -> {
              try {
                gotEvent.await();
              } catch (InterruptedException e) {
                throw new RuntimeException(e);
              }
              return id;
            })
        .thenAccept(id -> api.unSubscribe(id))
        .join();
  }

  @Test
  public void testExecution() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.execution(
                    "XBTUSDTM",
                    (__, ___, event) -> {
                      Assertions.assertNotNull(event.getSymbol());
                      Assertions.assertNotNull(event.getSequence());
                      Assertions.assertNotNull(event.getSide());
                      Assertions.assertNotNull(event.getSize());
                      Assertions.assertNotNull(event.getPrice());
                      Assertions.assertNotNull(event.getTakerOrderId());
                      Assertions.assertNotNull(event.getMakerOrderId());
                      Assertions.assertNotNull(event.getTradeId());
                      Assertions.assertNotNull(event.getTs());
                      log.info("event: {}", event.toString());
                      gotEvent.countDown();
                    }))
        .thenApply(
            id -> {
              try {
                gotEvent.await();
              } catch (InterruptedException e) {
                throw new RuntimeException(e);
              }
              return id;
            })
        .thenAccept(id -> api.unSubscribe(id))
        .join();
  }

  @Test
  public void testInstrument() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.instrument(
                    "XBTUSDTM",
                    (__, ___, event) -> {
                      Assertions.assertNotNull(event.getGranularity());
                      Assertions.assertNotNull(event.getTimestamp());
                      Assertions.assertNotNull(event.getMarkPrice());
                      Assertions.assertNotNull(event.getIndexPrice());
                      log.info("event: {}", event.toString());
                      gotEvent.countDown();
                    }))
        .thenApply(
            id -> {
              try {
                gotEvent.await();
              } catch (InterruptedException e) {
                throw new RuntimeException(e);
              }
              return id;
            })
        .thenAccept(id -> api.unSubscribe(id))
        .join();
  }

  @Test
  public void testKlines() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.klines(
                    "XBTUSDTM",
                    "1min",
                    (__, ___, event) -> {
                      Assertions.assertNotNull(event.getSymbol());
                      event.getCandles().forEach(item -> {});

                      Assertions.assertNotNull(event.getTime());
                      log.info("event: {}", event.toString());
                      gotEvent.countDown();
                    }))
        .thenApply(
            id -> {
              try {
                gotEvent.await();
              } catch (InterruptedException e) {
                throw new RuntimeException(e);
              }
              return id;
            })
        .thenAccept(id -> api.unSubscribe(id))
        .join();
  }

  @Test
  public void testOrderbookIncrement() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.orderbookIncrement(
                    "XBTUSDTM",
                    (__, ___, event) -> {
                      Assertions.assertNotNull(event.getSequence());
                      Assertions.assertNotNull(event.getChange());
                      Assertions.assertNotNull(event.getTimestamp());
                      log.info("event: {}", event.toString());
                      gotEvent.countDown();
                    }))
        .thenApply(
            id -> {
              try {
                gotEvent.await();
              } catch (InterruptedException e) {
                throw new RuntimeException(e);
              }
              return id;
            })
        .thenAccept(id -> api.unSubscribe(id))
        .join();
  }

  @Test
  public void testOrderbookLevel50() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.orderbookLevel50(
                    "XBTUSDTM",
                    (__, ___, event) -> {
                      event.getBids().forEach(item -> {});

                      Assertions.assertNotNull(event.getSequence());
                      Assertions.assertNotNull(event.getTimestamp());
                      Assertions.assertNotNull(event.getTs());
                      event.getAsks().forEach(item -> {});

                      log.info("event: {}", event.toString());
                      gotEvent.countDown();
                    }))
        .thenApply(
            id -> {
              try {
                gotEvent.await();
              } catch (InterruptedException e) {
                throw new RuntimeException(e);
              }
              return id;
            })
        .thenAccept(id -> api.unSubscribe(id))
        .join();
  }

  @Test
  public void testOrderbookLevel5() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.orderbookLevel5(
                    "XBTUSDTM",
                    (__, ___, event) -> {
                      event.getBids().forEach(item -> {});

                      Assertions.assertNotNull(event.getSequence());
                      Assertions.assertNotNull(event.getTimestamp());
                      Assertions.assertNotNull(event.getTs());
                      event.getAsks().forEach(item -> {});

                      log.info("event: {}", event.toString());
                      gotEvent.countDown();
                    }))
        .thenApply(
            id -> {
              try {
                gotEvent.await();
              } catch (InterruptedException e) {
                throw new RuntimeException(e);
              }
              return id;
            })
        .thenAccept(id -> api.unSubscribe(id))
        .join();
  }

  @Test
  public void testSymbolSnapshot() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.symbolSnapshot(
                    "XBTUSDTM",
                    (__, ___, event) -> {
                      Assertions.assertNotNull(event.getHighPrice());
                      Assertions.assertNotNull(event.getLastPrice());
                      Assertions.assertNotNull(event.getLowPrice());
                      Assertions.assertNotNull(event.getPrice24HoursBefore());
                      Assertions.assertNotNull(event.getPriceChg());
                      Assertions.assertNotNull(event.getPriceChgPct());
                      Assertions.assertNotNull(event.getSymbol());
                      Assertions.assertNotNull(event.getTs());
                      Assertions.assertNotNull(event.getTurnover());
                      Assertions.assertNotNull(event.getVolume());
                      log.info("event: {}", event.toString());
                      gotEvent.countDown();
                    }))
        .thenApply(
            id -> {
              try {
                gotEvent.await();
              } catch (InterruptedException e) {
                throw new RuntimeException(e);
              }
              return id;
            })
        .thenAccept(id -> api.unSubscribe(id))
        .join();
  }

  @Test
  public void testTickerV1() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.tickerV1(
                    "XBTUSDTM",
                    (__, ___, event) -> {
                      Assertions.assertNotNull(event.getSymbol());
                      Assertions.assertNotNull(event.getSequence());
                      Assertions.assertNotNull(event.getSide());
                      Assertions.assertNotNull(event.getSize());
                      Assertions.assertNotNull(event.getPrice());
                      Assertions.assertNotNull(event.getBestBidSize());
                      Assertions.assertNotNull(event.getBestBidPrice());
                      Assertions.assertNotNull(event.getBestAskPrice());
                      Assertions.assertNotNull(event.getTradeId());
                      Assertions.assertNotNull(event.getBestAskSize());
                      Assertions.assertNotNull(event.getTs());
                      log.info("event: {}", event.toString());
                      gotEvent.countDown();
                    }))
        .thenApply(
            id -> {
              try {
                gotEvent.await();
              } catch (InterruptedException e) {
                throw new RuntimeException(e);
              }
              return id;
            })
        .thenAccept(id -> api.unSubscribe(id))
        .join();
  }

  @Test
  public void testTickerV2() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.tickerV2(
                    "XBTUSDTM",
                    (__, ___, event) -> {
                      Assertions.assertNotNull(event.getSymbol());
                      Assertions.assertNotNull(event.getSequence());
                      Assertions.assertNotNull(event.getBestBidSize());
                      Assertions.assertNotNull(event.getBestBidPrice());
                      Assertions.assertNotNull(event.getBestAskPrice());
                      Assertions.assertNotNull(event.getBestAskSize());
                      Assertions.assertNotNull(event.getTs());
                      log.info("event: {}", event.toString());
                      gotEvent.countDown();
                    }))
        .thenApply(
            id -> {
              try {
                gotEvent.await();
              } catch (InterruptedException e) {
                throw new RuntimeException(e);
              }
              return id;
            })
        .thenAccept(id -> api.unSubscribe(id))
        .join();
  }
}
