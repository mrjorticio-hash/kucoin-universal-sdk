package com.kucoin.universal.sdk.e2e.ws.spot;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.Client;
import com.kucoin.universal.sdk.api.DefaultClient;
import com.kucoin.universal.sdk.generate.spot.spotpublic.SpotPublicWs;
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

  private static SpotPublicWs api;

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

    Client client = new DefaultClient(clientOpt);
    api = client.getWsService().newSpotPublicWS();
    api.start();
  }

  @AfterAll
  public static void tearDown() {
    api.stop();
  }
    @Test
    public void testKlines() throws Exception {
        CountDownLatch gotEvent = new CountDownLatch(10);
        CompletableFuture.supplyAsync(
                        () ->
                                api.klines(
                                        "BTC-USDT", "1min",
                                        (a, b, event) -> {
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
                .thenAccept(id -> api.unSubscribe(id)).join();
    }


    @Test
    public void testOrderbookIncrement() throws Exception {
        CountDownLatch gotEvent = new CountDownLatch(10);
        CompletableFuture.supplyAsync(
                        () ->
                                api.orderbookIncrement(
                                        new String[]{"BTC-USDT", "ETH-USDT"},
                                        (a, b, event) -> {
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
                .thenAccept(id -> api.unSubscribe(id)).join();
    }


  @Test
  public void testMarketSnapshot() throws Exception {
    CountDownLatch gotEvent = new CountDownLatch(1);
    CompletableFuture.supplyAsync(
            () ->
                api.marketSnapshot(
                    "BTC-USDT",
                    (a, b, event) -> {
                      Assertions.assertNotNull(event.getSequence());
                      Assertions.assertNotNull(event.getData());
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
        .thenAccept(id -> api.unSubscribe(id)).join();
  }
}
