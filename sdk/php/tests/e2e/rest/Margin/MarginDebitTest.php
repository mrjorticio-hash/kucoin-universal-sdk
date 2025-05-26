<?php

namespace Tests\e2e\rest\Margin;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Margin\Debit\BorrowReq;
use KuCoin\UniversalSDK\Generate\Margin\Debit\DebitApi;
use KuCoin\UniversalSDK\Generate\Margin\Debit\GetBorrowHistoryReq;
use KuCoin\UniversalSDK\Generate\Margin\Debit\GetInterestHistoryReq;
use KuCoin\UniversalSDK\Generate\Margin\Debit\GetRepayHistoryReq;
use KuCoin\UniversalSDK\Generate\Margin\Debit\ModifyLeverageReq;
use KuCoin\UniversalSDK\Generate\Margin\Debit\RepayReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class MarginDebitTest extends TestCase
{
    /**
     * @var DebitApi $api
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
        $this->api = $kucoinRestService->getMarginService()->getDebitApi();
    }


    /**
     * borrow
     * Borrow
     * /api/v3/margin/borrow
     */
    public function testBorrow()
    {
        $builder = BorrowReq::builder();
        $builder->setCurrency("USDT")->setSize(10.0)->setTimeInForce("IOC")->setSymbol("")->
        setIsIsolated(false)->setIsHf(true);
        $req = $builder->build();
        $resp = $this->api->borrow($req);
        self::assertNotNull($resp->orderNo);
        self::assertNotNull($resp->actualSize);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getBorrowHistory
     * Get Borrow History
     * /api/v3/margin/borrow
     */
    public function testGetBorrowHistory()
    {
        $builder = GetBorrowHistoryReq::builder();
        $builder->setCurrency("USDT")->setIsIsolated(false);
        $req = $builder->build();
        $resp = $this->api->getBorrowHistory($req);
        self::assertNotNull($resp->timestamp);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->orderNo);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->size);
            self::assertNotNull($item->actualSize);
            self::assertNotNull($item->status);
            self::assertNotNull($item->createdTime);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * repay
     * Repay
     * /api/v3/margin/repay
     */
    public function testRepay()
    {
        $builder = RepayReq::builder();
        $builder->setCurrency("USDT")->setSize("10.0")->setIsIsolated(false)->setIsHf(true);
        $req = $builder->build();
        $resp = $this->api->repay($req);
        self::assertNotNull($resp->timestamp);
        self::assertNotNull($resp->orderNo);
        self::assertNotNull($resp->actualSize);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getRepayHistory
     * Get Repay History
     * /api/v3/margin/repay
     */
    public function testGetRepayHistory()
    {
        $builder = GetRepayHistoryReq::builder();
        $builder->setCurrency("USDT")->setIsIsolated(false);
        $req = $builder->build();
        $resp = $this->api->getRepayHistory($req);
        self::assertNotNull($resp->timestamp);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->orderNo);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->size);
            self::assertNotNull($item->principal);
            self::assertNotNull($item->interest);
            self::assertNotNull($item->status);
            self::assertNotNull($item->createdTime);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getInterestHistory
     * Get Interest History.
     * /api/v3/margin/interest
     */
    public function testGetInterestHistory()
    {
        $builder = GetInterestHistoryReq::builder();
        $builder->setCurrency("USDT")->setIsIsolated(false);
        $req = $builder->build();
        $resp = $this->api->getInterestHistory($req);
        self::assertNotNull($resp->timestamp);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->dayRatio);
            self::assertNotNull($item->interestAmount);
            self::assertNotNull($item->createdTime);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * modifyLeverage
     * Modify Leverage
     * /api/v3/position/update-user-leverage
     */
    public function testModifyLeverage()
    {
        $builder = ModifyLeverageReq::builder();
        $builder->setSymbol("BTC-USDT")->setIsIsolated(true)->setLeverage(10);
        $req = $builder->build();
        $resp = $this->api->modifyLeverage($req);
        self::assertNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }


}