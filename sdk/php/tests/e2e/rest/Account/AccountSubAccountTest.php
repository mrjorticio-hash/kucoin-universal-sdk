<?php

namespace Tests\e2e\rest\Account;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Extension\Interceptor\Logging;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\AddSubAccountApiReq;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\AddSubAccountFuturesPermissionReq;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\AddSubAccountMarginPermissionReq;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\AddSubAccountReq;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\DeleteSubAccountApiReq;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\GetFuturesSubAccountListV2Req;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\GetSpotSubAccountDetailReq;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\GetSpotSubAccountListV2Req;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\GetSpotSubAccountsSummaryV2Req;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\GetSubAccountApiListReq;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\ModifySubAccountApiReq;
use KuCoin\UniversalSDK\Generate\Account\Subaccount\SubAccountApi;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class AccountSubAccountTest extends TestCase
{
    /**
     * @var SubAccountApi $api
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
        $this->api = $kucoinRestService->getAccountService()->getSubAccountApi();
    }


    /**
     * addSubAccount
     * Add sub-account
     * /api/v2/sub/user/created
     */
    public function testAddSubAccount()
    {
        $builder = AddSubAccountReq::builder();
        $builder->setPassword("***@123")->setRemarks("****@123")->setSubName("sdkTestN")->setAccess("Spot");
        $req = $builder->build();
        $resp = $this->api->addSubAccount($req);
        self::assertNotNull($resp->uid);
        self::assertNotNull($resp->subName);
        self::assertNotNull($resp->remarks);
        self::assertNotNull($resp->access);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addSubAccountMarginPermission
     * Add sub-account Margin Permission
     * /api/v3/sub/user/margin/enable
     */
    public function testAddSubAccountMarginPermission()
    {
        $builder = AddSubAccountMarginPermissionReq::builder();
        $builder->setUid("****");
        $req = $builder->build();
        $resp = $this->api->addSubAccountMarginPermission($req);
        self::assertNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addSubAccountFuturesPermission
     * Add sub-account Futures Permission
     * /api/v3/sub/user/futures/enable
     */
    public function testAddSubAccountFuturesPermission()
    {
        $builder = AddSubAccountFuturesPermissionReq::builder();
        $builder->setUid("*****");
        $req = $builder->build();
        $resp = $this->api->addSubAccountFuturesPermission($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotSubAccountsSummaryV2
     * Get sub-account List - Summary Info
     * /api/v2/sub/user
     */
    public function testGetSpotSubAccountsSummaryV2()
    {
        $builder = GetSpotSubAccountsSummaryV2Req::builder();
        $builder->setCurrentPage(1)->setPageSize(10);
        $req = $builder->build();
        $resp = $this->api->getSpotSubAccountsSummaryV2($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->userId);
            self::assertNotNull($item->uid);
            self::assertNotNull($item->subName);
            self::assertNotNull($item->status);
            self::assertNotNull($item->type);
            self::assertNotNull($item->access);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->remarks);
            self::assertNotNull($item->tradeTypes);
            self::assertNotNull($item->openedTradeTypes);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotSubAccountDetail
     * Get sub-account Detail - Balance
     * /api/v1/sub-accounts/{subUserId}
     */
    public function testGetSpotSubAccountDetail()
    {
        $builder = GetSpotSubAccountDetailReq::builder();
        $builder->setSubUserId("*****")->setIncludeBaseAmount(false)->
        setBaseCurrency("USDT")->setBaseAmount("0.1");
        $req = $builder->build();
        $resp = $this->api->getSpotSubAccountDetail($req);
        self::assertNotNull($resp->subUserId);
        self::assertNotNull($resp->subName);
        foreach ($resp->mainAccounts as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->balance);
            self::assertNotNull($item->available);
            self::assertNotNull($item->holds);
            self::assertNotNull($item->baseCurrency);
            self::assertNotNull($item->baseCurrencyPrice);
            self::assertNotNull($item->baseAmount);
            self::assertNotNull($item->tag);
        }

        foreach ($resp->tradeAccounts as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->balance);
            self::assertNotNull($item->available);
            self::assertNotNull($item->holds);
            self::assertNotNull($item->baseCurrency);
            self::assertNotNull($item->baseCurrencyPrice);
            self::assertNotNull($item->baseAmount);
            self::assertNotNull($item->tag);
        }

        foreach ($resp->marginAccounts as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->balance);
            self::assertNotNull($item->available);
            self::assertNotNull($item->holds);
            self::assertNotNull($item->baseCurrency);
            self::assertNotNull($item->baseCurrencyPrice);
            self::assertNotNull($item->baseAmount);
            self::assertNotNull($item->tag);
        }

        foreach ($resp->tradeHFAccounts as $item) {
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotSubAccountListV2
     * Get sub-account List - Spot Balance (V2)
     * /api/v2/sub-accounts
     */
    public function testGetSpotSubAccountListV2()
    {
        $builder = GetSpotSubAccountListV2Req::builder();
        $builder->setCurrentPage(1)->setPageSize(10);
        $req = $builder->build();
        $resp = $this->api->getSpotSubAccountListV2($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->subUserId);
            self::assertNotNull($item->subName);
            self::assertNotNull($item->mainAccounts);
            self::assertNotNull($item->tradeAccounts);
            self::assertNotNull($item->marginAccounts);
            self::assertNotNull($item->tradeHFAccounts);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getFuturesSubAccountListV2
     * Get sub-account List - Futures Balance (V2)
     * /api/v1/account-overview-all
     */
    public function testGetFuturesSubAccountListV2()
    {
        $builder = GetFuturesSubAccountListV2Req::builder();
        $builder->setCurrency("XBT");
        $req = $builder->build();
        $resp = $this->api->getFuturesSubAccountListV2($req);
        self::assertNotNull($resp->summary);
        foreach ($resp->accounts as $item) {
            self::assertNotNull($item->accountName);
            self::assertNotNull($item->accountEquity);
            self::assertNotNull($item->unrealisedPNL);
            self::assertNotNull($item->marginBalance);
            self::assertNotNull($item->positionMargin);
            self::assertNotNull($item->orderMargin);
            self::assertNotNull($item->frozenFunds);
            self::assertNotNull($item->availableBalance);
            self::assertNotNull($item->currency);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addSubAccountApi
     * Add sub-account API
     * /api/v1/sub/api-key
     */
    public function testAddSubAccountApi()
    {
        $builder = AddSubAccountApiReq::builder();
        $builder->setPassphrase("********")->setRemark("remark3")->
        setIpWhitelist("192.1.1.1")->setPermission("General,Spot")->setExpire('30')->setSubName("sdkTestN");
        $req = $builder->build();
        $resp = $this->api->addSubAccountApi($req);
        self::assertNotNull($resp->subName);
        self::assertNotNull($resp->remark);
        self::assertNotNull($resp->apiKey);
        self::assertNotNull($resp->apiSecret);
        self::assertNotNull($resp->apiVersion);
        self::assertNotNull($resp->passphrase);
        self::assertNotNull($resp->permission);
        self::assertNotNull($resp->ipWhitelist);
        self::assertNotNull($resp->createdAt);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * modifySubAccountApi
     * Modify sub-account API
     * /api/v1/sub/api-key/update
     */
    public function testModifySubAccountApi()
    {
        $builder = ModifySubAccountApiReq::builder();
        $builder->setPassphrase("****")->setApiKey("******")->
        setIpWhitelist("192.1.1.2")->setPermission("General,Spot")->setExpire('30')->setSubName("sdkTestN");
        $req = $builder->build();
        $resp = $this->api->modifySubAccountApi($req);
        self::assertNotNull($resp->subName);
        self::assertNotNull($resp->apiKey);
        self::assertNotNull($resp->permission);
        self::assertNotNull($resp->ipWhitelist);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSubAccountApiList
     * Get sub-account API List
     * /api/v1/sub/api-key
     */
    public function testGetSubAccountApiList()
    {
        $builder = GetSubAccountApiListReq::builder();
        $builder->setApiKey("*******")->setSubName("sdkTestN");
        $req = $builder->build();
        $resp = $this->api->getSubAccountApiList($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->subName);
            self::assertNotNull($item->remark);
            self::assertNotNull($item->apiKey);
            self::assertNotNull($item->apiVersion);
            self::assertNotNull($item->permission);
            self::assertNotNull($item->ipWhitelist);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->uid);
            self::assertNotNull($item->isMaster);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * deleteSubAccountApi
     * Delete sub-account API
     * /api/v1/sub/api-key
     */
    public function testDeleteSubAccountApi()
    {
        $builder = DeleteSubAccountApiReq::builder();
        $builder->setApiKey("*****")->setSubName("sdkTestN")->setPassphrase("****");
        $req = $builder->build();
        $resp = $this->api->deleteSubAccountApi($req);
        self::assertNotNull($resp->subName);
        self::assertNotNull($resp->apiKey);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotSubAccountsSummaryV1
     * Get sub-account List - Summary Info (V1)
     * /api/v1/sub/user
     */
    public function testGetSpotSubAccountsSummaryV1()
    {
        $resp = $this->api->getSpotSubAccountsSummaryV1();
        foreach ($resp->data as $item) {
            self::assertNotNull($item->userId);
            self::assertNotNull($item->uid);
            self::assertNotNull($item->subName);
            self::assertNotNull($item->type);
            self::assertNotNull($item->remarks);
            self::assertNotNull($item->access);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotSubAccountListV1
     * Get sub-account List - Spot Balance (V1)
     * /api/v1/sub-accounts
     */
    public function testGetSpotSubAccountListV1()
    {
        $resp = $this->api->getSpotSubAccountListV1();
        foreach ($resp->data as $item) {
            self::assertNotNull($item->subUserId);
            self::assertNotNull($item->subName);
            self::assertNotNull($item->mainAccounts);
            self::assertNotNull($item->tradeAccounts);
            self::assertNotNull($item->marginAccounts);
            self::assertNotNull($item->tradeHFAccounts);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }


}