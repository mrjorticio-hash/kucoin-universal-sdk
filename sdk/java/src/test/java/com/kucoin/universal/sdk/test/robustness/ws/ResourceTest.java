package com.kucoin.universal.sdk.test.robustness.ws;

import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.spot.spotpublic.SpotPublicWs;
import com.kucoin.universal.sdk.generate.spot.spotpublic.TradeEvent;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import com.kucoin.universal.sdk.model.WebSocketClientOption;
import com.kucoin.universal.sdk.test.robustness.ResourceLeakStat;
import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.Test;

@Slf4j
public class ResourceTest {
  private static void noop(String topic, String subject, TradeEvent data) {}

  @Test
  public void testStarStop() throws Exception {
    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    ClientOption clientOpt =
        ClientOption.builder()
            .key(key)
            .secret(secret)
            .passphrase(passphrase)
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(TransportOption.defaults())
            .websocketClientOption(WebSocketClientOption.defaults())
            .build();

    KucoinClient kucoinClient = new DefaultKucoinClient(clientOpt);

    ResourceLeakStat.stat("before");

    for (int i = 0; i < 10; i++) {
      SpotPublicWs spotPublicWs = kucoinClient.getWsService().newSpotPublicWS();
      spotPublicWs.start();
      String id = spotPublicWs.trade(new String[] {"BTC-USDT", "ETH-USDT"}, ResourceTest::noop);
      spotPublicWs.unSubscribe(id);
      spotPublicWs.stop();
    }

    ResourceLeakStat.stat("after");

    // TaskRunner.INSTANCE
    // okhttp3.internal.concurrent.TaskRunner.RealBackend
    // ThreadPoolExecutor: keepAliveTime = 60s
    Thread.sleep(120 * 1000);

    ResourceLeakStat.printAllThreadsWithStack();
  }
}
