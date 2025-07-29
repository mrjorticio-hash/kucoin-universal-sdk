package com.kucoin.universal.sdk.test.e2e.ws.margin;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.margin.marginprivate.MarginPrivateWs;
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
public class PrivateTest {

  private static MarginPrivateWs api;

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
    api = kucoinClient.getWsService().newMarginPrivateWS();
    api.start();
  }

  @AfterAll
  public static void tearDown() {
    api.stop();
  }

  @Test
  public void testCrossMarginPosition() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.crossMarginPosition(
                    (__, ___, event) -> {
                      Assertions.assertNotNull(event.getDebtRatio());
                      Assertions.assertNotNull(event.getTotalAsset());
                      Assertions.assertNotNull(event.getMarginCoefficientTotalAsset());
                      Assertions.assertNotNull(event.getTotalDebt());
                      Assertions.assertNotNull(event.getAssetList());
                      Assertions.assertNotNull(event.getDebtList());
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
  public void testIsolatedMarginPosition() {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.isolatedMarginPosition(
                    "ETH-USDT",
                    (__, ___, event) -> {
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
