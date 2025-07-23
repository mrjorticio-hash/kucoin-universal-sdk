package com.kucoin.universal.sdk.test.e2e.rest.margin;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.margin.debit.*;
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
public class DebitApiTest {

  private static DebitApi api;

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
    api = kucoinClient.getRestService().getMarginService().getDebitApi();
  }

  /** borrow Borrow /api/v3/margin/borrow */
  @Test
  public void testBorrow() throws Exception {
    BorrowReq.BorrowReqBuilder builder = BorrowReq.builder();
    builder
        .currency("USDT")
        .size(10.0)
        .timeInForce(BorrowReq.TimeInForceEnum.IOC)
        .symbol("BTC-USDT")
        .isIsolated(true)
        .isHf(true);
    BorrowReq req = builder.build();
    BorrowResp resp = api.borrow(req);
    Assertions.assertNotNull(resp.getOrderNo());
    Assertions.assertNotNull(resp.getActualSize());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getBorrowHistory Get Borrow History /api/v3/margin/borrow */
  @Test
  public void testGetBorrowHistory() throws Exception {
    GetBorrowHistoryReq.GetBorrowHistoryReqBuilder builder = GetBorrowHistoryReq.builder();
    builder.currency("USDT").isIsolated(true).symbol("BTC-USDT");
    GetBorrowHistoryReq req = builder.build();
    GetBorrowHistoryResp resp = api.getBorrowHistory(req);
    Assertions.assertNotNull(resp.getTimestamp());
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getOrderNo());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getActualSize());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getCreatedTime());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** repay Repay /api/v3/margin/repay */
  @Test
  public void testRepay() throws Exception {
    RepayReq.RepayReqBuilder builder = RepayReq.builder();
    builder.currency("USDT").size(10.0).symbol("BTC-USDT").isIsolated(true).isHf(true);
    RepayReq req = builder.build();
    RepayResp resp = api.repay(req);
    Assertions.assertNotNull(resp.getTimestamp());
    Assertions.assertNotNull(resp.getOrderNo());
    Assertions.assertNotNull(resp.getActualSize());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getRepayHistory Get Repay History /api/v3/margin/repay */
  @Test
  public void testGetRepayHistory() throws Exception {
    GetRepayHistoryReq.GetRepayHistoryReqBuilder builder = GetRepayHistoryReq.builder();
    builder.currency("USDT").isIsolated(true).symbol("BTC-USDT");
    GetRepayHistoryReq req = builder.build();
    GetRepayHistoryResp resp = api.getRepayHistory(req);
    Assertions.assertNotNull(resp.getTimestamp());
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getOrderNo());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getPrincipal());
              Assertions.assertNotNull(item.getInterest());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getCreatedTime());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getInterestHistory Get Interest History. /api/v3/margin/interest */
  @Test
  public void testGetInterestHistory() throws Exception {
    GetInterestHistoryReq.GetInterestHistoryReqBuilder builder = GetInterestHistoryReq.builder();
    builder.symbol("BTC-USDT").isIsolated(true);
    GetInterestHistoryReq req = builder.build();
    GetInterestHistoryResp resp = api.getInterestHistory(req);
    Assertions.assertNotNull(resp.getTimestamp());
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getDayRatio());
              Assertions.assertNotNull(item.getInterestAmount());
              Assertions.assertNotNull(item.getCreatedTime());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** modifyLeverage Modify Leverage /api/v3/position/update-user-leverage */
  @Test
  public void testModifyLeverage() throws Exception {
    ModifyLeverageReq.ModifyLeverageReqBuilder builder = ModifyLeverageReq.builder();
    builder.symbol("BTC-USDT").isIsolated(true).leverage("3.1");
    ModifyLeverageReq req = builder.build();
    ModifyLeverageResp resp = api.modifyLeverage(req);
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
