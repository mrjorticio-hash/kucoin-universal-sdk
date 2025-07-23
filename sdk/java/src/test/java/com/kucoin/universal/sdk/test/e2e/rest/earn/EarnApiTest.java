package com.kucoin.universal.sdk.test.e2e.rest.earn;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.earn.earn.*;
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
public class EarnApiTest {

  private static EarnApi api;

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
    api = kucoinClient.getRestService().getEarnService().getEarnApi();
  }

  /** purchase Purchase /api/v1/earn/orders */
  @Test
  public void testPurchase() throws Exception {
    PurchaseReq.PurchaseReqBuilder builder = PurchaseReq.builder();
    builder.productId("2152").amount("10").accountType(PurchaseReq.AccountTypeEnum.MAIN);
    PurchaseReq req = builder.build();
    PurchaseResp resp = api.purchase(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getOrderTxId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getRedeemPreview Get Redeem Preview /api/v1/earn/redeem-preview */
  @Test
  public void testGetRedeemPreview() throws Exception {
    GetRedeemPreviewReq.GetRedeemPreviewReqBuilder builder = GetRedeemPreviewReq.builder();
    builder.orderId("2155441").fromAccountType(GetRedeemPreviewReq.FromAccountTypeEnum.MAIN);
    GetRedeemPreviewReq req = builder.build();
    GetRedeemPreviewResp resp = api.getRedeemPreview(req);
    Assertions.assertNotNull(resp.getCurrency());
    Assertions.assertNotNull(resp.getRedeemAmount());
    Assertions.assertNotNull(resp.getPenaltyInterestAmount());
    Assertions.assertNotNull(resp.getRedeemPeriod());
    Assertions.assertNotNull(resp.getDeliverTime());
    Assertions.assertNotNull(resp.getManualRedeemable());
    Assertions.assertNotNull(resp.getRedeemAll());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** redeem Redeem /api/v1/earn/orders */
  @Test
  public void testRedeem() throws Exception {
    RedeemReq.RedeemReqBuilder builder = RedeemReq.builder();
    builder
        .orderId("2155441")
        .amount("10")
        .fromAccountType(RedeemReq.FromAccountTypeEnum.MAIN)
        .confirmPunishRedeem("1");
    RedeemReq req = builder.build();
    RedeemResp resp = api.redeem(req);
    Assertions.assertNotNull(resp.getOrderTxId());
    Assertions.assertNotNull(resp.getDeliverTime());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getAmount());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSavingsProducts Get Savings Products /api/v1/earn/saving/products */
  @Test
  public void testGetSavingsProducts() throws Exception {
    GetSavingsProductsReq.GetSavingsProductsReqBuilder builder = GetSavingsProductsReq.builder();
    builder.currency("USDT");
    GetSavingsProductsReq req = builder.build();
    GetSavingsProductsResp resp = api.getSavingsProducts(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getCategory());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getPrecision());
              Assertions.assertNotNull(item.getProductUpperLimit());
              Assertions.assertNotNull(item.getUserUpperLimit());
              Assertions.assertNotNull(item.getUserLowerLimit());
              Assertions.assertNotNull(item.getRedeemPeriod());
              Assertions.assertNotNull(item.getLockStartTime());
              Assertions.assertNotNull(item.getApplyStartTime());
              Assertions.assertNotNull(item.getReturnRate());
              Assertions.assertNotNull(item.getIncomeCurrency());
              Assertions.assertNotNull(item.getEarlyRedeemSupported());
              Assertions.assertNotNull(item.getProductRemainAmount());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getRedeemType());
              Assertions.assertNotNull(item.getIncomeReleaseType());
              Assertions.assertNotNull(item.getInterestDate());
              Assertions.assertNotNull(item.getDuration());
              Assertions.assertNotNull(item.getNewUserOnly());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getPromotionProducts Get Promotion Products /api/v1/earn/promotion/products */
  @Test
  public void testGetPromotionProducts() throws Exception {
    GetPromotionProductsReq.GetPromotionProductsReqBuilder builder =
        GetPromotionProductsReq.builder();
    builder.currency("USDT");
    GetPromotionProductsReq req = builder.build();
    GetPromotionProductsResp resp = api.getPromotionProducts(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getCategory());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getPrecision());
              Assertions.assertNotNull(item.getProductUpperLimit());
              Assertions.assertNotNull(item.getUserUpperLimit());
              Assertions.assertNotNull(item.getUserLowerLimit());
              Assertions.assertNotNull(item.getRedeemPeriod());
              Assertions.assertNotNull(item.getLockStartTime());
              Assertions.assertNotNull(item.getApplyStartTime());
              Assertions.assertNotNull(item.getReturnRate());
              Assertions.assertNotNull(item.getIncomeCurrency());
              Assertions.assertNotNull(item.getEarlyRedeemSupported());
              Assertions.assertNotNull(item.getProductRemainAmount());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getRedeemType());
              Assertions.assertNotNull(item.getIncomeReleaseType());
              Assertions.assertNotNull(item.getInterestDate());
              Assertions.assertNotNull(item.getDuration());
              Assertions.assertNotNull(item.getNewUserOnly());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getStakingProducts Get Staking Products /api/v1/earn/staking/products */
  @Test
  public void testGetStakingProducts() throws Exception {
    GetStakingProductsReq.GetStakingProductsReqBuilder builder = GetStakingProductsReq.builder();
    builder.currency("ATOM");
    GetStakingProductsReq req = builder.build();
    GetStakingProductsResp resp = api.getStakingProducts(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getCategory());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getPrecision());
              Assertions.assertNotNull(item.getProductUpperLimit());
              Assertions.assertNotNull(item.getUserUpperLimit());
              Assertions.assertNotNull(item.getUserLowerLimit());
              Assertions.assertNotNull(item.getRedeemPeriod());
              Assertions.assertNotNull(item.getLockStartTime());
              Assertions.assertNotNull(item.getApplyStartTime());
              Assertions.assertNotNull(item.getReturnRate());
              Assertions.assertNotNull(item.getIncomeCurrency());
              Assertions.assertNotNull(item.getEarlyRedeemSupported());
              Assertions.assertNotNull(item.getProductRemainAmount());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getRedeemType());
              Assertions.assertNotNull(item.getIncomeReleaseType());
              Assertions.assertNotNull(item.getInterestDate());
              Assertions.assertNotNull(item.getDuration());
              Assertions.assertNotNull(item.getNewUserOnly());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getKcsStakingProducts Get KCS Staking Products /api/v1/earn/kcs-staking/products */
  @Test
  public void testGetKcsStakingProducts() throws Exception {
    GetKcsStakingProductsReq.GetKcsStakingProductsReqBuilder builder =
        GetKcsStakingProductsReq.builder();
    builder.currency("KCS");
    GetKcsStakingProductsReq req = builder.build();
    GetKcsStakingProductsResp resp = api.getKcsStakingProducts(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getCategory());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getPrecision());
              Assertions.assertNotNull(item.getProductUpperLimit());
              Assertions.assertNotNull(item.getUserUpperLimit());
              Assertions.assertNotNull(item.getUserLowerLimit());
              Assertions.assertNotNull(item.getRedeemPeriod());
              Assertions.assertNotNull(item.getLockStartTime());
              Assertions.assertNotNull(item.getApplyStartTime());
              Assertions.assertNotNull(item.getReturnRate());
              Assertions.assertNotNull(item.getIncomeCurrency());
              Assertions.assertNotNull(item.getEarlyRedeemSupported());
              Assertions.assertNotNull(item.getProductRemainAmount());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getRedeemType());
              Assertions.assertNotNull(item.getIncomeReleaseType());
              Assertions.assertNotNull(item.getInterestDate());
              Assertions.assertNotNull(item.getDuration());
              Assertions.assertNotNull(item.getNewUserOnly());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getETHStakingProducts Get ETH Staking Products /api/v1/earn/eth-staking/products */
  @Test
  public void testGetETHStakingProducts() throws Exception {
    GetETHStakingProductsReq.GetETHStakingProductsReqBuilder builder =
        GetETHStakingProductsReq.builder();
    builder.currency("ETH");
    GetETHStakingProductsReq req = builder.build();
    GetETHStakingProductsResp resp = api.getETHStakingProducts(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getCategory());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getPrecision());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getIncomeCurrency());
              Assertions.assertNotNull(item.getReturnRate());
              Assertions.assertNotNull(item.getUserLowerLimit());
              Assertions.assertNotNull(item.getUserUpperLimit());
              Assertions.assertNotNull(item.getProductUpperLimit());
              Assertions.assertNotNull(item.getProductRemainAmount());
              Assertions.assertNotNull(item.getRedeemPeriod());
              Assertions.assertNotNull(item.getRedeemType());
              Assertions.assertNotNull(item.getIncomeReleaseType());
              Assertions.assertNotNull(item.getApplyStartTime());
              Assertions.assertNotNull(item.getLockStartTime());
              Assertions.assertNotNull(item.getInterestDate());
              Assertions.assertNotNull(item.getNewUserOnly());
              Assertions.assertNotNull(item.getEarlyRedeemSupported());
              Assertions.assertNotNull(item.getDuration());
              Assertions.assertNotNull(item.getStatus());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getAccountHolding Get Account Holding /api/v1/earn/hold-assets */
  @Test
  public void testGetAccountHolding() throws Exception {
    GetAccountHoldingReq.GetAccountHoldingReqBuilder builder = GetAccountHoldingReq.builder();
    builder
        .currency("USDT")
        .productId("2152")
        .productCategory(GetAccountHoldingReq.ProductCategoryEnum.DEMAND);
    GetAccountHoldingReq req = builder.build();
    GetAccountHoldingResp resp = api.getAccountHolding(req);
    Assertions.assertNotNull(resp.getTotalNum());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getOrderId());
              Assertions.assertNotNull(item.getProductId());
              Assertions.assertNotNull(item.getProductCategory());
              Assertions.assertNotNull(item.getProductType());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getIncomeCurrency());
              Assertions.assertNotNull(item.getReturnRate());
              Assertions.assertNotNull(item.getHoldAmount());
              Assertions.assertNotNull(item.getRedeemedAmount());
              Assertions.assertNotNull(item.getRedeemingAmount());
              Assertions.assertNotNull(item.getLockStartTime());
              Assertions.assertNotNull(item.getPurchaseTime());
              Assertions.assertNotNull(item.getRedeemPeriod());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getEarlyRedeemSupported());
            });

    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalPage());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
