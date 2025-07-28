package com.kucoin.test.regression;

import static org.junit.jupiter.api.Assertions.*;

import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinRestService;
import com.kucoin.universal.sdk.generate.account.fee.GetBasicFeeReq;
import com.kucoin.universal.sdk.generate.account.fee.GetBasicFeeResp;
import com.kucoin.universal.sdk.generate.earn.earn.GetSavingsProductsReq;
import com.kucoin.universal.sdk.generate.earn.earn.GetSavingsProductsResp;
import com.kucoin.universal.sdk.generate.futures.order.AddOrderResp;
import com.kucoin.universal.sdk.generate.futures.order.CancelOrderByIdReq;
import com.kucoin.universal.sdk.generate.service.SpotService;
import com.kucoin.universal.sdk.generate.spot.market.Get24hrStatsReq;
import com.kucoin.universal.sdk.generate.spot.market.Get24hrStatsResp;
import com.kucoin.universal.sdk.generate.spot.order.AddOrderSyncReq;
import com.kucoin.universal.sdk.generate.spot.order.AddOrderSyncResp;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.RestResponse;
import com.kucoin.universal.sdk.model.TransportOption;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.Test;
import org.junit.platform.engine.TestExecutionResult;
import org.junit.platform.engine.discovery.DiscoverySelectors;
import org.junit.platform.launcher.Launcher;
import org.junit.platform.launcher.LauncherDiscoveryRequest;
import org.junit.platform.launcher.TestExecutionListener;
import org.junit.platform.launcher.TestIdentifier;
import org.junit.platform.launcher.core.LauncherDiscoveryRequestBuilder;
import org.junit.platform.launcher.core.LauncherFactory;

public class RunServiceTest {

  private static KucoinRestService rest;

  @BeforeAll
  public static void setUp() {
    ClientOption clientOption =
        ClientOption.builder()
            .key(System.getenv("API_KEY"))
            .secret(System.getenv("API_SECRET"))
            .passphrase(System.getenv("API_PASSPHRASE"))
            .transportOption(TransportOption.builder().build())
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .build();

    rest = new DefaultKucoinClient(clientOption).getRestService();
  }

  private static void ok(RestResponse commonResponse) {
    assertEquals("200000", commonResponse.getCode());
    assertNotNull(commonResponse.getRateLimit());
  }

  @Test
  public void testAccount() {
    GetBasicFeeResp response =
        rest.getAccountService()
            .getFeeApi()
            .getBasicFee(
                GetBasicFeeReq.builder().currencyType(GetBasicFeeReq.CurrencyTypeEnum._0).build());
    ok(response.getCommonResponse());
    assertNotNull(response.getMakerFeeRate());
  }

  @Test
  public void testEarn() {
    GetSavingsProductsResp response =
        rest.getEarnService()
            .getEarnApi()
            .getSavingsProducts(GetSavingsProductsReq.builder().currency("USDT").build());
    ok(response.getCommonResponse());
    assertFalse(response.getData().isEmpty());
  }

  @Test
  public void testMargin() {
    com.kucoin.universal.sdk.generate.margin.order.OrderApi api =
        rest.getMarginService().getOrderApi();
    com.kucoin.universal.sdk.generate.margin.order.AddOrderResp add =
        api.addOrder(
            com.kucoin.universal.sdk.generate.margin.order.AddOrderReq.builder()
                .clientOid(UUID.randomUUID().toString())
                .side(com.kucoin.universal.sdk.generate.margin.order.AddOrderReq.SideEnum.BUY)
                .symbol("BTC-USDT")
                .type(com.kucoin.universal.sdk.generate.margin.order.AddOrderReq.TypeEnum.LIMIT)
                .price("10000")
                .size("0.001")
                .autoRepay(true)
                .autoBorrow(true)
                .isIsolated(false)
                .build());
    ok(add.getCommonResponse());

    api.cancelOrderByOrderId(
        com.kucoin.universal.sdk.generate.margin.order.CancelOrderByOrderIdReq.builder()
            .orderId(add.getOrderId())
            .symbol("BTC-USDT")
            .build());
  }

