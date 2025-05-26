<?php

namespace Tests\e2e\rest\CopyTrading;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\AddIsolatedMarginReq;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\AddOrderReq;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\AddOrderTestReq;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\AddTPSLOrderReq;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\CancelOrderByClientOidReq;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\CancelOrderByIdReq;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\FuturesApi;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\GetMaxOpenSizeReq;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\GetMaxWithdrawMarginReq;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\ModifyAutoDepositStatusReq;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\ModifyIsolatedMarginRiskLimtReq;
use KuCoin\UniversalSDK\Generate\CopyTrading\Futures\RemoveIsolatedMarginReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class FuturesTest extends TestCase
{
    /**
     * @var FuturesApi $api
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
        $this->api = $kucoinRestService->getCopytradingService()->getFuturesApi();
    }


    /**
     * addOrder
     * Add Order
     * /api/v1/copy-trade/futures/orders
     */
    public function testAddOrder()
    {
        $builder = AddOrderReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("XBTUSDTM")->
        setLeverage(3.0)->setType("limit")->setMarginMode("ISOLATED")
            ->setPrice("1")->setSize(1);
        $req = $builder->build();
        $resp = $this->api->addOrder($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addOrderTest
     * Add Order Test
     * /api/v1/copy-trade/futures/orders/test
     */
    public function testAddOrderTest()
    {
        $builder = AddOrderTestReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("XBTUSDTM")->
        setLeverage(3.0)->setType("limit")->setMarginMode("ISOLATED")
            ->setPrice("1")->setSize(1);
        $req = $builder->build();
        $resp = $this->api->addOrderTest($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addTPSLOrder
     * Add Take Profit And Stop Loss Order
     * /api/v1/copy-trade/futures/st-orders
     */
    public function testAddTPSLOrder()
    {
        $builder = AddTPSLOrderReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("XBTUSDTM")->
        setLeverage(3.0)->setType("limit")->setMarginMode("ISOLATED")->setStopPriceType("TP")
            ->setPrice("10000")->setTriggerStopUpPrice("8000")->setTriggerStopDownPrice("12000")->setSize(1);
        $req = $builder->build();
        $resp = $this->api->addTPSLOrder($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelOrderById
     * Cancel Order By OrderId
     * /api/v1/copy-trade/futures/orders
     */
    public function testCancelOrderById()
    {
        $builder = CancelOrderByIdReq::builder();
        $builder->setOrderId("312410953021030400");
        $req = $builder->build();
        $resp = $this->api->cancelOrderById($req);
        foreach ($resp->cancelledOrderIds as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelOrderByClientOid
     * Cancel Order By ClientOid
     * /api/v1/copy-trade/futures/orders/client-order
     */
    public function testCancelOrderByClientOid()
    {
        $builder = CancelOrderByClientOidReq::builder();
        $builder->setSymbol("XBTUSDTM")->setClientOid("bfccb19c-55d7-4b28-bcf6-09abf80fa26d");
        $req = $builder->build();
        $resp = $this->api->cancelOrderByClientOid($req);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getMaxOpenSize
     * Get Max Open Size
     * /api/v1/copy-trade/futures/get-max-open-size
     */
    public function testGetMaxOpenSize()
    {
        $builder = GetMaxOpenSizeReq::builder();
        $builder->setSymbol("XBTUSDTM")->setPrice(0.1)->setLeverage(10);
        $req = $builder->build();
        $resp = $this->api->getMaxOpenSize($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->maxBuyOpenSize);
        self::assertNotNull($resp->maxSellOpenSize);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getMaxWithdrawMargin
     * Get Max Withdraw Margin
     * /api/v1/copy-trade/futures/position/margin/max-withdraw-margin
     */
    public function testGetMaxWithdrawMargin()
    {
        $builder = GetMaxWithdrawMarginReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getMaxWithdrawMargin($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addIsolatedMargin
     * Add Isolated Margin
     * /api/v1/copy-trade/futures/position/margin/deposit-margin
     */
    public function testAddIsolatedMargin()
    {
        $builder = AddIsolatedMarginReq::builder();
        $builder->setSymbol("XBTUSDTM")->setMargin(3)->setBizNo(uuid::uuid4()->toString());
        $req = $builder->build();
        $resp = $this->api->addIsolatedMargin($req);
        self::assertNotNull($resp->id);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->autoDeposit);
        self::assertNotNull($resp->maintMarginReq);
        self::assertNotNull($resp->riskLimit);
        self::assertNotNull($resp->realLeverage);
        self::assertNotNull($resp->crossMode);
        self::assertNotNull($resp->delevPercentage);
        self::assertNotNull($resp->openingTimestamp);
        self::assertNotNull($resp->currentTimestamp);
        self::assertNotNull($resp->currentQty);
        self::assertNotNull($resp->currentCost);
        self::assertNotNull($resp->currentComm);
        self::assertNotNull($resp->unrealisedCost);
        self::assertNotNull($resp->realisedGrossCost);
        self::assertNotNull($resp->realisedCost);
        self::assertNotNull($resp->isOpen);
        self::assertNotNull($resp->markPrice);
        self::assertNotNull($resp->markValue);
        self::assertNotNull($resp->posCost);
        self::assertNotNull($resp->posCross);
        self::assertNotNull($resp->posInit);
        self::assertNotNull($resp->posComm);
        self::assertNotNull($resp->posLoss);
        self::assertNotNull($resp->posMargin);
        self::assertNotNull($resp->posMaint);
        self::assertNotNull($resp->maintMargin);
        self::assertNotNull($resp->realisedGrossPnl);
        self::assertNotNull($resp->realisedPnl);
        self::assertNotNull($resp->unrealisedPnl);
        self::assertNotNull($resp->unrealisedPnlPcnt);
        self::assertNotNull($resp->unrealisedRoePcnt);
        self::assertNotNull($resp->avgEntryPrice);
        self::assertNotNull($resp->liquidationPrice);
        self::assertNotNull($resp->bankruptPrice);
        self::assertNotNull($resp->settleCurrency);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * removeIsolatedMargin
     * Remove Isolated Margin
     * /api/v1/copy-trade/futures/position/margin/withdraw-margin
     */
    public function testRemoveIsolatedMargin()
    {
        $builder = RemoveIsolatedMarginReq::builder();
        $builder->setSymbol("XBTUSDTM")->setWithdrawAmount(0.000001);
        $req = $builder->build();
        $resp = $this->api->removeIsolatedMargin($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * modifyIsolatedMarginRiskLimt
     * Modify Isolated Margin Risk Limit
     * /api/v1/copy-trade/futures/position/risk-limit-level/change
     */
    public function testModifyIsolatedMarginRiskLimt()
    {
        $builder = ModifyIsolatedMarginRiskLimtReq::builder();
        $builder->setSymbol("XBTUSDTM")->setLevel(1);
        $req = $builder->build();
        $resp = $this->api->modifyIsolatedMarginRiskLimt($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * modifyAutoDepositStatus
     * Modify Isolated Margin Auto-Deposit Status
     * /api/v1/copy-trade/futures/position/margin/auto-deposit-status
     */
    public function testModifyAutoDepositStatus()
    {
        $builder = ModifyAutoDepositStatusReq::builder();
        $builder->setSymbol("XBTUSDTM")->setStatus(true);
        $req = $builder->build();
        $resp = $this->api->modifyAutoDepositStatus($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }
}