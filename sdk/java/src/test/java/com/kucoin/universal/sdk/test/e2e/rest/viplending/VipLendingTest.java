package com.kucoin.universal.sdk.test.e2e.rest.viplending;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.viplending.viplending.GetAccountsResp;
import com.kucoin.universal.sdk.generate.viplending.viplending.GetDiscountRateConfigsResp;
import com.kucoin.universal.sdk.generate.viplending.viplending.GetLoanInfoResp;
import com.kucoin.universal.sdk.generate.viplending.viplending.VIPLendingApi;
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

// TODO 401
@Slf4j
public class VipLendingTest {

  private static VIPLendingApi api;

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
    api = kucoinClient.getRestService().getVipLendingService().getVIPLendingApi();
  }

  /** getDiscountRateConfigs Get Discount Rate Configs /api/v1/otc-loan/discount-rate-configs */
  @Test
  public void testGetDiscountRateConfigs() throws Exception {
    GetDiscountRateConfigsResp resp = api.getDiscountRateConfigs();
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getUsdtLevels());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getLoanInfo Get Loan Info /api/v1/otc-loan/loan */
  @Test
  public void testGetLoanInfo() throws Exception {
    GetLoanInfoResp resp = api.getLoanInfo();
    Assertions.assertNotNull(resp.getParentUid());
    resp.getOrders()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getOrderId());
              Assertions.assertNotNull(item.getPrincipal());
              Assertions.assertNotNull(item.getInterest());
              Assertions.assertNotNull(item.getCurrency());
            });

    Assertions.assertNotNull(resp.getLtv());
    Assertions.assertNotNull(resp.getTotalMarginAmount());
    Assertions.assertNotNull(resp.getTransferMarginAmount());
    resp.getMargins()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getMarginCcy());
              Assertions.assertNotNull(item.getMarginQty());
              Assertions.assertNotNull(item.getMarginFactor());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getAccounts Get Accounts /api/v1/otc-loan/accounts */
  @Test
  public void testGetAccounts() throws Exception {
    GetAccountsResp resp = api.getAccounts();
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getUid());
              Assertions.assertNotNull(item.getMarginCcy());
              Assertions.assertNotNull(item.getMarginQty());
              Assertions.assertNotNull(item.getMarginFactor());
              Assertions.assertNotNull(item.getAccountType());
              Assertions.assertNotNull(item.getIsParent());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
