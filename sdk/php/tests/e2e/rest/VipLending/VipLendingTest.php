<?php

namespace Tests\e2e\rest\VipLending;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\VIPLending\Viplending\VIPLendingApi;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class VipLendingTest extends TestCase
{
    /**
     * @var VIPLendingApi $api
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
        $this->api = $kucoinRestService->getVipLendingService()->getVIPLendingApi();
    }


    /**
     * getDiscountRateConfigs
     * Get Discount Rate Configs
     * /api/v1/otc-loan/discount-rate-configs
     */
    public function testGetDiscountRateConfigs()
    {
        $resp = $this->api->getDiscountRateConfigs();
        foreach ($resp->data as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->usdtLevels);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getLoanInfo
     * Get Loan Info
     * /api/v1/otc-loan/loan
     * TODO 401
     */
    public function testGetLoanInfo()
    {
        $resp = $this->api->getLoanInfo();
        self::assertNotNull($resp->parentUid);
        foreach ($resp->orders as $item) {
            self::assertNotNull($item->orderId);
            self::assertNotNull($item->principal);
            self::assertNotNull($item->interest);
            self::assertNotNull($item->currency);
        }

        self::assertNotNull($resp->ltv);
        self::assertNotNull($resp->totalMarginAmount);
        self::assertNotNull($resp->transferMarginAmount);
        foreach ($resp->margins as $item) {
            self::assertNotNull($item->marginCcy);
            self::assertNotNull($item->marginQty);
            self::assertNotNull($item->marginFactor);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getAccounts
     * Get Accounts
     * /api/v1/otc-loan/accounts
     * TODO 401
     */
    public function testGetAccounts()
    {
        $resp = $this->api->getAccounts();
        foreach ($resp->data as $item) {
            self::assertNotNull($item->uid);
            self::assertNotNull($item->marginCcy);
            self::assertNotNull($item->marginQty);
            self::assertNotNull($item->marginFactor);
            self::assertNotNull($item->accountType);
            self::assertNotNull($item->isParent);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }


}