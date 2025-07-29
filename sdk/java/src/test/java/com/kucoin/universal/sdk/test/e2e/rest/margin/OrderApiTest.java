package com.kucoin.universal.sdk.test.e2e.rest.margin;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.margin.order.*;
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
    api = kucoinClient.getRestService().getMarginService().getOrderApi();
  }

  /** addOrder Add Order /api/v3/hf/margin/order */
  @Test
  public void testAddOrder() throws Exception {
    AddOrderReq.AddOrderReqBuilder builder = AddOrderReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderReq.SideEnum.BUY)
        .symbol("DOGE-USDT")
        .type(AddOrderReq.TypeEnum.LIMIT)
        .price("0.01")
        .size("100")
        .isIsolated(false)
        .autoBorrow(true)
        .autoRepay(true);
    AddOrderReq req = builder.build();
    AddOrderResp resp = api.addOrder(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getLoanApplyId());
    Assertions.assertNotNull(resp.getBorrowSize());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addOrderTest Add Order Test /api/v3/hf/margin/order/test */
  @Test
  public void testAddOrderTest() throws Exception {
    AddOrderTestReq.AddOrderTestReqBuilder builder = AddOrderTestReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderTestReq.SideEnum.BUY)
        .symbol("DOGE-USDT")
        .type(AddOrderTestReq.TypeEnum.LIMIT)
        .price("0.01")
        .size("100")
        .isIsolated(false)
        .autoBorrow(true)
        .autoRepay(true);

    AddOrderTestReq req = builder.build();
    AddOrderTestResp resp = api.addOrderTest(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelOrderByOrderId Cancel Order By OrderId /api/v3/hf/margin/orders/{orderId} */
  @Test
  public void testCancelOrderByOrderId() throws Exception {
    CancelOrderByOrderIdReq.CancelOrderByOrderIdReqBuilder builder =
        CancelOrderByOrderIdReq.builder();
    builder.symbol("DOGE-USDT").orderId("68807b1a275af50007592aff");
    CancelOrderByOrderIdReq req = builder.build();
    CancelOrderByOrderIdResp resp = api.cancelOrderByOrderId(req);
    Assertions.assertNotNull(resp.getOrderId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * cancelOrderByClientOid Cancel Order By ClientOid
   * /api/v3/hf/margin/orders/client-order/{clientOid}
   */
  @Test
  public void testCancelOrderByClientOid() throws Exception {
    CancelOrderByClientOidReq.CancelOrderByClientOidReqBuilder builder =
        CancelOrderByClientOidReq.builder();
    builder.symbol("DOGE-USDT").clientOid("1372c988-5b32-4fdf-80c0-d953de11e900");
    CancelOrderByClientOidReq req = builder.build();
    CancelOrderByClientOidResp resp = api.cancelOrderByClientOid(req);
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelAllOrdersBySymbol Cancel All Orders By Symbol /api/v3/hf/margin/orders */
  @Test
  public void testCancelAllOrdersBySymbol() throws Exception {
    CancelAllOrdersBySymbolReq.CancelAllOrdersBySymbolReqBuilder builder =
        CancelAllOrdersBySymbolReq.builder();
    builder.symbol("DOGE-USDT").tradeType(CancelAllOrdersBySymbolReq.TradeTypeEnum.MARGIN_TRADE);
    CancelAllOrdersBySymbolReq req = builder.build();
    CancelAllOrdersBySymbolResp resp = api.cancelAllOrdersBySymbol(req);
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSymbolsWithOpenOrder Get Symbols With Open Order /api/v3/hf/margin/order/active/symbols */
  @Test
  public void testGetSymbolsWithOpenOrder() throws Exception {
    GetSymbolsWithOpenOrderReq.GetSymbolsWithOpenOrderReqBuilder builder =
        GetSymbolsWithOpenOrderReq.builder();
    builder.tradeType(GetSymbolsWithOpenOrderReq.TradeTypeEnum.MARGIN_TRADE);
    GetSymbolsWithOpenOrderReq req = builder.build();
    GetSymbolsWithOpenOrderResp resp = api.getSymbolsWithOpenOrder(req);
    Assertions.assertNotNull(resp.getSymbolSize());
    resp.getSymbols().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOpenOrders Get Open Orders /api/v3/hf/margin/orders/active */
  @Test
  public void testGetOpenOrders() throws Exception {
    GetOpenOrdersReq.GetOpenOrdersReqBuilder builder = GetOpenOrdersReq.builder();
    builder.symbol("DOGE-USDT").tradeType(GetOpenOrdersReq.TradeTypeEnum.MARGIN_TRADE);
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
              Assertions.assertNotNull(item.getStopTriggered());
              Assertions.assertNotNull(item.getStopPrice());
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

  /** getClosedOrders Get Closed Orders /api/v3/hf/margin/orders/done */
  @Test
  public void testGetClosedOrders() throws Exception {
    GetClosedOrdersReq.GetClosedOrdersReqBuilder builder = GetClosedOrdersReq.builder();
    builder
        .symbol("DOGE-USDT")
        .tradeType(GetClosedOrdersReq.TradeTypeEnum.MARGIN_TRADE)
        .side(GetClosedOrdersReq.SideEnum.BUY);
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
              Assertions.assertNotNull(item.getStopTriggered());
              Assertions.assertNotNull(item.getStopPrice());
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

  /** getTradeHistory Get Trade History /api/v3/hf/margin/fills */
  @Test
  public void testGetTradeHistory() throws Exception {
    GetTradeHistoryReq.GetTradeHistoryReqBuilder builder = GetTradeHistoryReq.builder();
    builder.symbol("DOGE-USDT").tradeType(GetTradeHistoryReq.TradeTypeEnum.MARGIN_TRADE);
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
              Assertions.assertNotNull(item.getTax());
              Assertions.assertNotNull(item.getTaxRate());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getCreatedAt());
            });

    Assertions.assertNotNull(resp.getLastId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOrderByOrderId Get Order By OrderId /api/v3/hf/margin/orders/{orderId} */
  @Test
  public void testGetOrderByOrderId() throws Exception {
    GetOrderByOrderIdReq.GetOrderByOrderIdReqBuilder builder = GetOrderByOrderIdReq.builder();
    builder.symbol("DOGE-USDT").orderId("68807d17ad309c00072df99f");
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
    Assertions.assertNotNull(resp.getCancelExist());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getLastUpdatedAt());
    Assertions.assertNotNull(resp.getTradeType());
    Assertions.assertNotNull(resp.getInOrderBook());
    Assertions.assertNotNull(resp.getCancelledSize());
    Assertions.assertNotNull(resp.getCancelledFunds());
    Assertions.assertNotNull(resp.getRemainSize());
    Assertions.assertNotNull(resp.getTax());
    Assertions.assertNotNull(resp.getActive());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * getOrderByClientOid Get Order By ClientOid /api/v3/hf/margin/orders/client-order/{clientOid}
   */
  @Test
  public void testGetOrderByClientOid() throws Exception {
    GetOrderByClientOidReq.GetOrderByClientOidReqBuilder builder = GetOrderByClientOidReq.builder();
    builder.symbol("DOGE-USDT").clientOid("9e30b59f-bf21-4840-8288-df8cc6d2b6ee");
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
    Assertions.assertNotNull(resp.getCancelExist());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getLastUpdatedAt());
    Assertions.assertNotNull(resp.getTradeType());
    Assertions.assertNotNull(resp.getInOrderBook());
    Assertions.assertNotNull(resp.getCancelledSize());
    Assertions.assertNotNull(resp.getCancelledFunds());
    Assertions.assertNotNull(resp.getRemainSize());
    Assertions.assertNotNull(resp.getTax());
    Assertions.assertNotNull(resp.getActive());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addOrderV1 Add Order - V1 /api/v1/margin/order */
  @Test
  public void testAddOrderV1() throws Exception {
    AddOrderV1Req.AddOrderV1ReqBuilder builder = AddOrderV1Req.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderV1Req.SideEnum.BUY)
        .symbol("DOGE-USDT")
        .type(AddOrderV1Req.TypeEnum.LIMIT)
        .price("0.01")
        .size("100")
        .autoBorrow(true)
        .autoRepay(true)
        .marginModel(AddOrderV1Req.MarginModelEnum.CROSS);
    AddOrderV1Req req = builder.build();
    AddOrderV1Resp resp = api.addOrderV1(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getLoanApplyId());
    Assertions.assertNotNull(resp.getBorrowSize());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addOrderTestV1 Add Order Test - V1 /api/v1/margin/order/test */
  @Test
  public void testAddOrderTestV1() throws Exception {
    AddOrderTestV1Req.AddOrderTestV1ReqBuilder builder = AddOrderTestV1Req.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .side(AddOrderTestV1Req.SideEnum.BUY)
        .symbol("DOGE-USDT")
        .type(AddOrderTestV1Req.TypeEnum.LIMIT)
        .price("0.01")
        .size("100")
        .autoBorrow(true)
        .autoRepay(true)
        .marginModel(AddOrderTestV1Req.MarginModelEnum.CROSS);
    AddOrderTestV1Req req = builder.build();
    AddOrderTestV1Resp resp = api.addOrderTestV1(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getLoanApplyId());
    Assertions.assertNotNull(resp.getBorrowSize());
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
