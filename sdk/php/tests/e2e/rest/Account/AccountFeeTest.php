<?php

namespace Tests\e2e\rest\Account;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Account\Fee\FeeApi;
use KuCoin\UniversalSDK\Generate\Account\Fee\GetBasicFeeReq;
use KuCoin\UniversalSDK\Generate\Account\Fee\GetFuturesActualFeeReq;
use KuCoin\UniversalSDK\Generate\Account\Fee\GetSpotActualFeeReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class AccountFeeTest extends TestCase
{
    /**
     * @var FeeApi $api
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
        $this->api = $kucoinRestService->getAccountService()->getFeeApi();
    }


    /**
     * getBasicFee
     * Get Basic Fee - Spot/Margin
     * /api/v1/base-fee
     */
    public function testGetBasicFee()
    {
        $builder = GetBasicFeeReq::builder();
        $builder->setCurrencyType(0);
        $req = $builder->build();
        $resp = $this->api->getBasicFee($req);
        self::assertNotNull($resp->takerFeeRate);
        self::assertNotNull($resp->makerFeeRate);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotActualFee
     * Get Actual Fee - Spot/Margin
     * /api/v1/trade-fees
     */
    public function testGetSpotActualFee()
    {
        $builder = GetSpotActualFeeReq::builder();
        $builder->setSymbols('BTC-USDT,ETH-USDT');
        $req = $builder->build();
        $resp = $this->api->getSpotActualFee($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->takerFeeRate);
            self::assertNotNull($item->makerFeeRate);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getFuturesActualFee
     * Get Actual Fee - Futures
     * /api/v1/trade-fees
     */
    public function testGetFuturesActualFee()
    {
        $builder = GetFuturesActualFeeReq::builder();
        $builder->setSymbol("XBTUSDM");
        $req = $builder->build();
        $resp = $this->api->getFuturesActualFee($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->takerFeeRate);
        self::assertNotNull($resp->makerFeeRate);
        Logger::info($resp->jsonSerialize($this->serializer));
    }


}