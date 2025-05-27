<?php

namespace Tests\e2e\rest\Broker;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\AddSubAccountApiReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\AddSubAccountReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\DeleteSubAccountAPIReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\GetBrokerInfoReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\GetDepositDetailReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\GetDepositListReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\GetRebaseReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\GetSubAccountAPIReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\GetSubAccountReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\GetTransferHistoryReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\GetWithdrawDetailReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\ModifySubAccountApiReq;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\NDBrokerApi;
use KuCoin\UniversalSDK\Generate\Broker\Ndbroker\TransferReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class NDBrokerTest extends TestCase
{
    /**
     * @var NDBrokerApi $api
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
        $this->api = $kucoinRestService->getBrokerService()->getNDBrokerApi();
    }


    /**
     * getBrokerInfo
     * Get Broker Info
     * /api/v1/broker/nd/info
     */
    public function testGetBrokerInfo()
    {
        $builder = GetBrokerInfoReq::builder();
        $builder->setBegin("20240610")->setEnd("20241010")->setTradeType(1);
        $req = $builder->build();
        $resp = $this->api->getBrokerInfo($req);
        self::assertNotNull($resp->accountSize);
        self::assertNotNull($resp->level);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addSubAccount
     * Add sub-account
     * /api/v1/broker/nd/account
     */
    public function testAddSubAccount()
    {
        $builder = AddSubAccountReq::builder();
        $builder->setAccountName("sdk_test_4");
        $req = $builder->build();
        $resp = $this->api->addSubAccount($req);
        self::assertNotNull($resp->accountName);
        self::assertNotNull($resp->uid);
        self::assertNotNull($resp->createdAt);
        self::assertNotNull($resp->level);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSubAccount
     * Get sub-account
     * /api/v1/broker/nd/account
     */
    public function testGetSubAccount()
    {
        $builder = GetSubAccountReq::builder();
        $builder->setUid("****");
        $req = $builder->build();
        $resp = $this->api->getSubAccount($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->accountName);
            self::assertNotNull($item->uid);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->level);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addSubAccountApi
     * Add sub-account API
     * /api/v1/broker/nd/account/apikey
     */
    public function testAddSubAccountApi()
    {
        $builder = AddSubAccountApiReq::builder();
        $builder->setUid("*****")->setPassphrase("****")->setIpWhitelist(['127.0.0.1', '192.168.1.1'])
            ->setPermissions(["general", "spot"])->setLabel("label");
        $req = $builder->build();
        $resp = $this->api->addSubAccountApi($req);
        self::assertNotNull($resp->uid);
        self::assertNotNull($resp->label);
        self::assertNotNull($resp->apiKey);
        self::assertNotNull($resp->secretKey);
        self::assertNotNull($resp->apiVersion);
        foreach ($resp->permissions as $item) {
            self::assertNotNull($item->permission);
        }

        foreach ($resp->ipWhitelist as $item) {
            self::assertNotNull($item->ipWhitelist);
        }

        self::assertNotNull($resp->createdAt);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSubAccountAPI
     * Get sub-account API
     * /api/v1/broker/nd/account/apikey
     */
    public function testGetSubAccountAPI()
    {
        $builder = GetSubAccountAPIReq::builder();
        $builder->setUid("****")->setApiKey("6825a92bc1dfd90001057010");
        $req = $builder->build();
        $resp = $this->api->getSubAccountAPI($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->uid);
            self::assertNotNull($item->label);
            self::assertNotNull($item->apiKey);
            self::assertNotNull($item->apiVersion);
            self::assertNotNull($item->permissions);
            self::assertNotNull($item->ipWhitelist);
            self::assertNotNull($item->createdAt);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * modifySubAccountApi
     * Modify sub-account API
     * /api/v1/broker/nd/account/update-apikey
     */
    public function testModifySubAccountApi()
    {
        $builder = ModifySubAccountApiReq::builder();
        $builder->setUid("****")->setApiKey("****")
            ->setIpWhitelist(['127.0.0.1', '192.168.1.1'])
            ->setPermissions(["general", "spot"])->setLabel("label");
        $req = $builder->build();
        $resp = $this->api->modifySubAccountApi($req);
        self::assertNotNull($resp->uid);
        self::assertNotNull($resp->label);
        self::assertNotNull($resp->apiKey);
        self::assertNotNull($resp->apiVersion);
        foreach ($resp->permissions as $item) {
            self::assertNotNull($item);
        }

        foreach ($resp->ipWhitelist as $item) {
            self::assertNotNull($item);
        }

        self::assertNotNull($resp->createdAt);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * deleteSubAccountAPI
     * Delete sub-account API
     * /api/v1/broker/nd/account/apikey
     */
    public function testDeleteSubAccountAPI()
    {
        $builder = DeleteSubAccountAPIReq::builder();
        $builder->setUid("***")->setApiKey("***");
        $req = $builder->build();
        $resp = $this->api->deleteSubAccountAPI($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * transfer
     * Transfer
     * /api/v1/broker/nd/transfer
     */
    public function testTransfer()
    {
        $builder = TransferReq::builder();
        $builder->setCurrency("USDT")->setAmount("0.01")->setDirection("OUT")->setAccountType("TRADE")->
        setSpecialUid("****")->setSpecialAccountType("MAIN")->setClientOid(Uuid::uuid4()->toString());
        $req = $builder->build();
        $resp = $this->api->transfer($req);
        self::assertNotNull($resp->orderId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getTransferHistory
     * Get Transfer History
     * /api/v3/broker/nd/transfer/detail
     */
    public function testGetTransferHistory()
    {
        $builder = GetTransferHistoryReq::builder();
        $builder->setOrderId("6825aa624075ee00071c2e2f");
        $req = $builder->build();
        $resp = $this->api->getTransferHistory($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->currency);
        self::assertNotNull($resp->amount);
        self::assertNotNull($resp->fromUid);
        self::assertNotNull($resp->fromAccountType);
        self::assertNotNull($resp->fromAccountTag);
        self::assertNotNull($resp->toUid);
        self::assertNotNull($resp->toAccountType);
        self::assertNotNull($resp->toAccountTag);
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->createdAt);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getDepositList
     * Get Deposit List
     * /api/v1/asset/ndbroker/deposit/list
     */
    public function testGetDepositList()
    {
        $builder = GetDepositListReq::builder();
        $builder->setCurrency("USDT");
        $req = $builder->build();
        $resp = $this->api->getDepositList($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->uid);
            self::assertNotNull($item->hash);
            self::assertNotNull($item->address);
            self::assertNotNull($item->memo);
            self::assertNotNull($item->amount);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->isInner);
            self::assertNotNull($item->walletTxId);
            self::assertNotNull($item->status);
            self::assertNotNull($item->remark);
            self::assertNotNull($item->chain);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->updatedAt);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getDepositDetail
     * Get Deposit Detail
     * /api/v3/broker/nd/deposit/detail
     */
    public function testGetDepositDetail()
    {
        $builder = GetDepositDetailReq::builder();
        $builder->setCurrency("USDT")->setHash("6724e363a492800007ec602b");
        $req = $builder->build();
        $resp = $this->api->getDepositDetail($req);
        self::assertNotNull($resp->chain);
        self::assertNotNull($resp->hash);
        self::assertNotNull($resp->walletTxId);
        self::assertNotNull($resp->uid);
        self::assertNotNull($resp->updatedAt);
        self::assertNotNull($resp->amount);
        self::assertNotNull($resp->memo);
        self::assertNotNull($resp->fee);
        self::assertNotNull($resp->address);
        self::assertNotNull($resp->remark);
        self::assertNotNull($resp->isInner);
        self::assertNotNull($resp->currency);
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->createdAt);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getWithdrawDetail
     * Get Withdraw Detail
     * /api/v3/broker/nd/withdraw/detail
     */
    public function testGetWithdrawDetail()
    {
        $builder = GetWithdrawDetailReq::builder();
        $builder->setWithdrawalId("674686fa1ac01f0007b25768");
        $req = $builder->build();
        $resp = $this->api->getWithdrawDetail($req);
        self::assertNotNull($resp->id);
        self::assertNotNull($resp->chain);
        self::assertNotNull($resp->uid);
        self::assertNotNull($resp->amount);
        self::assertNotNull($resp->memo);
        self::assertNotNull($resp->fee);
        self::assertNotNull($resp->address);
        self::assertNotNull($resp->remark);
        self::assertNotNull($resp->isInner);
        self::assertNotNull($resp->currency);
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->createdAt);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getRebase
     * Get Broker Rebate
     * /api/v1/broker/nd/rebase/download
     */
    public function testGetRebase()
    {
        $builder = GetRebaseReq::builder();
        $builder->setBegin('20240610')->setEnd('20241010')->setTradeType(1);
        $req = $builder->build();
        $resp = $this->api->getRebase($req);
        self::assertNotNull($resp->url);
        Logger::info($resp->jsonSerialize($this->serializer));
    }


}