package com.kucoin.example;

import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.api.KucoinRestService;
import com.kucoin.universal.sdk.generate.spot.market.GetPartOrderBookReq;
import com.kucoin.universal.sdk.generate.spot.market.GetPartOrderBookResp;
import com.kucoin.universal.sdk.generate.spot.market.MarketApi;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import java.util.List;
import java.util.stream.Collectors;
import lombok.extern.slf4j.Slf4j;

@Slf4j
public class ExampleGetStarted {

  public static String stringifyDepth(List<List<String>> depth) {
    return depth.stream()
        .map(row -> "[" + String.join(", ", row) + "]")
        .collect(Collectors.joining(", "));
  }

  public static void example() {
    // Retrieve API secret information from environment variables
    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    // Set specific options, others will fall back to default values
    TransportOption httpTransportOption = TransportOption.builder().keepAlive(true).build();

    // Create a client using the specified options
    ClientOption clientOption =
        ClientOption.builder()
            .key(key)
            .secret(secret)
            .passphrase(passphrase)
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(httpTransportOption)
            .build();

    KucoinClient client = new DefaultKucoinClient(clientOption);

    // Get the Restful Service
    KucoinRestService kucoinRestService = client.getRestService();

    MarketApi spotMarketApi = kucoinRestService.getSpotService().getMarketApi();

    // Query partial order book depth data (aggregated by price).
    // Build the request using the builder pattern.
    GetPartOrderBookReq request =
        GetPartOrderBookReq.builder().symbol("BTC-USDT").size("20").build();

    GetPartOrderBookResp response = spotMarketApi.getPartOrderBook(request);

    log.info(
        "time={}, sequence={}, bids={}, asks={}",
        response.getTime(),
        response.getSequence(),
        stringifyDepth(response.getBids()),
        stringifyDepth(response.getAsks()));
  }

  public static void main(String[] args) {
    example();
  }
}
