package com.kucoin.example;

import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.account.account.GetCrossMarginAccountReq;
import com.kucoin.universal.sdk.generate.account.account.GetCrossMarginAccountResp;
import com.kucoin.universal.sdk.generate.account.account.GetFuturesAccountReq;
import com.kucoin.universal.sdk.generate.account.account.GetFuturesAccountResp;
import com.kucoin.universal.sdk.generate.account.account.GetSpotAccountListReq;
import com.kucoin.universal.sdk.generate.account.account.GetSpotAccountListResp;
import com.kucoin.universal.sdk.generate.futures.fundingfees.GetCurrentFundingRateReq;
import com.kucoin.universal.sdk.generate.futures.fundingfees.GetCurrentFundingRateResp;
import com.kucoin.universal.sdk.generate.futures.market.GetSymbolReq;
import com.kucoin.universal.sdk.generate.futures.market.GetSymbolResp;
import com.kucoin.universal.sdk.generate.service.AccountService;
import com.kucoin.universal.sdk.generate.service.FuturesService;
import com.kucoin.universal.sdk.generate.service.MarginService;
import com.kucoin.universal.sdk.generate.service.SpotService;
import com.kucoin.universal.sdk.generate.spot.market.GetTickerReq;
import com.kucoin.universal.sdk.generate.spot.market.GetTickerResp;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
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
public class ExampleArbitrageStrategy {

  private static final String SPOT_SYMBOL = "DOGE-USDT";
  private static final String FUTURES_SYMBOL = "DOGEUSDTM";
  private static final String BASE_CURRENCY = "USDT";
  private static final int MAX_PLACE_ORDER_WAIT_TIME_SEC = 15;

  public enum MarketSide {
    BUY,
    SELL
  }

  public enum MarketType {
    SPOT,
    MARGIN,
    FUTURES
  }

  public static KucoinClient initClient() {
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

    return new DefaultKucoinClient(clientOption);
  }

  public static boolean checkAvailableBalance(
      AccountService accountService, double price, double amount, String marketType) {
    double tradeValue = price * amount;

    if (marketType.equals(MarketType.SPOT.name())) {
      GetSpotAccountListReq request =
          GetSpotAccountListReq.builder()
              .type(GetSpotAccountListReq.TypeEnum.TRADE)
              .currency(BASE_CURRENCY)
              .build();

      GetSpotAccountListResp resp = accountService.getAccountApi().getSpotAccountList(request);
      double available =
          resp.getData().stream()
              .mapToDouble(item -> Double.parseDouble(item.getAvailable()))
              .sum();

      log.info(
          "[SPOT] Available {} balance: {}, Required: {}", BASE_CURRENCY, available, tradeValue);
      return available >= tradeValue;

    } else if (marketType.equals(MarketType.FUTURES.name())) {
      GetFuturesAccountReq request = GetFuturesAccountReq.builder().currency(BASE_CURRENCY).build();

      GetFuturesAccountResp resp = accountService.getAccountApi().getFuturesAccount(request);
      double available = resp.getAvailableBalance();

      log.info(
          "[FUTURES] Available {} balance: {}, Required: {}", BASE_CURRENCY, available, tradeValue);
      return available >= tradeValue;

    } else if (marketType.equals(MarketType.MARGIN.name())) {
      GetCrossMarginAccountReq request =
          GetCrossMarginAccountReq.builder()
              .queryType(GetCrossMarginAccountReq.QueryTypeEnum.MARGIN)
              .quoteCurrency(GetCrossMarginAccountReq.QuoteCurrencyEnum.USDT)
              .build();

      GetCrossMarginAccountResp resp =
          accountService.getAccountApi().getCrossMarginAccount(request);
      double available = Double.parseDouble(resp.getTotalAssetOfQuoteCurrency());

      log.info(
          "[MARGIN] Available {} balance: {}, Required: {}", BASE_CURRENCY, available, tradeValue);
      return available >= tradeValue;
    }

    return false;
  }

