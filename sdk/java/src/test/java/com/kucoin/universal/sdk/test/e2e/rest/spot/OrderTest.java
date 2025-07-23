package com.kucoin.universal.sdk.test.e2e.rest.spot;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.spot.order.*;
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
public class OrderTest {

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
    api = kucoinClient.getRestService().getSpotService().getOrderApi();
  }

  /** addOrder Add Order /api/v1/hf/orders */
  @Test
  public void testAddOrder() throws Exception {
    AddOrderReq.AddOrderReqBuilder builder = AddOrderReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderReq.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(AddOrderReq.TypeEnum.LIMIT)
        .remark("test")
        .price("1")
        .size("2");
    AddOrderReq req = builder.build();
    AddOrderResp resp = api.addOrder(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
    log.info("resp: {}", resp.toString());
  }

  /** addOrderSync Add Order Sync /api/v1/hf/orders/sync */
  @Test
  public void testAddOrderSync() throws Exception {
    AddOrderSyncReq.AddOrderSyncReqBuilder builder = AddOrderSyncReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderSyncReq.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(AddOrderSyncReq.TypeEnum.LIMIT)
        .remark("test")
        .price("1")
        .size("2");
    AddOrderSyncReq req = builder.build();
    AddOrderSyncResp resp = api.addOrderSync(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getOrderTime());
    Assertions.assertNotNull(resp.getOriginSize());
    Assertions.assertNotNull(resp.getDealSize());
    Assertions.assertNotNull(resp.getRemainSize());
    Assertions.assertNotNull(resp.getCanceledSize());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getMatchTime());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addOrderTest Add Order Test /api/v1/hf/orders/test */
  @Test
  public void testAddOrderTest() throws Exception {
    AddOrderTestReq.AddOrderTestReqBuilder builder = AddOrderTestReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderTestReq.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(AddOrderTestReq.TypeEnum.LIMIT)
        .remark("test")
        .price("1")
        .size("2");
    AddOrderTestReq req = builder.build();
    AddOrderTestResp resp = api.addOrderTest(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** batchAddOrders Batch Add Orders /api/v1/hf/orders/multi */
  @Test
  public void testBatchAddOrders() throws Exception {
    BatchAddOrdersReq.BatchAddOrdersReqBuilder builder = BatchAddOrdersReq.builder();

    BatchAddOrdersOrderList.BatchAddOrdersOrderListBuilder builder1 =
        BatchAddOrdersOrderList.builder();
    builder1
        .clientOid(UUID.randomUUID().toString())
        .side(BatchAddOrdersOrderList.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(BatchAddOrdersOrderList.TypeEnum.LIMIT)
        .remark("test")
        .price("1")
        .size("2");

    BatchAddOrdersOrderList.BatchAddOrdersOrderListBuilder builder2 =
        BatchAddOrdersOrderList.builder();
    builder2
        .clientOid(UUID.randomUUID().toString())
        .side(BatchAddOrdersOrderList.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(BatchAddOrdersOrderList.TypeEnum.LIMIT)
        .remark("test")
        .price("1")
        .size("2");

    builder.orderList(Arrays.asList(builder1.build(), builder2.build()));
    BatchAddOrdersReq req = builder.build();
    BatchAddOrdersResp resp = api.batchAddOrders(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getOrderId());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getSuccess());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** batchAddOrdersSync Batch Add Orders Sync /api/v1/hf/orders/multi/sync */
  @Test
  public void testBatchAddOrdersSync() throws Exception {
    BatchAddOrdersSyncReq.BatchAddOrdersSyncReqBuilder builder = BatchAddOrdersSyncReq.builder();

    BatchAddOrdersSyncOrderList.BatchAddOrdersSyncOrderListBuilder builder1 =
        BatchAddOrdersSyncOrderList.builder();
    builder1
        .clientOid(UUID.randomUUID().toString())
        .side(BatchAddOrdersSyncOrderList.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(BatchAddOrdersSyncOrderList.TypeEnum.LIMIT)
        .remark("test")
        .price("1")
        .size("2");

    BatchAddOrdersSyncOrderList.BatchAddOrdersSyncOrderListBuilder builder2 =
        BatchAddOrdersSyncOrderList.builder();
    builder2
        .clientOid(UUID.randomUUID().toString())
        .side(BatchAddOrdersSyncOrderList.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(BatchAddOrdersSyncOrderList.TypeEnum.LIMIT)
        .remark("test")
        .price("1")
        .size("2");
    builder.orderList(Arrays.asList(builder1.build(), builder2.build()));
    BatchAddOrdersSyncReq req = builder.build();
    BatchAddOrdersSyncResp resp = api.batchAddOrdersSync(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getOrderId());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getOrderTime());
              Assertions.assertNotNull(item.getOriginSize());
              Assertions.assertNotNull(item.getDealSize());
              Assertions.assertNotNull(item.getRemainSize());
              Assertions.assertNotNull(item.getCanceledSize());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getMatchTime());
              Assertions.assertNotNull(item.getSuccess());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelOrderByOrderId Cancel Order By OrderId /api/v1/hf/orders/{orderId} */
  @Test
  public void testCancelOrderByOrderId() throws Exception {
    CancelOrderByOrderIdReq.CancelOrderByOrderIdReqBuilder builder =
        CancelOrderByOrderIdReq.builder();
    builder.orderId("68808675ba0009000762a170").symbol("BTC-USDT");
    CancelOrderByOrderIdReq req = builder.build();
    CancelOrderByOrderIdResp resp = api.cancelOrderByOrderId(req);
    Assertions.assertNotNull(resp.getOrderId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelOrderByOrderIdSync Cancel Order By OrderId Sync /api/v1/hf/orders/sync/{orderId} */
  @Test
  public void testCancelOrderByOrderIdSync() throws Exception {
    CancelOrderByOrderIdSyncReq.CancelOrderByOrderIdSyncReqBuilder builder =
        CancelOrderByOrderIdSyncReq.builder();
    builder.symbol("BTC-USDT").orderId("688086d9abe6f40007d9d64a");
    CancelOrderByOrderIdSyncReq req = builder.build();
    CancelOrderByOrderIdSyncResp resp = api.cancelOrderByOrderIdSync(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getOriginSize());
    Assertions.assertNotNull(resp.getDealSize());
    Assertions.assertNotNull(resp.getRemainSize());
    Assertions.assertNotNull(resp.getCanceledSize());
    Assertions.assertNotNull(resp.getStatus());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelOrderByClientOid Cancel Order By ClientOid /api/v1/hf/orders/client-order/{clientOid} */
  @Test
  public void testCancelOrderByClientOid() throws Exception {
    CancelOrderByClientOidReq.CancelOrderByClientOidReqBuilder builder =
        CancelOrderByClientOidReq.builder();
    builder.clientOid("7c1413d2-9a7b-4a2b-ab9b-fb03d3d01520").symbol("BTC-USDT");
    CancelOrderByClientOidReq req = builder.build();
    CancelOrderByClientOidResp resp = api.cancelOrderByClientOid(req);
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * cancelOrderByClientOidSync Cancel Order By ClientOid Sync
   * /api/v1/hf/orders/sync/client-order/{clientOid}
   */
  @Test
  public void testCancelOrderByClientOidSync() throws Exception {
    CancelOrderByClientOidSyncReq.CancelOrderByClientOidSyncReqBuilder builder =
        CancelOrderByClientOidSyncReq.builder();
    builder.symbol("BTC-USDT").clientOid("0b455f26-f0fa-4b49-ad20-bf0b9a3a014d");
    CancelOrderByClientOidSyncReq req = builder.build();
    CancelOrderByClientOidSyncResp resp = api.cancelOrderByClientOidSync(req);
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getOriginSize());
    Assertions.assertNotNull(resp.getDealSize());
    Assertions.assertNotNull(resp.getRemainSize());
    Assertions.assertNotNull(resp.getCanceledSize());
    Assertions.assertNotNull(resp.getStatus());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelPartialOrder Cancel Partial Order /api/v1/hf/orders/cancel/{orderId} */
  @Test
  public void testCancelPartialOrder() throws Exception {
    CancelPartialOrderReq.CancelPartialOrderReqBuilder builder = CancelPartialOrderReq.builder();
    builder.orderId("688087a7bcd4f40007d0d563").symbol("BTC-USDT").cancelSize("1");
    CancelPartialOrderReq req = builder.build();
    CancelPartialOrderResp resp = api.cancelPartialOrder(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getCancelSize());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelAllOrdersBySymbol Cancel All Orders By Symbol /api/v1/hf/orders */
  @Test
  public void testCancelAllOrdersBySymbol() throws Exception {
    CancelAllOrdersBySymbolReq.CancelAllOrdersBySymbolReqBuilder builder =
        CancelAllOrdersBySymbolReq.builder();
    builder.symbol("BTC-USDT");
    CancelAllOrdersBySymbolReq req = builder.build();
    CancelAllOrdersBySymbolResp resp = api.cancelAllOrdersBySymbol(req);
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelAllOrders Cancel All Orders /api/v1/hf/orders/cancelAll */
  @Test
  public void testCancelAllOrders() throws Exception {
    CancelAllOrdersResp resp = api.cancelAllOrders();
    resp.getSucceedSymbols().forEach(item -> {});

    resp.getFailedSymbols()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getError());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** modifyOrder Modify Order /api/v1/hf/orders/alter */
  @Test
  public void testModifyOrder() throws Exception {
    ModifyOrderReq.ModifyOrderReqBuilder builder = ModifyOrderReq.builder();
    builder
        .clientOid("e66d0636-69f1-4d16-bcf6-e0436f9461e6")
        .symbol("BTC-USDT")
        .orderId("68808854edd3e00007270165")
        .newPrice("2")
        .newSize("4");
    ModifyOrderReq req = builder.build();
    ModifyOrderResp resp = api.modifyOrder(req);
    Assertions.assertNotNull(resp.getNewOrderId());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOrderByOrderId Get Order By OrderId /api/v1/hf/orders/{orderId} */
  @Test
  public void testGetOrderByOrderId() throws Exception {
    GetOrderByOrderIdReq.GetOrderByOrderIdReqBuilder builder = GetOrderByOrderIdReq.builder();
    builder.symbol("BTC-USDT").orderId("68808874c402b700073b85c4");
    GetOrderByOrderIdReq req = builder.build();
    GetOrderByOrderIdResp resp = api.getOrderByOrderId(req);
    Assertions.assertNotNull(resp.getId());
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getOpType());
    Assertions.assertNotNull(resp.getType());
    Assertions.assertNotNull(resp.getSide());
    Assertions.assertNotNull(resp.getPrice());
    Assertions.assertNotNull(resp.getSize());
    Assertions.assertNotNull(resp.getFunds());
    Assertions.assertNotNull(resp.getDealSize());
    Assertions.assertNotNull(resp.getDealFunds());
    Assertions.assertNotNull(resp.getFee());
    Assertions.assertNotNull(resp.getFeeCurrency());
    Assertions.assertNotNull(resp.getTimeInForce());
    Assertions.assertNotNull(resp.getPostOnly());
    Assertions.assertNotNull(resp.getHidden());
    Assertions.assertNotNull(resp.getIceberg());
    Assertions.assertNotNull(resp.getVisibleSize());
    Assertions.assertNotNull(resp.getCancelAfter());
    Assertions.assertNotNull(resp.getChannel());
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getRemark());
    Assertions.assertNotNull(resp.getCancelExist());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getLastUpdatedAt());
    Assertions.assertNotNull(resp.getTradeType());
    Assertions.assertNotNull(resp.getInOrderBook());
    Assertions.assertNotNull(resp.getCancelledSize());
    Assertions.assertNotNull(resp.getCancelledFunds());
    Assertions.assertNotNull(resp.getRemainSize());
    Assertions.assertNotNull(resp.getRemainFunds());
    Assertions.assertNotNull(resp.getTax());
    Assertions.assertNotNull(resp.getActive());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOrderByClientOid Get Order By ClientOid /api/v1/hf/orders/client-order/{clientOid} */
  @Test
  public void testGetOrderByClientOid() throws Exception {
    GetOrderByClientOidReq.GetOrderByClientOidReqBuilder builder = GetOrderByClientOidReq.builder();
    builder.symbol("BTC-USDT").clientOid("e66d0636-69f1-4d16-bcf6-e0436f9461e6");
    GetOrderByClientOidReq req = builder.build();
    GetOrderByClientOidResp resp = api.getOrderByClientOid(req);
    Assertions.assertNotNull(resp.getId());
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getOpType());
    Assertions.assertNotNull(resp.getType());
    Assertions.assertNotNull(resp.getSide());
    Assertions.assertNotNull(resp.getPrice());
    Assertions.assertNotNull(resp.getSize());
    Assertions.assertNotNull(resp.getFunds());
    Assertions.assertNotNull(resp.getDealSize());
    Assertions.assertNotNull(resp.getDealFunds());
    Assertions.assertNotNull(resp.getFee());
    Assertions.assertNotNull(resp.getFeeCurrency());
    Assertions.assertNotNull(resp.getTimeInForce());
    Assertions.assertNotNull(resp.getPostOnly());
    Assertions.assertNotNull(resp.getHidden());
    Assertions.assertNotNull(resp.getIceberg());
    Assertions.assertNotNull(resp.getVisibleSize());
    Assertions.assertNotNull(resp.getCancelAfter());
    Assertions.assertNotNull(resp.getChannel());
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getRemark());
    Assertions.assertNotNull(resp.getCancelExist());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getLastUpdatedAt());
    Assertions.assertNotNull(resp.getTradeType());
    Assertions.assertNotNull(resp.getInOrderBook());
    Assertions.assertNotNull(resp.getCancelledSize());
    Assertions.assertNotNull(resp.getCancelledFunds());
    Assertions.assertNotNull(resp.getRemainSize());
    Assertions.assertNotNull(resp.getRemainFunds());
    Assertions.assertNotNull(resp.getTax());
    Assertions.assertNotNull(resp.getActive());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSymbolsWithOpenOrder Get Symbols With Open Order /api/v1/hf/orders/active/symbols */
  @Test
  public void testGetSymbolsWithOpenOrder() throws Exception {
    GetSymbolsWithOpenOrderResp resp = api.getSymbolsWithOpenOrder();
    resp.getSymbols().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOpenOrders Get Open Orders /api/v1/hf/orders/active */
  @Test
  public void testGetOpenOrders() throws Exception {
    GetOpenOrdersReq.GetOpenOrdersReqBuilder builder = GetOpenOrdersReq.builder();
    builder.symbol("BTC-USDT");
    GetOpenOrdersReq req = builder.build();
    GetOpenOrdersResp resp = api.getOpenOrders(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getOpType());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getFunds());
              Assertions.assertNotNull(item.getDealSize());
              Assertions.assertNotNull(item.getDealFunds());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getTimeInForce());
              Assertions.assertNotNull(item.getPostOnly());
              Assertions.assertNotNull(item.getHidden());
              Assertions.assertNotNull(item.getIceberg());
              Assertions.assertNotNull(item.getVisibleSize());
              Assertions.assertNotNull(item.getCancelAfter());
              Assertions.assertNotNull(item.getChannel());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getRemark());
              Assertions.assertNotNull(item.getCancelExist());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getLastUpdatedAt());
              Assertions.assertNotNull(item.getTradeType());
              Assertions.assertNotNull(item.getInOrderBook());
              Assertions.assertNotNull(item.getCancelledSize());
              Assertions.assertNotNull(item.getCancelledFunds());
              Assertions.assertNotNull(item.getRemainSize());
              Assertions.assertNotNull(item.getRemainFunds());
              Assertions.assertNotNull(item.getTax());
              Assertions.assertNotNull(item.getActive());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOpenOrdersByPage Get Open Orders By Page /api/v1/hf/orders/active/page */
  @Test
  public void testGetOpenOrdersByPage() throws Exception {
    GetOpenOrdersByPageReq.GetOpenOrdersByPageReqBuilder builder = GetOpenOrdersByPageReq.builder();
    builder.symbol("BTC-USDT").pageNum(1).pageSize(10);
    GetOpenOrdersByPageReq req = builder.build();
    GetOpenOrdersByPageResp resp = api.getOpenOrdersByPage(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getOpType());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getFunds());
              Assertions.assertNotNull(item.getDealSize());
              Assertions.assertNotNull(item.getDealFunds());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getTimeInForce());
              Assertions.assertNotNull(item.getPostOnly());
              Assertions.assertNotNull(item.getHidden());
              Assertions.assertNotNull(item.getIceberg());
              Assertions.assertNotNull(item.getVisibleSize());
              Assertions.assertNotNull(item.getCancelAfter());
              Assertions.assertNotNull(item.getChannel());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getRemark());
              Assertions.assertNotNull(item.getCancelExist());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getLastUpdatedAt());
              Assertions.assertNotNull(item.getTradeType());
              Assertions.assertNotNull(item.getInOrderBook());
              Assertions.assertNotNull(item.getCancelledSize());
              Assertions.assertNotNull(item.getCancelledFunds());
              Assertions.assertNotNull(item.getRemainSize());
              Assertions.assertNotNull(item.getRemainFunds());
              Assertions.assertNotNull(item.getTax());
              Assertions.assertNotNull(item.getActive());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getClosedOrders Get Closed Orders /api/v1/hf/orders/done */
  @Test
  public void testGetClosedOrders() throws Exception {
    GetClosedOrdersReq.GetClosedOrdersReqBuilder builder = GetClosedOrdersReq.builder();
    builder.symbol("BTC-USDT").side(GetClosedOrdersReq.SideEnum.BUY);
    GetClosedOrdersReq req = builder.build();
    GetClosedOrdersResp resp = api.getClosedOrders(req);
    Assertions.assertNotNull(resp.getLastId());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getOpType());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getFunds());
              Assertions.assertNotNull(item.getDealSize());
              Assertions.assertNotNull(item.getDealFunds());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getTimeInForce());
              Assertions.assertNotNull(item.getPostOnly());
              Assertions.assertNotNull(item.getHidden());
              Assertions.assertNotNull(item.getIceberg());
              Assertions.assertNotNull(item.getVisibleSize());
              Assertions.assertNotNull(item.getCancelAfter());
              Assertions.assertNotNull(item.getChannel());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getRemark());
              Assertions.assertNotNull(item.getCancelExist());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getLastUpdatedAt());
              Assertions.assertNotNull(item.getTradeType());
              Assertions.assertNotNull(item.getInOrderBook());
              Assertions.assertNotNull(item.getCancelledSize());
              Assertions.assertNotNull(item.getCancelledFunds());
              Assertions.assertNotNull(item.getRemainSize());
              Assertions.assertNotNull(item.getRemainFunds());
              Assertions.assertNotNull(item.getTax());
              Assertions.assertNotNull(item.getActive());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getTradeHistory Get Trade History /api/v1/hf/fills */
  @Test
  public void testGetTradeHistory() throws Exception {
    GetTradeHistoryReq.GetTradeHistoryReqBuilder builder = GetTradeHistoryReq.builder();
    builder.symbol("DOGE-USDT");
    GetTradeHistoryReq req = builder.build();
    GetTradeHistoryResp resp = api.getTradeHistory(req);
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getTradeId());
              Assertions.assertNotNull(item.getOrderId());
              Assertions.assertNotNull(item.getCounterOrderId());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getLiquidity());
              Assertions.assertNotNull(item.getForceTaker());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getFunds());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getFeeRate());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getTradeType());
              Assertions.assertNotNull(item.getTaxRate());
              Assertions.assertNotNull(item.getTax());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getCreatedAt());
            });

    Assertions.assertNotNull(resp.getLastId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getDCP Get DCP /api/v1/hf/orders/dead-cancel-all/query */
  @Test
  public void testGetDCP() throws Exception {
    GetDCPResp resp = api.getDCP();
    Assertions.assertNotNull(resp.getTimeout());
    Assertions.assertNotNull(resp.getSymbols());
    Assertions.assertNotNull(resp.getCurrentTime());
    Assertions.assertNotNull(resp.getTriggerTime());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** setDCP Set DCP /api/v1/hf/orders/dead-cancel-all */
  @Test
  public void testSetDCP() throws Exception {
    SetDCPReq.SetDCPReqBuilder builder = SetDCPReq.builder();
    builder.timeout(5).symbols("BTC-USDT");
    SetDCPReq req = builder.build();
    SetDCPResp resp = api.setDCP(req);
    Assertions.assertNotNull(resp.getCurrentTime());
    Assertions.assertNotNull(resp.getTriggerTime());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addStopOrder Add Stop Order /api/v1/stop-order */
  @Test
  public void testAddStopOrder() throws Exception {
    AddStopOrderReq.AddStopOrderReqBuilder builder = AddStopOrderReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddStopOrderReq.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(AddStopOrderReq.TypeEnum.LIMIT)
        .remark("sdk_test")
        .price("8100")
        .size("0.001")
        .stopPrice("8000");
    AddStopOrderReq req = builder.build();
    AddStopOrderResp resp = api.addStopOrder(req);
    Assertions.assertNotNull(resp.getOrderId());
    log.info("resp: {} {}", mapper.writeValueAsString(resp), req.getClientOid());
  }

  /**
   * cancelStopOrderByClientOid Cancel Stop Order By ClientOid
   * /api/v1/stop-order/cancelOrderByClientOid
   */
  @Test
  public void testCancelStopOrderByClientOid() throws Exception {
    CancelStopOrderByClientOidReq.CancelStopOrderByClientOidReqBuilder builder =
        CancelStopOrderByClientOidReq.builder();
    builder.symbol("BTC-USDT").clientOid("001869b9-bc72-4196-828f-22034e8efa06");
    CancelStopOrderByClientOidReq req = builder.build();
    CancelStopOrderByClientOidResp resp = api.cancelStopOrderByClientOid(req);
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getCancelledOrderId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelStopOrderByOrderId Cancel Stop Order By OrderId /api/v1/stop-order/{orderId} */
  @Test
  public void testCancelStopOrderByOrderId() throws Exception {
    CancelStopOrderByOrderIdReq.CancelStopOrderByOrderIdReqBuilder builder =
        CancelStopOrderByOrderIdReq.builder();
    builder.orderId("vs93gq40hp29bcik003t1tvg");
    CancelStopOrderByOrderIdReq req = builder.build();
    CancelStopOrderByOrderIdResp resp = api.cancelStopOrderByOrderId(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** batchCancelStopOrder Batch Cancel Stop Orders /api/v1/stop-order/cancel */
  @Test
  public void testBatchCancelStopOrder() throws Exception {
    BatchCancelStopOrderReq.BatchCancelStopOrderReqBuilder builder =
        BatchCancelStopOrderReq.builder();
    builder.symbol("BTC-USDT").tradeType("TRADE").orderIds("vs93gq40hpucmad4003rdr00");
    BatchCancelStopOrderReq req = builder.build();
    BatchCancelStopOrderResp resp = api.batchCancelStopOrder(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getStopOrdersList Get Stop Orders List /api/v1/stop-order */
  @Test
  public void testGetStopOrdersList() throws Exception {
    GetStopOrdersListReq.GetStopOrdersListReqBuilder builder = GetStopOrdersListReq.builder();
    builder.symbol("BTC-USDT");
    GetStopOrdersListReq req = builder.build();
    GetStopOrdersListResp resp = api.getStopOrdersList(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getUserId());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getTimeInForce());
              Assertions.assertNotNull(item.getCancelAfter());
              Assertions.assertNotNull(item.getPostOnly());
              Assertions.assertNotNull(item.getHidden());
              Assertions.assertNotNull(item.getIceberg());
              Assertions.assertNotNull(item.getChannel());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getRemark());
              Assertions.assertNotNull(item.getOrderTime());
              Assertions.assertNotNull(item.getDomainId());
              Assertions.assertNotNull(item.getTradeSource());
              Assertions.assertNotNull(item.getTradeType());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getTakerFeeRate());
              Assertions.assertNotNull(item.getMakerFeeRate());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getStopPrice());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getStopOrderByOrderId Get Stop Order By OrderId /api/v1/stop-order/{orderId} */
  @Test
  public void testGetStopOrderByOrderId() throws Exception {
    GetStopOrderByOrderIdReq.GetStopOrderByOrderIdReqBuilder builder =
        GetStopOrderByOrderIdReq.builder();
    builder.orderId("vs93gq40hnslqnu6003sc6g7");
    GetStopOrderByOrderIdReq req = builder.build();
    GetStopOrderByOrderIdResp resp = api.getStopOrderByOrderId(req);
    Assertions.assertNotNull(resp.getId());
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getUserId());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getType());
    Assertions.assertNotNull(resp.getSide());
    Assertions.assertNotNull(resp.getPrice());
    Assertions.assertNotNull(resp.getSize());
    Assertions.assertNotNull(resp.getTimeInForce());
    Assertions.assertNotNull(resp.getCancelAfter());
    Assertions.assertNotNull(resp.getPostOnly());
    Assertions.assertNotNull(resp.getHidden());
    Assertions.assertNotNull(resp.getIceberg());
    Assertions.assertNotNull(resp.getChannel());
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getRemark());
    Assertions.assertNotNull(resp.getDomainId());
    Assertions.assertNotNull(resp.getTradeSource());
    Assertions.assertNotNull(resp.getTradeType());
    Assertions.assertNotNull(resp.getFeeCurrency());
    Assertions.assertNotNull(resp.getTakerFeeRate());
    Assertions.assertNotNull(resp.getMakerFeeRate());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getStop());
    Assertions.assertNotNull(resp.getStopPrice());
    Assertions.assertNotNull(resp.getOrderTime());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * getStopOrderByClientOid Get Stop Order By ClientOid /api/v1/stop-order/queryOrderByClientOid
   */
  @Test
  public void testGetStopOrderByClientOid() throws Exception {
    GetStopOrderByClientOidReq.GetStopOrderByClientOidReqBuilder builder =
        GetStopOrderByClientOidReq.builder();
    builder.clientOid("14cb6ad8-7304-4e88-ae9d-646daccca426").symbol("BTC-USDT");
    GetStopOrderByClientOidReq req = builder.build();
    GetStopOrderByClientOidResp resp = api.getStopOrderByClientOid(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getUserId());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getTimeInForce());
              Assertions.assertNotNull(item.getCancelAfter());
              Assertions.assertNotNull(item.getPostOnly());
              Assertions.assertNotNull(item.getHidden());
              Assertions.assertNotNull(item.getIceberg());
              Assertions.assertNotNull(item.getChannel());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getRemark());
              Assertions.assertNotNull(item.getDomainId());
              Assertions.assertNotNull(item.getTradeSource());
              Assertions.assertNotNull(item.getTradeType());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getTakerFeeRate());
              Assertions.assertNotNull(item.getMakerFeeRate());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getStopPrice());
              Assertions.assertNotNull(item.getOrderTime());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addOcoOrder Add OCO Order /api/v3/oco/order */
  @Test
  public void testAddOcoOrder() throws Exception {
    AddOcoOrderReq.AddOcoOrderReqBuilder builder = AddOcoOrderReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOcoOrderReq.SideEnum.BUY)
        .symbol("BTC-USDT")
        .price("94000")
        .size("0.001")
        .stopPrice("130000")
        .limitPrice("96000")
        .tradeType(AddOcoOrderReq.TradeTypeEnum.TRADE);
    AddOcoOrderReq req = builder.build();
    AddOcoOrderResp resp = api.addOcoOrder(req);
    Assertions.assertNotNull(resp.getOrderId());
    log.info("resp: {} {}", mapper.writeValueAsString(resp), req.getClientOid());
  }

  /** cancelOcoOrderByOrderId Cancel OCO Order By OrderId /api/v3/oco/order/{orderId} */
  @Test
  public void testCancelOcoOrderByOrderId() throws Exception {
    CancelOcoOrderByOrderIdReq.CancelOcoOrderByOrderIdReqBuilder builder =
        CancelOcoOrderByOrderIdReq.builder();
    builder.orderId("68809163cb29a40007b6ec43");
    CancelOcoOrderByOrderIdReq req = builder.build();
    CancelOcoOrderByOrderIdResp resp = api.cancelOcoOrderByOrderId(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * cancelOcoOrderByClientOid Cancel OCO Order By ClientOid /api/v3/oco/client-order/{clientOid}
   */
  @Test
  public void testCancelOcoOrderByClientOid() throws Exception {
    CancelOcoOrderByClientOidReq.CancelOcoOrderByClientOidReqBuilder builder =
        CancelOcoOrderByClientOidReq.builder();
    builder.clientOid("ca5d2e6b-98b3-4ae5-a540-2d277dba7213");
    CancelOcoOrderByClientOidReq req = builder.build();
    CancelOcoOrderByClientOidResp resp = api.cancelOcoOrderByClientOid(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** batchCancelOcoOrders Batch Cancel OCO Order /api/v3/oco/orders */
  @Test
  public void testBatchCancelOcoOrders() throws Exception {
    BatchCancelOcoOrdersReq.BatchCancelOcoOrdersReqBuilder builder =
        BatchCancelOcoOrdersReq.builder();
    builder.orderIds("688091bae09c5d0007025620").symbol("BTC-USDT");
    BatchCancelOcoOrdersReq req = builder.build();
    BatchCancelOcoOrdersResp resp = api.batchCancelOcoOrders(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOcoOrderByOrderId Get OCO Order By OrderId /api/v3/oco/order/{orderId} */
  @Test
  public void testGetOcoOrderByOrderId() throws Exception {
    GetOcoOrderByOrderIdReq.GetOcoOrderByOrderIdReqBuilder builder =
        GetOcoOrderByOrderIdReq.builder();
    builder.orderId("688091bae09c5d0007025620");
    GetOcoOrderByOrderIdReq req = builder.build();
    GetOcoOrderByOrderIdResp resp = api.getOcoOrderByOrderId(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getOrderTime());
    Assertions.assertNotNull(resp.getStatus());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOcoOrderByClientOid Get OCO Order By ClientOid /api/v3/oco/client-order/{clientOid} */
  @Test
  public void testGetOcoOrderByClientOid() throws Exception {
    GetOcoOrderByClientOidReq.GetOcoOrderByClientOidReqBuilder builder =
        GetOcoOrderByClientOidReq.builder();
    builder.clientOid("036aa8cb-b437-4130-9218-1c25320258d2");
    GetOcoOrderByClientOidReq req = builder.build();
    GetOcoOrderByClientOidResp resp = api.getOcoOrderByClientOid(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getOrderTime());
    Assertions.assertNotNull(resp.getStatus());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * getOcoOrderDetailByOrderId Get OCO Order Detail By OrderId /api/v3/oco/order/details/{orderId}
   */
  @Test
  public void testGetOcoOrderDetailByOrderId() throws Exception {
    GetOcoOrderDetailByOrderIdReq.GetOcoOrderDetailByOrderIdReqBuilder builder =
        GetOcoOrderDetailByOrderIdReq.builder();
    builder.orderId("688091bae09c5d0007025620");
    GetOcoOrderDetailByOrderIdReq req = builder.build();
    GetOcoOrderDetailByOrderIdResp resp = api.getOcoOrderDetailByOrderId(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getOrderTime());
    Assertions.assertNotNull(resp.getStatus());
    resp.getOrders()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getStopPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getStatus());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOcoOrderList Get OCO Order List /api/v3/oco/orders */
  @Test
  public void testGetOcoOrderList() throws Exception {
    GetOcoOrderListReq.GetOcoOrderListReqBuilder builder = GetOcoOrderListReq.builder();
    builder.symbol("BTC-USDT");
    GetOcoOrderListReq req = builder.build();
    GetOcoOrderListResp resp = api.getOcoOrderList(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getOrderId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getClientOid());
              Assertions.assertNotNull(item.getOrderTime());
              Assertions.assertNotNull(item.getStatus());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addOrderOld Add Order - Old /api/v1/orders */
  @Test
  public void testAddOrderOld() throws Exception {
    AddOrderOldReq.AddOrderOldReqBuilder builder = AddOrderOldReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderOldReq.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(AddOrderOldReq.TypeEnum.LIMIT)
        .remark("test")
        .price("1")
        .size("2");
    AddOrderOldReq req = builder.build();
    AddOrderOldResp resp = api.addOrderOld(req);
    Assertions.assertNotNull(resp.getOrderId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addOrderTestOld Add Order Test - Old /api/v1/orders/test */
  @Test
  public void testAddOrderTestOld() throws Exception {
    AddOrderTestOldReq.AddOrderTestOldReqBuilder builder = AddOrderTestOldReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderTestOldReq.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(AddOrderTestOldReq.TypeEnum.LIMIT)
        .remark("test")
        .price("1")
        .size("2");
    AddOrderTestOldReq req = builder.build();
    AddOrderTestOldResp resp = api.addOrderTestOld(req);
    Assertions.assertNotNull(resp.getOrderId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** batchAddOrdersOld Batch Add Orders - Old /api/v1/orders/multi */
  @Test
  public void testBatchAddOrdersOld() throws Exception {
    BatchAddOrdersOldReq.BatchAddOrdersOldReqBuilder builder = BatchAddOrdersOldReq.builder();

    BatchAddOrdersOldOrderList.BatchAddOrdersOldOrderListBuilder builder1 =
        BatchAddOrdersOldOrderList.builder();
    builder1
        .clientOid(UUID.randomUUID().toString())
        .side(BatchAddOrdersOldOrderList.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(BatchAddOrdersOldOrderList.TypeEnum.LIMIT)
        .remark("test")
        .price("1")
        .size("2");

    BatchAddOrdersOldOrderList.BatchAddOrdersOldOrderListBuilder builder2 =
        BatchAddOrdersOldOrderList.builder();
    builder2
        .clientOid(UUID.randomUUID().toString())
        .side(BatchAddOrdersOldOrderList.SideEnum.BUY)
        .symbol("BTC-USDT")
        .type(BatchAddOrdersOldOrderList.TypeEnum.LIMIT)
        .remark("test")
        .price("1")
        .size("2");

    builder.orderList(Arrays.asList(builder1.build(), builder2.build())).symbol("BTC-USDT");
    BatchAddOrdersOldReq req = builder.build();
    BatchAddOrdersOldResp resp = api.batchAddOrdersOld(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getStp());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getTimeInForce());
              Assertions.assertNotNull(item.getCancelAfter());
              Assertions.assertNotNull(item.getPostOnly());
              Assertions.assertNotNull(item.getHidden());
              Assertions.assertNotNull(item.getIceberge());
              Assertions.assertNotNull(item.getIceberg());
              Assertions.assertNotNull(item.getChannel());
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getClientOid());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelOrderByOrderIdOld Cancel Order By OrderId - Old /api/v1/orders/{orderId} */
  @Test
  public void testCancelOrderByOrderIdOld() throws Exception {
    CancelOrderByOrderIdOldReq.CancelOrderByOrderIdOldReqBuilder builder =
        CancelOrderByOrderIdOldReq.builder();
    builder.orderId("688092b513900400085ba585");
    CancelOrderByOrderIdOldReq req = builder.build();
    CancelOrderByOrderIdOldResp resp = api.cancelOrderByOrderIdOld(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * cancelOrderByClientOidOld Cancel Order By ClientOid - Old
   * /api/v1/order/client-order/{clientOid}
   */
  @Test
  public void testCancelOrderByClientOidOld() throws Exception {
    CancelOrderByClientOidOldReq.CancelOrderByClientOidOldReqBuilder builder =
        CancelOrderByClientOidOldReq.builder();
    builder.clientOid("67974f4a-44e6-4374-beca-e45dc7089d97");
    CancelOrderByClientOidOldReq req = builder.build();
    CancelOrderByClientOidOldResp resp = api.cancelOrderByClientOidOld(req);
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getCancelledOrderId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** batchCancelOrderOld Batch Cancel Order - Old /api/v1/orders */
  @Test
  public void testBatchCancelOrderOld() throws Exception {
    BatchCancelOrderOldReq.BatchCancelOrderOldReqBuilder builder = BatchCancelOrderOldReq.builder();
    builder.symbol("BTC-USDT").tradeType(BatchCancelOrderOldReq.TradeTypeEnum.TRADE);
    BatchCancelOrderOldReq req = builder.build();
    BatchCancelOrderOldResp resp = api.batchCancelOrderOld(req);
    resp.getCancelledOrderIds().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOrdersListOld Get Orders List - Old /api/v1/orders */
  @Test
  public void testGetOrdersListOld() throws Exception {
    GetOrdersListOldReq.GetOrdersListOldReqBuilder builder = GetOrdersListOldReq.builder();
    builder.symbol("BTC-USDT");
    GetOrdersListOldReq req = builder.build();
    GetOrdersListOldResp resp = api.getOrdersListOld(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getOpType());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getDealFunds());
              Assertions.assertNotNull(item.getDealSize());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getStopTriggered());
              Assertions.assertNotNull(item.getStopPrice());
              Assertions.assertNotNull(item.getTimeInForce());
              Assertions.assertNotNull(item.getPostOnly());
              Assertions.assertNotNull(item.getHidden());
              Assertions.assertNotNull(item.getIceberg());
              Assertions.assertNotNull(item.getCancelAfter());
              Assertions.assertNotNull(item.getChannel());
              Assertions.assertNotNull(item.getIsActive());
              Assertions.assertNotNull(item.getCancelExist());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getTradeType());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getRecentOrdersListOld Get Recent Orders List - Old /api/v1/limit/orders */
  @Test
  public void testGetRecentOrdersListOld() throws Exception {
    GetRecentOrdersListOldResp resp = api.getRecentOrdersListOld();
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getOpType());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getDealFunds());
              Assertions.assertNotNull(item.getDealSize());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getStopTriggered());
              Assertions.assertNotNull(item.getStopPrice());
              Assertions.assertNotNull(item.getTimeInForce());
              Assertions.assertNotNull(item.getPostOnly());
              Assertions.assertNotNull(item.getHidden());
              Assertions.assertNotNull(item.getIceberg());
              Assertions.assertNotNull(item.getCancelAfter());
              Assertions.assertNotNull(item.getChannel());
              Assertions.assertNotNull(item.getIsActive());
              Assertions.assertNotNull(item.getCancelExist());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getTradeType());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOrderByOrderIdOld Get Order By OrderId - Old /api/v1/orders/{orderId} */
  @Test
  public void testGetOrderByOrderIdOld() throws Exception {
    GetOrderByOrderIdOldReq.GetOrderByOrderIdOldReqBuilder builder =
        GetOrderByOrderIdOldReq.builder();
    builder.orderId("688092dd988d1a0007415ab7");
    GetOrderByOrderIdOldReq req = builder.build();
    GetOrderByOrderIdOldResp resp = api.getOrderByOrderIdOld(req);
    Assertions.assertNotNull(resp.getId());
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getOpType());
    Assertions.assertNotNull(resp.getType());
    Assertions.assertNotNull(resp.getSide());
    Assertions.assertNotNull(resp.getPrice());
    Assertions.assertNotNull(resp.getSize());
    Assertions.assertNotNull(resp.getFunds());
    Assertions.assertNotNull(resp.getDealFunds());
    Assertions.assertNotNull(resp.getDealSize());
    Assertions.assertNotNull(resp.getFee());
    Assertions.assertNotNull(resp.getFeeCurrency());
    Assertions.assertNotNull(resp.getStp());
    Assertions.assertNotNull(resp.getStop());
    Assertions.assertNotNull(resp.getStopTriggered());
    Assertions.assertNotNull(resp.getStopPrice());
    Assertions.assertNotNull(resp.getTimeInForce());
    Assertions.assertNotNull(resp.getPostOnly());
    Assertions.assertNotNull(resp.getHidden());
    Assertions.assertNotNull(resp.getIceberg());
    Assertions.assertNotNull(resp.getVisibleSize());
    Assertions.assertNotNull(resp.getCancelAfter());
    Assertions.assertNotNull(resp.getChannel());
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getRemark());
    Assertions.assertNotNull(resp.getIsActive());
    Assertions.assertNotNull(resp.getCancelExist());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getTradeType());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOrderByClientOidOld Get Order By ClientOid - Old /api/v1/order/client-order/{clientOid} */
  @Test
  public void testGetOrderByClientOidOld() throws Exception {
    GetOrderByClientOidOldReq.GetOrderByClientOidOldReqBuilder builder =
        GetOrderByClientOidOldReq.builder();
    builder.clientOid("391b2c3a-ebcc-4503-8fec-975219046f09");
    GetOrderByClientOidOldReq req = builder.build();
    GetOrderByClientOidOldResp resp = api.getOrderByClientOidOld(req);
    Assertions.assertNotNull(resp.getId());
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getOpType());
    Assertions.assertNotNull(resp.getType());
    Assertions.assertNotNull(resp.getSide());
    Assertions.assertNotNull(resp.getPrice());
    Assertions.assertNotNull(resp.getSize());
    Assertions.assertNotNull(resp.getFunds());
    Assertions.assertNotNull(resp.getDealFunds());
    Assertions.assertNotNull(resp.getDealSize());
    Assertions.assertNotNull(resp.getFee());
    Assertions.assertNotNull(resp.getFeeCurrency());
    Assertions.assertNotNull(resp.getStp());
    Assertions.assertNotNull(resp.getStop());
    Assertions.assertNotNull(resp.getStopTriggered());
    Assertions.assertNotNull(resp.getStopPrice());
    Assertions.assertNotNull(resp.getTimeInForce());
    Assertions.assertNotNull(resp.getPostOnly());
    Assertions.assertNotNull(resp.getHidden());
    Assertions.assertNotNull(resp.getIceberg());
    Assertions.assertNotNull(resp.getVisibleSize());
    Assertions.assertNotNull(resp.getCancelAfter());
    Assertions.assertNotNull(resp.getChannel());
    Assertions.assertNotNull(resp.getClientOid());
    Assertions.assertNotNull(resp.getRemark());
    Assertions.assertNotNull(resp.getIsActive());
    Assertions.assertNotNull(resp.getCancelExist());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getTradeType());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getTradeHistoryOld Get Trade History - Old /api/v1/fills */
  @Test
  public void testGetTradeHistoryOld() throws Exception {
    GetTradeHistoryOldReq.GetTradeHistoryOldReqBuilder builder = GetTradeHistoryOldReq.builder();
    builder.symbol("DOGE-USDT");
    GetTradeHistoryOldReq req = builder.build();
    GetTradeHistoryOldResp resp = api.getTradeHistoryOld(req);
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
              Assertions.assertNotNull(item.getCounterOrderId());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getLiquidity());
              Assertions.assertNotNull(item.getForceTaker());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getFunds());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getFeeRate());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getTradeType());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getCreatedAt());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getRecentTradeHistoryOld Get Recent Trade History - Old /api/v1/limit/fills */
  @Test
  public void testGetRecentTradeHistoryOld() throws Exception {
    GetRecentTradeHistoryOldResp resp = api.getRecentTradeHistoryOld();
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getTradeId());
              Assertions.assertNotNull(item.getOrderId());
              Assertions.assertNotNull(item.getCounterOrderId());
              Assertions.assertNotNull(item.getSide());
              Assertions.assertNotNull(item.getLiquidity());
              Assertions.assertNotNull(item.getForceTaker());
              Assertions.assertNotNull(item.getPrice());
              Assertions.assertNotNull(item.getSize());
              Assertions.assertNotNull(item.getFunds());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getFeeRate());
              Assertions.assertNotNull(item.getFeeCurrency());
              Assertions.assertNotNull(item.getStop());
              Assertions.assertNotNull(item.getTradeType());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getCreatedAt());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
