package com.kucoin.universal.sdk.test.e2e.rest.broker;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.broker.ndbroker.*;
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
public class NdBrokerTest {

  private static NDBrokerApi api;

  public static ObjectMapper mapper = new ObjectMapper();

  @BeforeAll
  public static void setUp() {

    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");
    String brokerName = System.getenv("BROKER_NAME");
    String brokerPartner = System.getenv("BROKER_PARTNER");
    String brokerKey = System.getenv("BROKER_KEY");

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
            .brokerName(brokerName)
            .brokerPartner(brokerPartner)
            .brokerKey(brokerKey)
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(httpTransport)
            .build();

    KucoinClient kucoinClient = new DefaultKucoinClient(clientOpt);
    api = kucoinClient.getRestService().getBrokerService().getNDBrokerApi();
  }

  /** submitKYC Submit KYC /api/kyc/ndBroker/proxyClient/submit */
  @Test
  public void testSubmitKYC() throws Exception {
    SubmitKYCReq.SubmitKYCReqBuilder builder = SubmitKYCReq.builder();
    builder
        .clientUid("226383154")
        .firstName("Kaylah")
        .lastName("Padberg")
        .issueCountry("JP")
        .birthDate("2000-01-01")
        .identityType(SubmitKYCReq.IdentityTypeEnum.PASSPORT)
        .identityNumber("55")
        .expireDate("2030-01-01")
        .frontPhoto("****")
        .backendPhoto("***")
        .facePhoto("***");
    SubmitKYCReq req = builder.build();
    SubmitKYCResp resp = api.submitKYC(req);
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getKYCStatus Get KYC Status /api/kyc/ndBroker/proxyClient/status/list */
  @Test
  public void testGetKYCStatus() throws Exception {
    GetKYCStatusReq.GetKYCStatusReqBuilder builder = GetKYCStatusReq.builder();
    builder.clientUids("226383154");
    GetKYCStatusReq req = builder.build();
    GetKYCStatusResp resp = api.getKYCStatus(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getClientUid());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getRejectReason());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getKYCStatusList Get KYC Status List /api/kyc/ndBroker/proxyClient/status/page */
  @Test
  public void testGetKYCStatusList() throws Exception {
    GetKYCStatusListReq.GetKYCStatusListReqBuilder builder = GetKYCStatusListReq.builder();
    builder.pageNumber(10).pageSize(1);
    GetKYCStatusListReq req = builder.build();
    GetKYCStatusListResp resp = api.getKYCStatusList(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getClientUid());
              Assertions.assertNotNull(item.getStatus());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getBrokerInfo Get Broker Info /api/v1/broker/nd/info */
  @Test
  public void testGetBrokerInfo() throws Exception {
    GetBrokerInfoReq.GetBrokerInfoReqBuilder builder = GetBrokerInfoReq.builder();
    builder.tradeType(GetBrokerInfoReq.TradeTypeEnum._1);
    GetBrokerInfoReq req = builder.build();
    GetBrokerInfoResp resp = api.getBrokerInfo(req);
    Assertions.assertNotNull(resp.getAccountSize());
    Assertions.assertNotNull(resp.getLevel());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addSubAccount Add sub-account /api/v1/broker/nd/account */
  @Test
  public void testAddSubAccount() throws Exception {
    AddSubAccountReq.AddSubAccountReqBuilder builder = AddSubAccountReq.builder();
    builder.accountName("sdk_test_5");
    AddSubAccountReq req = builder.build();
    AddSubAccountResp resp = api.addSubAccount(req);
    Assertions.assertNotNull(resp.getAccountName());
    Assertions.assertNotNull(resp.getUid());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getLevel());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSubAccount Get sub-account /api/v1/broker/nd/account */
  @Test
  public void testGetSubAccount() throws Exception {
    GetSubAccountReq.GetSubAccountReqBuilder builder = GetSubAccountReq.builder();
    builder.uid("248494737");
    GetSubAccountReq req = builder.build();
    GetSubAccountResp resp = api.getSubAccount(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getAccountName());
              Assertions.assertNotNull(item.getUid());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getLevel());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** addSubAccountApi Add sub-account API /api/v1/broker/nd/account/apikey */
  @Test
  public void testAddSubAccountApi() throws Exception {
    AddSubAccountApiReq.AddSubAccountApiReqBuilder builder = AddSubAccountApiReq.builder();
    builder
        .uid("248494737")
        .passphrase("****")
        .ipWhitelist(Arrays.asList("127.0.0.1", "192.168.1.1"))
        .permissions(Arrays.asList(AddSubAccountApiReq.PermissionsEnum.FUTURES))
        .label("labels");
    AddSubAccountApiReq req = builder.build();
    AddSubAccountApiResp resp = api.addSubAccountApi(req);
    Assertions.assertNotNull(resp.getUid());
    Assertions.assertNotNull(resp.getLabel());
    Assertions.assertNotNull(resp.getApiKey());
    Assertions.assertNotNull(resp.getSecretKey());
    Assertions.assertNotNull(resp.getApiVersion());
    resp.getPermissions().forEach(item -> {});

    resp.getIpWhitelist().forEach(item -> {});

    Assertions.assertNotNull(resp.getCreatedAt());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSubAccountAPI Get sub-account API /api/v1/broker/nd/account/apikey */
  @Test
  public void testGetSubAccountAPI() throws Exception {
    GetSubAccountAPIReq.GetSubAccountAPIReqBuilder builder = GetSubAccountAPIReq.builder();
    builder.uid("248494737").apiKey("6881ee4028335c0001f5c02a");
    GetSubAccountAPIReq req = builder.build();
    GetSubAccountAPIResp resp = api.getSubAccountAPI(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getUid());
              Assertions.assertNotNull(item.getLabel());
              Assertions.assertNotNull(item.getApiKey());
              Assertions.assertNotNull(item.getApiVersion());
              Assertions.assertNotNull(item.getPermissions());
              Assertions.assertNotNull(item.getIpWhitelist());
              Assertions.assertNotNull(item.getCreatedAt());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** TODO 404 modifySubAccountApi Modify sub-account API /api/v1/broker/nd/account/update-apikey */
  @Test
  public void testModifySubAccountApi() throws Exception {
    ModifySubAccountApiReq.ModifySubAccountApiReqBuilder builder = ModifySubAccountApiReq.builder();
    builder
        .uid("226383154")
        .ipWhitelist(Arrays.asList("127.0.0.1"))
        .permissions(Arrays.asList(ModifySubAccountApiReq.PermissionsEnum.FUTURES))
        .apiKey("6881f6c4dffe710001e66b16");
    ModifySubAccountApiReq req = builder.build();
    ModifySubAccountApiResp resp = api.modifySubAccountApi(req);
    Assertions.assertNotNull(resp.getUid());
    Assertions.assertNotNull(resp.getLabel());
    Assertions.assertNotNull(resp.getApiKey());
    Assertions.assertNotNull(resp.getApiVersion());
    resp.getPermissions().forEach(item -> {});

    resp.getIpWhitelist().forEach(item -> {});

    Assertions.assertNotNull(resp.getCreatedAt());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** deleteSubAccountAPI Delete sub-account API /api/v1/broker/nd/account/apikey */
  @Test
  public void testDeleteSubAccountAPI() throws Exception {
    DeleteSubAccountAPIReq.DeleteSubAccountAPIReqBuilder builder = DeleteSubAccountAPIReq.builder();
    builder.uid("226383154").apiKey("6881f6c4dffe710001e66b16");
    DeleteSubAccountAPIReq req = builder.build();
    DeleteSubAccountAPIResp resp = api.deleteSubAccountAPI(req);
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** transfer Transfer /api/v1/broker/nd/transfer */
  @Test
  public void testTransfer() throws Exception {
    TransferReq.TransferReqBuilder builder = TransferReq.builder();

    builder
        .currency("USDT")
        .amount("0.01")
        .direction(TransferReq.DirectionEnum.OUT)
        .accountType(TransferReq.AccountTypeEnum.TRADE)
        .specialUid("237082742")
        .specialAccountType(TransferReq.SpecialAccountTypeEnum.MAIN)
        .clientOid(UUID.randomUUID().toString());
    TransferReq req = builder.build();
    TransferResp resp = api.transfer(req);
    Assertions.assertNotNull(resp.getOrderId());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getTransferHistory Get Transfer History /api/v3/broker/nd/transfer/detail */
  @Test
  public void testGetTransferHistory() throws Exception {
    GetTransferHistoryReq.GetTransferHistoryReqBuilder builder = GetTransferHistoryReq.builder();
    builder.orderId("6881ef67d3bb93000750693a");
    GetTransferHistoryReq req = builder.build();
    GetTransferHistoryResp resp = api.getTransferHistory(req);
    Assertions.assertNotNull(resp.getOrderId());
    Assertions.assertNotNull(resp.getCurrency());
    Assertions.assertNotNull(resp.getAmount());
    Assertions.assertNotNull(resp.getFromUid());
    Assertions.assertNotNull(resp.getFromAccountType());
    Assertions.assertNotNull(resp.getFromAccountTag());
    Assertions.assertNotNull(resp.getToUid());
    Assertions.assertNotNull(resp.getToAccountType());
    Assertions.assertNotNull(resp.getToAccountTag());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getCreatedAt());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getDepositList Get Deposit List /api/v1/asset/ndbroker/deposit/list */
  @Test
  public void testGetDepositList() throws Exception {
    GetDepositListReq.GetDepositListReqBuilder builder = GetDepositListReq.builder();
    builder.currency("USDT");
    GetDepositListReq req = builder.build();
    GetDepositListResp resp = api.getDepositList(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getUid());
              Assertions.assertNotNull(item.getHash());
              Assertions.assertNotNull(item.getAddress());
              Assertions.assertNotNull(item.getMemo());
              Assertions.assertNotNull(item.getAmount());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getIsInner());
              Assertions.assertNotNull(item.getWalletTxId());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getRemark());
              Assertions.assertNotNull(item.getChain());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getUpdatedAt());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getDepositDetail Get Deposit Detail /api/v3/broker/nd/deposit/detail */
  @Test
  public void testGetDepositDetail() throws Exception {
    GetDepositDetailReq.GetDepositDetailReqBuilder builder = GetDepositDetailReq.builder();
    builder.currency("USDT").hash("6724e363a492800007ec602b");
    GetDepositDetailReq req = builder.build();
    GetDepositDetailResp resp = api.getDepositDetail(req);
    Assertions.assertNotNull(resp.getChain());
    Assertions.assertNotNull(resp.getHash());
    Assertions.assertNotNull(resp.getWalletTxId());
    Assertions.assertNotNull(resp.getUid());
    Assertions.assertNotNull(resp.getUpdatedAt());
    Assertions.assertNotNull(resp.getAmount());
    Assertions.assertNotNull(resp.getMemo());
    Assertions.assertNotNull(resp.getFee());
    Assertions.assertNotNull(resp.getAddress());
    Assertions.assertNotNull(resp.getRemark());
    Assertions.assertNotNull(resp.getIsInner());
    Assertions.assertNotNull(resp.getCurrency());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getCreatedAt());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getWithdrawDetail Get Withdraw Detail /api/v3/broker/nd/withdraw/detail */
  @Test
  public void testGetWithdrawDetail() throws Exception {
    GetWithdrawDetailReq.GetWithdrawDetailReqBuilder builder = GetWithdrawDetailReq.builder();
    builder.withdrawalId("674686fa1ac01f0007b25768");
    GetWithdrawDetailReq req = builder.build();
    GetWithdrawDetailResp resp = api.getWithdrawDetail(req);
    Assertions.assertNotNull(resp.getId());
    Assertions.assertNotNull(resp.getChain());
    Assertions.assertNotNull(resp.getUid());
    Assertions.assertNotNull(resp.getAmount());
    Assertions.assertNotNull(resp.getMemo());
    Assertions.assertNotNull(resp.getFee());
    Assertions.assertNotNull(resp.getAddress());
    Assertions.assertNotNull(resp.getRemark());
    Assertions.assertNotNull(resp.getIsInner());
    Assertions.assertNotNull(resp.getCurrency());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getCreatedAt());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getRebase Get Broker Rebate /api/v1/broker/nd/rebase/download */
  @Test
  public void testGetRebase() throws Exception {
    GetRebaseReq.GetRebaseReqBuilder builder = GetRebaseReq.builder();
    builder.begin("20240610").end("20241010").tradeType(GetRebaseReq.TradeTypeEnum._1);
    GetRebaseReq req = builder.build();
    GetRebaseResp resp = api.getRebase(req);
    Assertions.assertNotNull(resp.getUrl());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
