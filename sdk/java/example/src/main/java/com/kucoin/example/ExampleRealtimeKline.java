package com.kucoin.example;

import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.spot.spotpublic.SpotPublicWs;
import com.kucoin.universal.sdk.generate.spot.spotpublic.TradeEvent;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import com.kucoin.universal.sdk.model.WebSocketClientOption;
import java.util.HashMap;
import java.util.Map;
import lombok.Data;
import lombok.extern.slf4j.Slf4j;

/**
 * DISCLAIMER: This strategy is provided for educational and illustrative purposes only. It is not
 * intended to be used as financial or investment advice. Trading cryptocurrencies involves
 * significant risk, and you should carefully consider your investment objectives, level of
 * experience, and risk appetite. Past performance of any trading strategy is not indicative of
 * future results.
 *
 * <p>The authors and contributors of this example are not responsible for any financial losses or
 * damages that may occur from using this code. Use it at your own discretion and consult with a
 * professional financial advisor if necessary.
 */
@Slf4j
public class ExampleRealtimeKline {

  // === KLine definition ===
  @Data
  public static class KLine {
    private double open;
    private double high;
    private double low;
    private double close;
    private double volume;
    private int startTime;
    private int endTime;

    public KLine(double price, double size, int startTime, int endTime) {
      this.open = price;
      this.high = price;
      this.low = price;
      this.close = price;
      this.volume = size;
      this.startTime = startTime;
      this.endTime = endTime;
    }

    public void update(double price, double size) {
      this.high = Math.max(this.high, price);
      this.low = Math.min(this.low, price);
      this.close = price;
      this.volume += size;
    }
  }

  // === Global data ===
  private static final int TIME_INTERVAL = 60; // seconds
  private static final Map<Integer, Map<String, KLine>> klineData = new HashMap<>();

  public static void processTradeToKline(String topic, String subject, TradeEvent tradeEvent) {
    String symbol = tradeEvent.getSymbol();
    double price = Double.parseDouble(tradeEvent.getPrice());
    double size = Double.parseDouble(tradeEvent.getSize());
    int timestamp = (int) (Long.parseLong(tradeEvent.getTime()) / 1_000_000_000L);

    int periodStart = Math.floorDiv(timestamp, TIME_INTERVAL) * TIME_INTERVAL;
    int periodEnd = periodStart + TIME_INTERVAL;

    klineData.computeIfAbsent(periodStart, k -> new HashMap<>());
    klineData
        .get(periodStart)
        .computeIfAbsent(symbol, s -> new KLine(price, size, periodStart, periodEnd))
        .update(price, size);

    log.info(
        "KLine @{} [{}]: O={} H={} L={} C={} V={}",
        new java.text.SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ssXXX")
            .format(new java.util.Date(periodStart * 1000L)),
        symbol,
        klineData.get(periodStart).get(symbol).getOpen(),
        klineData.get(periodStart).get(symbol).getHigh(),
        klineData.get(periodStart).get(symbol).getLow(),
        klineData.get(periodStart).get(symbol).getClose(),
        klineData.get(periodStart).get(symbol).getVolume());
  }

  public static void printKlineData() {
    klineData.keySet().stream()
        .sorted()
        .forEach(
            periodStart -> {
              System.out.println(
                  "\nTime Period: "
                      + new java.text.SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ssXXX")
                          .format(new java.util.Date(periodStart * 1000L)));
              Map<String, KLine> symbols = klineData.get(periodStart);
              symbols.keySet().stream()
                  .sorted()
                  .forEach(
                      symbol -> {
                        KLine kline = symbols.get(symbol);
                        System.out.println("  Symbol: " + symbol);
                        System.out.println("    Open: " + kline.getOpen());
                        System.out.println("    High: " + kline.getHigh());
                        System.out.println("    Low: " + kline.getLow());
                        System.out.println("    Close: " + kline.getClose());
                        System.out.println("    Volume: " + kline.getVolume());
                        System.out.println(
                            "    Start Time: "
                                + new java.text.SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ssXXX")
                                    .format(new java.util.Date(kline.getStartTime() * 1000L)));
                        System.out.println(
                            "    End Time:   "
                                + new java.text.SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ssXXX")
                                    .format(new java.util.Date(kline.getEndTime() * 1000L)));
                      });
            });
  }

  public static void main(String[] args) throws Exception {

    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    TransportOption httpOption = TransportOption.builder().keepAlive(true).build();
    WebSocketClientOption wsOption = WebSocketClientOption.defaults();

    ClientOption clientOption =
        ClientOption.builder()
            .key(key)
            .secret(secret)
            .passphrase(passphrase)
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(httpOption)
            .websocketClientOption(wsOption)
            .build();

    KucoinClient client = new DefaultKucoinClient(clientOption);
    SpotPublicWs spotWs = client.getWsService().newSpotPublicWS();
    spotWs.start();

    String[] symbols = {"BTC-USDT", "ETH-USDT"};
    String subId = spotWs.trade(symbols, ExampleRealtimeKline::processTradeToKline);

    log.info("Subscribed with ID: {}", subId);
    Thread.sleep(180 * 1000);

    log.info("Unsubscribing...");
    spotWs.unSubscribe(subId);
    spotWs.stop();

    printKlineData();
  }
}
