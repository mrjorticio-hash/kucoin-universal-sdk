package com.kucoin.universal.sdk.test.e2e.rest.account;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.account.fee.*;
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
public class AccountFeeTest {

  private static FeeApi api;

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
    api = kucoinClient.getRestService().getAccountService().getFeeApi();
  }

  /** getBasicFee Get Basic Fee - Spot/Margin /api/v1/base-fee */
  @Test
  public void testGetBasicFee() throws Exception {
    GetBasicFeeReq.GetBasicFeeReqBuilder builder = GetBasicFeeReq.builder();
    builder.currencyType(GetBasicFeeReq.CurrencyTypeEnum._0);
    GetBasicFeeReq req = builder.build();
    GetBasicFeeResp resp = api.getBasicFee(req);
    Assertions.assertNotNull(resp.getTakerFeeRate());
    Assertions.assertNotNull(resp.getMakerFeeRate());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotActualFee Get Actual Fee - Spot/Margin /api/v1/trade-fees */
  @Test
  public void testGetSpotActualFee() throws Exception {
    GetSpotActualFeeReq.GetSpotActualFeeReqBuilder builder = GetSpotActualFeeReq.builder();
    builder.symbols("BTC-USDT,ETH-USDT");
    GetSpotActualFeeReq req = builder.build();
    GetSpotActualFeeResp resp = api.getSpotActualFee(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getTakerFeeRate());
              Assertions.assertNotNull(item.getMakerFeeRate());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getFuturesActualFee Get Actual Fee - Futures /api/v1/trade-fees */
  @Test
  public void testGetFuturesActualFee() throws Exception {
    GetFuturesActualFeeReq.GetFuturesActualFeeReqBuilder builder = GetFuturesActualFeeReq.builder();
    builder.symbol("XBTUSDM");
    GetFuturesActualFeeReq req = builder.build();
    GetFuturesActualFeeResp resp = api.getFuturesActualFee(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getTakerFeeRate());
    Assertions.assertNotNull(resp.getMakerFeeRate());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