  @Test
  public void testSpot() {
    SpotService spot = rest.getSpotService();
    Get24hrStatsResp marketResponse =
        spot.getMarketApi().get24hrStats(Get24hrStatsReq.builder().symbol("BTC-USDT").build());
    ok(marketResponse.getCommonResponse());

    com.kucoin.universal.sdk.generate.spot.order.OrderApi orderApi = spot.getOrderApi();
    AddOrderSyncResp add =
        orderApi.addOrderSync(
            AddOrderSyncReq.builder()
                .clientOid(UUID.randomUUID().toString())
                .side(AddOrderSyncReq.SideEnum.BUY)
                .symbol("BTC-USDT")
                .type(AddOrderSyncReq.TypeEnum.LIMIT)
                .remark("sdk_test")
                .price("10000")
                .size("0.001")
                .build());
    ok(add.getCommonResponse());

    orderApi.cancelOrderByOrderId(
        com.kucoin.universal.sdk.generate.spot.order.CancelOrderByOrderIdReq.builder()
            .orderId(add.getOrderId())
            .symbol("BTC-USDT")
            .build());
  }

  @Test
  public void testFutures() {
    com.kucoin.universal.sdk.generate.futures.order.OrderApi orderApi =
        rest.getFuturesService().getOrderApi();
    AddOrderResp add =
        orderApi.addOrder(
            com.kucoin.universal.sdk.generate.futures.order.AddOrderReq.builder()
                .clientOid(UUID.randomUUID().toString())
                .side(com.kucoin.universal.sdk.generate.futures.order.AddOrderReq.SideEnum.BUY)
                .symbol("XBTUSDTM")
                .leverage(1)
                .type(com.kucoin.universal.sdk.generate.futures.order.AddOrderReq.TypeEnum.LIMIT)
                .remark("sdk_test")
                .marginMode(
                    com.kucoin.universal.sdk.generate.futures.order.AddOrderReq.MarginModeEnum
                        .CROSS)
                .price("1")
                .size(1)
                .build());
    ok(add.getCommonResponse());

    orderApi.cancelOrderById(CancelOrderByIdReq.builder().orderId(add.getOrderId()).build());
  }

  public static void main(String[] args) {
    LauncherDiscoveryRequest request =
        LauncherDiscoveryRequestBuilder.request()
            .selectors(DiscoverySelectors.selectClass(RunServiceTest.class))
            .build();

    Launcher launcher = LauncherFactory.create();

    class TestResult {
      String displayName;
      boolean success;
      Throwable exception;
    }

    List<TestResult> results = new ArrayList<>();
    int[] failedCount = {0};

    TestExecutionListener listener =
        new TestExecutionListener() {
          @Override
          public void executionFinished(
              TestIdentifier testIdentifier, TestExecutionResult testExecutionResult) {
            if (!testIdentifier.isTest()) return;

            TestResult result = new TestResult();
            result.displayName = testIdentifier.getDisplayName();

            if (testExecutionResult.getStatus() == TestExecutionResult.Status.SUCCESSFUL) {
              result.success = true;
            } else {
              result.success = false;
              result.exception = testExecutionResult.getThrowable().orElse(null);
              failedCount[0]++;
            }

            results.add(result);
          }
        };

    launcher.registerTestExecutionListeners(listener);
    launcher.execute(request);

    System.out.println("\nTest Execution Summary:\n");

    for (TestResult r : results) {
      if (r.success) {
        System.out.println(r.displayName + " - OK");
      } else {
        System.out.println(r.displayName + " - FAILED");
        if (r.exception != null) {
          System.out.println("  Exception: " + r.exception.toString());
          r.exception.printStackTrace(System.out);
        }
      }
    }

    System.out.printf(
        "\nTotal: %d, Passed: %d, Failed: %d%n",
        results.size(), results.size() - failedCount[0], failedCount[0]);

    if (failedCount[0] > 0) {
      System.exit(1);
    }
  }
}
