package com.kucoin.example;

import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.api.KucoinRestService;
import com.kucoin.universal.sdk.generate.account.account.AccountApi;
import com.kucoin.universal.sdk.generate.account.account.GetSpotAccountListData;
import com.kucoin.universal.sdk.generate.account.account.GetSpotAccountListReq;
import com.kucoin.universal.sdk.generate.account.account.GetSpotAccountListResp;
import com.kucoin.universal.sdk.generate.spot.market.Get24hrStatsReq;
import com.kucoin.universal.sdk.generate.spot.market.Get24hrStatsResp;
import com.kucoin.universal.sdk.generate.spot.market.GetKlinesReq;
import com.kucoin.universal.sdk.generate.spot.market.GetKlinesResp;
import com.kucoin.universal.sdk.generate.spot.market.MarketApi;
import com.kucoin.universal.sdk.generate.spot.order.AddOrderSyncReq;
import com.kucoin.universal.sdk.generate.spot.order.AddOrderSyncResp;
import com.kucoin.universal.sdk.generate.spot.order.CancelAllOrdersBySymbolReq;
import com.kucoin.universal.sdk.generate.spot.order.CancelAllOrdersBySymbolResp;
import com.kucoin.universal.sdk.generate.spot.order.GetOpenOrdersReq;
import com.kucoin.universal.sdk.generate.spot.order.GetOpenOrdersResp;
import com.kucoin.universal.sdk.generate.spot.order.OrderApi;
import com.kucoin.universal.sdk.generate.spot.order.SetDCPReq;
import com.kucoin.universal.sdk.generate.spot.order.SetDCPResp;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;
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
public class ExampleMAStrategy {

  public enum Action {
    BUY,
    SELL,
    SKIP
  }

  public static String simpleMovingAverageStrategy(
      MarketApi marketApi, String symbol, int shortWindow, int longWindow, Long endTime) {
    Long startTime = endTime - longWindow * 60L;
    log.info(
        "Query kline data start Time: {}, end Time: {}",
        new java.text.SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
            .format(new java.util.Date(startTime * 1000L)),
        new java.text.SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
            .format(new java.util.Date(endTime * 1000L)));

    GetKlinesReq getKlineReq =
        GetKlinesReq.builder()
            .symbol(symbol)
            .type(GetKlinesReq.TypeEnum._1MIN)
            .startAt(startTime)
            .endAt(endTime)
            .build();

    GetKlinesResp klineResp = marketApi.getKlines(getKlineReq);

    List<Double> prices = new ArrayList<>();
    for (List<String> kline : klineResp.getData()) {
      prices.add(Double.parseDouble(kline.get(2)));
    }

    double shortMA =
        prices.subList(prices.size() - shortWindow, prices.size()).stream()
                .mapToDouble(Double::doubleValue)
                .sum()
            / shortWindow;
    double longMA =
        prices.subList(prices.size() - longWindow, prices.size()).stream()
                .mapToDouble(Double::doubleValue)
                .sum()
            / longWindow;

    log.info("Short MA: {}, Long MA: {}", shortMA, longMA);

    if (shortMA > longMA) {
      log.info("{}: Short MA > Long MA. Should place a BUY order.", symbol);
      return Action.BUY.name();
    } else if (shortMA < longMA) {
      log.info("{}: Short MA < Long MA. Should place a SELL order.", symbol);
      return Action.SELL.name();
    } else {
      return Action.SKIP.name();
    }
  }

  public static double getLastTradePrice(MarketApi marketApi, String symbol) {
    Get24hrStatsReq req = Get24hrStatsReq.builder().symbol(symbol).build();
    Get24hrStatsResp resp = marketApi.get24hrStats(req);
    return Double.parseDouble(resp.getLast());
  }

  public static boolean checkAvailableBalance(
      AccountApi accountApi, double lastPrice, double amount, String action) {
    double tradeValue = lastPrice * amount;
    String currency = action.equals(Action.BUY.name()) ? "USDT" : "DOGE";
    log.info("Checking balance for currency: {}", currency);

    GetSpotAccountListReq req =
        GetSpotAccountListReq.builder()
            .type(GetSpotAccountListReq.TypeEnum.TRADE)
            .currency(currency)
            .build();
    GetSpotAccountListResp resp = accountApi.getSpotAccountList(req);

    double available = 0.0;
    for (GetSpotAccountListData acc : resp.getData()) {
      available += Double.parseDouble(acc.getAvailable());
    }

    log.info("Available {} balance: {}", currency, available);

    if (action.equals(Action.BUY.name())) {
      if (tradeValue <= available) {
        log.info("Balance is sufficient for the trade: {} {} required.", tradeValue, currency);
        return true;
      } else {
        log.info(
            "Insufficient balance: {} {} required, but only {} available.",
            tradeValue,
            currency,
            available);
        return false;
      }
    } else {
      return amount <= available;
    }
  }

