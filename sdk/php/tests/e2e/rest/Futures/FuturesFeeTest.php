<?php

namespace Tests\e2e\rest\Futures;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Futures\Fundingfees\FundingFeesApi;
use KuCoin\UniversalSDK\Generate\Futures\Fundingfees\GetCurrentFundingRateReq;
use KuCoin\UniversalSDK\Generate\Futures\Fundingfees\GetPrivateFundingHistoryReq;
use KuCoin\UniversalSDK\Generate\Futures\Fundingfees\GetPublicFundingHistoryReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class FuturesFeeTest extends TestCase
{
    /**
     * @var FundingFeesApi $api
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
        $this->api = $kucoinRestService->getFuturesService()->getFundingFeesApi();
    }


    /**
     * getCurrentFundingRate
     * Get Current Funding Rate.
     * /api/v1/funding-rate/{symbol}/current
     */
    public function testGetCurrentFundingRate()
    {
        $builder = GetCurrentFundingRateReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getCurrentFundingRate($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->granularity);
        self::assertNotNull($resp->timePoint);
        self::assertNotNull($resp->value);
        self::assertNotNull($resp->predictedValue);
        self::assertNotNull($resp->fundingRateCap);
        self::assertNotNull($resp->fundingRateFloor);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPublicFundingHistory
     * Get Public Funding History
     * /api/v1/contract/funding-rates
     */
    public function testGetPublicFundingHistory()
    {
        $builder = GetPublicFundingHistoryReq::builder();
        $builder->setSymbol("XBTUSDTM")->setFrom(1700310700000)->setTo(1702310700000);
        $req = $builder->build();
        $resp = $this->api->getPublicFundingHistory($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->fundingRate);
            self::assertNotNull($item->timepoint);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPrivateFundingHistory
     * Get Private Funding History
     * /api/v1/funding-history
     */
    public function testGetPrivateFundingHistory()
    {
        $builder = GetPrivateFundingHistoryReq::builder();
        $builder->setSymbol("DOGEUSDTM")->setStartAt(1747152000000)->setEndAt(1747238400000)->
        setReverse(true)->setMaxCount(100);
        $req = $builder->build();
        $resp = $this->api->getPrivateFundingHistory($req);
        foreach ($resp->dataList as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->timePoint);
            self::assertNotNull($item->fundingRate);
            self::assertNotNull($item->markPrice);
            self::assertNotNull($item->positionQty);
            self::assertNotNull($item->positionCost);
            self::assertNotNull($item->funding);
            self::assertNotNull($item->settleCurrency);
            self::assertNotNull($item->context);
            self::assertNotNull($item->marginMode);
        }

        self::assertNotNull($resp->hasMore);
        Logger::info($resp->jsonSerialize($this->serializer));
    }



}