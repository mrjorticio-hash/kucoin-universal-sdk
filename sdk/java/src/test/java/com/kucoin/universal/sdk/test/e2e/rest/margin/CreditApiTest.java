package com.kucoin.universal.sdk.test.e2e.rest.margin;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.margin.credit.*;
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
public class CreditApiTest {

  private static CreditApi api;

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
    api = kucoinClient.getRestService().getMarginService().getCreditApi();
  }

  /** getLoanMarket Get Loan Market /api/v3/project/list */
  @Test
  public void testGetLoanMarket() throws Exception {
    GetLoanMarketReq.GetLoanMarketReqBuilder builder = GetLoanMarketReq.builder();
    builder.currency("DOGE");
    GetLoanMarketReq req = builder.build();
    GetLoanMarketResp resp = api.getLoanMarket(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getPurchaseEnable());
              Assertions.assertNotNull(item.getRedeemEnable());
              Assertions.assertNotNull(item.getIncrement());
              Assertions.assertNotNull(item.getMinPurchaseSize());
              Assertions.assertNotNull(item.getMinInterestRate());
              Assertions.assertNotNull(item.getMaxInterestRate());
              Assertions.assertNotNull(item.getInterestIncrement());
              Assertions.assertNotNull(item.getMaxPurchaseSize());
              Assertions.assertNotNull(item.getMarketInterestRate());
              Assertions.assertNotNull(item.getAutoPurchaseEnable());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getLoanMarketInterestRate Get Loan Market Interest Rate /api/v3/project/marketInterestRate */
  @Test
  public void testGetLoanMarketInterestRate() throws Exception {
    GetLoanMarketInterestRateReq.GetLoanMarketInterestRateReqBuilder builder =
        GetLoanMarketInterestRateReq.builder();
    builder.currency("DOGE");
    GetLoanMarketInterestRateReq req = builder.build();
    GetLoanMarketInterestRateResp resp = api.getLoanMarketInterestRate(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getTime());
              Assertions.assertNotNull(item.getMarketInterestRate());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** purchase Purchase /api/v3/purchase */
  @Test
  public void testPurchase() throws Exception {
    PurchaseReq.PurchaseReqBuilder builder = PurchaseReq.builder();
    builder.currency("DOGE").size("10").interestRate("0.01");
    PurchaseReq req = builder.build();
    PurchaseResp resp = api.purchase(req);
    Assertions.assertNotNull(resp.getOrderNo());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** modifyPurchase Modify Purchase /api/v3/lend/purchase/update */
  @Test
  public void testModifyPurchase() throws Exception {
    ModifyPurchaseReq.ModifyPurchaseReqBuilder builder = ModifyPurchaseReq.builder();
    builder.currency("DOGE").interestRate("0.011").purchaseOrderNo("68805d5b3cffab0007189d08");
    ModifyPurchaseReq req = builder.build();
    ModifyPurchaseResp resp = api.modifyPurchase(req);
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getPurchaseOrders Get Purchase Orders /api/v3/purchase/orders */
  @Test
  public void testGetPurchaseOrders() throws Exception {
    GetPurchaseOrdersReq.GetPurchaseOrdersReqBuilder builder = GetPurchaseOrdersReq.builder();
    builder
        .status(GetPurchaseOrdersReq.StatusEnum.DONE)
        .currency("DOGE")
        .purchaseOrderNo("67aabb111a8c110007ba2e5a");
    GetPurchaseOrdersReq req = builder.build();
    GetPurchaseOrdersResp resp = api.getPurchaseOrders(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getPurchaseOrderNo());
              Assertions.assertNotNull(item.getPurchaseSize());
              Assertions.assertNotNull(item.getMatchSize());
              Assertions.assertNotNull(item.getInterestRate());
              Assertions.assertNotNull(item.getIncomeSize());
              Assertions.assertNotNull(item.getApplyTime());
              Assertions.assertNotNull(item.getStatus());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** redeem Redeem /api/v3/redeem */
  @Test
  public void testRedeem() throws Exception {
    RedeemReq.RedeemReqBuilder builder = RedeemReq.builder();
    builder.currency("DOGE").size("10").purchaseOrderNo("68805d5b3cffab0007189d08");
    RedeemReq req = builder.build();
    RedeemResp resp = api.redeem(req);
    Assertions.assertNotNull(resp.getOrderNo());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getRedeemOrders Get Redeem Orders /api/v3/redeem/orders */
  @Test
  public void testGetRedeemOrders() throws Exception {
    GetRedeemOrdersReq.GetRedeemOrdersReqBuilder builder = GetRedeemOrdersReq.builder();
    builder
        .status(GetRedeemOrdersReq.StatusEnum.DONE)
        .currency("DOGE")
        .redeemOrderNo("68805d5b3cffab0007189d08");
    GetRedeemOrdersReq req = builder.build();
    GetRedeemOrdersResp resp = api.getRedeemOrders(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getPurchaseOrderNo());
              Assertions.assertNotNull(item.getRedeemOrderNo());
              Assertions.assertNotNull(item.getRedeemSize());
              Assertions.assertNotNull(item.getReceiptSize());
              Assertions.assertNotNull(item.getApplyTime());
              Assertions.assertNotNull(item.getStatus());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
