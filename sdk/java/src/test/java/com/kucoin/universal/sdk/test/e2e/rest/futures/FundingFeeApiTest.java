package com.kucoin.universal.sdk.test.e2e.rest.futures;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.futures.fundingfees.*;
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
public class FundingFeeApiTest {

  private static FundingFeesApi api;

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
    api = kucoinClient.getRestService().getFuturesService().getFundingFeesApi();
  }

  /** getCurrentFundingRate Get Current Funding Rate /api/v1/funding-rate/{symbol}/current */
  @Test
  public void testGetCurrentFundingRate() throws Exception {
    GetCurrentFundingRateReq.GetCurrentFundingRateReqBuilder builder =
        GetCurrentFundingRateReq.builder();
    builder.symbol("XBTUSDTM");
    GetCurrentFundingRateReq req = builder.build();
    GetCurrentFundingRateResp resp = api.getCurrentFundingRate(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getGranularity());
    Assertions.assertNotNull(resp.getTimePoint());
    Assertions.assertNotNull(resp.getValue());
    Assertions.assertNotNull(resp.getFundingRateCap());
    Assertions.assertNotNull(resp.getFundingRateFloor());
    Assertions.assertNotNull(resp.getPeriod());
    Assertions.assertNotNull(resp.getFundingTime());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getPublicFundingHistory Get Public Funding History /api/v1/contract/funding-rates */
  @Test
  public void testGetPublicFundingHistory() throws Exception {
    GetPublicFundingHistoryReq.GetPublicFundingHistoryReqBuilder builder =
        GetPublicFundingHistoryReq.builder();
    builder.symbol("XBTUSDTM").from(1753286400000L).to(1753372800000L);
    GetPublicFundingHistoryReq req = builder.build();
    GetPublicFundingHistoryResp resp = api.getPublicFundingHistory(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getFundingRate());
              Assertions.assertNotNull(item.getTimepoint());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getPrivateFundingHistory Get Private Funding History /api/v1/funding-history */
  @Test
  public void testGetPrivateFundingHistory() throws Exception {
    GetPrivateFundingHistoryReq.GetPrivateFundingHistoryReqBuilder builder =
        GetPrivateFundingHistoryReq.builder();
    builder.symbol("XBTUSDTM").startAt(1753200000000L).endAt(1753372800000L);
    GetPrivateFundingHistoryReq req = builder.build();
    GetPrivateFundingHistoryResp resp = api.getPrivateFundingHistory(req);
    resp.getDataList()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getTimePoint());
              Assertions.assertNotNull(item.getFundingRate());
              Assertions.assertNotNull(item.getMarkPrice());
              Assertions.assertNotNull(item.getPositionQty());
              Assertions.assertNotNull(item.getPositionCost());
              Assertions.assertNotNull(item.getFunding());
              Assertions.assertNotNull(item.getSettleCurrency());
              Assertions.assertNotNull(item.getContext());
              Assertions.assertNotNull(item.getMarginMode());
            });

    Assertions.assertNotNull(resp.getHasMore());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