  public static double[] getLastTradedPrice(
      SpotService spotService, FuturesService futuresService) {
    GetTickerResp spotPriceResp =
        spotService.getMarketApi().getTicker(GetTickerReq.builder().symbol(SPOT_SYMBOL).build());
    double spotPrice = Double.parseDouble(spotPriceResp.getPrice());

    GetSymbolResp futuresSymbolResp =
        futuresService
            .getMarketApi()
            .getSymbol(GetSymbolReq.builder().symbol(FUTURES_SYMBOL).build());
    double futuresPrice = futuresSymbolResp.getLastTradePrice();

    log.info("[PRICE] Spot Price: {}, Futures Price: {}", spotPrice, futuresPrice);

    return new double[] {spotPrice, futuresPrice};
  }

  /** Executes the funding rate arbitrage strategy. */
  public static void fundingRateArbitrageStrategy(
      FuturesService futuresService,
      SpotService spotService,
      MarginService marginService,
      AccountService accountService,
      double amount,
      double threshold) {
    try {
      // Step 1: Fetch funding rate
      GetCurrentFundingRateReq fundingRateReq =
          GetCurrentFundingRateReq.builder().symbol(FUTURES_SYMBOL).build();

      GetCurrentFundingRateResp fundingRateResp =
          futuresService.getFundingFeesApi().getCurrentFundingRate(fundingRateReq);
      double fundingRate = fundingRateResp.getValue() * 100;

      log.info("[STRATEGY] Funding rate for {}: {}%", FUTURES_SYMBOL, fundingRate);

      // Step 2: Check if funding rate meets threshold
      if (Math.abs(fundingRate) < threshold) {
        log.info(
            "[STRATEGY] No arbitrage opportunity: Funding rate ({}%) below threshold" + " ({}%).",
            fundingRate, threshold);
        return;
      }

      // Step 3: Get spot and futures prices
      double[] prices = getLastTradedPrice(spotService, futuresService);
      double spotPrice = prices[0];
      double futuresPrice = prices[1];

      // Get futures multiplier
      GetSymbolResp futuresSymbolResp =
          futuresService
              .getMarketApi()
              .getSymbol(GetSymbolReq.builder().symbol(FUTURES_SYMBOL).build());
      double multiplier = futuresSymbolResp.getMultiplier();
      int futuresAmount = (int) Math.ceil(amount / multiplier);

      if (fundingRate > 0) {
        log.info(
            "[STRATEGY] Positive funding rate. Executing LONG spot and SHORT futures arbitrage.");

        if (!checkAvailableBalance(accountService, spotPrice, amount, MarketType.SPOT.name())) {
          log.warn("[STRATEGY] Insufficient balance in spot account.");
          return;
        }
        if (!checkAvailableBalance(
            accountService, futuresPrice, amount, MarketType.FUTURES.name())) {
          log.warn("[STRATEGY] Insufficient balance in futures account.");
          return;
        }

        if (!addSpotOrderWaitFill(
            spotService, SPOT_SYMBOL, MarketSide.BUY.name(), amount, spotPrice)) {
          log.warn("[STRATEGY] Failed to execute spot order.");
          return;
        }
        if (!addFuturesOrderWaitFill(
            futuresService, FUTURES_SYMBOL, MarketSide.SELL.name(), futuresAmount, futuresPrice)) {
          log.warn("[STRATEGY] Failed to execute futures order.");
          return;
        }

      } else {
        log.info(
            "[STRATEGY] Negative funding rate. Executing SHORT margin and LONG futures arbitrage.");

        if (!checkAvailableBalance(accountService, spotPrice, amount, MarketType.MARGIN.name())) {
          log.warn("[STRATEGY] Insufficient balance in margin account.");
          return;
        }
        if (!checkAvailableBalance(
            accountService, futuresPrice, amount, MarketType.FUTURES.name())) {
          log.warn("[STRATEGY] Insufficient balance in futures account.");
          return;
        }

        if (!addMarginOrderWaitFill(marginService, SPOT_SYMBOL, amount, spotPrice)) {
          log.warn("[STRATEGY] Failed to execute margin order.");
          return;
        }
        if (!addFuturesOrderWaitFill(
            futuresService, FUTURES_SYMBOL, MarketSide.BUY.name(), futuresAmount, futuresPrice)) {
          log.warn("[STRATEGY] Failed to execute futures order.");
          return;
        }
      }

      log.info("[STRATEGY] Arbitrage execution completed successfully.");

    } catch (Exception e) {
      log.error("[STRATEGY] Error executing arbitrage strategy: {}", e.getMessage(), e);
    }
  }

