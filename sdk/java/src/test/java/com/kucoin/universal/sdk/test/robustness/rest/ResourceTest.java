package com.kucoin.universal.sdk.test.robustness.rest;

import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import com.kucoin.universal.sdk.test.robustness.ResourceLeakStat;
import java.time.Duration;
import java.util.concurrent.CountDownLatch;
import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;

@Slf4j
public class ResourceTest {

  @Test
  public void testPools() throws Exception {
    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    ResourceLeakStat.stat("begin");
    int start = Math.toIntExact(ResourceLeakStat.getTcpConnCount());

    TransportOption httpTransport =
        TransportOption.builder()
            .maxIdleConnections(2)
            .maxRequests(2)
            .maxRequestsPerHost(2)
            .keepAlive(true)
            .keepAliveDuration(Duration.ofSeconds(5))
            .build();

    ClientOption clientOpt =
        ClientOption.builder()
            .key(key)
            .secret(secret)
            .passphrase(passphrase)
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(httpTransport)
            .build();

    KucoinClient kucoinClient = new DefaultKucoinClient(clientOpt);
    for (int i = 0; i < 5; i++) {
      try {
        kucoinClient.getRestService().getAccountService().getAccountApi().getAccountInfo();
      } catch (Exception e) {
        Assertions.fail(e);
      }
    }

    ResourceLeakStat.stat("end");
    int end = Math.toIntExact(ResourceLeakStat.getTcpConnCount());
    Assertions.assertEquals(1, end - start);
  }

  @Test
  public void testResourceLeak() throws Exception {
    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    ResourceLeakStat.stat("begin");
    TransportOption httpTransport =
        TransportOption.builder()
            .maxIdleConnections(2)
            .maxRequests(2)
            .maxRequestsPerHost(2)
            .keepAlive(true)
            .keepAliveDuration(Duration.ofSeconds(5))
            .build();

    ClientOption clientOpt =
        ClientOption.builder()
            .key(key)
            .secret(secret)
            .passphrase(passphrase)
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(httpTransport)
            .build();

    KucoinClient kucoinClient = new DefaultKucoinClient(clientOpt);

    ResourceLeakStat.stat("begin");
    CountDownLatch latch = new CountDownLatch(2);
    for (int i = 0; i < 2; i++) {
      new Thread(
              () -> {
                try {
                  for (int j = 0; j < 100; j++) {
                    kucoinClient
                        .getRestService()
                        .getSpotService()
                        .getMarketApi()
                        .getAllCurrencies();
                    Thread.sleep(1000);
                  }
                } catch (InterruptedException e) {
                  throw new RuntimeException(e);
                } finally {
                  latch.countDown();
                }
              })
          .start();
    }

    new Thread(
            () -> {
              while (true) {
                try {
                  Thread.sleep(1000);
                } catch (InterruptedException e) {
                  throw new RuntimeException(e);
                }
                ResourceLeakStat.stat("end");
              }
            })
        .start();

    latch.await();
    ResourceLeakStat.stat("end");
  }
}
