package com.kucoin.universal.sdk.test.e2e.rest.margin;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.margin.market.*;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import java.io.IOException;
import java.util.Collections;
import lombok.extern.slf4j.Slf4j;
import okhttp3.Interceptor;
import okhttp3.Request;
import okhttp3.Response;
import org.jetbrains.annotations.NotNull;
import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.Test;

@Slf4j
public class MarketApiTest {

  private static MarketApi api;

  public static ObjectMapper mapper = new ObjectMapper();

  @BeforeAll
  public static void setUp() {

    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    TransportOption httpTransport =
        TransportOption.builder()
            .interceptors(
                Collections.singleton(
                    new Interceptor() {
                      @NotNull @Override
                      public Response intercept(@NotNull Chain chain) throws IOException {
                        Request request = chain.request();

                        System.out.println("========== Request ==========");
                        System.out.println(request.method() + " " + request.url());

                        Response response = chain.proceed(request);

                        System.out.println("========== Response ==========");
                        System.out.println("Status Code: " + response.code());
                        System.out.println("Message: " + response.message());
                        return response;
                      }
                    }))
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
    api = kucoinClient.getRestService().getMarginService().getMarketApi();
  }

  /** getCrossMarginSymbols Get Symbols - Cross Margin /api/v3/margin/symbols */
  @Test
  public void testGetCrossMarginSymbols() throws Exception {
    GetCrossMarginSymbolsReq.GetCrossMarginSymbolsReqBuilder builder =
        GetCrossMarginSymbolsReq.builder();
    builder.symbol("BTC-USDT");
    GetCrossMarginSymbolsReq req = builder.build();
    GetCrossMarginSymbolsResp resp = api.getCrossMarginSymbols(req);
    Assertions.assertNotNull(resp.getTimestamp());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getName());
              Assertions.assertNotNull(item.getEnableTrading());
              Assertions.assertNotNull(item.getMarket());
              Assertions.assertNotNull(item.getBaseCurrency());
              Assertions.assertNotNull(item.getQuoteCurrency());
              Assertions.assertNotNull(item.getBaseIncrement());
              Assertions.assertNotNull(item.getBaseMinSize());
              Assertions.assertNotNull(item.getQuoteIncrement());
              Assertions.assertNotNull(item.getQuoteMinSize());
              Assertions.assertNotNull(item.getBaseMaxSize());
              Assertions.assertNotNull(item.getQuoteMaxSize());
              Assertions.assertNotNull(item.getPriceIncrement());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getPriceLimitRate());
              Assertions.assertNotNull(item.getMinFunds());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getETFInfo Get ETF Info /api/v3/etf/info */
  @Test
  public void testGetETFInfo() throws Exception {
    GetETFInfoReq.GetETFInfoReqBuilder builder = GetETFInfoReq.builder();
    builder.currency("BTCUP");
    GetETFInfoReq req = builder.build();
    GetETFInfoResp resp = api.getETFInfo(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getNetAsset());
              Assertions.assertNotNull(item.getTargetLeverage());
              Assertions.assertNotNull(item.getActualLeverage());
              Assertions.assertNotNull(item.getIssuedSize());
              Assertions.assertNotNull(item.getBasket());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getMarkPriceDetail Get Mark Price Detail /api/v1/mark-price/{symbol}/current */
  @Test
  public void testGetMarkPriceDetail() throws Exception {
    GetMarkPriceDetailReq.GetMarkPriceDetailReqBuilder builder = GetMarkPriceDetailReq.builder();
    builder.symbol("USDT-BTC");
    GetMarkPriceDetailReq req = builder.build();
    GetMarkPriceDetailResp resp = api.getMarkPriceDetail(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getTimePoint());
    Assertions.assertNotNull(resp.getValue());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getMarginConfig Get Margin Config /api/v1/margin/config */
  @Test
  public void testGetMarginConfig() throws Exception {
    GetMarginConfigResp resp = api.getMarginConfig();
    resp.getCurrencyList().forEach(item -> {});

    Assertions.assertNotNull(resp.getMaxLeverage());
    Assertions.assertNotNull(resp.getWarningDebtRatio());
    Assertions.assertNotNull(resp.getLiqDebtRatio());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getMarkPriceList Get Mark Price List /api/v3/mark-price/all-symbols */
  @Test
  public void testGetMarkPriceList() throws Exception {
    GetMarkPriceListResp resp = api.getMarkPriceList();
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getTimePoint());
              Assertions.assertNotNull(item.getValue());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getIsolatedMarginSymbols Get Symbols - Isolated Margin /api/v1/isolated/symbols */
  @Test
  public void testGetIsolatedMarginSymbols() throws Exception {
    GetIsolatedMarginSymbolsResp resp = api.getIsolatedMarginSymbols();
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getSymbolName());
              Assertions.assertNotNull(item.getBaseCurrency());
              Assertions.assertNotNull(item.getQuoteCurrency());
              Assertions.assertNotNull(item.getMaxLeverage());
              Assertions.assertNotNull(item.getFlDebtRatio());
              Assertions.assertNotNull(item.getTradeEnable());
              Assertions.assertNotNull(item.getAutoRenewMaxDebtRatio());
              Assertions.assertNotNull(item.getBaseBorrowEnable());
              Assertions.assertNotNull(item.getQuoteBorrowEnable());
              Assertions.assertNotNull(item.getBaseTransferInEnable());
              Assertions.assertNotNull(item.getQuoteTransferInEnable());
              Assertions.assertNotNull(item.getBaseBorrowCoefficient());
              Assertions.assertNotNull(item.getQuoteBorrowCoefficient());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