  /**
   * Places a spot order and waits for it to be filled.
   *
   * @return true if the order was filled, false if it was cancelled or failed.
   */
  public static boolean addSpotOrderWaitFill(
      SpotService spotService, String symbol, String side, double amount, double price) {
    com.kucoin.universal.sdk.generate.spot.order.AddOrderSyncReq orderReq =
        com.kucoin.universal.sdk.generate.spot.order.AddOrderSyncReq.builder()
            .clientOid(UUID.randomUUID().toString())
            .side(
                side.equals(MarketSide.BUY.name())
                    ? com.kucoin.universal.sdk.generate.spot.order.AddOrderSyncReq.SideEnum.BUY
                    : com.kucoin.universal.sdk.generate.spot.order.AddOrderSyncReq.SideEnum.SELL)
            .symbol(symbol)
            .type(com.kucoin.universal.sdk.generate.spot.order.AddOrderSyncReq.TypeEnum.LIMIT)
            .remark("arbitrage")
            .price(String.format("%.4f", price))
            .size(String.format("%.4f", amount))
            .build();

    com.kucoin.universal.sdk.generate.spot.order.AddOrderSyncResp orderResp =
        spotService.getOrderApi().addOrderSync(orderReq);

    log.info(
        "[SPOT ORDER] Placed {} order for {} {} at {}. Order ID: {}",
        side.toUpperCase(),
        amount,
        symbol,
        price,
        orderResp.getOrderId());

    double deadline = System.currentTimeMillis() / 1000.0 + MAX_PLACE_ORDER_WAIT_TIME_SEC;

    while (System.currentTimeMillis() / 1000.0 < deadline) {
      try {
        Thread.sleep(1000);
        log.info("[SPOT ORDER] Checking order status...");

        com.kucoin.universal.sdk.generate.spot.order.GetOrderByOrderIdReq detailReq =
            com.kucoin.universal.sdk.generate.spot.order.GetOrderByOrderIdReq.builder()
                .symbol(symbol)
                .orderId(orderResp.getOrderId())
                .build();

        com.kucoin.universal.sdk.generate.spot.order.GetOrderByOrderIdResp orderDetail =
            spotService.getOrderApi().getOrderByOrderId(detailReq);

        if (!orderDetail.getActive()) {
          log.info(
              "[SPOT ORDER] Order filled successfully: {} {} {}. Order ID: {}",
              side.toUpperCase(),
              amount,
              symbol,
              orderResp.getOrderId());
          return true;
        }
      } catch (InterruptedException e) {
        log.error("[SPOT ORDER] Sleep interrupted: {}", e.getMessage(), e);
        break;
      }
    }

    log.warn(
        "[SPOT ORDER] Order not filled within {} seconds. Cancelling order...",
        MAX_PLACE_ORDER_WAIT_TIME_SEC);

    com.kucoin.universal.sdk.generate.spot.order.CancelOrderByOrderIdSyncReq cancelReq =
        com.kucoin.universal.sdk.generate.spot.order.CancelOrderByOrderIdSyncReq.builder()
            .orderId(orderResp.getOrderId())
            .symbol(symbol)
            .build();

    com.kucoin.universal.sdk.generate.spot.order.CancelOrderByOrderIdSyncResp cancelResp =
        spotService.getOrderApi().cancelOrderByOrderIdSync(cancelReq);

    if (cancelResp.getStatus()
        != com.kucoin.universal.sdk.generate.spot.order.CancelOrderByOrderIdSyncResp.StatusEnum
            .DONE) {
      throw new RuntimeException(
          "[SPOT ORDER] Failed to cancel order. Order ID: " + orderResp.getOrderId());
    }

    log.info("[SPOT ORDER] Order cancelled successfully. Order ID: {}", orderResp.getOrderId());
    return false;
  }

