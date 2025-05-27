<?php

namespace Tests\e2e\rest\Futures;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Futures\Order\AddOrderReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\AddOrderTestReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\AddTPSLOrderReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\BatchAddOrdersItem;
use KuCoin\UniversalSDK\Generate\Futures\Order\BatchAddOrdersReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\BatchCancelOrdersClientOidsList;
use KuCoin\UniversalSDK\Generate\Futures\Order\BatchCancelOrdersReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\CancelAllOrdersV1Req;
use KuCoin\UniversalSDK\Generate\Futures\Order\CancelAllOrdersV3Req;
use KuCoin\UniversalSDK\Generate\Futures\Order\CancelAllStopOrdersReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\CancelOrderByClientOidReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\CancelOrderByIdReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\GetOpenOrderValueReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\GetOrderByClientOidReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\GetOrderByOrderIdReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\GetOrderListReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\GetRecentClosedOrdersReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\GetRecentTradeHistoryReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\GetStopOrderListReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\GetTradeHistoryReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\OrderApi;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class FuturesOrderTest extends TestCase
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
        $this->api = $kucoinRestService->getFuturesService()->getOrderApi();
    }


    /**
     * addOrder
     * Add Order
     * /api/v1/orders
     */
    public function testAddOrder()
    {
        $builder = AddOrderReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("XBTUSDTM")->
        setLeverage(3.0)->setType("limit")->setRemark("order_test")->setMarginMode("ISOLATED")
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
     * /api/v1/orders/test
     */
    public function testAddOrderTest()
    {
        $builder = AddOrderTestReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("XBTUSDTM")->
        setLeverage(3.0)->setType("limit")->setRemark("order_test")->setMarginMode("ISOLATED")
            ->setPrice("1")->setSize(1);
        $req = $builder->build();
        $resp = $this->api->addOrderTest($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * batchAddOrders
     * Batch Add Orders
     * /api/v1/orders/multi
     */
    public function testBatchAddOrders()
    {
        $builder = BatchAddOrdersReq::builder();

        $builder1 = BatchAddOrdersItem::builder();
        $builder1->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("XBTUSDTM")->
        setLeverage(3.0)->setType("limit")->setRemark("order_test")->setMarginMode("ISOLATED")
            ->setPrice("1")->setSize(1);

        $builder2 = BatchAddOrdersItem::builder();
        $builder2->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("XBTUSDTM")->
        setLeverage(3.0)->setType("limit")->setRemark("order_test")->setMarginMode("ISOLATED")
            ->setPrice("1")->setSize(1);

        $builder->setItems([$builder1->build(), $builder2->build()]);
        $req = $builder->build();
        $resp = $this->api->batchAddOrders($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->orderId);
            self::assertNotNull($item->clientOid);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->code);
            self::assertNotNull($item->msg);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addTPSLOrder
     * Add Take Profit And Stop Loss Order
     * /api/v1/st-orders
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
     * /api/v1/orders/{orderId}
     */
    public function testCancelOrderById()
    {
        $builder = CancelOrderByIdReq::builder();
        $builder->setOrderId("312020515508359168");
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
     * /api/v1/orders/client-order/{clientOid}
     */
    public function testCancelOrderByClientOid()
    {
        $builder = CancelOrderByClientOidReq::builder();
        $builder->setSymbol("XBTUSDTM")->setClientOid("1f1a2cc8-680f-4a2e-8046-5cb1a7716c1e");
        $req = $builder->build();
        $resp = $this->api->cancelOrderByClientOid($req);
        self::assertNotNull($resp->clientOid);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * batchCancelOrders
     * Batch Cancel Orders
     * /api/v1/orders/multi-cancel
     */
    public function testBatchCancelOrders()
    {
        $builder = BatchCancelOrdersReq::builder();
        $builder->setOrderIdsList(["312019242151075840", "312019242213990400"])->setClientOidsList([
                BatchCancelOrdersClientOidsList::create(["clientOid" => "424798aa-faeb-4aad-9d7d-024b22097cd5", "symbol" => "XBTUSDTM"]),
                BatchCancelOrdersClientOidsList::create(["clientOid" => "48f499b1-d57b-40f6-973e-71fa314109e8", "symbol" => "XBTUSDTM"])]
        );
        $req = $builder->build();
        $resp = $this->api->batchCancelOrders($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->orderId);
            self::assertNotNull($item->code);
            self::assertNotNull($item->msg);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelAllOrdersV3
     * Cancel All Orders
     * /api/v3/orders
     */
    public function testCancelAllOrdersV3()
    {
        $builder = CancelAllOrdersV3Req::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->cancelAllOrdersV3($req);
        foreach ($resp->cancelledOrderIds as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelAllStopOrders
     * Cancel All Stop orders
     * /api/v1/stopOrders
     */
    public function testCancelAllStopOrders()
    {
        $builder = CancelAllStopOrdersReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->cancelAllStopOrders($req);
        foreach ($resp->cancelledOrderIds as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOrderByOrderId
     * Get Order By OrderId
     * /api/v1/orders/{order-id}
     */
    public function testGetOrderByOrderId()
    {
        $builder = GetOrderByOrderIdReq::builder();
        $builder->setOrderId("312021877147213824");
        $req = $builder->build();
        $resp = $this->api->getOrderByOrderId($req);
        self::assertNotNull($resp->id);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->type);
        self::assertNotNull($resp->side);
        self::assertNotNull($resp->price);
        self::assertNotNull($resp->size);
        self::assertNotNull($resp->value);
        self::assertNotNull($resp->dealValue);
        self::assertNotNull($resp->dealSize);
        self::assertNotNull($resp->stp);
        self::assertNotNull($resp->stop);
        self::assertNotNull($resp->stopPriceType);
        self::assertNotNull($resp->stopTriggered);
        self::assertNotNull($resp->timeInForce);
        self::assertNotNull($resp->postOnly);
        self::assertNotNull($resp->hidden);
        self::assertNotNull($resp->iceberg);
        self::assertNotNull($resp->leverage);
        self::assertNotNull($resp->forceHold);
        self::assertNotNull($resp->closeOrder);
        self::assertNotNull($resp->visibleSize);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->tags);
        self::assertNotNull($resp->isActive);
        self::assertNotNull($resp->cancelExist);
        self::assertNotNull($resp->createdAt);
        self::assertNotNull($resp->updatedAt);
        self::assertNotNull($resp->orderTime);
        self::assertNotNull($resp->settleCurrency);
        self::assertNotNull($resp->marginMode);
        self::assertNotNull($resp->avgDealPrice);
        self::assertNotNull($resp->filledSize);
        self::assertNotNull($resp->filledValue);
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->reduceOnly);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOrderByClientOid
     * Get Order By ClientOid
     * /api/v1/orders/byClientOid
     */
    public function testGetOrderByClientOid()
    {
        $builder = GetOrderByClientOidReq::builder();
        $builder->setClientOid("5bf36c07-6e4e-4331-9726-dfee9f565688");
        $req = $builder->build();
        $resp = $this->api->getOrderByClientOid($req);
        self::assertNotNull($resp->id);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->type);
        self::assertNotNull($resp->side);
        self::assertNotNull($resp->price);
        self::assertNotNull($resp->size);
        self::assertNotNull($resp->value);
        self::assertNotNull($resp->dealValue);
        self::assertNotNull($resp->dealSize);
        self::assertNotNull($resp->stp);
        self::assertNotNull($resp->stop);
        self::assertNotNull($resp->stopPriceType);
        self::assertNotNull($resp->stopTriggered);
        self::assertNotNull($resp->timeInForce);
        self::assertNotNull($resp->postOnly);
        self::assertNotNull($resp->hidden);
        self::assertNotNull($resp->iceberg);
        self::assertNotNull($resp->leverage);
        self::assertNotNull($resp->forceHold);
        self::assertNotNull($resp->closeOrder);
        self::assertNotNull($resp->visibleSize);
        self::assertNotNull($resp->clientOid);
        self::assertNotNull($resp->tags);
        self::assertNotNull($resp->isActive);
        self::assertNotNull($resp->cancelExist);
        self::assertNotNull($resp->createdAt);
        self::assertNotNull($resp->updatedAt);
        self::assertNotNull($resp->orderTime);
        self::assertNotNull($resp->settleCurrency);
        self::assertNotNull($resp->marginMode);
        self::assertNotNull($resp->avgDealPrice);
        self::assertNotNull($resp->filledSize);
        self::assertNotNull($resp->filledValue);
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->reduceOnly);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOrderList
     * Get Order List
     * /api/v1/orders
     */
    public function testGetOrderList()
    {
        $builder = GetOrderListReq::builder();
        $builder->setStatus("active")->setSymbol("XBTUSDTM")->setSide("buy");
        $req = $builder->build();
        $resp = $this->api->getOrderList($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->type);
            self::assertNotNull($item->side);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->value);
            self::assertNotNull($item->dealValue);
            self::assertNotNull($item->dealSize);
            self::assertNotNull($item->stp);
            self::assertNotNull($item->stop);
            self::assertNotNull($item->stopPriceType);
            self::assertNotNull($item->stopTriggered);
            self::assertNotNull($item->timeInForce);
            self::assertNotNull($item->postOnly);
            self::assertNotNull($item->hidden);
            self::assertNotNull($item->iceberg);
            self::assertNotNull($item->leverage);
            self::assertNotNull($item->forceHold);
            self::assertNotNull($item->closeOrder);
            self::assertNotNull($item->visibleSize);
            self::assertNotNull($item->clientOid);
            self::assertNotNull($item->tags);
            self::assertNotNull($item->isActive);
            self::assertNotNull($item->cancelExist);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->updatedAt);
            self::assertNotNull($item->orderTime);
            self::assertNotNull($item->settleCurrency);
            self::assertNotNull($item->marginMode);
            self::assertNotNull($item->avgDealPrice);
            self::assertNotNull($item->status);
            self::assertNotNull($item->filledSize);
            self::assertNotNull($item->filledValue);
            self::assertNotNull($item->reduceOnly);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getRecentClosedOrders
     * Get Recent Closed Orders
     * /api/v1/recentDoneOrders
     */
    public function testGetRecentClosedOrders()
    {
        $builder = GetRecentClosedOrdersReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getRecentClosedOrders($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->type);
            self::assertNotNull($item->side);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->value);
            self::assertNotNull($item->dealValue);
            self::assertNotNull($item->dealSize);
            self::assertNotNull($item->stp);
            self::assertNotNull($item->stop);
            self::assertNotNull($item->stopPriceType);
            self::assertNotNull($item->stopTriggered);
            self::assertNotNull($item->timeInForce);
            self::assertNotNull($item->postOnly);
            self::assertNotNull($item->hidden);
            self::assertNotNull($item->iceberg);
            self::assertNotNull($item->leverage);
            self::assertNotNull($item->forceHold);
            self::assertNotNull($item->closeOrder);
            self::assertNotNull($item->visibleSize);
            self::assertNotNull($item->clientOid);
            self::assertNotNull($item->remark);
            self::assertNotNull($item->tags);
            self::assertNotNull($item->isActive);
            self::assertNotNull($item->cancelExist);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->updatedAt);
            self::assertNotNull($item->endAt);
            self::assertNotNull($item->orderTime);
            self::assertNotNull($item->settleCurrency);
            self::assertNotNull($item->marginMode);
            self::assertNotNull($item->avgDealPrice);
            self::assertNotNull($item->filledSize);
            self::assertNotNull($item->filledValue);
            self::assertNotNull($item->status);
            self::assertNotNull($item->reduceOnly);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getStopOrderList
     * Get Stop Order List
     * /api/v1/stopOrders
     */
    public function testGetStopOrderList()
    {
        $builder = GetStopOrderListReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getStopOrderList($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->type);
            self::assertNotNull($item->side);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->value);
            self::assertNotNull($item->dealValue);
            self::assertNotNull($item->dealSize);
            self::assertNotNull($item->stop);
            self::assertNotNull($item->stopPriceType);
            self::assertNotNull($item->stopTriggered);
            self::assertNotNull($item->stopPrice);
            self::assertNotNull($item->timeInForce);
            self::assertNotNull($item->postOnly);
            self::assertNotNull($item->hidden);
            self::assertNotNull($item->iceberg);
            self::assertNotNull($item->leverage);
            self::assertNotNull($item->forceHold);
            self::assertNotNull($item->closeOrder);
            self::assertNotNull($item->visibleSize);
            self::assertNotNull($item->clientOid);
            self::assertNotNull($item->tags);
            self::assertNotNull($item->isActive);
            self::assertNotNull($item->cancelExist);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->updatedAt);
            self::assertNotNull($item->orderTime);
            self::assertNotNull($item->settleCurrency);
            self::assertNotNull($item->marginMode);
            self::assertNotNull($item->filledSize);
            self::assertNotNull($item->filledValue);
            self::assertNotNull($item->status);
            self::assertNotNull($item->reduceOnly);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getOpenOrderValue
     * Get Open Order Value
     * /api/v1/openOrderStatistics
     */
    public function testGetOpenOrderValue()
    {
        $builder = GetOpenOrderValueReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getOpenOrderValue($req);
        self::assertNotNull($resp->openOrderBuySize);
        self::assertNotNull($resp->openOrderSellSize);
        self::assertNotNull($resp->openOrderBuyCost);
        self::assertNotNull($resp->openOrderSellCost);
        self::assertNotNull($resp->settleCurrency);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getRecentTradeHistory
     * Get Recent Trade History
     * /api/v1/recentFills
     */
    public function testGetRecentTradeHistory()
    {
        $builder = GetRecentTradeHistoryReq::builder();
        $builder->setSymbol("DOGEUSDTM");
        $req = $builder->build();
        $resp = $this->api->getRecentTradeHistory($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->tradeId);
            self::assertNotNull($item->orderId);
            self::assertNotNull($item->side);
            self::assertNotNull($item->liquidity);
            self::assertNotNull($item->forceTaker);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->value);
            self::assertNotNull($item->openFeePay);
            self::assertNotNull($item->closeFeePay);
            self::assertNotNull($item->stop);
            self::assertNotNull($item->feeRate);
            self::assertNotNull($item->fixFee);
            self::assertNotNull($item->feeCurrency);
            self::assertNotNull($item->tradeTime);
            self::assertNotNull($item->marginMode);
            self::assertNotNull($item->displayType);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->settleCurrency);
            self::assertNotNull($item->orderType);
            self::assertNotNull($item->tradeType);
            self::assertNotNull($item->createdAt);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getTradeHistory
     * Get Trade History
     * /api/v1/fills
     */
    public function testGetTradeHistory()
    {
        $builder = GetTradeHistoryReq::builder();
        $builder->setOrderId("312023620819886081")->setSymbol("DOGEUSDTM")->setSide("buy");
        $req = $builder->build();
        $resp = $this->api->getTradeHistory($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->tradeId);
            self::assertNotNull($item->orderId);
            self::assertNotNull($item->side);
            self::assertNotNull($item->liquidity);
            self::assertNotNull($item->forceTaker);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->value);
            self::assertNotNull($item->openFeePay);
            self::assertNotNull($item->closeFeePay);
            self::assertNotNull($item->stop);
            self::assertNotNull($item->feeRate);
            self::assertNotNull($item->fixFee);
            self::assertNotNull($item->feeCurrency);
            self::assertNotNull($item->tradeTime);
            self::assertNotNull($item->marginMode);
            self::assertNotNull($item->settleCurrency);
            self::assertNotNull($item->displayType);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->orderType);
            self::assertNotNull($item->tradeType);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->openFeeTaxPay);
            self::assertNotNull($item->closeFeeTaxPay);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelAllOrdersV1
     * Cancel All Orders - V1
     * /api/v1/orders
     */
    public function testCancelAllOrdersV1()
    {
        $builder = CancelAllOrdersV1Req::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->cancelAllOrdersV1($req);
        foreach ($resp->cancelledOrderIds as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }


}