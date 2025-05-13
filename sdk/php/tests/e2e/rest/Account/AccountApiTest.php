<?php

namespace Tests\e2e\rest\Account;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Account\Account\AccountApi;
use KuCoin\UniversalSDK\Generate\Account\Account\GetCrossMarginAccountReq;
use KuCoin\UniversalSDK\Generate\Account\Account\GetFuturesAccountReq;
use KuCoin\UniversalSDK\Generate\Account\Account\GetFuturesLedgerReq;
use KuCoin\UniversalSDK\Generate\Account\Account\GetIsolatedMarginAccountDetailV1Req;
use KuCoin\UniversalSDK\Generate\Account\Account\GetIsolatedMarginAccountListV1Req;
use KuCoin\UniversalSDK\Generate\Account\Account\GetIsolatedMarginAccountReq;
use KuCoin\UniversalSDK\Generate\Account\Account\GetMarginHFLedgerReq;
use KuCoin\UniversalSDK\Generate\Account\Account\GetSpotAccountDetailReq;
use KuCoin\UniversalSDK\Generate\Account\Account\GetSpotAccountListReq;
use KuCoin\UniversalSDK\Generate\Account\Account\GetSpotHFLedgerReq;
use KuCoin\UniversalSDK\Generate\Account\Account\GetSpotLedgerReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class AccountApiTest extends TestCase
{
    /**
     * @var AccountApi $api
     */
    private $api;

    /**
     * @var Serializer $serializer
     */
    private $serializer;

    protected function setUp(): void
    {

        $this->serializer = SerializerBuilder::create()
            ->addDefaultHandlers()
            ->configureHandlers(function ($handlerRegistry) {
                $handlerRegistry->registerSubscribingHandler(
                    new JsonSerializedHandler()
                );
            })
            ->build();

        // Retrieve API secret information from environment variables
        $key = getenv('API_KEY') ?: '';
        $secret = getenv('API_SECRET') ?: '';
        $passphrase = getenv('API_PASSPHRASE') ?: '';

        // Optional: Retrieve broker secret information from environment variables; applicable for broker API only
        $brokerName = getenv('BROKER_NAME');
        $brokerKey = getenv('BROKER_KEY');
        $brokerPartner = getenv('BROKER_PARTNER');

        // Set specific options, others will fall back to default values
        $httpTransportOption = (new TransportOptionBuilder())
            ->setKeepAlive(true)
            ->setMaxConnections(10)
            ->build();

        // Create a client using the specified options
        $clientOption = (new ClientOptionBuilder())
            ->setKey($key)
            ->setSecret($secret)
            ->setPassphrase($passphrase)
            ->setBrokerName($brokerName)
            ->setBrokerKey($brokerKey)
            ->setBrokerPartner($brokerPartner)
            ->setSpotEndpoint(Constants::GLOBAL_API_ENDPOINT)
            ->setFuturesEndpoint(Constants::GLOBAL_FUTURES_API_ENDPOINT)
            ->setBrokerEndpoint(Constants::GLOBAL_BROKER_API_ENDPOINT)
            ->setTransportOption($httpTransportOption)
            ->build();

        $client = new DefaultClient($clientOption);
        $kucoinRestService = $client->restService();
        $this->api = $kucoinRestService->getAccountService()->getAccountApi();
    }


    /**
     * getAccountInfo
     * Get Account Summary Info
     * /api/v2/user-info
     */
    public function testGetAccountInfo()
    {
        $resp = $this->api->getAccountInfo();
        self::assertNotNull($resp->level);
        self::assertNotNull($resp->subQuantity);
        self::assertNotNull($resp->spotSubQuantity);
        self::assertNotNull($resp->marginSubQuantity);
        self::assertNotNull($resp->futuresSubQuantity);
        self::assertNotNull($resp->optionSubQuantity);
        self::assertNotNull($resp->maxSubQuantity);
        self::assertNotNull($resp->maxDefaultSubQuantity);
        self::assertNotNull($resp->maxSpotSubQuantity);
        self::assertNotNull($resp->maxMarginSubQuantity);
        self::assertNotNull($resp->maxFuturesSubQuantity);
        self::assertNotNull($resp->maxOptionSubQuantity);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getApikeyInfo
     * Get Apikey Info
     * /api/v1/user/api-key
     */
    public function testGetApikeyInfo()
    {
        $resp = $this->api->getApikeyInfo();
        self::assertNotNull($resp->remark);
        self::assertNotNull($resp->apiKey);
        self::assertNotNull($resp->apiVersion);
        self::assertNotNull($resp->permission);
        self::assertNotNull($resp->ipWhitelist);
        self::assertNotNull($resp->createdAt);
        self::assertNotNull($resp->uid);
        self::assertNotNull($resp->isMaster);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotAccountType
     * Get Account Type - Spot
     * /api/v1/hf/accounts/opened
     */
    public function testGetSpotAccountType()
    {
        $resp = $this->api->getSpotAccountType();
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotAccountList
     * Get Account List - Spot
     * /api/v1/accounts
     */
    public function testGetSpotAccountList()
    {
        $builder = GetSpotAccountListReq::builder();
        $builder->setCurrency("")->setType("main");
        $req = $builder->build();
        $resp = $this->api->getSpotAccountList($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->type);
            self::assertNotNull($item->balance);
            self::assertNotNull($item->available);
            self::assertNotNull($item->holds);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotAccountDetail
     * Get Account Detail - Spot
     * /api/v1/accounts/{accountId}
     */
    public function testGetSpotAccountDetail()
    {
        $builder = GetSpotAccountDetailReq::builder();
        $builder->setAccountId("6582f6a48c171f000769b867");
        $req = $builder->build();
        $resp = $this->api->getSpotAccountDetail($req);
        self::assertNotNull($resp->currency);
        self::assertNotNull($resp->balance);
        self::assertNotNull($resp->available);
        self::assertNotNull($resp->holds);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getCrossMarginAccount
     * Get Account - Cross Margin
     * /api/v3/margin/accounts
     */
    public function testGetCrossMarginAccount()
    {
        $builder = GetCrossMarginAccountReq::builder();
        $builder->setQuoteCurrency("USDT")->setQueryType("MARGIN");
        $req = $builder->build();
        $resp = $this->api->getCrossMarginAccount($req);
        self::assertNotNull($resp->totalAssetOfQuoteCurrency);
        self::assertNotNull($resp->totalLiabilityOfQuoteCurrency);
        self::assertNotNull($resp->debtRatio);
        self::assertNotNull($resp->status);
        foreach ($resp->accounts as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->total);
            self::assertNotNull($item->available);
            self::assertNotNull($item->hold);
            self::assertNotNull($item->liability);
            self::assertNotNull($item->maxBorrowSize);
            self::assertNotNull($item->borrowEnabled);
            self::assertNotNull($item->transferInEnabled);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getIsolatedMarginAccount
     * Get Account - Isolated Margin
     * /api/v3/isolated/accounts
     */
    public function testGetIsolatedMarginAccount()
    {
        $builder = GetIsolatedMarginAccountReq::builder();
        $builder->setSymbol("BTC-USDT")->setQuoteCurrency("USDT")->setQueryType("ISOLATED");
        $req = $builder->build();
        $resp = $this->api->getIsolatedMarginAccount($req);
        self::assertNotNull($resp->totalAssetOfQuoteCurrency);
        self::assertNotNull($resp->totalLiabilityOfQuoteCurrency);
        self::assertNotNull($resp->timestamp);
        foreach ($resp->assets as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->status);
            self::assertNotNull($item->debtRatio);
            self::assertNotNull($item->baseAsset);
            self::assertNotNull($item->quoteAsset);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getFuturesAccount
     * Get Account - Futures
     * /api/v1/account-overview
     */
    public function testGetFuturesAccount()
    {
        $builder = GetFuturesAccountReq::builder();
        $builder->setCurrency("XBT");
        $req = $builder->build();
        $resp = $this->api->getFuturesAccount($req);
        self::assertNotNull($resp->accountEquity);
        self::assertNotNull($resp->unrealisedPNL);
        self::assertNotNull($resp->marginBalance);
        self::assertNotNull($resp->positionMargin);
        self::assertNotNull($resp->orderMargin);
        self::assertNotNull($resp->frozenFunds);
        self::assertNotNull($resp->availableBalance);
        self::assertNotNull($resp->currency);
        self::assertNotNull($resp->riskRatio);
        self::assertNotNull($resp->maxWithdrawAmount);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotLedger
     * Get Account Ledgers - Spot/Margin
     * /api/v1/accounts/ledgers
     */
    public function testGetSpotLedger()
    {
        $builder = GetSpotLedgerReq::builder();
        $builder->setCurrency("USDT")->setDirection("in")->setCurrentPage(1)->setPageSize(100);
        $req = $builder->build();
        $resp = $this->api->getSpotLedger($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->amount);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->balance);
            self::assertNotNull($item->accountType);
            self::assertNotNull($item->bizType);
            self::assertNotNull($item->direction);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->context);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotHFLedger
     * Get Account Ledgers - Trade_hf
     * /api/v1/hf/accounts/ledgers
     */
    public function testGetSpotHFLedger()
    {
        $builder = GetSpotHFLedgerReq::builder();
        $builder->setCurrency("USDT");
        $req = $builder->build();
        $resp = $this->api->getSpotHFLedger($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->amount);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->tax);
            self::assertNotNull($item->balance);
            self::assertNotNull($item->accountType);
            self::assertNotNull($item->bizType);
            self::assertNotNull($item->direction);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->context);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getMarginHFLedger
     * Get Account Ledgers - Margin_hf
     * /api/v3/hf/margin/account/ledgers
     */
    public function testGetMarginHFLedger()
    {
        $builder = GetMarginHFLedgerReq::builder();
        $builder->setCurrency("USDT");
        $req = $builder->build();
        $resp = $this->api->getMarginHFLedger($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->amount);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->balance);
            self::assertNotNull($item->accountType);
            self::assertNotNull($item->bizType);
            self::assertNotNull($item->direction);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->context);
            self::assertNotNull($item->tax);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getFuturesLedger
     * Get Account Ledgers - Futures
     * /api/v1/transaction-history
     */
    public function testGetFuturesLedger()
    {
        $builder = GetFuturesLedgerReq::builder();
        $builder->setCurrency("USDT");
        $req = $builder->build();
        $resp = $this->api->getFuturesLedger($req);
        foreach ($resp->dataList as $item) {
            self::assertNotNull($item->time);
            self::assertNotNull($item->type);
            self::assertNotNull($item->amount);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->accountEquity);
            self::assertNotNull($item->status);
            self::assertNotNull($item->remark);
            self::assertNotNull($item->offset);
            self::assertNotNull($item->currency);
        }

        self::assertNotNull($resp->hasMore);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getMarginAccountDetail
     * Get Account Detail - Margin
     * /api/v1/margin/account
     */
    public function testGetMarginAccountDetail()
    {
        $resp = $this->api->getMarginAccountDetail();
        self::assertNotNull($resp->debtRatio);
        foreach ($resp->accounts as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->totalBalance);
            self::assertNotNull($item->availableBalance);
            self::assertNotNull($item->holdBalance);
            self::assertNotNull($item->liability);
            self::assertNotNull($item->maxBorrowSize);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getIsolatedMarginAccountListV1
     * Get Account List - Isolated Margin - V1
     * /api/v1/isolated/accounts
     */
    public function testGetIsolatedMarginAccountListV1()
    {
        $builder = GetIsolatedMarginAccountListV1Req::builder();
        $builder->setBalanceCurrency("BTC");
        $req = $builder->build();
        $resp = $this->api->getIsolatedMarginAccountListV1($req);
        self::assertNotNull($resp->totalConversionBalance);
        self::assertNotNull($resp->liabilityConversionBalance);
        foreach ($resp->assets as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->status);
            self::assertNotNull($item->debtRatio);
            self::assertNotNull($item->baseAsset);
            self::assertNotNull($item->quoteAsset);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getIsolatedMarginAccountDetailV1
     * Get Account Detail - Isolated Margin - V1
     * /api/v1/isolated/account/{symbol}
     */
    public function testGetIsolatedMarginAccountDetailV1()
    {
        $builder = GetIsolatedMarginAccountDetailV1Req::builder();
        $builder->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->getIsolatedMarginAccountDetailV1($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->debtRatio);
        self::assertNotNull($resp->baseAsset);
        self::assertNotNull($resp->quoteAsset);
        Logger::info($resp->jsonSerialize($this->serializer));
    }


}