  /**
   * Places a futures order and waits for it to be filled.
   *
   * @return true if the order was filled, false if cancelled or failed.
   */
  public static boolean addFuturesOrderWaitFill(
      FuturesService futuresService, String symbol, String side, int amount, double price) {
    com.kucoin.universal.sdk.generate.futures.order.AddOrderReq orderReq =
        com.kucoin.universal.sdk.generate.futures.order.AddOrderReq.builder()
            .clientOid(UUID.randomUUID().toString())
            .side(
                side.equals(MarketSide.BUY.name())
                    ? com.kucoin.universal.sdk.generate.futures.order.AddOrderReq.SideEnum.BUY
                    : com.kucoin.universal.sdk.generate.futures.order.AddOrderReq.SideEnum.SELL)
            .symbol(symbol)
            .type(com.kucoin.universal.sdk.generate.futures.order.AddOrderReq.TypeEnum.LIMIT)
            .marginMode(
                com.kucoin.universal.sdk.generate.futures.order.AddOrderReq.MarginModeEnum.CROSS)
            .remark("arbitrage")
            .price(String.format("%.4f", price))
            .leverage(1)
            .size(amount)
            .build();

    com.kucoin.universal.sdk.generate.futures.order.AddOrderResp orderResp =
        futuresService.getOrderApi().addOrder(orderReq);

    log.info(
        "[FUTURES ORDER] Placed {} order for {} {} at {}. Order ID: {}",
        side.toUpperCase(),
        amount,
        symbol,
        price,
        orderResp.getOrderId());

    double deadline = System.currentTimeMillis() / 1000.0 + MAX_PLACE_ORDER_WAIT_TIME_SEC;

    while (System.currentTimeMillis() / 1000.0 < deadline) {
      try {
        Thread.sleep(1000);
        log.info("[FUTURES ORDER] Checking order status...");

        com.kucoin.universal.sdk.generate.futures.order.GetOrderByOrderIdReq detailReq =
            com.kucoin.universal.sdk.generate.futures.order.GetOrderByOrderIdReq.builder()
                .orderId(orderResp.getOrderId())
                .build();

        com.kucoin.universal.sdk.generate.futures.order.GetOrderByOrderIdResp orderDetail =
            futuresService.getOrderApi().getOrderByOrderId(detailReq);

        if (orderDetail.getStatus()
            == com.kucoin.universal.sdk.generate.futures.order.GetOrderByOrderIdResp.StatusEnum
                .DONE) {
          log.info(
              "[FUTURES ORDER] Order filled successfully: {} {} {}. Order ID: {}",
              side.toUpperCase(),
              amount,
              symbol,
              orderResp.getOrderId());
          return true;
        }
      } catch (InterruptedException e) {
        log.error("[FUTURES ORDER] Sleep interrupted: {}", e.getMessage(), e);
        break;
      }
    }

    log.warn(
        "[FUTURES ORDER] Order not filled within {} seconds. Cancelling order...",
        MAX_PLACE_ORDER_WAIT_TIME_SEC);

    com.kucoin.universal.sdk.generate.futures.order.CancelOrderByIdReq cancelReq =
        com.kucoin.universal.sdk.generate.futures.order.CancelOrderByIdReq.builder()
            .orderId(orderResp.getOrderId())
            .build();

    com.kucoin.universal.sdk.generate.futures.order.CancelOrderByIdResp cancelResp =
        futuresService.getOrderApi().cancelOrderById(cancelReq);

    if (!cancelResp.getCancelledOrderIds().contains(orderResp.getOrderId())) {
      throw new RuntimeException(
          "[FUTURES ORDER] Failed to cancel order. Order ID: " + orderResp.getOrderId());
    }

    log.info("[FUTURES ORDER] Order cancelled successfully. Order ID: {}", orderResp.getOrderId());
    return false;
  }

