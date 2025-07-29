package com.kucoin.test.regression;

import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.api.KucoinWSService;
import com.kucoin.universal.sdk.generate.spot.market.GetAllSymbolsReq;
import com.kucoin.universal.sdk.generate.spot.market.GetAllSymbolsResp;
import com.kucoin.universal.sdk.generate.spot.market.MarketApi;
import com.kucoin.universal.sdk.generate.spot.spotpublic.SpotPublicWs;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import com.kucoin.universal.sdk.model.WebSocketClientOption;
import java.util.concurrent.CountDownLatch;
import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.atomic.AtomicInteger;
import lombok.extern.slf4j.Slf4j;

/**
 * Runner class to periodically fetch market data, manage WebSocket subscriptions, and track
 * statistics.
 */
@Slf4j
public class RunForeverTest {

  private static final double STEP = 5.0; // seconds

  private AtomicInteger wsMsgCnt = new AtomicInteger(0);
  private AtomicInteger wsErrCnt = new AtomicInteger(0);
  private AtomicInteger mkErrCnt = new AtomicInteger(0);

  private final KucoinWSService wsSvc;
  private final MarketApi marketApi;
  private final ScheduledExecutorService executor;

  public RunForeverTest() {
    this.executor = Executors.newScheduledThreadPool(Runtime.getRuntime().availableProcessors());
    ClientOption clientOption =
        ClientOption.builder()
            .key(System.getenv("API_KEY"))
            .secret(System.getenv("API_SECRET"))
            .passphrase(System.getenv("API_PASSPHRASE"))
            .transportOption(TransportOption.builder().keepAlive(true).build())
            .websocketClientOption(WebSocketClientOption.defaults())
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .build();

    KucoinClient client = new DefaultKucoinClient(clientOption);
    this.wsSvc = client.getWsService();
    this.marketApi = client.getRestService().getSpotService().getMarketApi();
  }

  public void run() {
    try {
      marketLoop();
      wsForever();
      wsStartStopLoop();
      statLoop();

      // Keep the application running
      CountDownLatch latch = new CountDownLatch(1);
      latch.await();
    } catch (InterruptedException e) {
      log.error("Main thread interrupted", e);
      Thread.currentThread().interrupt();
    } finally {
      executor.shutdown();
    }
  }

  private void marketLoop() {
    executor.scheduleAtFixedRate(
        () -> {
          try {
            GetAllSymbolsResp resp =
                marketApi.getAllSymbols(GetAllSymbolsReq.builder().market("USDS").build());
            log.info("MARKET API [OK] {}", resp.getData().size());
          } catch (Exception e) {
            mkErrCnt.incrementAndGet();
            log.info("MARKET API [ERROR] " + e.getMessage());
          }
        },
        0,
        (long) STEP,
        TimeUnit.SECONDS);
  }

  private void wsForever() {
    SpotPublicWs ws = wsSvc.newSpotPublicWS();
    ws.start();
    ws.orderbookLevel50(
        new String[] {"ETH-USDT", "BTC-USDT"}, (____, __, ___) -> wsMsgCnt.incrementAndGet());
  }

  private void wsStartStopLoop() {
    executor.scheduleAtFixedRate(
        () -> {
          try {

            SpotPublicWs ws = wsSvc.newSpotPublicWS();
            ws.start();
            String id = ws.ticker(new String[] {"ETH-USDT", "BTC-USDT"}, (__, ___, ____) -> {});
            Thread.sleep(1000);
            ws.unSubscribe(id);
            ws.stop();
            log.info("WS START/STOP [OK]");
          } catch (Exception e) {
            wsErrCnt.incrementAndGet();
            log.info("WS START/STOP [ERROR] " + e.getMessage());
          }
        },
        0,
        (long) STEP,
        TimeUnit.SECONDS);
  }

  private void statLoop() {
    executor.scheduleAtFixedRate(
        () -> {
          log.info(
              "Stat Market_ERR:{} WS_SS_ERR:{} WS_MSG:{}",
              mkErrCnt.get(),
              wsErrCnt.get(),
              wsMsgCnt.get());
        },
        0,
        (long) STEP,
        TimeUnit.SECONDS);
  }

  public static void main(String[] args) {
    new RunForeverTest().run();
  }
}
