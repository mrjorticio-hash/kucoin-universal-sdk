package com.kucoin.universal.sdk.test.e2e.rest.futures;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.futures.order.*;
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
public class OrderApiTest {

  private static OrderApi api;

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
    api = kucoinClient.getRestService().getFuturesService().getOrderApi();
  }

  /** addOrder Add Order /api/v1/orders */
  @Test
  public void testAddOrder() throws Exception {
    AddOrderReq.AddOrderReqBuilder builder = AddOrderReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderReq.SideEnum.BUY)
        .symbol("XBTUSDTM")
        .leverage(3)
        .type(AddOrderReq.TypeEnum.LIMIT)
        .remark("order_test")
        .marginMode(AddOrderReq.MarginModeEnum.CROSS)
        .price("1")
        .size(1);
    AddOrderReq req = builder.build();
    AddOrderResp resp = api.addOrder(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addOrderTest Add Order Test /api/v1/orders/test */
  @Test
  public void testAddOrderTest() throws Exception {
    AddOrderTestReq.AddOrderTestReqBuilder builder = AddOrderTestReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderTestReq.SideEnum.BUY)
        .symbol("XBTUSDTM")
        .leverage(3)
        .type(AddOrderTestReq.TypeEnum.LIMIT)
        .remark("order_test")
        .marginMode(AddOrderTestReq.MarginModeEnum.CROSS)
        .price("1")
        .size(1);
    AddOrderTestReq req = builder.build();
    AddOrderTestResp resp = api.addOrderTest(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** batchAddOrders Batch Add Orders /api/v1/orders/multi */
  @Test
  public void testBatchAddOrders() throws Exception {
    BatchAddOrdersReq.BatchAddOrdersReqBuilder builder = BatchAddOrdersReq.builder();

    BatchAddOrdersItem.BatchAddOrdersItemBuilder builder1 = BatchAddOrdersItem.builder();
    builder1
        .clientOid(UUID.randomUUID().toString())
        .side(BatchAddOrdersItem.SideEnum.BUY)
        .symbol("XBTUSDTM")
        .leverage(3)
        .type(BatchAddOrdersItem.TypeEnum.LIMIT)
        .remark("order_test")
        .marginMode(BatchAddOrdersItem.MarginModeEnum.CROSS)
        .price("1")
        .size(1);

    BatchAddOrdersItem.BatchAddOrdersItemBuilder builder2 = BatchAddOrdersItem.builder();
    builder2
        .clientOid(UUID.randomUUID().toString())
        .side(BatchAddOrdersItem.SideEnum.BUY)
        .symbol("XBTUSDTM")
        .leverage(3)
        .type(BatchAddOrdersItem.TypeEnum.LIMIT)
        .remark("order_test")
        .marginMode(BatchAddOrdersItem.MarginModeEnum.CROSS)
        .price("1")
        .size(1);

    builder.items(Arrays.asList(builder1.build(), builder2.build()));
    BatchAddOrdersReq req = builder.build();
    BatchAddOrdersResp resp = api.batchAddOrders(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getOrderId());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getCode());
              Assertions.assertNotNull(item.getMsg());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addTPSLOrder Add Take Profit And Stop Loss Order /api/v1/st-orders */
  @Test
  public void testAddTPSLOrder() throws Exception {
    AddTPSLOrderReq.AddTPSLOrderReqBuilder builder = AddTPSLOrderReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddTPSLOrderReq.SideEnum.BUY)
        .symbol("XBTUSDTM")
        .leverage(3)
        .type(AddTPSLOrderReq.TypeEnum.LIMIT)
        .remark("order_test")
        .stopPriceType(AddTPSLOrderReq.StopPriceTypeEnum.TRADEPRICE)
        .marginMode(AddTPSLOrderReq.MarginModeEnum.CROSS)
        .price("10000")
        .size(1)
        .triggerStopUpPrice("8000")
        .triggerStopDownPrice("12000");
    AddTPSLOrderReq req = builder.build();
    AddTPSLOrderResp resp = api.addTPSLOrder(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelOrderById Cancel Order By OrderId /api/v1/orders/{orderId} */
  @Test
  public void testCancelOrderById() throws Exception {
    CancelOrderByIdReq.CancelOrderByIdReqBuilder builder = CancelOrderByIdReq.builder();
    builder.orderId("337705650295418881");
    CancelOrderByIdReq req = builder.build();
    CancelOrderByIdResp resp = api.cancelOrderById(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelOrderByClientOid Cancel Order By ClientOid /api/v1/orders/client-order/{clientOid} */
  @Test
  public void testCancelOrderByClientOid() throws Exception {
    CancelOrderByClientOidReq.CancelOrderByClientOidReqBuilder builder =
        CancelOrderByClientOidReq.builder();
    builder.symbol("XBTUSDTM").clientOid("2252fd15-bcee-4d05-ba37-65c2e97af013");
    CancelOrderByClientOidReq req = builder.build();
    CancelOrderByClientOidResp resp = api.cancelOrderByClientOid(req);
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** batchCancelOrders Batch Cancel Orders /api/v1/orders/multi-cancel */
  @Test
  public void testBatchCancelOrders() throws Exception {
    BatchCancelOrdersReq.BatchCancelOrdersReqBuilder builder = BatchCancelOrdersReq.builder();
    builder
        .orderIdsList(Arrays.asList("337760909529260032", "337760909562814464"))
        .clientOidsList(
            Arrays.asList(
                BatchCancelOrdersClientOidsList.builder()
                    .symbol("XBTUSDTM")
                    .clientOid("7bd1ac22-0cbc-4b78-95b9-fa422d1f86f4")
                    .build(),
                (BatchCancelOrdersClientOidsList.builder()
                    .symbol("XBTUSDTM")
                    .clientOid("00f46911-45a5-416a-9591-61e1e5009407")
                    .build())));
    BatchCancelOrdersReq req = builder.build();
    BatchCancelOrdersResp resp = api.batchCancelOrders(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getOrderId());
              Assertions.assertNotNull(item.getCode());
              Assertions.assertNotNull(item.getMsg());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelAllOrdersV3 Cancel All Orders /api/v3/orders */
  @Test
  public void testCancelAllOrdersV3() throws Exception {
    CancelAllOrdersV3Req.CancelAllOrdersV3ReqBuilder builder = CancelAllOrdersV3Req.builder();
    builder.symbol("XBTUSDTM");
    CancelAllOrdersV3Req req = builder.build();
    CancelAllOrdersV3Resp resp = api.cancelAllOrdersV3(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelAllStopOrders Cancel All Stop orders /api/v1/stopOrders */
  @Test
  public void testCancelAllStopOrders() throws Exception {
    CancelAllStopOrdersReq.CancelAllStopOrdersReqBuilder builder = CancelAllStopOrdersReq.builder();
    builder.symbol("XBTUSDTM");
    CancelAllStopOrdersReq req = builder.build();
    CancelAllStopOrdersResp resp = api.cancelAllStopOrders(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOrderByOrderId Get Order By OrderId /api/v1/orders/{order-id} */
  @Test
  public void testGetOrderByOrderId() throws Exception {
    GetOrderByOrderIdReq.GetOrderByOrderIdReqBuilder builder = GetOrderByOrderIdReq.builder();
    builder.orderId("337761538230231040");
    GetOrderByOrderIdReq req = builder.build();
    GetOrderByOrderIdResp resp = api.getOrderByOrderId(req);
    Assertions.assertNotNull(resp.getId());
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getType());
    Assertions.assertNotNull(resp.getSide());
    Assertions.assertNotNull(resp.getPrice());
    Assertions.assertNotNull(resp.getSize());
    Assertions.assertNotNull(resp.getValue());
    Assertions.assertNotNull(resp.getDealValue());
    Assertions.assertNotNull(resp.getDealSize());
    Assertions.assertNotNull(resp.getStp());
    Assertions.assertNotNull(resp.getStop());
    Assertions.assertNotNull(resp.getStopPriceType());
    Assertions.assertNotNull(resp.getStopTriggered());
    Assertions.assertNotNull(resp.getTimeInForce());
    Assertions.assertNotNull(resp.getPostOnly());
    Assertions.assertNotNull(resp.getHidden());
    Assertions.assertNotNull(resp.getIceberg());
    Assertions.assertNotNull(resp.getLeverage());
    Assertions.assertNotNull(resp.getForceHold());
    Assertions.assertNotNull(resp.getCloseOrder());
    Assertions.assertNotNull(resp.getVisibleSize());
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getTags());
    Assertions.assertNotNull(resp.getIsActive());
    Assertions.assertNotNull(resp.getCancelExist());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getUpdatedAt());
    Assertions.assertNotNull(resp.getOrderTime());
    Assertions.assertNotNull(resp.getSettleCurrency());
    Assertions.assertNotNull(resp.getMarginMode());
    Assertions.assertNotNull(resp.getAvgDealPrice());
    Assertions.assertNotNull(resp.getFilledSize());
    Assertions.assertNotNull(resp.getFilledValue());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getReduceOnly());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOrderByClientOid Get Order By ClientOid /api/v1/orders/byClientOid */
  @Test
  public void testGetOrderByClientOid() throws Exception {
    GetOrderByClientOidReq.GetOrderByClientOidReqBuilder builder = GetOrderByClientOidReq.builder();
    builder.clientOid("6e68ec8c-8613-451a-b29b-d60e007c0138");
    GetOrderByClientOidReq req = builder.build();
    GetOrderByClientOidResp resp = api.getOrderByClientOid(req);
    Assertions.assertNotNull(resp.getId());
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getType());
    Assertions.assertNotNull(resp.getSide());
    Assertions.assertNotNull(resp.getPrice());
    Assertions.assertNotNull(resp.getSize());
    Assertions.assertNotNull(resp.getValue());
    Assertions.assertNotNull(resp.getDealValue());
    Assertions.assertNotNull(resp.getDealSize());
    Assertions.assertNotNull(resp.getStp());
    Assertions.assertNotNull(resp.getStop());
    Assertions.assertNotNull(resp.getStopPriceType());
    Assertions.assertNotNull(resp.getStopTriggered());
    Assertions.assertNotNull(resp.getTimeInForce());
    Assertions.assertNotNull(resp.getPostOnly());
    Assertions.assertNotNull(resp.getHidden());
    Assertions.assertNotNull(resp.getIceberg());
    Assertions.assertNotNull(resp.getLeverage());
    Assertions.assertNotNull(resp.getForceHold());
    Assertions.assertNotNull(resp.getCloseOrder());
    Assertions.assertNotNull(resp.getVisibleSize());
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getTags());
    Assertions.assertNotNull(resp.getIsActive());
    Assertions.assertNotNull(resp.getCancelExist());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getUpdatedAt());
    Assertions.assertNotNull(resp.getOrderTime());
    Assertions.assertNotNull(resp.getSettleCurrency());
    Assertions.assertNotNull(resp.getMarginMode());
    Assertions.assertNotNull(resp.getAvgDealPrice());
    Assertions.assertNotNull(resp.getFilledSize());
    Assertions.assertNotNull(resp.getFilledValue());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getReduceOnly());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOrderList Get Order List /api/v1/orders */
  @Test
  public void testGetOrderList() throws Exception {
    GetOrderListReq.GetOrderListReqBuilder builder = GetOrderListReq.builder();
    builder.status(GetOrderListReq.StatusEnum.DONE).symbol("XBTUSDTM");
    GetOrderListReq req = builder.build();
    GetOrderListResp resp = api.getOrderList(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getValue());
              Assertions.assertNotNull(item.getDealValue());
              Assertions.assertNotNull(item.getDealSize());
              Assertions.assertNotNull(item.getStp());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getStopPriceType());
              Assertions.assertNotNull(item.getStopTriggered());
              Assertions.assertNotNull(item.getTimeInForce());
              Assertions.assertNotNull(item.getPostOnly());
              Assertions.assertNotNull(item.getHidden());
              Assertions.assertNotNull(item.getIceberg());
              Assertions.assertNotNull(item.getLeverage());
              Assertions.assertNotNull(item.getForceHold());
              Assertions.assertNotNull(item.getCloseOrder());
              Assertions.assertNotNull(item.getVisibleSize());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getTags());
              Assertions.assertNotNull(item.getIsActive());
              Assertions.assertNotNull(item.getCancelExist());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getUpdatedAt());
              Assertions.assertNotNull(item.getEndAt());
              Assertions.assertNotNull(item.getOrderTime());
              Assertions.assertNotNull(item.getSettleCurrency());
              Assertions.assertNotNull(item.getMarginMode());
              Assertions.assertNotNull(item.getAvgDealPrice());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getFilledSize());
              Assertions.assertNotNull(item.getFilledValue());
              Assertions.assertNotNull(item.getReduceOnly());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getRecentClosedOrders Get Recent Closed Orders /api/v1/recentDoneOrders */
  @Test
  public void testGetRecentClosedOrders() throws Exception {
    GetRecentClosedOrdersReq.GetRecentClosedOrdersReqBuilder builder =
        GetRecentClosedOrdersReq.builder();
    builder.symbol("XBTUSDTM");
    GetRecentClosedOrdersReq req = builder.build();
    GetRecentClosedOrdersResp resp = api.getRecentClosedOrders(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getValue());
              Assertions.assertNotNull(item.getDealValue());
              Assertions.assertNotNull(item.getDealSize());
              Assertions.assertNotNull(item.getStp());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getStopPriceType());
              Assertions.assertNotNull(item.getStopTriggered());
              Assertions.assertNotNull(item.getTimeInForce());
              Assertions.assertNotNull(item.getPostOnly());
              Assertions.assertNotNull(item.getHidden());
              Assertions.assertNotNull(item.getIceberg());
              Assertions.assertNotNull(item.getLeverage());
              Assertions.assertNotNull(item.getForceHold());
              Assertions.assertNotNull(item.getCloseOrder());
              Assertions.assertNotNull(item.getVisibleSize());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getTags());
              Assertions.assertNotNull(item.getIsActive());
              Assertions.assertNotNull(item.getCancelExist());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getUpdatedAt());
              Assertions.assertNotNull(item.getEndAt());
              Assertions.assertNotNull(item.getOrderTime());
              Assertions.assertNotNull(item.getSettleCurrency());
              Assertions.assertNotNull(item.getMarginMode());
              Assertions.assertNotNull(item.getAvgDealPrice());
              Assertions.assertNotNull(item.getFilledSize());
              Assertions.assertNotNull(item.getFilledValue());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getReduceOnly());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getStopOrderList Get Stop Order List /api/v1/stopOrders */
  @Test
  public void testGetStopOrderList() throws Exception {
    GetStopOrderListReq.GetStopOrderListReqBuilder builder = GetStopOrderListReq.builder();
    builder.symbol("XBTUSDTM");
    GetStopOrderListReq req = builder.build();
    GetStopOrderListResp resp = api.getStopOrderList(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getValue());
              Assertions.assertNotNull(item.getDealValue());
              Assertions.assertNotNull(item.getDealSize());
              Assertions.assertNotNull(item.getStp());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getStopPriceType());
              Assertions.assertNotNull(item.getStopTriggered());
              Assertions.assertNotNull(item.getStopPrice());
              Assertions.assertNotNull(item.getTimeInForce());
              Assertions.assertNotNull(item.getPostOnly());
              Assertions.assertNotNull(item.getHidden());
              Assertions.assertNotNull(item.getIceberg());
              Assertions.assertNotNull(item.getLeverage());
              Assertions.assertNotNull(item.getForceHold());
              Assertions.assertNotNull(item.getCloseOrder());
              Assertions.assertNotNull(item.getVisibleSize());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getRemark());
              Assertions.assertNotNull(item.getTags());
              Assertions.assertNotNull(item.getIsActive());
              Assertions.assertNotNull(item.getCancelExist());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getUpdatedAt());
              Assertions.assertNotNull(item.getEndAt());
              Assertions.assertNotNull(item.getOrderTime());
              Assertions.assertNotNull(item.getSettleCurrency());
              Assertions.assertNotNull(item.getMarginMode());
              Assertions.assertNotNull(item.getAvgDealPrice());
              Assertions.assertNotNull(item.getFilledSize());
              Assertions.assertNotNull(item.getFilledValue());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getReduceOnly());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOpenOrderValue Get Open Order Value /api/v1/openOrderStatistics */
  @Test
  public void testGetOpenOrderValue() throws Exception {
    GetOpenOrderValueReq.GetOpenOrderValueReqBuilder builder = GetOpenOrderValueReq.builder();
    builder.symbol("XBTUSDTM");
    GetOpenOrderValueReq req = builder.build();
    GetOpenOrderValueResp resp = api.getOpenOrderValue(req);
    Assertions.assertNotNull(resp.getOpenOrderBuySize());
    Assertions.assertNotNull(resp.getOpenOrderSellSize());
    Assertions.assertNotNull(resp.getOpenOrderBuyCost());
    Assertions.assertNotNull(resp.getOpenOrderSellCost());
    Assertions.assertNotNull(resp.getSettleCurrency());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getRecentTradeHistory Get Recent Trade History /api/v1/recentFills */
  @Test
  public void testGetRecentTradeHistory() throws Exception {
    GetRecentTradeHistoryReq.GetRecentTradeHistoryReqBuilder builder =
        GetRecentTradeHistoryReq.builder();
    builder.symbol("XBTUSDTM");
    GetRecentTradeHistoryReq req = builder.build();
    GetRecentTradeHistoryResp resp = api.getRecentTradeHistory(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getTradeId());
              Assertions.assertNotNull(item.getOrderId());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getLiquidity());
              Assertions.assertNotNull(item.getForceTaker());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getValue());
              Assertions.assertNotNull(item.getOpenFeePay());
              Assertions.assertNotNull(item.getCloseFeePay());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getFeeRate());
              Assertions.assertNotNull(item.getFixFee());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getTradeTime());
              Assertions.assertNotNull(item.getMarginMode());
              Assertions.assertNotNull(item.getDisplayType());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getSettleCurrency());
              Assertions.assertNotNull(item.getOrderType());
              Assertions.assertNotNull(item.getTradeType());
              Assertions.assertNotNull(item.getCreatedAt());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getTradeHistory Get Trade History /api/v1/fills */
  @Test
  public void testGetTradeHistory() throws Exception {
    GetTradeHistoryReq.GetTradeHistoryReqBuilder builder = GetTradeHistoryReq.builder();
    builder.symbol("XBTUSDTM");
    GetTradeHistoryReq req = builder.build();
    GetTradeHistoryResp resp = api.getTradeHistory(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getTradeId());
              Assertions.assertNotNull(item.getOrderId());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getLiquidity());
              Assertions.assertNotNull(item.getForceTaker());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getValue());
              Assertions.assertNotNull(item.getOpenFeePay());
              Assertions.assertNotNull(item.getCloseFeePay());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getFeeRate());
              Assertions.assertNotNull(item.getFixFee());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getTradeTime());
              Assertions.assertNotNull(item.getMarginMode());
              Assertions.assertNotNull(item.getSettleCurrency());
              Assertions.assertNotNull(item.getDisplayType());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getOrderType());
              Assertions.assertNotNull(item.getTradeType());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getOpenFeeTaxPay());
              Assertions.assertNotNull(item.getCloseFeeTaxPay());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelAllOrdersV1 Cancel All Orders - V1 /api/v1/orders */
  @Test
  public void testCancelAllOrdersV1() throws Exception {
    CancelAllOrdersV1Req.CancelAllOrdersV1ReqBuilder builder = CancelAllOrdersV1Req.builder();
    builder.symbol("XBTUSDTM");
    CancelAllOrdersV1Req req = builder.build();
    CancelAllOrdersV1Resp resp = api.cancelAllOrdersV1(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
