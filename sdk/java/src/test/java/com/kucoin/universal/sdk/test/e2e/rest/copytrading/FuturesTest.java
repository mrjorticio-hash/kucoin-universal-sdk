package com.kucoin.universal.sdk.test.e2e.rest.copytrading;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.copytrading.futures.*;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import java.io.IOException;
import java.util.Collections;
import java.util.UUID;
import lombok.extern.slf4j.Slf4j;
import okhttp3.Interceptor;
import okhttp3.Request;
import okhttp3.Response;
import org.jetbrains.annotations.NotNull;
import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.Test;

@Slf4j
public class FuturesTest {

  private static FuturesApi api;

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
    api = kucoinClient.getRestService().getCopytradingService().getFuturesApi();
  }

  /** addOrder Add Order /api/v1/copy-trade/futures/orders */
  @Test
  public void testAddOrder() throws Exception {
    AddOrderReq.AddOrderReqBuilder builder = AddOrderReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderReq.SideEnum.BUY)
        .symbol("XBTUSDTM")
        .leverage(3)
        .type(AddOrderReq.TypeEnum.LIMIT)
        .reduceOnly(false)
        .marginMode(AddOrderReq.MarginModeEnum.ISOLATED)
        .price("0.1")
        .size(1)
        .timeInForce(AddOrderReq.TimeInForceEnum.GOODTILLCANCELED);
    AddOrderReq req = builder.build();
    AddOrderResp resp = api.addOrder(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addOrderTest Add Order Test /api/v1/copy-trade/futures/orders/test */
  @Test
  public void testAddOrderTest() throws Exception {
    AddOrderTestReq.AddOrderTestReqBuilder builder = AddOrderTestReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderTestReq.SideEnum.BUY)
        .symbol("XBTUSDTM")
        .leverage(3)
        .type(AddOrderTestReq.TypeEnum.LIMIT)
        .reduceOnly(false)
        .marginMode(AddOrderTestReq.MarginModeEnum.ISOLATED)
        .price("0.1")
        .size(1)
        .timeInForce(AddOrderTestReq.TimeInForceEnum.GOODTILLCANCELED);
    AddOrderTestReq req = builder.build();
    AddOrderTestResp resp = api.addOrderTest(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addTPSLOrder Add Take Profit And Stop Loss Order /api/v1/copy-trade/futures/st-orders */
  @Test
  public void testAddTPSLOrder() throws Exception {
    AddTPSLOrderReq.AddTPSLOrderReqBuilder builder = AddTPSLOrderReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddTPSLOrderReq.SideEnum.BUY)
        .symbol("XBTUSDTM")
        .leverage(3)
        .type(AddTPSLOrderReq.TypeEnum.LIMIT)
        .reduceOnly(false)
        .marginMode(AddTPSLOrderReq.MarginModeEnum.ISOLATED)
        .price("0.1")
        .size(1)
        .timeInForce(AddTPSLOrderReq.TimeInForceEnum.GOODTILLCANCELED)
        .triggerStopUpPrice("0.3")
        .triggerStopDownPrice("0.1")
        .stopPriceType(AddTPSLOrderReq.StopPriceTypeEnum.TRADEPRICE);
    AddTPSLOrderReq req = builder.build();
    AddTPSLOrderResp resp = api.addTPSLOrder(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelOrderById Cancel Order By OrderId /api/v1/copy-trade/futures/orders */
  @Test
  public void testCancelOrderById() throws Exception {
    CancelOrderByIdReq.CancelOrderByIdReqBuilder builder = CancelOrderByIdReq.builder();
    builder.orderId("338035702044397568");
    CancelOrderByIdReq req = builder.build();
    CancelOrderByIdResp resp = api.cancelOrderById(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * cancelOrderByClientOid Cancel Order By ClientOid /api/v1/copy-trade/futures/orders/client-order
   */
  @Test
  public void testCancelOrderByClientOid() throws Exception {
    CancelOrderByClientOidReq.CancelOrderByClientOidReqBuilder builder =
        CancelOrderByClientOidReq.builder();
    builder.symbol("XBTUSDTM").clientOid("465e8c0f-6026-4331-ae8f-2c75aaf1370f");
    CancelOrderByClientOidReq req = builder.build();
    CancelOrderByClientOidResp resp = api.cancelOrderByClientOid(req);
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getMaxOpenSize Get Max Open Size /api/v1/copy-trade/futures/get-max-open-size */
  @Test
  public void testGetMaxOpenSize() throws Exception {
    GetMaxOpenSizeReq.GetMaxOpenSizeReqBuilder builder = GetMaxOpenSizeReq.builder();
    builder.symbol("XBTUSDTM").price(0.1).leverage(10);
    GetMaxOpenSizeReq req = builder.build();
    GetMaxOpenSizeResp resp = api.getMaxOpenSize(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getMaxBuyOpenSize());
    Assertions.assertNotNull(resp.getMaxSellOpenSize());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * getMaxWithdrawMargin Get Max Withdraw Margin
   * /api/v1/copy-trade/futures/position/margin/max-withdraw-margin
   */
  @Test
  public void testGetMaxWithdrawMargin() throws Exception {
    GetMaxWithdrawMarginReq.GetMaxWithdrawMarginReqBuilder builder =
        GetMaxWithdrawMarginReq.builder();
    builder.symbol("XBTUSDTM");
    GetMaxWithdrawMarginReq req = builder.build();
    GetMaxWithdrawMarginResp resp = api.getMaxWithdrawMargin(req);
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * addIsolatedMargin Add Isolated Margin /api/v1/copy-trade/futures/position/margin/deposit-margin
   */
  @Test
  public void testAddIsolatedMargin() throws Exception {
    AddIsolatedMarginReq.AddIsolatedMarginReqBuilder builder = AddIsolatedMarginReq.builder();
    builder.symbol("XBTUSDTM").margin(3.0).bizNo(UUID.randomUUID().toString());
    AddIsolatedMarginReq req = builder.build();
    AddIsolatedMarginResp resp = api.addIsolatedMargin(req);
    Assertions.assertNotNull(resp.getId());
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getAutoDeposit());
    Assertions.assertNotNull(resp.getMaintMarginReq());
    Assertions.assertNotNull(resp.getRiskLimit());
    Assertions.assertNotNull(resp.getRealLeverage());
    Assertions.assertNotNull(resp.getCrossMode());
    Assertions.assertNotNull(resp.getDelevPercentage());
    Assertions.assertNotNull(resp.getOpeningTimestamp());
    Assertions.assertNotNull(resp.getCurrentTimestamp());
    Assertions.assertNotNull(resp.getCurrentQty());
    Assertions.assertNotNull(resp.getCurrentCost());
    Assertions.assertNotNull(resp.getCurrentComm());
    Assertions.assertNotNull(resp.getUnrealisedCost());
    Assertions.assertNotNull(resp.getRealisedGrossCost());
    Assertions.assertNotNull(resp.getRealisedCost());
    Assertions.assertNotNull(resp.getIsOpen());
    Assertions.assertNotNull(resp.getMarkPrice());
    Assertions.assertNotNull(resp.getMarkValue());
    Assertions.assertNotNull(resp.getPosCost());
    Assertions.assertNotNull(resp.getPosCross());
    Assertions.assertNotNull(resp.getPosInit());
    Assertions.assertNotNull(resp.getPosComm());
    Assertions.assertNotNull(resp.getPosLoss());
    Assertions.assertNotNull(resp.getPosMargin());
    Assertions.assertNotNull(resp.getPosMaint());
    Assertions.assertNotNull(resp.getMaintMargin());
    Assertions.assertNotNull(resp.getRealisedGrossPnl());
    Assertions.assertNotNull(resp.getRealisedPnl());
    Assertions.assertNotNull(resp.getUnrealisedPnl());
    Assertions.assertNotNull(resp.getUnrealisedPnlPcnt());
    Assertions.assertNotNull(resp.getUnrealisedRoePcnt());
    Assertions.assertNotNull(resp.getAvgEntryPrice());
    Assertions.assertNotNull(resp.getLiquidationPrice());
    Assertions.assertNotNull(resp.getBankruptPrice());
    Assertions.assertNotNull(resp.getSettleCurrency());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * removeIsolatedMargin Remove Isolated Margin
   * /api/v1/copy-trade/futures/position/margin/withdraw-margin
   */
  @Test
  public void testRemoveIsolatedMargin() throws Exception {
    RemoveIsolatedMarginReq.RemoveIsolatedMarginReqBuilder builder =
        RemoveIsolatedMarginReq.builder();
    builder.symbol("XBTUSDTM").withdrawAmount(0.000001);
    RemoveIsolatedMarginReq req = builder.build();
    RemoveIsolatedMarginResp resp = api.removeIsolatedMargin(req);
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * modifyIsolatedMarginRiskLimt Modify Isolated Margin Risk Limit
   * /api/v1/copy-trade/futures/position/risk-limit-level/change
   */
  @Test
  public void testModifyIsolatedMarginRiskLimt() throws Exception {
    ModifyIsolatedMarginRiskLimtReq.ModifyIsolatedMarginRiskLimtReqBuilder builder =
        ModifyIsolatedMarginRiskLimtReq.builder();
    builder.symbol("XBTUSDTM").level(1);
    ModifyIsolatedMarginRiskLimtReq req = builder.build();
    ModifyIsolatedMarginRiskLimtResp resp = api.modifyIsolatedMarginRiskLimt(req);
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * modifyAutoDepositStatus Modify Isolated Margin Auto-Deposit Status
   * /api/v1/copy-trade/futures/position/margin/auto-deposit-status
   */
  @Test
  public void testModifyAutoDepositStatus() throws Exception {
    ModifyAutoDepositStatusReq.ModifyAutoDepositStatusReqBuilder builder =
        ModifyAutoDepositStatusReq.builder();
    builder.symbol("XBTUSDTM").status(true);
    ModifyAutoDepositStatusReq req = builder.build();
    ModifyAutoDepositStatusResp resp = api.modifyAutoDepositStatus(req);
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
