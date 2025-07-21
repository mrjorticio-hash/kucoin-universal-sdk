package com.kucoin.universal.sdk.e2e.rest.spot;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.spot.order.*;
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

  /** cancelOrderByClientOid Cancel Order By ClientOid /api/v1/hf/orders/client-order/{clientOid} */
  @Test
  public void testCancelOrderByClientOid() throws Exception {
    CancelOrderByClientOidReq.CancelOrderByClientOidReqBuilder builder =
        CancelOrderByClientOidReq.builder();
    builder.clientOid("120617f6-20c0-4317-9ba4-b1b07c4cdf49").symbol("BTC-USDT");
    CancelOrderByClientOidReq req = builder.build();
    CancelOrderByClientOidResp resp = api.cancelOrderByClientOid(req);
    Assertions.assertNotNull(resp.getClientOid());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getOrderByOrderId Get Order By OrderId /api/v1/hf/orders/{orderId} */
  @Test
  public void testGetOrderByOrderId() throws Exception {
    GetOrderByOrderIdReq.GetOrderByOrderIdReqBuilder builder = GetOrderByOrderIdReq.builder();
    builder.symbol("BTC-USDT").orderId("6874ca31c402b70007f7f1de");
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
}
