package com.kucoin.universal.sdk.test.e2e.rest.account;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.account.transfer.*;
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
public class AccountTransferTest {

  private static TransferApi api;

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
    api = kucoinClient.getRestService().getAccountService().getTransferApi();
  }

  /** getTransferQuotas Get Transfer Quotas /api/v1/accounts/transferable */
  @Test
  public void testGetTransferQuotas() throws Exception {
    GetTransferQuotasReq.GetTransferQuotasReqBuilder builder = GetTransferQuotasReq.builder();
    builder.currency("USDT").type(GetTransferQuotasReq.TypeEnum.MAIN).tag("");
    GetTransferQuotasReq req = builder.build();
    GetTransferQuotasResp resp = api.getTransferQuotas(req);
    Assertions.assertNotNull(resp.getCurrency());
    Assertions.assertNotNull(resp.getBalance());
    Assertions.assertNotNull(resp.getAvailable());
    Assertions.assertNotNull(resp.getHolds());
    Assertions.assertNotNull(resp.getTransferable());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** flexTransfer Flex Transfer /api/v3/accounts/universal-transfer */
  @Test
  public void testFlexTransfer() throws Exception {
    FlexTransferReq.FlexTransferReqBuilder builder = FlexTransferReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .currency("USDT")
        .amount("1")
        .fromAccountType(FlexTransferReq.FromAccountTypeEnum.TRADE)
        .toAccountType(FlexTransferReq.ToAccountTypeEnum.MAIN)
        .type(FlexTransferReq.TypeEnum.INTERNAL);
    FlexTransferReq req = builder.build();
    FlexTransferResp resp = api.flexTransfer(req);
    Assertions.assertNotNull(resp.getOrderId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** subAccountTransfer Sub-account Transfer /api/v2/accounts/sub-transfer */
  @Test
  public void testSubAccountTransfer() throws Exception {
    SubAccountTransferReq.SubAccountTransferReqBuilder builder = SubAccountTransferReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .currency("USDT")
        .amount("1")
        .direction(SubAccountTransferReq.DirectionEnum.OUT)
        .accountType(SubAccountTransferReq.AccountTypeEnum.MAIN)
        .subAccountType(SubAccountTransferReq.SubAccountTypeEnum.MAIN)
        .subUserId("6744227ce235b300012232d6");
    SubAccountTransferReq req = builder.build();
    SubAccountTransferResp resp = api.subAccountTransfer(req);
    Assertions.assertNotNull(resp.getOrderId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** innerTransfer Internal Transfer /api/v2/accounts/inner-transfer */
  @Test
  public void testInnerTransfer() throws Exception {
    InnerTransferReq.InnerTransferReqBuilder builder = InnerTransferReq.builder();
    builder
        .clientOid(UUID.randomUUID().toString())
        .currency("USDT")
        .amount("1")
        .to(InnerTransferReq.ToEnum.MAIN)
        .from(InnerTransferReq.FromEnum.TRADE);
    InnerTransferReq req = builder.build();
    InnerTransferResp resp = api.innerTransfer(req);
    Assertions.assertNotNull(resp.getOrderId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * getFuturesAccountTransferOutLedger Get Futures Account Transfer Out Ledger
   * /api/v1/transfer-list
   */
  @Test
  public void testGetFuturesAccountTransferOutLedger() throws Exception {
    GetFuturesAccountTransferOutLedgerReq.GetFuturesAccountTransferOutLedgerReqBuilder builder =
        GetFuturesAccountTransferOutLedgerReq.builder();
    builder.currency("USDT").type(GetFuturesAccountTransferOutLedgerReq.TypeEnum.MAIN);
    GetFuturesAccountTransferOutLedgerReq req = builder.build();
    GetFuturesAccountTransferOutLedgerResp resp = api.getFuturesAccountTransferOutLedger(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getApplyId());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getRecRemark());
              Assertions.assertNotNull(item.getRecSystem());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getAmount());
              Assertions.assertNotNull(item.getReason());
              Assertions.assertNotNull(item.getOffset());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getRemark());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** futuresAccountTransferOut Futures Account Transfer Out /api/v3/transfer-out */
  @Test
  public void testFuturesAccountTransferOut() throws Exception {
    FuturesAccountTransferOutReq.FuturesAccountTransferOutReqBuilder builder =
        FuturesAccountTransferOutReq.builder();
    builder
        .currency("USDT")
        .amount(1.0)
        .recAccountType(FuturesAccountTransferOutReq.RecAccountTypeEnum.MAIN);
    FuturesAccountTransferOutReq req = builder.build();
    FuturesAccountTransferOutResp resp = api.futuresAccountTransferOut(req);
    Assertions.assertNotNull(resp.getApplyId());
    Assertions.assertNotNull(resp.getBizNo());
    Assertions.assertNotNull(resp.getPayAccountType());
    Assertions.assertNotNull(resp.getPayTag());
    Assertions.assertNotNull(resp.getRemark());
    Assertions.assertNotNull(resp.getRecAccountType());
    Assertions.assertNotNull(resp.getRecTag());
    Assertions.assertNotNull(resp.getRecRemark());
    Assertions.assertNotNull(resp.getRecSystem());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getCurrency());
    Assertions.assertNotNull(resp.getAmount());
    Assertions.assertNotNull(resp.getFee());
    Assertions.assertNotNull(resp.getSn());
    Assertions.assertNotNull(resp.getReason());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getUpdatedAt());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** futuresAccountTransferIn Futures Account Transfer In /api/v1/transfer-in */
  @Test
  public void testFuturesAccountTransferIn() throws Exception {
    FuturesAccountTransferInReq.FuturesAccountTransferInReqBuilder builder =
        FuturesAccountTransferInReq.builder();
    builder
        .currency("USDT")
        .amount(1.0)
        .payAccountType(FuturesAccountTransferInReq.PayAccountTypeEnum.MAIN);
    FuturesAccountTransferInReq req = builder.build();
    FuturesAccountTransferInResp resp = api.futuresAccountTransferIn(req);
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
