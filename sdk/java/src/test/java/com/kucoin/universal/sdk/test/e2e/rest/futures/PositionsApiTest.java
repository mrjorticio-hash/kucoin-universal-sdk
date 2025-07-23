package com.kucoin.universal.sdk.test.e2e.rest.futures;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.futures.market.*;
import com.kucoin.universal.sdk.generate.futures.positions.*;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import java.io.IOException;
import java.util.Arrays;
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
public class PositionsApiTest {

  private static PositionsApi api;

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
    api = kucoinClient.getRestService().getFuturesService().getPositionsApi();
  }

  /** getMarginMode Get Margin Mode /api/v2/position/getMarginMode */
  @Test
  public void testGetMarginMode() throws Exception {
    GetMarginModeReq.GetMarginModeReqBuilder builder = GetMarginModeReq.builder();
    builder.symbol("XBTUSDTM");
    GetMarginModeReq req = builder.build();
    GetMarginModeResp resp = api.getMarginMode(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getMarginMode());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** switchMarginMode Switch Margin Mode /api/v2/position/changeMarginMode */
  @Test
  public void testSwitchMarginMode() throws Exception {
    SwitchMarginModeReq.SwitchMarginModeReqBuilder builder = SwitchMarginModeReq.builder();
    builder.symbol("XBTUSDTM").marginMode(SwitchMarginModeReq.MarginModeEnum.CROSS);
    SwitchMarginModeReq req = builder.build();
    SwitchMarginModeResp resp = api.switchMarginMode(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getMarginMode());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** batchSwitchMarginMode Batch Switch Margin Mode /api/v2/position/batchChangeMarginMode */
  @Test
  public void testBatchSwitchMarginMode() throws Exception {
    BatchSwitchMarginModeReq.BatchSwitchMarginModeReqBuilder builder =
        BatchSwitchMarginModeReq.builder();
    builder
        .marginMode(BatchSwitchMarginModeReq.MarginModeEnum.CROSS)
        .symbols(Arrays.asList("XBTUSDTM", "ETHUSDTM"));
    BatchSwitchMarginModeReq req = builder.build();
    BatchSwitchMarginModeResp resp = api.batchSwitchMarginMode(req);
    Assertions.assertNotNull(resp.getMarginMode());
    resp.getErrors()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCode());
              Assertions.assertNotNull(item.getMsg());
              Assertions.assertNotNull(item.getSymbol());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getMaxOpenSize Get Max Open Size /api/v2/getMaxOpenSize */
  @Test
  public void testGetMaxOpenSize() throws Exception {
    GetMaxOpenSizeReq.GetMaxOpenSizeReqBuilder builder = GetMaxOpenSizeReq.builder();
    builder.symbol("XBTUSDTM").price("10000").leverage(4);
    GetMaxOpenSizeReq req = builder.build();
    GetMaxOpenSizeResp resp = api.getMaxOpenSize(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getMaxBuyOpenSize());
    Assertions.assertNotNull(resp.getMaxSellOpenSize());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getPositionDetails Get Position Details /api/v1/position */
  @Test
  public void testGetPositionDetails() throws Exception {
    GetPositionDetailsReq.GetPositionDetailsReqBuilder builder = GetPositionDetailsReq.builder();
    builder.symbol("XBTUSDTM");
    GetPositionDetailsReq req = builder.build();
    GetPositionDetailsResp resp = api.getPositionDetails(req);
    Assertions.assertNotNull(resp.getId());
    Assertions.assertNotNull(resp.getSymbol());
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
    Assertions.assertNotNull(resp.getPosInit());
    Assertions.assertNotNull(resp.getPosMargin());
    Assertions.assertNotNull(resp.getRealisedGrossPnl());
    Assertions.assertNotNull(resp.getRealisedPnl());
    Assertions.assertNotNull(resp.getUnrealisedPnl());
    Assertions.assertNotNull(resp.getUnrealisedPnlPcnt());
    Assertions.assertNotNull(resp.getUnrealisedRoePcnt());
    Assertions.assertNotNull(resp.getAvgEntryPrice());
    Assertions.assertNotNull(resp.getLiquidationPrice());
    Assertions.assertNotNull(resp.getBankruptPrice());
    Assertions.assertNotNull(resp.getSettleCurrency());
    Assertions.assertNotNull(resp.getIsInverse());
    Assertions.assertNotNull(resp.getMarginMode());
    Assertions.assertNotNull(resp.getPositionSide());
    Assertions.assertNotNull(resp.getMaintMarginReq());
    Assertions.assertNotNull(resp.getRiskLimit());
    Assertions.assertNotNull(resp.getRealLeverage());
    Assertions.assertNotNull(resp.getPosCross());
    Assertions.assertNotNull(resp.getPosComm());
    Assertions.assertNotNull(resp.getPosMaint());
    Assertions.assertNotNull(resp.getMaintMargin());
    Assertions.assertNotNull(resp.getMaintainMargin());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getPositionList Get Position List /api/v1/positions */
  @Test
  public void testGetPositionList() throws Exception {
    GetPositionListReq.GetPositionListReqBuilder builder = GetPositionListReq.builder();
    builder.currency("USDT");
    GetPositionListReq req = builder.build();
    GetPositionListResp resp = api.getPositionList(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getCrossMode());
              Assertions.assertNotNull(item.getDelevPercentage());
              Assertions.assertNotNull(item.getOpeningTimestamp());
              Assertions.assertNotNull(item.getCurrentTimestamp());
              Assertions.assertNotNull(item.getCurrentQty());
              Assertions.assertNotNull(item.getCurrentCost());
              Assertions.assertNotNull(item.getCurrentComm());
              Assertions.assertNotNull(item.getUnrealisedCost());
              Assertions.assertNotNull(item.getRealisedGrossCost());
              Assertions.assertNotNull(item.getRealisedCost());
              Assertions.assertNotNull(item.getIsOpen());
              Assertions.assertNotNull(item.getMarkPrice());
              Assertions.assertNotNull(item.getMarkValue());
              Assertions.assertNotNull(item.getPosCost());
              Assertions.assertNotNull(item.getPosInit());
              Assertions.assertNotNull(item.getPosMargin());
              Assertions.assertNotNull(item.getRealisedGrossPnl());
              Assertions.assertNotNull(item.getRealisedPnl());
              Assertions.assertNotNull(item.getUnrealisedPnl());
              Assertions.assertNotNull(item.getUnrealisedPnlPcnt());
              Assertions.assertNotNull(item.getUnrealisedRoePcnt());
              Assertions.assertNotNull(item.getAvgEntryPrice());
              Assertions.assertNotNull(item.getLiquidationPrice());
              Assertions.assertNotNull(item.getBankruptPrice());
              Assertions.assertNotNull(item.getSettleCurrency());
              Assertions.assertNotNull(item.getIsInverse());
              Assertions.assertNotNull(item.getMarginMode());
              Assertions.assertNotNull(item.getPositionSide());
              Assertions.assertNotNull(item.getLeverage());
              Assertions.assertNotNull(item.getMaintMarginReq());
              Assertions.assertNotNull(item.getPosMaint());
              Assertions.assertNotNull(item.getMaintainMargin());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getPositionsHistory Get Positions History /api/v1/history-positions */
  @Test
  public void testGetPositionsHistory() throws Exception {
    GetPositionsHistoryReq.GetPositionsHistoryReqBuilder builder = GetPositionsHistoryReq.builder();
    builder.symbol("XBTUSDTM");
    GetPositionsHistoryReq req = builder.build();
    GetPositionsHistoryResp resp = api.getPositionsHistory(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCloseId());
              Assertions.assertNotNull(item.getUserId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getSettleCurrency());
              Assertions.assertNotNull(item.getLeverage());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getPnl());
              Assertions.assertNotNull(item.getRealisedGrossCost());
              Assertions.assertNotNull(item.getWithdrawPnl());
              Assertions.assertNotNull(item.getTradeFee());
              Assertions.assertNotNull(item.getFundingFee());
              Assertions.assertNotNull(item.getOpenTime());
              Assertions.assertNotNull(item.getCloseTime());
              Assertions.assertNotNull(item.getOpenPrice());
              Assertions.assertNotNull(item.getClosePrice());
              Assertions.assertNotNull(item.getMarginMode());
              Assertions.assertNotNull(item.getRealisedGrossCostNew());
              Assertions.assertNotNull(item.getTax());
              Assertions.assertNotNull(item.getRoe());
              Assertions.assertNotNull(item.getSide());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getMaxWithdrawMargin Get Max Withdraw Margin /api/v1/margin/maxWithdrawMargin */
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

  /** getCrossMarginLeverage Get Cross Margin Leverage /api/v2/getCrossUserLeverage */
  @Test
  public void testGetCrossMarginLeverage() throws Exception {
    GetCrossMarginLeverageReq.GetCrossMarginLeverageReqBuilder builder =
        GetCrossMarginLeverageReq.builder();
    builder.symbol("XBTUSDTM");
    GetCrossMarginLeverageReq req = builder.build();
    GetCrossMarginLeverageResp resp = api.getCrossMarginLeverage(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getLeverage());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** modifyMarginLeverage Modify Cross Margin Leverage /api/v2/changeCrossUserLeverage */
  @Test
  public void testModifyMarginLeverage() throws Exception {
    ModifyMarginLeverageReq.ModifyMarginLeverageReqBuilder builder =
        ModifyMarginLeverageReq.builder();
    builder.symbol("XBTUSDTM").leverage("10");
    ModifyMarginLeverageReq req = builder.build();
    ModifyMarginLeverageResp resp = api.modifyMarginLeverage(req);
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addIsolatedMargin Add Isolated Margin /api/v1/position/margin/deposit-margin */
  @Test
  public void testAddIsolatedMargin() throws Exception {
    AddIsolatedMarginReq.AddIsolatedMarginReqBuilder builder = AddIsolatedMarginReq.builder();
    builder.symbol("XBTUSDTM").margin(1.23).bizNo(UUID.randomUUID().toString());
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

  /** removeIsolatedMargin Remove Isolated Margin /api/v1/margin/withdrawMargin */
  @Test
  public void testRemoveIsolatedMargin() throws Exception {
    RemoveIsolatedMarginReq.RemoveIsolatedMarginReqBuilder builder =
        RemoveIsolatedMarginReq.builder();
    builder.symbol("XBTUSDTM").withdrawAmount("1.0");
    RemoveIsolatedMarginReq req = builder.build();
    RemoveIsolatedMarginResp resp = api.removeIsolatedMargin(req);
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getCrossMarginRiskLimit Get Cross Margin Risk Limit /api/v2/batchGetCrossOrderLimit */
  @Test
  public void testGetCrossMarginRiskLimit() throws Exception {
    GetCrossMarginRiskLimitReq.GetCrossMarginRiskLimitReqBuilder builder =
        GetCrossMarginRiskLimitReq.builder();
    builder.symbol("XBTUSDTM").totalMargin("1").leverage(1);
    GetCrossMarginRiskLimitReq req = builder.build();
    GetCrossMarginRiskLimitResp resp = api.getCrossMarginRiskLimit(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getMaxOpenSize());
              Assertions.assertNotNull(item.getMaxOpenValue());
              Assertions.assertNotNull(item.getTotalMargin());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getLeverage());
              Assertions.assertNotNull(item.getMmr());
              Assertions.assertNotNull(item.getImr());
              Assertions.assertNotNull(item.getCurrency());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * getIsolatedMarginRiskLimit Get Isolated Margin Risk Limit /api/v1/contracts/risk-limit/{symbol}
   */
  @Test
  public void testGetIsolatedMarginRiskLimit() throws Exception {
    GetIsolatedMarginRiskLimitReq.GetIsolatedMarginRiskLimitReqBuilder builder =
        GetIsolatedMarginRiskLimitReq.builder();
    builder.symbol("XBTUSDTM");
    GetIsolatedMarginRiskLimitReq req = builder.build();
    GetIsolatedMarginRiskLimitResp resp = api.getIsolatedMarginRiskLimit(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getLevel());
              Assertions.assertNotNull(item.getMaxRiskLimit());
              Assertions.assertNotNull(item.getMinRiskLimit());
              Assertions.assertNotNull(item.getMaxLeverage());
              Assertions.assertNotNull(item.getInitialMargin());
              Assertions.assertNotNull(item.getMaintainMargin());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * modifyIsolatedMarginRiskLimt Modify Isolated Margin Risk Limit
   * /api/v1/position/risk-limit-level/change
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
   * /api/v1/position/margin/auto-deposit-status
   */
  @Test
  public void testModifyAutoDepositStatus() throws Exception {
    ModifyAutoDepositStatusReq.ModifyAutoDepositStatusReqBuilder builder =
        ModifyAutoDepositStatusReq.builder();
    builder.symbol("XBTUSDTM").status(false);
    ModifyAutoDepositStatusReq req = builder.build();
    ModifyAutoDepositStatusResp resp = api.modifyAutoDepositStatus(req);
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