  public static void placeOrder(
      OrderApi orderApi,
      String symbol,
      String action,
      double lastPrice,
      double amount,
      double delta) {
    GetOpenOrdersReq openOrdersReq = GetOpenOrdersReq.builder().symbol(symbol).build();
    GetOpenOrdersResp openOrdersResp = orderApi.getOpenOrders(openOrdersReq);

    if (!openOrdersResp.getData().isEmpty()) {
      CancelAllOrdersBySymbolReq cancelReq =
          CancelAllOrdersBySymbolReq.builder().symbol(symbol).build();
      CancelAllOrdersBySymbolResp cancelResp = orderApi.cancelAllOrdersBySymbol(cancelReq);
      log.info("Canceled all open orders: {}", cancelResp.getData());
    }

    AddOrderSyncReq.SideEnum side = AddOrderSyncReq.SideEnum.BUY;
    double price = lastPrice * (1 - delta);
    if (action.equals(Action.SELL.name())) {
      side = AddOrderSyncReq.SideEnum.SELL;
      price = lastPrice * (1 + delta);
    }

    log.info("Placing a {} order at {} for {}", side.name(), price, symbol);

    AddOrderSyncReq orderReq =
        AddOrderSyncReq.builder()
            .clientOid(UUID.randomUUID().toString())
            .side(side)
            .symbol(symbol)
            .type(AddOrderSyncReq.TypeEnum.LIMIT)
            .remark("ma")
            .price(String.format("%.2f", price))
            .size(String.format("%.8f", amount))
            .build();

    AddOrderSyncResp orderResp = orderApi.addOrderSync(orderReq);
    log.info("Order placed successfully with ID: {}", orderResp.getOrderId());

    SetDCPReq dcpReq = SetDCPReq.builder().symbols(symbol).timeout(30).build();
    SetDCPResp dcpResp = orderApi.setDCP(dcpReq);
    log.info(
        "DCP set: current_time={}, trigger_time={}",
        dcpResp.getCurrentTime(),
        dcpResp.getTriggerTime());
  }

  public static void example() {
    // Entry point
    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    TransportOption transportOption = TransportOption.builder().keepAlive(true).build();

    ClientOption clientOption =
        ClientOption.builder()
            .key(key)
            .secret(secret)
            .passphrase(passphrase)
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(transportOption)
            .build();

    KucoinClient client = new DefaultKucoinClient(clientOption);
    KucoinRestService rest = client.getRestService();

    MarketApi marketApi = rest.getSpotService().getMarketApi();
    OrderApi orderApi = rest.getSpotService().getOrderApi();
    AccountApi accountApi = rest.getAccountService().getAccountApi();

    final String SYMBOL = "DOGE-USDT";
    final double ORDER_AMOUNT = 10;
    final double PRICE_DELTA = 0.1;

    long currentTime = (System.currentTimeMillis() / 1000 / 60) * 60;

    log.info("Starting the moving average strategy using K-line data. Press Ctrl+C to stop.");
    while (true) {
      String action = simpleMovingAverageStrategy(marketApi, SYMBOL, 10, 30, currentTime);
      if (!action.equals(Action.SKIP.name())) {
        double lastPrice = getLastTradePrice(marketApi, SYMBOL);
        log.info("Last trade price for {}: {}", SYMBOL, lastPrice);
        if (checkAvailableBalance(accountApi, lastPrice, ORDER_AMOUNT, action)) {
          log.info("Sufficient balance available. Attempting to place the order...");
          placeOrder(orderApi, SYMBOL, action, lastPrice, ORDER_AMOUNT, PRICE_DELTA);
        } else {
          log.info("Insufficient balance. Skipping the trade...");
        }
      }
      log.info("Waiting for 1 minute before the next run...");
      try {
        Thread.sleep(60 * 1000);
      } catch (InterruptedException e) {
        log.error("Sleep interrupted", e);
        break;
      }
      currentTime += 60;
    }
  }

  public static void main(String[] args) {
    example();
  }
}
