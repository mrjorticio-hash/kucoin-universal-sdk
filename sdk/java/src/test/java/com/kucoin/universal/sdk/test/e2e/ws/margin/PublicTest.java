package com.kucoin.universal.sdk.test.e2e.ws.margin;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.margin.marginpublic.MarginPublicWs;
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

  private static MarginPublicWs api;

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
    api = kucoinClient.getWsService().newMarginPublicWS();
    api.start();
  }

  @AfterAll
  public static void tearDown() {
    api.stop();
  }

  @Test
  public void testIndexPrice() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.indexPrice(
                    new String[] {"ETH-USDT", "BTC-USDT"},
                    (__, ___, event) -> {
                      Assertions.assertNotNull(event.getSymbol());
                      Assertions.assertNotNull(event.getGranularity());
                      Assertions.assertNotNull(event.getTimestamp());
                      Assertions.assertNotNull(event.getValue());
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
  public void testMarkPrice() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.markPrice(
                    new String[] {"ETH-USDT", "BTC-USDT"},
                    (__, ___, event) -> {
                      Assertions.assertNotNull(event.getSymbol());
                      Assertions.assertNotNull(event.getGranularity());
                      Assertions.assertNotNull(event.getTimestamp());
                      Assertions.assertNotNull(event.getValue());
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
