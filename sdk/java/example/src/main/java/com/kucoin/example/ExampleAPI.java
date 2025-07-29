package com.kucoin.example;

import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.api.KucoinRestService;
import com.kucoin.universal.sdk.generate.account.account.AccountApi;
import com.kucoin.universal.sdk.generate.account.account.GetAccountInfoResp;
import com.kucoin.universal.sdk.generate.account.fee.FeeApi;
import com.kucoin.universal.sdk.generate.account.fee.GetSpotActualFeeData;
import com.kucoin.universal.sdk.generate.account.fee.GetSpotActualFeeReq;
import com.kucoin.universal.sdk.generate.account.fee.GetSpotActualFeeResp;
import com.kucoin.universal.sdk.generate.futures.market.*;
import com.kucoin.universal.sdk.generate.service.AccountService;
import com.kucoin.universal.sdk.generate.service.FuturesService;
import com.kucoin.universal.sdk.generate.service.SpotService;
import com.kucoin.universal.sdk.generate.spot.order.*;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.UUID;
import lombok.extern.slf4j.Slf4j;

@Slf4j
public class ExampleAPI {

  public static void restExample() {
    // Retrieve API secret information from environment variables
    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    // Optional: Retrieve broker secret information from environment variables; applicable for
    // broker API only
    String brokerName = System.getenv("BROKER_NAME");
    String brokerKey = System.getenv("BROKER_KEY");
    String brokerPartner = System.getenv("BROKER_PARTNER");

    // Set specific options, others will fall back to default values
    TransportOption httpTransportOption = TransportOption.builder().keepAlive(true).build();

    // Create a client using the specified options
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
            .build();

    KucoinClient client = new DefaultKucoinClient(clientOption);
    KucoinRestService kucoinRestService = client.getRestService();

    accountServiceExample(kucoinRestService.getAccountService());
    spotServiceExample(kucoinRestService.getSpotService());
    futuresServiceExample(kucoinRestService.getFuturesService());
  }

  public static void accountServiceExample(AccountService accountService) {
    AccountApi accountApi = accountService.getAccountApi();
    GetAccountInfoResp accountInfoResp = accountApi.getAccountInfo();
    log.info(
        "account info: level: {}, SubAccountSize: {}",
        accountInfoResp.getLevel(),
        accountInfoResp.getSubQuantity());

    FeeApi feeApi = accountService.getFeeApi();
    GetSpotActualFeeReq getActualFeeReq =
        GetSpotActualFeeReq.builder().symbols("BTC-USDT,ETH-USDT").build();

    GetSpotActualFeeResp getActualFeeResp = feeApi.getSpotActualFee(getActualFeeReq);

    for (GetSpotActualFeeData feeData : getActualFeeResp.getData()) {
      log.info(
          "Fee info: symbol: {}, TakerFee: {}, MakerFee: {}",
          feeData.getSymbol(),
          feeData.getTakerFeeRate(),
          feeData.getMakerFeeRate());
    }
  }

  public static void spotServiceExample(SpotService spotService) {
    OrderApi orderApi = spotService.getOrderApi();

    AddOrderSyncReq addOrderReq =
        AddOrderSyncReq.builder()
            .clientOid(UUID.randomUUID().toString())
            .side(AddOrderSyncReq.SideEnum.BUY)
            .symbol("BTC-USDT")
            .type(AddOrderSyncReq.TypeEnum.LIMIT)
            .remark("sdk_example")
            .price("10000")
            .size("0.001")
            .build();

    AddOrderSyncResp resp = orderApi.addOrderSync(addOrderReq);
    log.info("Add order success, id: {}, oid: {}", resp.getOrderId(), resp.getClientOid());

    GetOrderByOrderIdReq queryOrderDetailReq =
        GetOrderByOrderIdReq.builder().orderId(resp.getOrderId()).symbol("BTC-USDT").build();
    GetOrderByOrderIdResp orderDetailResp = orderApi.getOrderByOrderId(queryOrderDetailReq);
    log.info("Order detail: {}", orderDetailResp.toString());

    CancelOrderByOrderIdSyncReq cancelOrderReq =
        CancelOrderByOrderIdSyncReq.builder().orderId(resp.getOrderId()).symbol("BTC-USDT").build();
    CancelOrderByOrderIdSyncResp cancelOrderResp =
        orderApi.cancelOrderByOrderIdSync(cancelOrderReq);
    log.info("Cancel order success, id: {}", cancelOrderResp.getOrderId());
  }

  public static void futuresServiceExample(FuturesService futuresService) {
    MarketApi marketApi = futuresService.getMarketApi();

    GetAllSymbolsResp allSymbolResp = marketApi.getAllSymbols();
    int maxQuery = Math.min(allSymbolResp.getData().size(), 10);

    for (int i = 0; i < maxQuery; i++) {
      GetAllSymbolsData symbol = allSymbolResp.getData().get(i);

      long start = (long) ((System.currentTimeMillis() - 600000));
      long end = (long) (System.currentTimeMillis());

      GetKlinesReq getKlineReq =
          GetKlinesReq.builder()
              .symbol(symbol.getSymbol())
              .granularity(GetKlinesReq.GranularityEnum._1)
              .from(start)
              .to(end)
              .build();

      GetKlinesResp getKlineResp = marketApi.getKlines(getKlineReq);
      List<String> rows = new ArrayList<>();

      for (List<Double> row : getKlineResp.getData()) {
        String timestamp =
            new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(new Date(row.get(0).longValue()));
        String formattedRow =
            String.format(
                "[Time: %s, Open: %.2f, High: %.2f, Low: %.2f, Close: %.2f, Volume: %.2f]",
                timestamp, row.get(1), row.get(2), row.get(3), row.get(4), row.get(5));
        rows.add(formattedRow);
      }

      log.info("Symbol: {}, Kline: {}", symbol.getSymbol(), String.join(", ", rows));
    }
  }

  public static void main(String[] args) {
    restExample();
  }
}
