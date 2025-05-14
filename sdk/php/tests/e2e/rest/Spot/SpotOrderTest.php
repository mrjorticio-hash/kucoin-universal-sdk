<?php

namespace Tests\e2e\rest\Spot;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Spot\Order\AddOcoOrderReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\AddOrderOldReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\AddOrderReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\AddOrderSyncReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\AddOrderTestOldReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\AddOrderTestReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\AddStopOrderReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\BatchAddOrdersOldOrderList;
use KuCoin\UniversalSDK\Generate\Spot\Order\BatchAddOrdersOldReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\BatchAddOrdersOrderList;
use KuCoin\UniversalSDK\Generate\Spot\Order\BatchAddOrdersReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\BatchAddOrdersSyncOrderList;
use KuCoin\UniversalSDK\Generate\Spot\Order\BatchAddOrdersSyncReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\BatchCancelOcoOrdersReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\BatchCancelOrderOldReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\BatchCancelStopOrderReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelAllOrdersBySymbolReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelOcoOrderByClientOidReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelOcoOrderByOrderIdReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelOrderByClientOidOldReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelOrderByClientOidReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelOrderByClientOidSyncReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelOrderByOrderIdOldReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelOrderByOrderIdReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelOrderByOrderIdSyncReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelPartialOrderReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelStopOrderByClientOidReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelStopOrderByOrderIdReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetClosedOrdersReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOcoOrderByClientOidReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOcoOrderByOrderIdReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOcoOrderDetailByOrderIdReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOcoOrderListReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOpenOrdersByPageReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOpenOrdersReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOrderByClientOidOldReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOrderByClientOidReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOrderByOrderIdOldReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOrderByOrderIdReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOrdersListOldReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetStopOrderByClientOidReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetStopOrderByOrderIdReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetStopOrdersListReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetTradeHistoryOldReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetTradeHistoryReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\ModifyOrderReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\OrderApi;
use KuCoin\UniversalSDK\Generate\Spot\Order\SetDCPReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class SpotOrderTest extends TestCase
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
        $this->api = $kucoinRestService->getSpotService()->getOrderApi();
    }


    /**
     * addOrder
     * Add Order
     * /api/v1/hf/orders
     */
    public function testAddOrder()
    {
        $builder = AddOrderReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
        setRemark("test")->setPrice("1")->setSize(2);
        $req = $builder->build();
        $resp = $this->api->addOrder($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addOrderSync
     * Add Order Sync
     * /api/v1/hf/orders/sync
     */
    public function testAddOrderSync()
    {
        $builder = AddOrderSyncReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
        setRemark("test")->setPrice("1")->setSize(1);
        $req = $builder->build();
        $resp = $this->api->addOrderSync($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->orderTime);
        self::assertNotNull($resp->originSize);
        self::assertNotNull($resp->dealSize);
        self::assertNotNull($resp->remainSize);
        self::assertNotNull($resp->canceledSize);
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->matchTime);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addOrderTest
     * Add Order Test
     * /api/v1/hf/orders/test
     */
    public function testAddOrderTest()
    {
        $builder = AddOrderTestReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
        setRemark("test")->setPrice("1")->setSize(1);
        $req = $builder->build();
        $resp = $this->api->addOrderTest($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * batchAddOrders
     * Batch Add Orders
     * /api/v1/hf/orders/multi
     */
    public function testBatchAddOrders()
    {
        $builder = BatchAddOrdersReq::builder();

        $builder1 = BatchAddOrdersOrderList::builder();
        $builder1->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
        setRemark("test")->setPrice("1")->setSize(1);

        $builder2 = BatchAddOrdersOrderList::builder();
        $builder2->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
        setRemark("test")->setPrice("1")->setSize(1);

        $builder->setOrderList([$builder1->build(), $builder2->build()]);
        $req = $builder->build();
        $resp = $this->api->batchAddOrders($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->orderId);
            self::assertNotNull($item->clientOid);
            self::assertNotNull($item->success);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * batchAddOrdersSync
     * Batch Add Orders Sync
     * /api/v1/hf/orders/multi/sync
     */
    public function testBatchAddOrdersSync()
    {
        $builder = BatchAddOrdersSyncReq::builder();

        $builder1 = BatchAddOrdersSyncOrderList::builder();
        $builder1->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
        setRemark("test")->setPrice("1")->setSize(1);

        $builder2 = BatchAddOrdersSyncOrderList::builder();
        $builder2->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
        setRemark("test")->setPrice("1")->setSize(1);

        $builder->setOrderList([$builder1->build(), $builder2->build()]);
        $req = $builder->build();
        $resp = $this->api->batchAddOrdersSync($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->orderId);
            self::assertNotNull($item->clientOid);
            self::assertNotNull($item->orderTime);
            self::assertNotNull($item->originSize);
            self::assertNotNull($item->dealSize);
            self::assertNotNull($item->remainSize);
            self::assertNotNull($item->canceledSize);
            self::assertNotNull($item->status);
            self::assertNotNull($item->matchTime);
            self::assertNotNull($item->success);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelOrderByOrderId
     * Cancel Order By OrderId
     * /api/v1/hf/orders/{orderId}
     */
    public function testCancelOrderByOrderId()
    {
        $builder = CancelOrderByOrderIdReq::builder();
        $builder->setOrderId("68244ea7ac47960007e42c59")->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->cancelOrderByOrderId($req);
        self::assertNotNull($resp->orderId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelOrderByOrderIdSync
     * Cancel Order By OrderId Sync
     * /api/v1/hf/orders/sync/{orderId}
     */
    public function testCancelOrderByOrderIdSync()
    {
        $builder = CancelOrderByOrderIdSyncReq::builder();
        $builder->setSymbol("BTC-USDT")->setOrderId("68244ec4dfddfb0007a272c6");
        $req = $builder->build();
        $resp = $this->api->cancelOrderByOrderIdSync($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->originSize);
        self::assertNotNull($resp->dealSize);
        self::assertNotNull($resp->remainSize);
        self::assertNotNull($resp->canceledSize);
        self::assertNotNull($resp->status);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelOrderByClientOid
     * Cancel Order By ClientOid
     * /api/v1/hf/orders/client-order/{clientOid}
     */
    public function testCancelOrderByClientOid()
    {
        $builder = CancelOrderByClientOidReq::builder();
        $builder->setClientOid("267ad3de-9d46-4215-b325-4efb1786ca19")->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->cancelOrderByClientOid($req);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelOrderByClientOidSync
     * Cancel Order By ClientOid Sync
     * /api/v1/hf/orders/sync/client-order/{clientOid}
     */
    public function testCancelOrderByClientOidSync()
    {
        $builder = CancelOrderByClientOidSyncReq::builder();
        $builder->setSymbol("BTC-USDT")->setClientOid("5a281c54-288d-4665-ab5a-1a1e76feda2e");
        $req = $builder->build();
        $resp = $this->api->cancelOrderByClientOidSync($req);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->originSize);
        self::assertNotNull($resp->dealSize);
        self::assertNotNull($resp->remainSize);
        self::assertNotNull($resp->canceledSize);
        self::assertNotNull($resp->status);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelPartialOrder
     * Cancel Partial Order
     * /api/v1/hf/orders/cancel/{orderId}
     */
    public function testCancelPartialOrder()
    {
        $builder = CancelPartialOrderReq::builder();
        $builder->setOrderId("6824501a23f7b600072cb84c")->setSymbol("BTC-USDT")->setCancelSize(1);
        $req = $builder->build();
        $resp = $this->api->cancelPartialOrder($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->cancelSize);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelAllOrdersBySymbol
     * Cancel All Orders By Symbol
     * /api/v1/hf/orders
     */
    public function testCancelAllOrdersBySymbol()
    {
        $builder = CancelAllOrdersBySymbolReq::builder();
        $builder->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->cancelAllOrdersBySymbol($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelAllOrders
     * Cancel All Orders
     * /api/v1/hf/orders/cancelAll
     */
    public function testCancelAllOrders()
    {
        $resp = $this->api->cancelAllOrders();
        foreach ($resp->succeedSymbols as $item) {
            self::assertNotNull($item);
        }

        foreach ($resp->failedSymbols as $item) {
            self::assertNotNull($item->symbol);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * modifyOrder
     * Modify Order
     * /api/v1/hf/orders/alter
     */
    public function testModifyOrder()
    {
        $builder = ModifyOrderReq::builder();
        $builder->setClientOid("")->setSymbol("BTC-USDT")->setOrderId("682450761c60c70007a143c6")->setNewPrice("1")->setNewSize(3);
        $req = $builder->build();
        $resp = $this->api->modifyOrder($req);
        self::assertNotNull($resp->newOrderId);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOrderByOrderId
     * Get Order By OrderId
     * /api/v1/hf/orders/{orderId}
     */
    public function testGetOrderByOrderId()
    {
        $builder = GetOrderByOrderIdReq::builder();
        $builder->setSymbol("BTC-USDT")->setOrderId("6824507caeae7e000771737f");
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
        self::assertNotNull($resp->remainFunds);
        self::assertNotNull($resp->tax);
        self::assertNotNull($resp->active);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOrderByClientOid
     * Get Order By ClientOid
     * /api/v1/hf/orders/client-order/{clientOid}
     */
    public function testGetOrderByClientOid()
    {
        $builder = GetOrderByClientOidReq::builder();
        $builder->setSymbol("BTC-USDT")->setClientOid("fef9a16e-ded4-4a40-a86c-0ae816c22688");
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
        self::assertNotNull($resp->remainFunds);
        self::assertNotNull($resp->tax);
        self::assertNotNull($resp->active);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSymbolsWithOpenOrder
     * Get Symbols With Open Order
     * /api/v1/hf/orders/active/symbols
     */
    public function testGetSymbolsWithOpenOrder()
    {
        $resp = $this->api->getSymbolsWithOpenOrder();
        foreach ($resp->symbols as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOpenOrders
     * Get Open Orders
     * /api/v1/hf/orders/active
     */
    public function testGetOpenOrders()
    {
        $builder = GetOpenOrdersReq::builder();
        $builder->setSymbol('BTC-USDT');
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
     * getOpenOrdersByPage
     * Get Open Orders By Page
     * /api/v1/hf/orders/active/page
     */
    public function testGetOpenOrdersByPage()
    {
        $builder = GetOpenOrdersByPageReq::builder();
        $builder->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->getOpenOrdersByPage($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
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
     * /api/v1/hf/orders/done
     */
    public function testGetClosedOrders()
    {
        $builder = GetClosedOrdersReq::builder();
        $builder->setSymbol("BTC-USDT")->setSide('buy');
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
     * /api/v1/hf/fills
     */
    public function testGetTradeHistory()
    {
        $builder = GetTradeHistoryReq::builder();
        $builder->setSymbol('DOGE-USDT');
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
            self::assertNotNull($item->taxRate);
            self::assertNotNull($item->tax);
            self::assertNotNull($item->type);
            self::assertNotNull($item->createdAt);
        }

        self::assertNotNull($resp->lastId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getDCP
     * Get DCP
     * /api/v1/hf/orders/dead-cancel-all/query
     */
    public function testGetDCP()
    {
        $resp = $this->api->getDCP();
        self::assertNotNull($resp->timeout);
        self::assertNotNull($resp->symbols);
        self::assertNotNull($resp->currentTime);
        self::assertNotNull($resp->triggerTime);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * setDCP
     * Set DCP
     * /api/v1/hf/orders/dead-cancel-all
     */
    public function testSetDCP()
    {
        $builder = SetDCPReq::builder();
        $builder->setTimeout(100)->setSymbols("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->setDCP($req);
        self::assertNotNull($resp->currentTime);
        self::assertNotNull($resp->triggerTime);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addStopOrder
     * Add Stop Order
     * /api/v1/stop-order
     */
    public function testAddStopOrder()
    {
        $builder = AddStopOrderReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->
        setType("limit")->setRemark("test")->setPrice("1000")->setSize("0.001")->setStopPrice("800");
        $req = $builder->build();
        $resp = $this->api->addStopOrder($req);
        self::assertNotNull($resp->orderId);
        Logger::info($resp->jsonSerialize($this->serializer), ['oid' => $req->clientOid]);

    }

    /**
     * cancelStopOrderByClientOid
     * Cancel Stop Order By ClientOid
     * /api/v1/stop-order/cancelOrderByClientOid
     */
    public function testCancelStopOrderByClientOid()
    {
        $builder = CancelStopOrderByClientOidReq::builder();
        $builder->setSymbol('BTC-USDT')->setClientOid("815adce1-d2d0-4edd-a9c0-8c0284b37aae");
        $req = $builder->build();
        $resp = $this->api->cancelStopOrderByClientOid($req);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->cancelledOrderId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelStopOrderByOrderId
     * Cancel Stop Order By OrderId
     * /api/v1/stop-order/{orderId}
     */
    public function testCancelStopOrderByOrderId()
    {
        $builder = CancelStopOrderByOrderIdReq::builder();
        $builder->setOrderId("vs93gq14a9f7qjm3003m1m84");
        $req = $builder->build();
        $resp = $this->api->cancelStopOrderByOrderId($req);
        foreach ($resp->cancelledOrderIds as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * batchCancelStopOrder
     * Batch Cancel Stop Orders
     * /api/v1/stop-order/cancel
     */
    public function testBatchCancelStopOrder()
    {
        $builder = BatchCancelStopOrderReq::builder();
        $builder->setSymbol('BTC-USDT')->setTradeType("TRADE")->setOrderIds("vs93gq14aaegljki003pt6al");
        $req = $builder->build();
        $resp = $this->api->batchCancelStopOrder($req);
        foreach ($resp->cancelledOrderIds as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getStopOrdersList
     * Get Stop Orders List
     * /api/v1/stop-order
     */
    public function testGetStopOrdersList()
    {
        $builder = GetStopOrdersListReq::builder();
        $builder->setSymbol("BTC-USDT")->setSide('buy');
        $req = $builder->build();
        $resp = $this->api->getStopOrdersList($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->userId);
            self::assertNotNull($item->status);
            self::assertNotNull($item->type);
            self::assertNotNull($item->side);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->timeInForce);
            self::assertNotNull($item->cancelAfter);
            self::assertNotNull($item->postOnly);
            self::assertNotNull($item->hidden);
            self::assertNotNull($item->iceberg);
            self::assertNotNull($item->channel);
            self::assertNotNull($item->clientOid);
            self::assertNotNull($item->remark);
            self::assertNotNull($item->orderTime);
            self::assertNotNull($item->domainId);
            self::assertNotNull($item->tradeSource);
            self::assertNotNull($item->tradeType);
            self::assertNotNull($item->feeCurrency);
            self::assertNotNull($item->takerFeeRate);
            self::assertNotNull($item->makerFeeRate);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->stop);
            self::assertNotNull($item->stopPrice);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getStopOrderByOrderId
     * Get Stop Order By OrderId
     * /api/v1/stop-order/{orderId}
     */
    public function testGetStopOrderByOrderId()
    {
        $builder = GetStopOrderByOrderIdReq::builder();
        $builder->setOrderId("vs93gq14adumd65o003tgome");
        $req = $builder->build();
        $resp = $this->api->getStopOrderByOrderId($req);
        self::assertNotNull($resp->id);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->userId);
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->type);
        self::assertNotNull($resp->side);
        self::assertNotNull($resp->price);
        self::assertNotNull($resp->size);
        self::assertNotNull($resp->timeInForce);
        self::assertNotNull($resp->cancelAfter);
        self::assertNotNull($resp->postOnly);
        self::assertNotNull($resp->hidden);
        self::assertNotNull($resp->iceberg);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->remark);
        self::assertNotNull($resp->domainId);
        self::assertNotNull($resp->tradeSource);
        self::assertNotNull($resp->tradeType);
        self::assertNotNull($resp->feeCurrency);
        self::assertNotNull($resp->takerFeeRate);
        self::assertNotNull($resp->makerFeeRate);
        self::assertNotNull($resp->createdAt);
        self::assertNotNull($resp->stop);
        self::assertNotNull($resp->stopPrice);
        self::assertNotNull($resp->orderTime);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getStopOrderByClientOid
     * Get Stop Order By ClientOid
     * /api/v1/stop-order/queryOrderByClientOid
     */
    public function testGetStopOrderByClientOid()
    {
        $builder = GetStopOrderByClientOidReq::builder();
        $builder->setClientOid("8054ea57-d00e-4782-97f4-35a0ac25db7b")->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->getStopOrderByClientOid($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->userId);
            self::assertNotNull($item->status);
            self::assertNotNull($item->type);
            self::assertNotNull($item->side);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->timeInForce);
            self::assertNotNull($item->cancelAfter);
            self::assertNotNull($item->postOnly);
            self::assertNotNull($item->hidden);
            self::assertNotNull($item->iceberg);
            self::assertNotNull($item->channel);
            self::assertNotNull($item->clientOid);
            self::assertNotNull($item->remark);
            self::assertNotNull($item->domainId);
            self::assertNotNull($item->tradeSource);
            self::assertNotNull($item->tradeType);
            self::assertNotNull($item->feeCurrency);
            self::assertNotNull($item->takerFeeRate);
            self::assertNotNull($item->makerFeeRate);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->stop);
            self::assertNotNull($item->stopPrice);
            self::assertNotNull($item->orderTime);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addOcoOrder
     * Add OCO Order
     * /api/v3/oco/order
     */
    public function testAddOcoOrder()
    {
        $builder = AddOcoOrderReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->
        setRemark("test")->setSize("0.001")->setPrice("94000")->setStopPrice("150000")->setLimitPrice("180000");
        $req = $builder->build();
        $resp = $this->api->addOcoOrder($req);
        self::assertNotNull($resp->orderId);
        Logger::info($resp->jsonSerialize($this->serializer), ["id" => $req->clientOid]);
    }

    /**
     * cancelOcoOrderByOrderId
     * Cancel OCO Order By OrderId
     * /api/v3/oco/order/{orderId}
     */
    public function testCancelOcoOrderByOrderId()
    {
        $builder = CancelOcoOrderByOrderIdReq::builder();
        $builder->setOrderId("682454d67d4ec3000760d948");
        $req = $builder->build();
        $resp = $this->api->cancelOcoOrderByOrderId($req);
        foreach ($resp->cancelledOrderIds as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelOcoOrderByClientOid
     * Cancel OCO Order By ClientOid
     * /api/v3/oco/client-order/{clientOid}
     */
    public function testCancelOcoOrderByClientOid()
    {
        $builder = CancelOcoOrderByClientOidReq::builder();
        $builder->setClientOid("6bf63644-1933-4252-b150-d48e5771f47b");
        $req = $builder->build();
        $resp = $this->api->cancelOcoOrderByClientOid($req);
        foreach ($resp->cancelledOrderIds as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * batchCancelOcoOrders
     * Batch Cancel OCO Order
     * /api/v3/oco/orders
     */
    public function testBatchCancelOcoOrders()
    {
        $builder = BatchCancelOcoOrdersReq::builder();
        $builder->setOrderIds("6824553aac741d00079d2f7a")->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->batchCancelOcoOrders($req);
        foreach ($resp->cancelledOrderIds as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOcoOrderByOrderId
     * Get OCO Order By OrderId
     * /api/v3/oco/order/{orderId}
     */
    public function testGetOcoOrderByOrderId()
    {
        $builder = GetOcoOrderByOrderIdReq::builder();
        $builder->setOrderId("68245560ac741d00079d2f82");
        $req = $builder->build();
        $resp = $this->api->getOcoOrderByOrderId($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->orderTime);
        self::assertNotNull($resp->status);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOcoOrderByClientOid
     * Get OCO Order By ClientOid
     * /api/v3/oco/client-order/{clientOid}
     */
    public function testGetOcoOrderByClientOid()
    {
        $builder = GetOcoOrderByClientOidReq::builder();
        $builder->setClientOid("633b6f61-c31b-467a-a1bc-48edc13aa7e6");
        $req = $builder->build();
        $resp = $this->api->getOcoOrderByClientOid($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->orderTime);
        self::assertNotNull($resp->status);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOcoOrderDetailByOrderId
     * Get OCO Order Detail By OrderId
     * /api/v3/oco/order/details/{orderId}
     */
    public function testGetOcoOrderDetailByOrderId()
    {
        $builder = GetOcoOrderDetailByOrderIdReq::builder();
        $builder->setOrderId('68245560ac741d00079d2f82');
        $req = $builder->build();
        $resp = $this->api->getOcoOrderDetailByOrderId($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->orderTime);
        self::assertNotNull($resp->status);
        foreach ($resp->orders as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->side);
            self::assertNotNull($item->price);
            self::assertNotNull($item->stopPrice);
            self::assertNotNull($item->size);
            self::assertNotNull($item->status);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }
//

    /**
     * getOcoOrderList
     * Get OCO Order List
     * /api/v3/oco/orders
     */
    public function testGetOcoOrderList()
    {
        $builder = GetOcoOrderListReq::builder();
        $builder->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->getOcoOrderList($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->orderId);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->clientOid);
            self::assertNotNull($item->orderTime);
            self::assertNotNull($item->status);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addOrderOld
     * Add Order - Old
     * /api/v1/orders
     */
    public function testAddOrderOld()
    {
        $builder = AddOrderOldReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
        setRemark("test")->setPrice("1")->setSize(2);

        $req = $builder->build();
        $resp = $this->api->addOrderOld($req);
        self::assertNotNull($resp->orderId);
        Logger::info($resp->jsonSerialize($this->serializer), ["cid" => $req->clientOid]);
    }

    /**
     * addOrderTestOld
     * Add Order Test - Old
     * /api/v1/orders/test
     */
    public function testAddOrderTestOld()
    {
        $builder = AddOrderTestOldReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
        setRemark("test")->setPrice("1")->setSize(2);
        $req = $builder->build();
        $resp = $this->api->addOrderTestOld($req);
        self::assertNotNull($resp->orderId);
        Logger::info($resp->jsonSerialize($this->serializer), ["cid" => $req->clientOid]);
    }

    /**
     * batchAddOrdersOld
     * Batch Add Orders - Old
     * /api/v1/orders/multi
     */
    public function testBatchAddOrdersOld()
    {
        $builder = BatchAddOrdersOldReq::builder();

        $builder1 = BatchAddOrdersOldOrderList::builder();
        $builder1->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
        setRemark("test")->setPrice("1")->setSize(2);

        $builder2 = BatchAddOrdersOldOrderList::builder();
        $builder2->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
        setRemark("test")->setPrice("1")->setSize(2);

        $builder->setOrderList([$builder1->build(), $builder2->build()])->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->batchAddOrdersOld($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->type);
            self::assertNotNull($item->side);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->stp);
            self::assertNotNull($item->stop);
            self::assertNotNull($item->timeInForce);
            self::assertNotNull($item->cancelAfter);
            self::assertNotNull($item->postOnly);
            self::assertNotNull($item->hidden);
            self::assertNotNull($item->iceberge);
            self::assertNotNull($item->iceberg);
            self::assertNotNull($item->channel);
            self::assertNotNull($item->id);
            self::assertNotNull($item->status);
            self::assertNotNull($item->clientOid);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelOrderByOrderIdOld
     * Cancel Order By OrderId - Old
     * /api/v1/orders/{orderId}
     */
    public function testCancelOrderByOrderIdOld()
    {
        $builder = CancelOrderByOrderIdOldReq::builder();
        $builder->setOrderId("682455d123f7b6000750db68");
        $req = $builder->build();
        $resp = $this->api->cancelOrderByOrderIdOld($req);
        foreach ($resp->cancelledOrderIds as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelOrderByClientOidOld
     * Cancel Order By ClientOid - Old
     * /api/v1/order/client-order/{clientOid}
     */
    public function testCancelOrderByClientOidOld()
    {
        $builder = CancelOrderByClientOidOldReq::builder();
        $builder->setClientOid("43495d96-8c24-4228-a6b9-decf2e617a4f");
        $req = $builder->build();
        $resp = $this->api->cancelOrderByClientOidOld($req);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->cancelledOrderId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * batchCancelOrderOld
     * Batch Cancel Order - Old
     * /api/v1/orders
     */
    public function testBatchCancelOrderOld()
    {
        $builder = BatchCancelOrderOldReq::builder();
        $builder->setSymbol("BTC-USDT")->setTradeType("TRADE");
        $req = $builder->build();
        $resp = $this->api->batchCancelOrderOld($req);
        foreach ($resp->cancelledOrderIds as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOrdersListOld
     * Get Orders List - Old
     * /api/v1/orders
     */
    public function testGetOrdersListOld()
    {
        $builder = GetOrdersListOldReq::builder();
        $builder->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->getOrdersListOld($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->opType);
            self::assertNotNull($item->type);
            self::assertNotNull($item->side);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->dealFunds);
            self::assertNotNull($item->dealSize);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->feeCurrency);
            self::assertNotNull($item->stop);
            self::assertNotNull($item->stopTriggered);
            self::assertNotNull($item->stopPrice);
            self::assertNotNull($item->timeInForce);
            self::assertNotNull($item->postOnly);
            self::assertNotNull($item->hidden);
            self::assertNotNull($item->iceberg);
            self::assertNotNull($item->cancelAfter);
            self::assertNotNull($item->channel);
            self::assertNotNull($item->isActive);
            self::assertNotNull($item->cancelExist);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->tradeType);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getRecentOrdersListOld
     * Get Recent Orders List - Old
     * /api/v1/limit/orders
     */
    public function testGetRecentOrdersListOld()
    {
        $resp = $this->api->getRecentOrdersListOld();
        foreach ($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->opType);
            self::assertNotNull($item->type);
            self::assertNotNull($item->side);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->dealFunds);
            self::assertNotNull($item->dealSize);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->feeCurrency);
            self::assertNotNull($item->stop);
            self::assertNotNull($item->stopTriggered);
            self::assertNotNull($item->stopPrice);
            self::assertNotNull($item->timeInForce);
            self::assertNotNull($item->postOnly);
            self::assertNotNull($item->hidden);
            self::assertNotNull($item->iceberg);
            self::assertNotNull($item->cancelAfter);
            self::assertNotNull($item->channel);
            self::assertNotNull($item->isActive);
            self::assertNotNull($item->cancelExist);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->tradeType);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOrderByOrderIdOld
     * Get Order By OrderId - Old
     * /api/v1/orders/{orderId}
     */
    public function testGetOrderByOrderIdOld()
    {
        $builder = GetOrderByOrderIdOldReq::builder();
        $builder->setOrderId("6824585234c47100073f13b0");
        $req = $builder->build();
        $resp = $this->api->getOrderByOrderIdOld($req);
        self::assertNotNull($resp->id);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->opType);
        self::assertNotNull($resp->type);
        self::assertNotNull($resp->side);
        self::assertNotNull($resp->price);
        self::assertNotNull($resp->size);
        self::assertNotNull($resp->funds);
        self::assertNotNull($resp->dealFunds);
        self::assertNotNull($resp->dealSize);
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
        self::assertNotNull($resp->isActive);
        self::assertNotNull($resp->cancelExist);
        self::assertNotNull($resp->createdAt);
        self::assertNotNull($resp->tradeType);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOrderByClientOidOld
     * Get Order By ClientOid - Old
     * /api/v1/order/client-order/{clientOid}
     */
    public function testGetOrderByClientOidOld()
    {
        $builder = GetOrderByClientOidOldReq::builder();
        $builder->setClientOid("3773c6b6-304e-422f-a9fd-5f1cc71437ef");
        $req = $builder->build();
        $resp = $this->api->getOrderByClientOidOld($req);
        self::assertNotNull($resp->id);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->opType);
        self::assertNotNull($resp->type);
        self::assertNotNull($resp->side);
        self::assertNotNull($resp->price);
        self::assertNotNull($resp->size);
        self::assertNotNull($resp->funds);
        self::assertNotNull($resp->dealFunds);
        self::assertNotNull($resp->dealSize);
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
        self::assertNotNull($resp->isActive);
        self::assertNotNull($resp->cancelExist);
        self::assertNotNull($resp->createdAt);
        self::assertNotNull($resp->tradeType);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getTradeHistoryOld
     * Get Trade History - Old
     * /api/v1/fills
     */
    public function testGetTradeHistoryOld()
    {
        $builder = GetTradeHistoryOldReq::builder();
        $builder->setSymbol("DOGE-USDT");
        $req = $builder->build();
        $resp = $this->api->getTradeHistoryOld($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
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
            self::assertNotNull($item->type);
            self::assertNotNull($item->createdAt);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getRecentTradeHistoryOld
     * Get Recent Trade History - Old
     * /api/v1/limit/fills
     */
    public function testGetRecentTradeHistoryOld()
    {
        $resp = $this->api->getRecentTradeHistoryOld();
        foreach ($resp->data as $item) {
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
            self::assertNotNull($item->type);
            self::assertNotNull($item->createdAt);
        }
        Logger::info($resp->jsonSerialize($this->serializer));
    }
}