<?php

namespace Tests\e2e\rest\Margin;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Extension\Interceptor\Logging;
use KuCoin\UniversalSDK\Generate\Margin\Order\AddOrderReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\AddOrderTestReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\AddOrderTestV1Req;
use KuCoin\UniversalSDK\Generate\Margin\Order\AddOrderV1Req;
use KuCoin\UniversalSDK\Generate\Margin\Order\CancelAllOrdersBySymbolReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\CancelOrderByClientOidReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\CancelOrderByOrderIdReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\GetClosedOrdersReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\GetOpenOrdersReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\GetOrderByClientOidReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\GetOrderByOrderIdReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\GetSymbolsWithOpenOrderReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\GetTradeHistoryReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\OrderApi;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class MarginOrderTest extends TestCase
{
    /**
     * @var OrderApi $api
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
        $this->api = $kucoinRestService->getMarginService()->getOrderApi();
    }


    /**
     * addOrder
     * Add Order
     * /api/v3/hf/margin/order
     */
    public function testAddOrder()
    {
        $builder = AddOrderReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("DOGE-USDT")->setType("limit")
            ->setSize("100")->setPrice("0.001")->setIsIsolated(false)->setAutoBorrow(true)->setAutoRepay(true);
        $req = $builder->build();
        $resp = $this->api->addOrder($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->loanApplyId);
        self::assertNotNull($resp->borrowSize);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer), ["oid" => $req->clientOid]);
    }

    /**
     * addOrderTest
     * Add Order Test
     * /api/v3/hf/margin/order/test
     */
    public function testAddOrderTest()
    {
        $builder = AddOrderTestReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("DOGE-USDT")->setType("limit")
            ->setSize("100")->setPrice("0.01")->setIsIsolated(false)->setAutoBorrow(true)->setAutoRepay(true);
        $req = $builder->build();
        $resp = $this->api->addOrderTest($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->loanApplyId);
        self::assertNotNull($resp->borrowSize);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelOrderByOrderId
     * Cancel Order By OrderId
     * /api/v3/hf/margin/orders/{orderId}
     */
    public function testCancelOrderByOrderId()
    {
        $builder = CancelOrderByOrderIdReq::builder();
        $builder->setOrderId("68258cc8f87d5c000796251a")->setSymbol("DOGE-USDT");
        $req = $builder->build();
        $resp = $this->api->cancelOrderByOrderId($req);
        self::assertNotNull($resp->orderId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelOrderByClientOid
     * Cancel Order By ClientOid
     * /api/v3/hf/margin/orders/client-order/{clientOid}
     */
    public function testCancelOrderByClientOid()
    {
        $builder = CancelOrderByClientOidReq::builder();
        $builder->setClientOid("3db200e0-3b94-43a8-ad5a-246a1a5ba46a")->setSymbol("DOGE-USDT");
        $req = $builder->build();
        $resp = $this->api->cancelOrderByClientOid($req);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelAllOrdersBySymbol
     * Cancel All Orders By Symbol
     * /api/v3/hf/margin/orders
     */
    public function testCancelAllOrdersBySymbol()
    {
        $builder = CancelAllOrdersBySymbolReq::builder();
        $builder->setSymbol("DOGE-USDT")->setTradeType("MARGIN_TRADE");
        $req = $builder->build();
        $resp = $this->api->cancelAllOrdersBySymbol($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSymbolsWithOpenOrder
     * Get Symbols With Open Order
     * /api/v3/hf/margin/order/active/symbols
     */
    public function testGetSymbolsWithOpenOrder()
    {
        $builder = GetSymbolsWithOpenOrderReq::builder();
        $builder->setTradeType("MARGIN_TRADE");
        $req = $builder->build();
        $resp = $this->api->getSymbolsWithOpenOrder($req);
        self::assertNotNull($resp->symbolSize);
        foreach ($resp->symbols as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOpenOrders
     * Get Open Orders
     * /api/v3/hf/margin/orders/active
     */
    public function testGetOpenOrders()
    {
        $builder = GetOpenOrdersReq::builder();
        $builder->setSymbol("DOGE-USDT")->setTradeType("MARGIN_TRADE");
        $req = $builder->build();
        $resp = $this->api->getOpenOrders($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->opType);
            self::assertNotNull($item->type);
            self::assertNotNull($item->side);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->funds);
            self::assertNotNull($item->dealSize);
            self::assertNotNull($item->dealFunds);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->feeCurrency);
            self::assertNotNull($item->stopTriggered);
            self::assertNotNull($item->stopPrice);
            self::assertNotNull($item->timeInForce);
            self::assertNotNull($item->postOnly);
            self::assertNotNull($item->hidden);
            self::assertNotNull($item->iceberg);
            self::assertNotNull($item->visibleSize);
            self::assertNotNull($item->cancelAfter);
            self::assertNotNull($item->channel);
            self::assertNotNull($item->clientOid);
            self::assertNotNull($item->remark);
            self::assertNotNull($item->cancelExist);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->lastUpdatedAt);
            self::assertNotNull($item->tradeType);
            self::assertNotNull($item->inOrderBook);
            self::assertNotNull($item->cancelledSize);
            self::assertNotNull($item->cancelledFunds);
            self::assertNotNull($item->remainSize);
            self::assertNotNull($item->remainFunds);
            self::assertNotNull($item->tax);
            self::assertNotNull($item->active);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getClosedOrders
     * Get Closed Orders
     * /api/v3/hf/margin/orders/done
     */
    public function testGetClosedOrders()
    {
        $builder = GetClosedOrdersReq::builder();
        $builder->setSymbol("DOGE-USDT")->setTradeType("MARGIN_TRADE")->setSide("buy")->setType("limit");
        $req = $builder->build();
        $resp = $this->api->getClosedOrders($req);
        self::assertNotNull($resp->lastId);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->opType);
            self::assertNotNull($item->type);
            self::assertNotNull($item->side);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->funds);
            self::assertNotNull($item->dealSize);
            self::assertNotNull($item->dealFunds);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->feeCurrency);
            self::assertNotNull($item->stopTriggered);
            self::assertNotNull($item->stopPrice);
            self::assertNotNull($item->timeInForce);
            self::assertNotNull($item->postOnly);
            self::assertNotNull($item->hidden);
            self::assertNotNull($item->iceberg);
            self::assertNotNull($item->visibleSize);
            self::assertNotNull($item->cancelAfter);
            self::assertNotNull($item->channel);
            self::assertNotNull($item->clientOid);
            self::assertNotNull($item->remark);
            self::assertNotNull($item->cancelExist);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->lastUpdatedAt);
            self::assertNotNull($item->tradeType);
            self::assertNotNull($item->inOrderBook);
            self::assertNotNull($item->cancelledSize);
            self::assertNotNull($item->cancelledFunds);
            self::assertNotNull($item->remainSize);
            self::assertNotNull($item->remainFunds);
            self::assertNotNull($item->tax);
            self::assertNotNull($item->active);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getTradeHistory
     * Get Trade History
     * /api/v3/hf/margin/fills
     */
    public function testGetTradeHistory()
    {
        $builder = GetTradeHistoryReq::builder();
        $builder->setSymbol("DOGE-USDT")->setTradeType("MARGIN_TRADE");
        $req = $builder->build();
        $resp = $this->api->getTradeHistory($req);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->tradeId);
            self::assertNotNull($item->orderId);
            self::assertNotNull($item->counterOrderId);
            self::assertNotNull($item->side);
            self::assertNotNull($item->liquidity);
            self::assertNotNull($item->forceTaker);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->funds);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->feeRate);
            self::assertNotNull($item->feeCurrency);
            self::assertNotNull($item->stop);
            self::assertNotNull($item->tradeType);
            self::assertNotNull($item->tax);
            self::assertNotNull($item->taxRate);
            self::assertNotNull($item->type);
            self::assertNotNull($item->createdAt);
        }

        self::assertNotNull($resp->lastId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOrderByOrderId
     * Get Order By OrderId
     * /api/v3/hf/margin/orders/{orderId}
     */
    public function testGetOrderByOrderId()
    {
        $builder = GetOrderByOrderIdReq::builder();
        $builder->setOrderId("68258f4a3c90fd0007fc542f")->setSymbol("DOGE-USDT");
        $req = $builder->build();
        $resp = $this->api->getOrderByOrderId($req);
        self::assertNotNull($resp->id);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->opType);
        self::assertNotNull($resp->type);
        self::assertNotNull($resp->side);
        self::assertNotNull($resp->price);
        self::assertNotNull($resp->size);
        self::assertNotNull($resp->funds);
        self::assertNotNull($resp->dealSize);
        self::assertNotNull($resp->dealFunds);
        self::assertNotNull($resp->fee);
        self::assertNotNull($resp->feeCurrency);
        self::assertNotNull($resp->stopTriggered);
        self::assertNotNull($resp->stopPrice);
        self::assertNotNull($resp->timeInForce);
        self::assertNotNull($resp->postOnly);
        self::assertNotNull($resp->hidden);
        self::assertNotNull($resp->iceberg);
        self::assertNotNull($resp->visibleSize);
        self::assertNotNull($resp->cancelAfter);
        self::assertNotNull($resp->channel);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->remark);
        self::assertNotNull($resp->cancelExist);
        self::assertNotNull($resp->createdAt);
        self::assertNotNull($resp->lastUpdatedAt);
        self::assertNotNull($resp->tradeType);
        self::assertNotNull($resp->inOrderBook);
        self::assertNotNull($resp->cancelledSize);
        self::assertNotNull($resp->cancelledFunds);
        self::assertNotNull($resp->remainSize);
        self::assertNotNull($resp->tax);
        self::assertNotNull($resp->active);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOrderByClientOid
     * Get Order By ClientOid
     * /api/v3/hf/margin/orders/client-order/{clientOid}
     */
    public function testGetOrderByClientOid()
    {
        $builder = GetOrderByClientOidReq::builder();
        $builder->setClientOid("033d50d3-b0a6-4d37-88d2-fb175da3d482")->setSymbol("DOGE-USDT");
        $req = $builder->build();
        $resp = $this->api->getOrderByClientOid($req);
        self::assertNotNull($resp->id);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->opType);
        self::assertNotNull($resp->type);
        self::assertNotNull($resp->side);
        self::assertNotNull($resp->price);
        self::assertNotNull($resp->size);
        self::assertNotNull($resp->funds);
        self::assertNotNull($resp->dealSize);
        self::assertNotNull($resp->dealFunds);
        self::assertNotNull($resp->fee);
        self::assertNotNull($resp->feeCurrency);
        self::assertNotNull($resp->stopTriggered);
        self::assertNotNull($resp->stopPrice);
        self::assertNotNull($resp->timeInForce);
        self::assertNotNull($resp->postOnly);
        self::assertNotNull($resp->hidden);
        self::assertNotNull($resp->iceberg);
        self::assertNotNull($resp->visibleSize);
        self::assertNotNull($resp->cancelAfter);
        self::assertNotNull($resp->channel);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->remark);
        self::assertNotNull($resp->cancelExist);
        self::assertNotNull($resp->createdAt);
        self::assertNotNull($resp->lastUpdatedAt);
        self::assertNotNull($resp->tradeType);
        self::assertNotNull($resp->inOrderBook);
        self::assertNotNull($resp->cancelledSize);
        self::assertNotNull($resp->cancelledFunds);
        self::assertNotNull($resp->remainSize);
        self::assertNotNull($resp->tax);
        self::assertNotNull($resp->active);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addOrderV1
     * Add Order - V1
     * /api/v1/margin/order
     */
    public function testAddOrderV1()
    {
        $builder = AddOrderV1Req::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("DOGE-USDT")->setType("limit")
            ->setSize("100")->setPrice("0.001")->setAutoBorrow(true)->setAutoRepay(true);
        $req = $builder->build();
        $resp = $this->api->addOrderV1($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->loanApplyId);
        self::assertNotNull($resp->borrowSize);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addOrderTestV1
     * Add Order Test - V1
     * /api/v1/margin/order/test
     */
    public function testAddOrderTestV1()
    {
        $builder = AddOrderTestV1Req::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("DOGE-USDT")->setType("limit")
            ->setSize("100")->setPrice("0.001")->setAutoBorrow(true)->setAutoRepay(true);
        $req = $builder->build();
        $resp = $this->api->addOrderTestV1($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->loanApplyId);
        self::assertNotNull($resp->borrowSize);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }


}