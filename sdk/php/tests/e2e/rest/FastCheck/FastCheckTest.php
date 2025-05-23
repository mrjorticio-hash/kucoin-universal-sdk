<?php

namespace Tests\e2e\rest\FastCheck;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Extension\Interceptor\LoggingInterceptor;
use KuCoin\UniversalSDK\Generate\Spot\Order\AddOrderReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelOrderByOrderIdReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOrderByOrderIdReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Swoole\Event;
use Swoole\Runtime;

class FastCheckTest extends TestCase
{
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
    }


    public function testFastCheck()
    {
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
            ->setInterceptors([new LoggingInterceptor()])
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
        $api = $kucoinRestService->getSpotService()->getOrderApi();


        $orderId = "";

        // add order
        {
            $builder = AddOrderReq::builder();
            $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
            setRemark("test")->setPrice("1")->setSize(2);
            $req = $builder->build();
            $resp = $api->addOrder($req);
            self::assertNotNull($resp->orderId);
            self::assertNotNull($resp->clientOid);
            self::assertNotNull($resp->commonResponse->rateLimit->limit);
            self::assertNotNull($resp->commonResponse->rateLimit->reset);
            self::assertNotNull($resp->commonResponse->rateLimit->remaining);
            self::assertNotNull($resp->commonResponse->code);
            self::assertNotNull($resp->commonResponse->data);
            $orderId = $resp->orderId;
            Logger::info($resp->jsonSerialize($this->serializer));
        }

        // query order
        {
            $builder = GetOrderByOrderIdReq::builder();
            $builder->setSymbol("BTC-USDT")->setOrderId($orderId);
            $req = $builder->build();
            $resp = $api->getOrderByOrderId($req);
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
            self::assertNotNull($resp->commonResponse->rateLimit->limit);
            self::assertNotNull($resp->commonResponse->rateLimit->reset);
            self::assertNotNull($resp->commonResponse->rateLimit->remaining);
            self::assertNotNull($resp->commonResponse->code);
            self::assertNotNull($resp->commonResponse->data);
            Logger::info($resp->jsonSerialize($this->serializer));
        }

        // cancel order
        {
            $builder = CancelOrderByOrderIdReq::builder();
            $builder->setOrderId($orderId)->setSymbol("BTC-USDT");
            $req = $builder->build();
            $resp = $api->cancelOrderByOrderId($req);
            self::assertNotNull($resp->orderId);
            Logger::info($resp->jsonSerialize($this->serializer));
        }
    }


    public function testFastCheckCoroutine()
    {
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
            ->setUseCoroutineHttp(true)
            ->setInterceptors([new LoggingInterceptor()])
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
        $api = $kucoinRestService->getSpotService()->getOrderApi();


        Runtime::enableCoroutine();


        go(function () use (&$api) {
            $orderId = "";
            // add order
            {
                $builder = AddOrderReq::builder();
                $builder->setClientOid(Uuid::uuid4()->toString())->setSide("buy")->setSymbol("BTC-USDT")->setType("limit")->
                setRemark("test")->setPrice("1")->setSize(2);
                $req = $builder->build();
                $resp = $api->addOrder($req);
                self::assertNotNull($resp->orderId);
                self::assertNotNull($resp->clientOid);
                self::assertNotNull($resp->commonResponse->rateLimit->limit);
                self::assertNotNull($resp->commonResponse->rateLimit->reset);
                self::assertNotNull($resp->commonResponse->rateLimit->remaining);
                self::assertNotNull($resp->commonResponse->code);
                self::assertNotNull($resp->commonResponse->data);
                $orderId = $resp->orderId;
                Logger::info($resp->jsonSerialize($this->serializer));
            }

            // query order
            {
                $builder = GetOrderByOrderIdReq::builder();
                $builder->setSymbol("BTC-USDT")->setOrderId($orderId);
                $req = $builder->build();
                $resp = $api->getOrderByOrderId($req);
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
                self::assertNotNull($resp->commonResponse->rateLimit->limit);
                self::assertNotNull($resp->commonResponse->rateLimit->reset);
                self::assertNotNull($resp->commonResponse->rateLimit->remaining);
                self::assertNotNull($resp->commonResponse->code);
                self::assertNotNull($resp->commonResponse->data);
                Logger::info($resp->jsonSerialize($this->serializer));
            }

            // cancel order
            {
                $builder = CancelOrderByOrderIdReq::builder();
                $builder->setOrderId($orderId)->setSymbol("BTC-USDT");
                $req = $builder->build();
                $resp = $api->cancelOrderByOrderId($req);
                self::assertNotNull($resp->orderId);
                Logger::info($resp->jsonSerialize($this->serializer));
            }
        });

        Event::wait();
    }

}