  /**
   * Places a margin (cross) order and waits for it to be filled.
   *
   * @return true if the order was filled, false if cancelled or failed.
   */
  public static boolean addMarginOrderWaitFill(
      MarginService marginService, String symbol, double amount, double price) {
    com.kucoin.universal.sdk.generate.margin.order.AddOrderReq orderReq =
        com.kucoin.universal.sdk.generate.margin.order.AddOrderReq.builder()
            .clientOid(UUID.randomUUID().toString())
            .side(com.kucoin.universal.sdk.generate.margin.order.AddOrderReq.SideEnum.BUY)
            .symbol(symbol)
            .type(com.kucoin.universal.sdk.generate.margin.order.AddOrderReq.TypeEnum.LIMIT)
            .isIsolated(false)
            .autoBorrow(true)
            .autoRepay(true)
            .price(String.format("%.4f", price))
            .size(String.format("%.4f", amount))
            .build();

    com.kucoin.universal.sdk.generate.margin.order.AddOrderResp orderResp =
        marginService.getOrderApi().addOrder(orderReq);

    log.info(
        "[MARGIN ORDER] Placed BUY order for {} {} at {}. Order ID: {}",
        amount,
        symbol,
        price,
        orderResp.getOrderId());

    double deadline = System.currentTimeMillis() / 1000.0 + MAX_PLACE_ORDER_WAIT_TIME_SEC;

    while (System.currentTimeMillis() / 1000.0 < deadline) {
      try {
        Thread.sleep(1000);
        log.info("[MARGIN ORDER] Checking order status...");

        com.kucoin.universal.sdk.generate.margin.order.GetOrderByOrderIdReq detailReq =
            com.kucoin.universal.sdk.generate.margin.order.GetOrderByOrderIdReq.builder()
                .symbol(symbol)
                .orderId(orderResp.getOrderId())
                .build();

        com.kucoin.universal.sdk.generate.margin.order.GetOrderByOrderIdResp orderDetail =
            marginService.getOrderApi().getOrderByOrderId(detailReq);

        if (!orderDetail.getActive()) {
          log.info(
              "[MARGIN ORDER] Order filled successfully: BUY {} {}. Order ID: {}",
              amount,
              symbol,
              orderResp.getOrderId());
          return true;
        }
      } catch (InterruptedException e) {
        log.error("[MARGIN ORDER] Sleep interrupted: {}", e.getMessage(), e);
        break;
      }
    }

    log.warn(
        "[MARGIN ORDER] Order not filled within {} seconds. Cancelling order...",
        MAX_PLACE_ORDER_WAIT_TIME_SEC);

    com.kucoin.universal.sdk.generate.margin.order.CancelOrderByOrderIdReq cancelReq =
        com.kucoin.universal.sdk.generate.margin.order.CancelOrderByOrderIdReq.builder()
            .orderId(orderResp.getOrderId())
            .symbol(symbol)
            .build();

    com.kucoin.universal.sdk.generate.margin.order.CancelOrderByOrderIdResp cancelResp =
        marginService.getOrderApi().cancelOrderByOrderId(cancelReq);

    if (cancelResp.getOrderId() == null) {
      throw new RuntimeException(
          "[MARGIN ORDER] Failed to cancel order. Order ID: " + orderResp.getOrderId());
    }

    log.info("[MARGIN ORDER] Order cancelled successfully. Order ID: {}", orderResp.getOrderId());
    return false;
  }

  public static void main(String[] args) {
    log.info("Initializing APIs...");

    KucoinClient client = initClient();
    FuturesService futuresService = client.getRestService().getFuturesService();
    SpotService spotService = client.getRestService().getSpotService();
    MarginService marginService = client.getRestService().getMarginService();
    AccountService accountService = client.getRestService().getAccountService();

    double amount = 100.0; // Amount to trade
    double threshold = 0.005; // 0.5% minimum funding rate difference

    log.info("Starting funding rate arbitrage strategy...");
    try {
      fundingRateArbitrageStrategy(
          futuresService, spotService, marginService, accountService, amount, threshold);
    } catch (Exception e) {
      log.error("Error running arbitrage strategy: {}", e.getMessage(), e);
    }
  }
}
