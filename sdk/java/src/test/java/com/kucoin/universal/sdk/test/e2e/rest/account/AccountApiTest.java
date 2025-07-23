package com.kucoin.universal.sdk.test.e2e.rest.account;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.account.account.*;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import java.io.IOException;
import java.util.Collections;
import lombok.extern.slf4j.Slf4j;
import okhttp3.*;
import org.jetbrains.annotations.NotNull;
import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.Test;

@Slf4j
public class AccountApiTest {

  private static AccountApi api;

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
    api = kucoinClient.getRestService().getAccountService().getAccountApi();
  }

  /** getAccountInfo Get Account Summary Info /api/v2/user-info */
  @Test
  public void testGetAccountInfo() throws Exception {
    GetAccountInfoResp resp = api.getAccountInfo();
    Assertions.assertNotNull(resp.getLevel());
    Assertions.assertNotNull(resp.getSubQuantity());
    Assertions.assertNotNull(resp.getSpotSubQuantity());
    Assertions.assertNotNull(resp.getMarginSubQuantity());
    Assertions.assertNotNull(resp.getFuturesSubQuantity());
    Assertions.assertNotNull(resp.getOptionSubQuantity());
    Assertions.assertNotNull(resp.getMaxSubQuantity());
    Assertions.assertNotNull(resp.getMaxDefaultSubQuantity());
    Assertions.assertNotNull(resp.getMaxSpotSubQuantity());
    Assertions.assertNotNull(resp.getMaxMarginSubQuantity());
    Assertions.assertNotNull(resp.getMaxFuturesSubQuantity());
    Assertions.assertNotNull(resp.getMaxOptionSubQuantity());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getApikeyInfo Get Apikey Info /api/v1/user/api-key */
  @Test
  public void testGetApikeyInfo() throws Exception {
    GetApikeyInfoResp resp = api.getApikeyInfo();
    Assertions.assertNotNull(resp.getRemark());
    Assertions.assertNotNull(resp.getApiKey());
    Assertions.assertNotNull(resp.getApiVersion());
    Assertions.assertNotNull(resp.getPermission());
    Assertions.assertNotNull(resp.getIpWhitelist());
    Assertions.assertNotNull(resp.getCreatedAt());
    Assertions.assertNotNull(resp.getUid());
    Assertions.assertNotNull(resp.getIsMaster());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotAccountType Get Account Type - Spot /api/v1/hf/accounts/opened */
  @Test
  public void testGetSpotAccountType() throws Exception {
    GetSpotAccountTypeResp resp = api.getSpotAccountType();
    Assertions.assertNotNull(resp.getData());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotAccountList Get Account List - Spot /api/v1/accounts */
  @Test
  public void testGetSpotAccountList() throws Exception {
    GetSpotAccountListReq.GetSpotAccountListReqBuilder builder = GetSpotAccountListReq.builder();
    builder.type(GetSpotAccountListReq.TypeEnum.MAIN);
    GetSpotAccountListReq req = builder.build();
    GetSpotAccountListResp resp = api.getSpotAccountList(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getBalance());
              Assertions.assertNotNull(item.getAvailable());
              Assertions.assertNotNull(item.getHolds());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotAccountDetail Get Account Detail - Spot /api/v1/accounts/{accountId} */
  @Test
  public void testGetSpotAccountDetail() throws Exception {
    GetSpotAccountDetailReq.GetSpotAccountDetailReqBuilder builder =
        GetSpotAccountDetailReq.builder();
    builder.accountId("6582f6a48c171f000769b867");
    GetSpotAccountDetailReq req = builder.build();
    GetSpotAccountDetailResp resp = api.getSpotAccountDetail(req);
    Assertions.assertNotNull(resp.getCurrency());
    Assertions.assertNotNull(resp.getBalance());
    Assertions.assertNotNull(resp.getAvailable());
    Assertions.assertNotNull(resp.getHolds());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getCrossMarginAccount Get Account - Cross Margin /api/v3/margin/accounts */
  @Test
  public void testGetCrossMarginAccount() throws Exception {
    GetCrossMarginAccountReq.GetCrossMarginAccountReqBuilder builder =
        GetCrossMarginAccountReq.builder();
    builder
        .quoteCurrency(GetCrossMarginAccountReq.QuoteCurrencyEnum.USDT)
        .queryType(GetCrossMarginAccountReq.QueryTypeEnum.MARGIN);
    GetCrossMarginAccountReq req = builder.build();
    GetCrossMarginAccountResp resp = api.getCrossMarginAccount(req);
    Assertions.assertNotNull(resp.getTotalAssetOfQuoteCurrency());
    Assertions.assertNotNull(resp.getTotalLiabilityOfQuoteCurrency());
    Assertions.assertNotNull(resp.getDebtRatio());
    Assertions.assertNotNull(resp.getStatus());
    resp.getAccounts()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getTotal());
              Assertions.assertNotNull(item.getAvailable());
              Assertions.assertNotNull(item.getHold());
              Assertions.assertNotNull(item.getLiability());
              Assertions.assertNotNull(item.getMaxBorrowSize());
              Assertions.assertNotNull(item.getBorrowEnabled());
              Assertions.assertNotNull(item.getTransferInEnabled());
              Assertions.assertNotNull(item.getLiabilityPrincipal());
              Assertions.assertNotNull(item.getLiabilityInterest());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getIsolatedMarginAccount Get Account - Isolated Margin /api/v3/isolated/accounts */
  @Test
  public void testGetIsolatedMarginAccount() throws Exception {
    GetIsolatedMarginAccountReq.GetIsolatedMarginAccountReqBuilder builder =
        GetIsolatedMarginAccountReq.builder();
    builder
        .symbol("BTC-USDT")
        .quoteCurrency(GetIsolatedMarginAccountReq.QuoteCurrencyEnum.USDT)
        .queryType(GetIsolatedMarginAccountReq.QueryTypeEnum.ISOLATED);
    GetIsolatedMarginAccountReq req = builder.build();
    GetIsolatedMarginAccountResp resp = api.getIsolatedMarginAccount(req);
    Assertions.assertNotNull(resp.getTotalAssetOfQuoteCurrency());
    Assertions.assertNotNull(resp.getTotalLiabilityOfQuoteCurrency());
    Assertions.assertNotNull(resp.getTimestamp());
    resp.getAssets()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getDebtRatio());
              Assertions.assertNotNull(item.getBaseAsset());
              Assertions.assertNotNull(item.getQuoteAsset());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getFuturesAccount Get Account - Futures /api/v1/account-overview */
  @Test
  public void testGetFuturesAccount() throws Exception {
    GetFuturesAccountReq.GetFuturesAccountReqBuilder builder = GetFuturesAccountReq.builder();
    builder.currency("XBT");
    GetFuturesAccountReq req = builder.build();
    GetFuturesAccountResp resp = api.getFuturesAccount(req);
    Assertions.assertNotNull(resp.getAccountEquity());
    Assertions.assertNotNull(resp.getUnrealisedPNL());
    Assertions.assertNotNull(resp.getMarginBalance());
    Assertions.assertNotNull(resp.getPositionMargin());
    Assertions.assertNotNull(resp.getOrderMargin());
    Assertions.assertNotNull(resp.getFrozenFunds());
    Assertions.assertNotNull(resp.getAvailableBalance());
    Assertions.assertNotNull(resp.getCurrency());
    Assertions.assertNotNull(resp.getRiskRatio());
    Assertions.assertNotNull(resp.getMaxWithdrawAmount());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotLedger Get Account Ledgers - Spot/Margin /api/v1/accounts/ledgers */
  @Test
  public void testGetSpotLedger() throws Exception {
    GetSpotLedgerReq.GetSpotLedgerReqBuilder builder = GetSpotLedgerReq.builder();
    builder.currency("USDT").direction(GetSpotLedgerReq.DirectionEnum.IN);
    GetSpotLedgerReq req = builder.build();
    GetSpotLedgerResp resp = api.getSpotLedger(req);
    Assertions.assertNotNull(resp.getCurrentPage());
    Assertions.assertNotNull(resp.getPageSize());
    Assertions.assertNotNull(resp.getTotalNum());
    Assertions.assertNotNull(resp.getTotalPage());
    resp.getItems()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getAmount());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getBalance());
              Assertions.assertNotNull(item.getAccountType());
              Assertions.assertNotNull(item.getBizType());
              Assertions.assertNotNull(item.getDirection());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getContext());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getSpotHFLedger Get Account Ledgers - Trade_hf /api/v1/hf/accounts/ledgers */
  @Test
  public void testGetSpotHFLedger() throws Exception {
    GetSpotHFLedgerReq.GetSpotHFLedgerReqBuilder builder = GetSpotHFLedgerReq.builder();
    builder.currency("USDT").bizType(GetSpotHFLedgerReq.BizTypeEnum.TRANSFER);
    GetSpotHFLedgerReq req = builder.build();
    GetSpotHFLedgerResp resp = api.getSpotHFLedger(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getAmount());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getTax());
              Assertions.assertNotNull(item.getBalance());
              Assertions.assertNotNull(item.getAccountType());
              Assertions.assertNotNull(item.getBizType());
              Assertions.assertNotNull(item.getDirection());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getContext());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getMarginHFLedger Get Account Ledgers - Margin_hf /api/v3/hf/margin/account/ledgers */
  @Test
  public void testGetMarginHFLedger() throws Exception {
    GetMarginHFLedgerReq.GetMarginHFLedgerReqBuilder builder = GetMarginHFLedgerReq.builder();
    builder.currency("USDT");
    GetMarginHFLedgerReq req = builder.build();
    GetMarginHFLedgerResp resp = api.getMarginHFLedger(req);
    resp.getData()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getId());
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getAmount());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getBalance());
              Assertions.assertNotNull(item.getAccountType());
              Assertions.assertNotNull(item.getBizType());
              Assertions.assertNotNull(item.getDirection());
              Assertions.assertNotNull(item.getCreatedAt());
              Assertions.assertNotNull(item.getContext());
              Assertions.assertNotNull(item.getTax());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getFuturesLedger Get Account Ledgers - Futures /api/v1/transaction-history */
  @Test
  public void testGetFuturesLedger() throws Exception {
    GetFuturesLedgerReq.GetFuturesLedgerReqBuilder builder = GetFuturesLedgerReq.builder();
    builder.currency("USDT");
    GetFuturesLedgerReq req = builder.build();
    GetFuturesLedgerResp resp = api.getFuturesLedger(req);
    resp.getDataList()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getTime());
              Assertions.assertNotNull(item.getType());
              Assertions.assertNotNull(item.getAmount());
              Assertions.assertNotNull(item.getFee());
              Assertions.assertNotNull(item.getAccountEquity());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getRemark());
              Assertions.assertNotNull(item.getOffset());
              Assertions.assertNotNull(item.getCurrency());
            });

    Assertions.assertNotNull(resp.getHasMore());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /** getMarginAccountDetail Get Account Detail - Margin /api/v1/margin/account */
  @Test
  public void testGetMarginAccountDetail() throws Exception {
    GetMarginAccountDetailResp resp = api.getMarginAccountDetail();
    Assertions.assertNotNull(resp.getDebtRatio());
    resp.getAccounts()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getCurrency());
              Assertions.assertNotNull(item.getTotalBalance());
              Assertions.assertNotNull(item.getAvailableBalance());
              Assertions.assertNotNull(item.getHoldBalance());
              Assertions.assertNotNull(item.getLiability());
              Assertions.assertNotNull(item.getMaxBorrowSize());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * getIsolatedMarginAccountListV1 Get Account List - Isolated Margin - V1
   * /api/v1/isolated/accounts
   */
  @Test
  public void testGetIsolatedMarginAccountListV1() throws Exception {
    GetIsolatedMarginAccountListV1Req.GetIsolatedMarginAccountListV1ReqBuilder builder =
        GetIsolatedMarginAccountListV1Req.builder();
    builder.balanceCurrency(GetIsolatedMarginAccountListV1Req.BalanceCurrencyEnum.BTC);
    GetIsolatedMarginAccountListV1Req req = builder.build();
    GetIsolatedMarginAccountListV1Resp resp = api.getIsolatedMarginAccountListV1(req);
    Assertions.assertNotNull(resp.getTotalConversionBalance());
    Assertions.assertNotNull(resp.getLiabilityConversionBalance());
    resp.getAssets()
        .forEach(
            item -> {
              Assertions.assertNotNull(item.getSymbol());
              Assertions.assertNotNull(item.getStatus());
              Assertions.assertNotNull(item.getDebtRatio());
              Assertions.assertNotNull(item.getBaseAsset());
              Assertions.assertNotNull(item.getQuoteAsset());
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }

  /**
   * getIsolatedMarginAccountDetailV1 Get Account Detail - Isolated Margin - V1
   * /api/v1/isolated/account/{symbol}
   */
  @Test
  public void testGetIsolatedMarginAccountDetailV1() throws Exception {
    GetIsolatedMarginAccountDetailV1Req.GetIsolatedMarginAccountDetailV1ReqBuilder builder =
        GetIsolatedMarginAccountDetailV1Req.builder();
    builder.symbol("BTC-USDT");
    GetIsolatedMarginAccountDetailV1Req req = builder.build();
    GetIsolatedMarginAccountDetailV1Resp resp = api.getIsolatedMarginAccountDetailV1(req);
    Assertions.assertNotNull(resp.getSymbol());
    Assertions.assertNotNull(resp.getStatus());
    Assertions.assertNotNull(resp.getDebtRatio());
    Assertions.assertNotNull(resp.getBaseAsset());
    Assertions.assertNotNull(resp.getQuoteAsset());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
