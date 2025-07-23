package com.kucoin.universal.sdk.test.e2e.rest.account;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.account.subaccount.*;
import com.kucoin.universal.sdk.generate.account.withdrawal.*;
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
public class AccountWithdrawTest {

  private static WithdrawalApi api;

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
    api = kucoinClient.getRestService().getAccountService().getWithdrawalApi();
  }

  /** getWithdrawalQuotas Get Withdrawal Quotas /api/v1/withdrawals/quotas */
  @Test
  public void testGetWithdrawalQuotas() throws Exception {
    GetWithdrawalQuotasReq.GetWithdrawalQuotasReqBuilder builder = GetWithdrawalQuotasReq.builder();
    builder.currency("USDT").chain("bsc");
    GetWithdrawalQuotasReq req = builder.build();
    GetWithdrawalQuotasResp resp = api.getWithdrawalQuotas(req);
    Assertions.assertNotNull(resp.getCurrency());
    Assertions.assertNotNull(resp.getLimitBTCAmount());
    Assertions.assertNotNull(resp.getUsedBTCAmount());
    Assertions.assertNotNull(resp.getQuotaCurrency());
    Assertions.assertNotNull(resp.getLimitQuotaCurrencyAmount());
    Assertions.assertNotNull(resp.getUsedQuotaCurrencyAmount());
    Assertions.assertNotNull(resp.getRemainAmount());
    Assertions.assertNotNull(resp.getAvailableAmount());
    Assertions.assertNotNull(resp.getWithdrawMinFee());
    Assertions.assertNotNull(resp.getInnerWithdrawMinFee());
    Assertions.assertNotNull(resp.getWithdrawMinSize());
    Assertions.assertNotNull(resp.getIsWithdrawEnabled());
    Assertions.assertNotNull(resp.getPrecision());
    Assertions.assertNotNull(resp.getChain());
    Assertions.assertNotNull(resp.getLockedAmount());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** withdrawalV3 Withdraw (V3) /api/v3/withdrawals */
  @Test
  public void testWithdrawalV3() throws Exception {
    WithdrawalV3Req.WithdrawalV3ReqBuilder builder = WithdrawalV3Req.builder();
    builder
        .currency("USDT")
        .chain("bsc")
        .amount("10")
        .memo("")
        .isInner(false)
        .remark("****")
        .feeDeductType("INTERNAL")
        .toAddress("***")
        .withdrawType(WithdrawalV3Req.WithdrawTypeEnum.ADDRESS);
    WithdrawalV3Req req = builder.build();
    WithdrawalV3Resp resp = api.withdrawalV3(req);
    Assertions.assertNotNull(resp.getWithdrawalId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** cancelWithdrawal Cancel Withdrawal /api/v1/withdrawals/{withdrawalId} */
  @Test
  public void testCancelWithdrawal() throws Exception {
    CancelWithdrawalReq.CancelWithdrawalReqBuilder builder = CancelWithdrawalReq.builder();
    builder.withdrawalId("68804a886538a800078cdde3");
    CancelWithdrawalReq req = builder.build();
    CancelWithdrawalResp resp = api.cancelWithdrawal(req);
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getWithdrawalHistory Get Withdrawal History /api/v1/withdrawals */
  @Test
  public void testGetWithdrawalHistory() throws Exception {
    GetWithdrawalHistoryReq.GetWithdrawalHistoryReqBuilder builder =
        GetWithdrawalHistoryReq.builder();
    builder.currency("USDT");
    GetWithdrawalHistoryReq req = builder.build();
    GetWithdrawalHistoryResp resp = api.getWithdrawalHistory(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getChain());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getAddress());
              Assertions.assertNotNull(item.getMemo());
              Assertions.assertNotNull(item.getIsInner());
              Assertions.assertNotNull(item.getAmount());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getUpdatedAt());
              Assertions.assertNotNull(item.getRemark());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getWithdrawalHistoryById Get Withdrawal History By ID /api/v1/withdrawals/{withdrawalId} */
  @Test
  public void testGetWithdrawalHistoryById() throws Exception {
    GetWithdrawalHistoryByIdReq.GetWithdrawalHistoryByIdReqBuilder builder =
        GetWithdrawalHistoryByIdReq.builder();
    builder.withdrawalId("68804a886538a800078cdde3");
    GetWithdrawalHistoryByIdReq req = builder.build();
    GetWithdrawalHistoryByIdResp resp = api.getWithdrawalHistoryById(req);
    Assertions.assertNotNull(resp.getId());
    Assertions.assertNotNull(resp.getUid());
    Assertions.assertNotNull(resp.getCurrency());
    Assertions.assertNotNull(resp.getChainId());
    Assertions.assertNotNull(resp.getChainName());
    Assertions.assertNotNull(resp.getCurrencyName());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getFailureReason());
    Assertions.assertNotNull(resp.getAddress());
    Assertions.assertNotNull(resp.getMemo());
    Assertions.assertNotNull(resp.getIsInner());
    Assertions.assertNotNull(resp.getAmount());
    Assertions.assertNotNull(resp.getFee());
    Assertions.assertNotNull(resp.getWalletTxId());
    Assertions.assertNotNull(resp.getAddressRemark());
    Assertions.assertNotNull(resp.getRemark());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getCancelType());

    Assertions.assertNotNull(resp.getReturnStatus());
    Assertions.assertNotNull(resp.getReturnCurrency());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getWithdrawalHistoryOld Get Withdrawal History - Old /api/v1/hist-withdrawals */
  @Test
  public void testGetWithdrawalHistoryOld() throws Exception {
    GetWithdrawalHistoryOldReq.GetWithdrawalHistoryOldReqBuilder builder =
        GetWithdrawalHistoryOldReq.builder();
    builder.currency("USDT").startAt(1703001600000L).endAt(1703260800000L);
    GetWithdrawalHistoryOldReq req = builder.build();
    GetWithdrawalHistoryOldResp resp = api.getWithdrawalHistoryOld(req);
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
              Assertions.assertNotNull(item.getAddress());
              Assertions.assertNotNull(item.getWalletTxId());
              Assertions.assertNotNull(item.getIsInner());
              Assertions.assertNotNull(item.getStatus());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** withdrawalV1 Withdraw - V1 /api/v1/withdrawals */
  @Test
  public void testWithdrawalV1() throws Exception {
    WithdrawalV1Req.WithdrawalV1ReqBuilder builder = WithdrawalV1Req.builder();
    builder.currency("USDT").chain("bsc").address("***").amount(10L).memo("").isInner(false);
    WithdrawalV1Req req = builder.build();
    WithdrawalV1Resp resp = api.withdrawalV1(req);
    Assertions.assertNotNull(resp.getWithdrawalId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
