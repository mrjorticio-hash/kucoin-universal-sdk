<?php

namespace Tests\e2e\rest\Margin;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Margin\Risklimit\GetMarginRiskLimitReq;
use KuCoin\UniversalSDK\Generate\Margin\Risklimit\RiskLimitApi;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class MarginRisklimitTest extends TestCase
{
    /**
     * @var RiskLimitApi $api
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
        $this->api = $kucoinRestService->getMarginService()->getRiskLimitApi();
    }


    /**
     * getMarginRiskLimit
     * Get Margin Risk Limit
     * /api/v3/margin/currencies
     */
    public function testGetMarginRiskLimit()
    {
        $builder = GetMarginRiskLimitReq::builder();
        $builder->setIsIsolated('false')->setCurrency('BTC');
        $req = $builder->build();
        $resp = $this->api->getMarginRiskLimit($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->timestamp);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->borrowMaxAmount);
            self::assertNotNull($item->buyMaxAmount);
            self::assertNotNull($item->holdMaxAmount);
            self::assertNotNull($item->borrowCoefficient);
            self::assertNotNull($item->marginCoefficient);
            self::assertNotNull($item->precision);
            self::assertNotNull($item->borrowMinAmount);
            self::assertNotNull($item->borrowMinUnit);
            self::assertNotNull($item->borrowEnabled);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }
}