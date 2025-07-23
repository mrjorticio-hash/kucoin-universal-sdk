package com.kucoin.universal.sdk.test.e2e.rest.account;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.account.fee.*;
import com.kucoin.universal.sdk.generate.account.subaccount.*;
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
public class AccountSubAccountTest {

  private static SubAccountApi api;

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
    api = kucoinClient.getRestService().getAccountService().getSubAccountApi();
  }

  /** addSubAccount Add sub-account /api/v2/sub/user/created */
  @Test
  public void testAddSubAccount() throws Exception {
    AddSubAccountReq.AddSubAccountReqBuilder builder = AddSubAccountReq.builder();
    builder
        .password("********@123")
        .remarks("")
        .subName("sdkTestIntegration3")
        .access(AddSubAccountReq.AccessEnum.SPOT);
    AddSubAccountReq req = builder.build();
    AddSubAccountResp resp = api.addSubAccount(req);
    Assertions.assertNotNull(resp.getUid());
    Assertions.assertNotNull(resp.getSubName());
    Assertions.assertNotNull(resp.getRemarks());
    Assertions.assertNotNull(resp.getAccess());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * addSubAccountMarginPermission Add sub-account Margin Permission /api/v3/sub/user/margin/enable
   */
  @Test
  public void testAddSubAccountMarginPermission() throws Exception {
    AddSubAccountMarginPermissionReq.AddSubAccountMarginPermissionReqBuilder builder =
        AddSubAccountMarginPermissionReq.builder();
    builder.uid("248443791");
    AddSubAccountMarginPermissionReq req = builder.build();
    AddSubAccountMarginPermissionResp resp = api.addSubAccountMarginPermission(req);
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * addSubAccountFuturesPermission Add sub-account Futures Permission
   * /api/v3/sub/user/futures/enable
   */
  @Test
  public void testAddSubAccountFuturesPermission() throws Exception {
    AddSubAccountFuturesPermissionReq.AddSubAccountFuturesPermissionReqBuilder builder =
        AddSubAccountFuturesPermissionReq.builder();
    builder.uid("248443791");
    AddSubAccountFuturesPermissionReq req = builder.build();
    AddSubAccountFuturesPermissionResp resp = api.addSubAccountFuturesPermission(req);
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotSubAccountsSummaryV2 Get sub-account List - Summary Info /api/v2/sub/user */
  @Test
  public void testGetSpotSubAccountsSummaryV2() throws Exception {
    GetSpotSubAccountsSummaryV2Req.GetSpotSubAccountsSummaryV2ReqBuilder builder =
        GetSpotSubAccountsSummaryV2Req.builder();
    builder.currentPage(1).pageSize(10);
    GetSpotSubAccountsSummaryV2Req req = builder.build();
    GetSpotSubAccountsSummaryV2Resp resp = api.getSpotSubAccountsSummaryV2(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getUserId());
              Assertions.assertNotNull(item.getUid());
              Assertions.assertNotNull(item.getSubName());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getAccess());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getRemarks());
              Assertions.assertNotNull(item.getTradeTypes());
              Assertions.assertNotNull(item.getOpenedTradeTypes());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotSubAccountDetail Get sub-account Detail - Balance /api/v1/sub-accounts/{subUserId} */
  @Test
  public void testGetSpotSubAccountDetail() throws Exception {
    GetSpotSubAccountDetailReq.GetSpotSubAccountDetailReqBuilder builder =
        GetSpotSubAccountDetailReq.builder();
    builder
        .subUserId("6745b7bc890ba20001911363")
        .includeBaseAmount(false)
        .baseCurrency("USDT")
        .baseAmount("0.1");
    GetSpotSubAccountDetailReq req = builder.build();
    GetSpotSubAccountDetailResp resp = api.getSpotSubAccountDetail(req);
    Assertions.assertNotNull(resp.getSubUserId());
    Assertions.assertNotNull(resp.getSubName());
    resp.getMainAccounts()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getBalance());
              Assertions.assertNotNull(item.getAvailable());
              Assertions.assertNotNull(item.getHolds());
              Assertions.assertNotNull(item.getBaseCurrency());
              Assertions.assertNotNull(item.getBaseCurrencyPrice());
              Assertions.assertNotNull(item.getBaseAmount());
              Assertions.assertNotNull(item.getTag());
            });

    resp.getTradeAccounts()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getBalance());
              Assertions.assertNotNull(item.getAvailable());
              Assertions.assertNotNull(item.getHolds());
              Assertions.assertNotNull(item.getBaseCurrency());
              Assertions.assertNotNull(item.getBaseCurrencyPrice());
              Assertions.assertNotNull(item.getBaseAmount());
              Assertions.assertNotNull(item.getTag());
            });

    resp.getMarginAccounts()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getBalance());
              Assertions.assertNotNull(item.getAvailable());
              Assertions.assertNotNull(item.getHolds());
              Assertions.assertNotNull(item.getBaseCurrency());
              Assertions.assertNotNull(item.getBaseCurrencyPrice());
              Assertions.assertNotNull(item.getBaseAmount());
              Assertions.assertNotNull(item.getTag());
            });

    resp.getTradeHFAccounts().forEach(item -> {});

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotSubAccountListV2 Get sub-account List - Spot Balance (V2) /api/v2/sub-accounts */
  @Test
  public void testGetSpotSubAccountListV2() throws Exception {
    GetSpotSubAccountListV2Req.GetSpotSubAccountListV2ReqBuilder builder =
        GetSpotSubAccountListV2Req.builder();
    builder.currentPage(1).pageSize(10);
    GetSpotSubAccountListV2Req req = builder.build();
    GetSpotSubAccountListV2Resp resp = api.getSpotSubAccountListV2(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSubUserId());
              Assertions.assertNotNull(item.getSubName());
              Assertions.assertNotNull(item.getMainAccounts());
              Assertions.assertNotNull(item.getTradeAccounts());
              Assertions.assertNotNull(item.getMarginAccounts());
              Assertions.assertNotNull(item.getTradeHFAccounts());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * getFuturesSubAccountListV2 Get sub-account List - Futures Balance (V2)
   * /api/v1/account-overview-all
   */
  @Test
  public void testGetFuturesSubAccountListV2() throws Exception {
    GetFuturesSubAccountListV2Req.GetFuturesSubAccountListV2ReqBuilder builder =
        GetFuturesSubAccountListV2Req.builder();
    builder.currency("XBT");
    GetFuturesSubAccountListV2Req req = builder.build();
    GetFuturesSubAccountListV2Resp resp = api.getFuturesSubAccountListV2(req);
    Assertions.assertNotNull(resp.getSummary());
    resp.getAccounts()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getAccountName());
              Assertions.assertNotNull(item.getAccountEquity());
              Assertions.assertNotNull(item.getUnrealisedPNL());
              Assertions.assertNotNull(item.getMarginBalance());
              Assertions.assertNotNull(item.getPositionMargin());
              Assertions.assertNotNull(item.getOrderMargin());
              Assertions.assertNotNull(item.getFrozenFunds());
              Assertions.assertNotNull(item.getAvailableBalance());
              Assertions.assertNotNull(item.getCurrency());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addSubAccountApi Add sub-account API /api/v1/sub/api-key */
  @Test
  public void testAddSubAccountApi() throws Exception {
    AddSubAccountApiReq.AddSubAccountApiReqBuilder builder = AddSubAccountApiReq.builder();
    builder
        .passphrase("******")
        .remark("******")
        .permission("General,Spot")
        .ipWhitelist("")
        .expire(AddSubAccountApiReq.ExpireEnum._30)
        .subName("sdkTestIntegration3");
    AddSubAccountApiReq req = builder.build();
    AddSubAccountApiResp resp = api.addSubAccountApi(req);
    Assertions.assertNotNull(resp.getApiKey());
    Assertions.assertNotNull(resp.getApiSecret());
    Assertions.assertNotNull(resp.getApiVersion());
    Assertions.assertNotNull(resp.getPassphrase());
    Assertions.assertNotNull(resp.getPermission());
    Assertions.assertNotNull(resp.getCreatedAt());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** modifySubAccountApi Modify sub-account API /api/v1/sub/api-key/update */
  @Test
  public void testModifySubAccountApi() throws Exception {
    ModifySubAccountApiReq.ModifySubAccountApiReqBuilder builder = ModifySubAccountApiReq.builder();
    builder
        .passphrase("******")
        .expire(ModifySubAccountApiReq.ExpireEnum._90)
        .subName("sdkTestIntegration3")
        .apiKey("6880428d28335c0001f5b45d");
    ModifySubAccountApiReq req = builder.build();
    ModifySubAccountApiResp resp = api.modifySubAccountApi(req);
    Assertions.assertNotNull(resp.getSubName());
    Assertions.assertNotNull(resp.getApiKey());
    Assertions.assertNotNull(resp.getPermission());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSubAccountApiList Get sub-account API List /api/v1/sub/api-key */
  @Test
  public void testGetSubAccountApiList() throws Exception {
    GetSubAccountApiListReq.GetSubAccountApiListReqBuilder builder =
        GetSubAccountApiListReq.builder();
    builder.apiKey("6880428d28335c0001f5b45d").subName("sdkTestIntegration3");
    GetSubAccountApiListReq req = builder.build();
    GetSubAccountApiListResp resp = api.getSubAccountApiList(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSubName());
              Assertions.assertNotNull(item.getRemark());
              Assertions.assertNotNull(item.getApiKey());
              Assertions.assertNotNull(item.getApiVersion());
              Assertions.assertNotNull(item.getPermission());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getUid());
              Assertions.assertNotNull(item.getIsMaster());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** deleteSubAccountApi Delete sub-account API /api/v1/sub/api-key */
  @Test
  public void testDeleteSubAccountApi() throws Exception {
    DeleteSubAccountApiReq.DeleteSubAccountApiReqBuilder builder = DeleteSubAccountApiReq.builder();
    builder
        .apiKey("6880428d28335c0001f5b45d")
        .subName("sdkTestIntegration3")
        .passphrase("123hhhhakjsbc");
    DeleteSubAccountApiReq req = builder.build();
    DeleteSubAccountApiResp resp = api.deleteSubAccountApi(req);
    Assertions.assertNotNull(resp.getSubName());
    Assertions.assertNotNull(resp.getApiKey());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotSubAccountsSummaryV1 Get sub-account List - Summary Info (V1) /api/v1/sub/user */
  @Test
  public void testGetSpotSubAccountsSummaryV1() throws Exception {
    GetSpotSubAccountsSummaryV1Resp resp = api.getSpotSubAccountsSummaryV1();
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getUserId());
              Assertions.assertNotNull(item.getUid());
              Assertions.assertNotNull(item.getSubName());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getRemarks());
              Assertions.assertNotNull(item.getAccess());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotSubAccountListV1 Get sub-account List - Spot Balance (V1) /api/v1/sub-accounts */
  @Test
  public void testGetSpotSubAccountListV1() throws Exception {
    GetSpotSubAccountListV1Resp resp = api.getSpotSubAccountListV1();
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSubUserId());
              Assertions.assertNotNull(item.getSubName());
              Assertions.assertNotNull(item.getMainAccounts());
              Assertions.assertNotNull(item.getTradeAccounts());
              Assertions.assertNotNull(item.getMarginAccounts());
              Assertions.assertNotNull(item.getTradeHFAccounts());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
