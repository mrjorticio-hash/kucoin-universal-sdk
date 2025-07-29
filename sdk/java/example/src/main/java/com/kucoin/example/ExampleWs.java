package com.kucoin.example;

import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.spot.spotpublic.SpotPublicWs;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import com.kucoin.universal.sdk.model.WebSocketClientOption;
import lombok.extern.slf4j.Slf4j;

@Slf4j
public class ExampleWs {

  public static void wsExample() throws Exception {
    // Credentials & setup
    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    String brokerName = System.getenv("BROKER_NAME");
    String brokerKey = System.getenv("BROKER_KEY");
    String brokerPartner = System.getenv("BROKER_PARTNER");

    TransportOption httpTransportOption = TransportOption.builder().keepAlive(true).build();

    WebSocketClientOption websocketTransportOption = WebSocketClientOption.defaults();

    ClientOption clientOption =
        ClientOption.builder()
            .key(key)
            .secret(secret)
            .passphrase(passphrase)
            .brokerName(brokerName)
            .brokerKey(brokerKey)
            .brokerPartner(brokerPartner)
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(httpTransportOption)
            .websocketClientOption(websocketTransportOption)
            .build();

    KucoinClient client = new DefaultKucoinClient(clientOption);
    SpotPublicWs spotWs = client.getWsService().newSpotPublicWS();

    spotWs.start();
    log.info("WebSocket started");

    String subId =
        spotWs.allTickers(
            (topic, subject, event) -> {
              log.info(
                  "Ticker update: topic={}, subject={}, bestBid={}, bestAsk={}",
                  topic,
                  subject,
                  event.getBestBid(),
                  event.getBestAsk());
            });
    log.info("Subscribed with ID: {}", subId);
    Thread.sleep(5000);

    log.info("Unsubscribing...");
    spotWs.unSubscribe(subId);
    spotWs.stop();
    log.info("Done");
  }

  public static void main(String[] args) throws Exception {
    wsExample();
  }
}
