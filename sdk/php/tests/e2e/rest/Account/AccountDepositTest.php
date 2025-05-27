<?php

namespace Tests\e2e\rest\Account;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Account\Deposit\AddDepositAddressV1Req;
use KuCoin\UniversalSDK\Generate\Account\Deposit\AddDepositAddressV3Req;
use KuCoin\UniversalSDK\Generate\Account\Deposit\DepositApi;
use KuCoin\UniversalSDK\Generate\Account\Deposit\GetDepositAddressV1Req;
use KuCoin\UniversalSDK\Generate\Account\Deposit\GetDepositAddressV2Req;
use KuCoin\UniversalSDK\Generate\Account\Deposit\GetDepositAddressV3Req;
use KuCoin\UniversalSDK\Generate\Account\Deposit\GetDepositHistoryOldReq;
use KuCoin\UniversalSDK\Generate\Account\Deposit\GetDepositHistoryReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class AccountDepositTest extends TestCase
{
    /**
     * @var DepositApi $api
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
        $this->api = $kucoinRestService->getAccountService()->getDepositApi();
    }


    /**
     * addDepositAddressV3
     * Add Deposit Address (V3)
     * /api/v3/deposit-address/create
     */
    public function testAddDepositAddressV3()
    {
        $builder = AddDepositAddressV3Req::builder();
        $builder->setCurrency("TON")->setChain("ton")->setTo("main")->setAmount("1");
        $req = $builder->build();
        $resp = $this->api->addDepositAddressV3($req);
        self::assertNotNull($resp->address);
        self::assertNotNull($resp->memo);
        self::assertNotNull($resp->chainId);
        self::assertNotNull($resp->to);
        self::assertNotNull($resp->expirationDate);
        self::assertNotNull($resp->currency);
        self::assertNotNull($resp->chainName);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getDepositAddressV3
     * Get Deposit Address (V3)
     * /api/v3/deposit-addresses
     */
    public function testGetDepositAddressV3()
    {
        $builder = GetDepositAddressV3Req::builder();
        $builder->setCurrency("TON")->setAmount("1")->setChain("ton");
        $req = $builder->build();
        $resp = $this->api->getDepositAddressV3($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->address);
            self::assertNotNull($item->memo);
            self::assertNotNull($item->chainId);
            self::assertNotNull($item->to);
            self::assertNotNull($item->expirationDate);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->contractAddress);
            self::assertNotNull($item->chainName);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getDepositHistory
     * Get Deposit History
     * /api/v1/deposits
     */
    public function testGetDepositHistory()
    {
        $builder = GetDepositHistoryReq::builder();
        $builder->setCurrency("USDT")->setStartAt(1673496371000)->setEndAt(1705032371000);
        $req = $builder->build();
        $resp = $this->api->getDepositHistory($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->chain);
            self::assertNotNull($item->status);
            self::assertNotNull($item->address);
            self::assertNotNull($item->memo);
            self::assertNotNull($item->isInner);
            self::assertNotNull($item->amount);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->walletTxId);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->updatedAt);
            self::assertNotNull($item->remark);
            self::assertNotNull($item->arrears);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getDepositAddressV2
     * Get Deposit Addresses (V2)
     * /api/v2/deposit-addresses
     */
    public function testGetDepositAddressV2()
    {
        $builder = GetDepositAddressV2Req::builder();
        $builder->setCurrency("USDT")->setChain("SOL");
        $req = $builder->build();
        $resp = $this->api->getDepositAddressV2($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->address);
            self::assertNotNull($item->memo);
            self::assertNotNull($item->chain);
            self::assertNotNull($item->chainId);
            self::assertNotNull($item->to);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->contractAddress);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getDepositAddressV1
     * Get Deposit Addresses - V1
     * /api/v1/deposit-addresses
     */
    public function testGetDepositAddressV1()
    {
        $builder = GetDepositAddressV1Req::builder();
        $builder->setCurrency("USDT")->setChain("eth");
        $req = $builder->build();
        $resp = $this->api->getDepositAddressV1($req);
        self::assertNotNull($resp->address);
        self::assertNotNull($resp->memo);
        self::assertNotNull($resp->chain);
        self::assertNotNull($resp->chainId);
        self::assertNotNull($resp->to);
        self::assertNotNull($resp->currency);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getDepositHistoryOld
     * Get Deposit History - Old
     * /api/v1/hist-deposits
     */
    public function testGetDepositHistoryOld()
    {
        $builder = GetDepositHistoryOldReq::builder();
        $builder->setStartAt(1714492800000)->setEndAt(1732982400000);
        $req = $builder->build();
        $resp = $this->api->getDepositHistoryOld($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->createAt);
            self::assertNotNull($item->amount);
            self::assertNotNull($item->walletTxId);
            self::assertNotNull($item->isInner);
            self::assertNotNull($item->status);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addDepositAddressV1
     * Add Deposit Address - V1
     * /api/v1/deposit-addresses
     */
    public function testAddDepositAddressV1()
    {
        $builder = AddDepositAddressV1Req::builder();
        $builder->setCurrency("AGLD")->setChain("eth")->setTo("MAIN");
        $req = $builder->build();
        $resp = $this->api->addDepositAddressV1($req);
        self::assertNotNull($resp->address);
        self::assertNotNull($resp->chain);
        self::assertNotNull($resp->chainId);
        self::assertNotNull($resp->to);
        self::assertNotNull($resp->currency);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

}