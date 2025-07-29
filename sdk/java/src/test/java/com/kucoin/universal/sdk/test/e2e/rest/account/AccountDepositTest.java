package com.kucoin.universal.sdk.test.e2e.rest.account;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.account.deposit.*;
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
public class AccountDepositTest {

  private static DepositApi api;

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
    api = kucoinClient.getRestService().getAccountService().getDepositApi();
  }

  /** addDepositAddressV3 Add Deposit Address (V3) /api/v3/deposit-address/create */
  @Test
  public void testAddDepositAddressV3() throws Exception {
    AddDepositAddressV3Req.AddDepositAddressV3ReqBuilder builder = AddDepositAddressV3Req.builder();
    builder.currency("BTC").chain("kcc").to(AddDepositAddressV3Req.ToEnum.MAIN).amount("1.0");
    AddDepositAddressV3Req req = builder.build();
    AddDepositAddressV3Resp resp = api.addDepositAddressV3(req);
    Assertions.assertNotNull(resp.getAddress());
    Assertions.assertNotNull(resp.getChainId());
    Assertions.assertNotNull(resp.getTo());
    Assertions.assertNotNull(resp.getExpirationDate());
    Assertions.assertNotNull(resp.getCurrency());
    Assertions.assertNotNull(resp.getChainName());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getDepositAddressV3 Get Deposit Address (V3) /api/v3/deposit-addresses */
  @Test
  public void testGetDepositAddressV3() throws Exception {
    GetDepositAddressV3Req.GetDepositAddressV3ReqBuilder builder = GetDepositAddressV3Req.builder();
    builder.currency("ETH").amount("10").chain("eth");
    GetDepositAddressV3Req req = builder.build();
    GetDepositAddressV3Resp resp = api.getDepositAddressV3(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getAddress());
              Assertions.assertNotNull(item.getMemo());
              Assertions.assertNotNull(item.getChainId());
              Assertions.assertNotNull(item.getTo());
              Assertions.assertNotNull(item.getExpirationDate());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getContractAddress());
              Assertions.assertNotNull(item.getChainName());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getDepositHistory Get Deposit History /api/v1/deposits */
  @Test
  public void testGetDepositHistory() throws Exception {
    GetDepositHistoryReq.GetDepositHistoryReqBuilder builder = GetDepositHistoryReq.builder();
    builder.currency("USDT").startAt(1673496371000L).endAt(1705032371000L);
    GetDepositHistoryReq req = builder.build();
    GetDepositHistoryResp resp = api.getDepositHistory(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getChain());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getAddress());
              Assertions.assertNotNull(item.getMemo());
              Assertions.assertNotNull(item.getIsInner());
              Assertions.assertNotNull(item.getAmount());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getWalletTxId());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getUpdatedAt());
              Assertions.assertNotNull(item.getRemark());
              Assertions.assertNotNull(item.getArrears());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getDepositAddressV2 Get Deposit Addresses (V2) /api/v2/deposit-addresses */
  @Test
  public void testGetDepositAddressV2() throws Exception {
    GetDepositAddressV2Req.GetDepositAddressV2ReqBuilder builder = GetDepositAddressV2Req.builder();
    builder.currency("USDT").chain("SOL");
    GetDepositAddressV2Req req = builder.build();
    GetDepositAddressV2Resp resp = api.getDepositAddressV2(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getAddress());
              Assertions.assertNotNull(item.getMemo());
              Assertions.assertNotNull(item.getChain());
              Assertions.assertNotNull(item.getChainId());
              Assertions.assertNotNull(item.getTo());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getContractAddress());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getDepositAddressV1 Get Deposit Addresses - V1 /api/v1/deposit-addresses */
  @Test
  public void testGetDepositAddressV1() throws Exception {
    GetDepositAddressV1Req.GetDepositAddressV1ReqBuilder builder = GetDepositAddressV1Req.builder();
    builder.currency("USDT").chain("eth");
    GetDepositAddressV1Req req = builder.build();
    GetDepositAddressV1Resp resp = api.getDepositAddressV1(req);
    Assertions.assertNotNull(resp.getAddress());
    Assertions.assertNotNull(resp.getMemo());
    Assertions.assertNotNull(resp.getChain());
    Assertions.assertNotNull(resp.getChainId());
    Assertions.assertNotNull(resp.getTo());
    Assertions.assertNotNull(resp.getCurrency());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getDepositHistoryOld Get Deposit History - Old /api/v1/hist-deposits */
  @Test
  public void testGetDepositHistoryOld() throws Exception {
    GetDepositHistoryOldReq.GetDepositHistoryOldReqBuilder builder =
        GetDepositHistoryOldReq.builder();
    builder.startAt(1714492800000L).endAt(1732982400000L);
    GetDepositHistoryOldReq req = builder.build();
    GetDepositHistoryOldResp resp = api.getDepositHistoryOld(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getCreateAt());
              Assertions.assertNotNull(item.getAmount());
              Assertions.assertNotNull(item.getWalletTxId());
              Assertions.assertNotNull(item.getIsInner());
              Assertions.assertNotNull(item.getStatus());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addDepositAddressV1 Add Deposit Address - V1 /api/v1/deposit-addresses */
  @Test
  public void testAddDepositAddressV1() throws Exception {
    AddDepositAddressV1Req.AddDepositAddressV1ReqBuilder builder = AddDepositAddressV1Req.builder();
    builder.currency("ETH").chain("base").to(AddDepositAddressV1Req.ToEnum.TRADE);
    AddDepositAddressV1Req req = builder.build();
    AddDepositAddressV1Resp resp = api.addDepositAddressV1(req);
    Assertions.assertNotNull(resp.getAddress());
    Assertions.assertNotNull(resp.getChain());
    Assertions.assertNotNull(resp.getChainId());
    Assertions.assertNotNull(resp.getTo());
    Assertions.assertNotNull(resp.getCurrency());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
