package com.kucoin.universal.sdk.test.e2e.rest.futures;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.futures.market.*;
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
    api = kucoinClient.getRestService().getFuturesService().getMarketApi();
  }

  /** getSymbol Get Symbol /api/v1/contracts/{symbol} */
  @Test
  public void testGetSymbol() throws Exception {
    GetSymbolReq.GetSymbolReqBuilder builder = GetSymbolReq.builder();
    builder.symbol("XBTUSDTM");
    GetSymbolReq req = builder.build();
    GetSymbolResp resp = api.getSymbol(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getRootSymbol());
    Assertions.assertNotNull(resp.getType());
    Assertions.assertNotNull(resp.getFirstOpenDate());
    Assertions.assertNotNull(resp.getBaseCurrency());
    Assertions.assertNotNull(resp.getQuoteCurrency());
    Assertions.assertNotNull(resp.getSettleCurrency());
    Assertions.assertNotNull(resp.getMaxOrderQty());
    Assertions.assertNotNull(resp.getMaxPrice());
    Assertions.assertNotNull(resp.getLotSize());
    Assertions.assertNotNull(resp.getTickSize());
    Assertions.assertNotNull(resp.getIndexPriceTickSize());
    Assertions.assertNotNull(resp.getMultiplier());
    Assertions.assertNotNull(resp.getInitialMargin());
    Assertions.assertNotNull(resp.getMaintainMargin());
    Assertions.assertNotNull(resp.getMaxRiskLimit());
    Assertions.assertNotNull(resp.getMinRiskLimit());
    Assertions.assertNotNull(resp.getRiskStep());
    Assertions.assertNotNull(resp.getMakerFeeRate());
    Assertions.assertNotNull(resp.getTakerFeeRate());
    Assertions.assertNotNull(resp.getTakerFixFee());
    Assertions.assertNotNull(resp.getMakerFixFee());
    Assertions.assertNotNull(resp.getIsDeleverage());
    Assertions.assertNotNull(resp.getIsQuanto());
    Assertions.assertNotNull(resp.getIsInverse());
    Assertions.assertNotNull(resp.getMarkMethod());
    Assertions.assertNotNull(resp.getFairMethod());
    Assertions.assertNotNull(resp.getFundingBaseSymbol());
    Assertions.assertNotNull(resp.getFundingQuoteSymbol());
    Assertions.assertNotNull(resp.getFundingRateSymbol());
    Assertions.assertNotNull(resp.getIndexSymbol());
    Assertions.assertNotNull(resp.getSettlementSymbol());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getFundingFeeRate());
    Assertions.assertNotNull(resp.getFundingRateGranularity());
    Assertions.assertNotNull(resp.getOpenInterest());
    Assertions.assertNotNull(resp.getTurnoverOf24h());
    Assertions.assertNotNull(resp.getVolumeOf24h());
    Assertions.assertNotNull(resp.getMarkPrice());
    Assertions.assertNotNull(resp.getIndexPrice());
    Assertions.assertNotNull(resp.getLastTradePrice());
    Assertions.assertNotNull(resp.getNextFundingRateTime());
    Assertions.assertNotNull(resp.getMaxLeverage());
    resp.getSourceExchanges().forEach(item -> {});

    Assertions.assertNotNull(resp.getPremiumsSymbol1M());
    Assertions.assertNotNull(resp.getPremiumsSymbol8H());
    Assertions.assertNotNull(resp.getFundingBaseSymbol1M());
    Assertions.assertNotNull(resp.getFundingQuoteSymbol1M());
    Assertions.assertNotNull(resp.getLowPrice());
    Assertions.assertNotNull(resp.getHighPrice());
    Assertions.assertNotNull(resp.getPriceChgPct());
    Assertions.assertNotNull(resp.getPriceChg());
    Assertions.assertNotNull(resp.getK());
    Assertions.assertNotNull(resp.getM());
    Assertions.assertNotNull(resp.getF());
    Assertions.assertNotNull(resp.getMmrLimit());
    Assertions.assertNotNull(resp.getMmrLevConstant());
    Assertions.assertNotNull(resp.getSupportCross());
    Assertions.assertNotNull(resp.getBuyLimit());
    Assertions.assertNotNull(resp.getSellLimit());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getAllSymbols Get All Symbols /api/v1/contracts/active */
  @Test
  public void testGetAllSymbols() throws Exception {
    GetAllSymbolsResp resp = api.getAllSymbols();
    Assertions.assertNotNull(resp.getData());

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getTicker Get Ticker /api/v1/ticker */
  @Test
  public void testGetTicker() throws Exception {
    GetTickerReq.GetTickerReqBuilder builder = GetTickerReq.builder();
    builder.symbol("XBTUSDTM");
    GetTickerReq req = builder.build();
    GetTickerResp resp = api.getTicker(req);
    Assertions.assertNotNull(resp.getSequence());
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getSide());
    Assertions.assertNotNull(resp.getSize());
    Assertions.assertNotNull(resp.getTradeId());
    Assertions.assertNotNull(resp.getPrice());
    Assertions.assertNotNull(resp.getBestBidPrice());
    Assertions.assertNotNull(resp.getBestBidSize());
    Assertions.assertNotNull(resp.getBestAskPrice());
    Assertions.assertNotNull(resp.getBestAskSize());
    Assertions.assertNotNull(resp.getTs());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getAllTickers Get All Tickers /api/v1/allTickers */
  @Test
  public void testGetAllTickers() throws Exception {
    GetAllTickersResp resp = api.getAllTickers();
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSequence());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getTradeId());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getBestBidPrice());
              Assertions.assertNotNull(item.getBestBidSize());
              Assertions.assertNotNull(item.getBestAskPrice());
              Assertions.assertNotNull(item.getBestAskSize());
              Assertions.assertNotNull(item.getTs());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getFullOrderBook Get Full OrderBook /api/v1/level2/snapshot */
  @Test
  public void testGetFullOrderBook() throws Exception {
    GetFullOrderBookReq.GetFullOrderBookReqBuilder builder = GetFullOrderBookReq.builder();
    builder.symbol("XBTUSDTM");
    GetFullOrderBookReq req = builder.build();
    GetFullOrderBookResp resp = api.getFullOrderBook(req);
    Assertions.assertNotNull(resp.getSequence());
    Assertions.assertNotNull(resp.getSymbol());
    resp.getBids().forEach(item -> {});

    resp.getAsks().forEach(item -> {});

    Assertions.assertNotNull(resp.getTs());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getPartOrderBook Get Part OrderBook /api/v1/level2/depth{size} */
  @Test
  public void testGetPartOrderBook() throws Exception {
    GetPartOrderBookReq.GetPartOrderBookReqBuilder builder = GetPartOrderBookReq.builder();
    builder.size("20").symbol("XBTUSDTM");
    GetPartOrderBookReq req = builder.build();
    GetPartOrderBookResp resp = api.getPartOrderBook(req);
    Assertions.assertNotNull(resp.getSequence());
    Assertions.assertNotNull(resp.getSymbol());
    resp.getBids().forEach(item -> {});

    resp.getAsks().forEach(item -> {});

    Assertions.assertNotNull(resp.getTs());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getTradeHistory Get Trade History /api/v1/trade/history */
  @Test
  public void testGetTradeHistory() throws Exception {
    GetTradeHistoryReq.GetTradeHistoryReqBuilder builder = GetTradeHistoryReq.builder();
    builder.symbol("XBTUSDTM");
    GetTradeHistoryReq req = builder.build();
    GetTradeHistoryResp resp = api.getTradeHistory(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSequence());
              Assertions.assertNotNull(item.getContractId());
              Assertions.assertNotNull(item.getTradeId());
              Assertions.assertNotNull(item.getMakerOrderId());
              Assertions.assertNotNull(item.getTakerOrderId());
              Assertions.assertNotNull(item.getTs());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSide());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getKlines Get Klines /api/v1/kline/query */
  @Test
  public void testGetKlines() throws Exception {
    GetKlinesReq.GetKlinesReqBuilder builder = GetKlinesReq.builder();
    builder
        .symbol("XBTUSDTM")
        .granularity(GetKlinesReq.GranularityEnum._1)
        .from(1753203600000L)
        .to(1753207200000L);
    GetKlinesReq req = builder.build();
    GetKlinesResp resp = api.getKlines(req);
    resp.getData().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getMarkPrice Get Mark Price /api/v1/mark-price/{symbol}/current */
  @Test
  public void testGetMarkPrice() throws Exception {
    GetMarkPriceReq.GetMarkPriceReqBuilder builder = GetMarkPriceReq.builder();
    builder.symbol("XBTUSDTM");
    GetMarkPriceReq req = builder.build();
    GetMarkPriceResp resp = api.getMarkPrice(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getGranularity());
    Assertions.assertNotNull(resp.getTimePoint());
    Assertions.assertNotNull(resp.getValue());
    Assertions.assertNotNull(resp.getIndexPrice());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotIndexPrice Get Spot Index Price /api/v1/index/query */
  @Test
  public void testGetSpotIndexPrice() throws Exception {
    GetSpotIndexPriceReq.GetSpotIndexPriceReqBuilder builder = GetSpotIndexPriceReq.builder();
    builder.symbol(".KXBTUSDT");
    GetSpotIndexPriceReq req = builder.build();
    GetSpotIndexPriceResp resp = api.getSpotIndexPrice(req);
    resp.getDataList()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getGranularity());
              Assertions.assertNotNull(item.getTimePoint());
              Assertions.assertNotNull(item.getValue());
              Assertions.assertNotNull(item.getDecomposionList());
            });

    Assertions.assertNotNull(resp.getHasMore());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getInterestRateIndex Get Interest Rate Index /api/v1/interest/query */
  @Test
  public void testGetInterestRateIndex() throws Exception {
    GetInterestRateIndexReq.GetInterestRateIndexReqBuilder builder =
        GetInterestRateIndexReq.builder();
    builder.symbol(".XBTINT");
    GetInterestRateIndexReq req = builder.build();
    GetInterestRateIndexResp resp = api.getInterestRateIndex(req);
    resp.getDataList()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getGranularity());
              Assertions.assertNotNull(item.getTimePoint());
              Assertions.assertNotNull(item.getValue());
            });

    Assertions.assertNotNull(resp.getHasMore());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getPremiumIndex Get Premium Index /api/v1/premium/query */
  @Test
  public void testGetPremiumIndex() throws Exception {
    GetPremiumIndexReq.GetPremiumIndexReqBuilder builder = GetPremiumIndexReq.builder();
    builder.symbol("XBTUSDTMPI");
    GetPremiumIndexReq req = builder.build();
    GetPremiumIndexResp resp = api.getPremiumIndex(req);
    resp.getDataList()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getGranularity());
              Assertions.assertNotNull(item.getTimePoint());
              Assertions.assertNotNull(item.getValue());
            });

    Assertions.assertNotNull(resp.getHasMore());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** get24hrStats Get 24hr stats /api/v1/trade-statistics */
  @Test
  public void testGet24hrStats() throws Exception {
    Get24hrStatsResp resp = api.get24hrStats();
    Assertions.assertNotNull(resp.getTurnoverOf24h());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getServerTime Get Server Time /api/v1/timestamp */
  @Test
  public void testGetServerTime() throws Exception {
    GetServerTimeResp resp = api.getServerTime();
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getServiceStatus Get Service Status /api/v1/status */
  @Test
  public void testGetServiceStatus() throws Exception {
    GetServiceStatusResp resp = api.getServiceStatus();
    Assertions.assertNotNull(resp.getMsg());
    Assertions.assertNotNull(resp.getStatus());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  //
  /** getPublicToken Get Public Token - Futures /api/v1/bullet-public */
  @Test
  public void testGetPublicToken() throws Exception {
    GetPublicTokenResp resp = api.getPublicToken();
    Assertions.assertNotNull(resp.getToken());
    resp.getInstanceServers()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getEndpoint());
              Assertions.assertNotNull(item.getEncrypt());
              Assertions.assertNotNull(item.getProtocol());
              Assertions.assertNotNull(item.getPingInterval());
              Assertions.assertNotNull(item.getPingTimeout());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getPrivateToken Get Private Token - Futures /api/v1/bullet-private */
  @Test
  public void testGetPrivateToken() throws Exception {
    GetPrivateTokenResp resp = api.getPrivateToken();
    Assertions.assertNotNull(resp.getToken());
    resp.getInstanceServers()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getEndpoint());
              Assertions.assertNotNull(item.getEncrypt());
              Assertions.assertNotNull(item.getProtocol());
              Assertions.assertNotNull(item.getPingInterval());
              Assertions.assertNotNull(item.getPingTimeout());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
