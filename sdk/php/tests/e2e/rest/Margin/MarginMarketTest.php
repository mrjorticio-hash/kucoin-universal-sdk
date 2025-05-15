<?php

namespace Tests\e2e\rest\Margin;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Extension\Interceptor\Logging;
use KuCoin\UniversalSDK\Generate\Margin\Market\GetCrossMarginSymbolsReq;
use KuCoin\UniversalSDK\Generate\Margin\Market\GetETFInfoReq;
use KuCoin\UniversalSDK\Generate\Margin\Market\GetMarkPriceDetailReq;
use KuCoin\UniversalSDK\Generate\Margin\Market\MarketApi;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class MarginMarketTest extends TestCase
{
    /**
     * @var MarketApi $api
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
            ->setInterceptors([new Logging()])
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
        $this->api = $kucoinRestService->getMarginService()->getMarketApi();
    }


    /**
     * getCrossMarginSymbols
     * Get Symbols - Cross Margin
     * /api/v3/margin/symbols
     */
    public function testGetCrossMarginSymbols()
    {
        $builder = GetCrossMarginSymbolsReq::builder();
        $builder->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->getCrossMarginSymbols($req);
        self::assertNotNull($resp->timestamp);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->name);
            self::assertNotNull($item->enableTrading);
            self::assertNotNull($item->market);
            self::assertNotNull($item->baseCurrency);
            self::assertNotNull($item->quoteCurrency);
            self::assertNotNull($item->baseIncrement);
            self::assertNotNull($item->baseMinSize);
            self::assertNotNull($item->quoteIncrement);
            self::assertNotNull($item->quoteMinSize);
            self::assertNotNull($item->baseMaxSize);
            self::assertNotNull($item->quoteMaxSize);
            self::assertNotNull($item->priceIncrement);
            self::assertNotNull($item->feeCurrency);
            self::assertNotNull($item->priceLimitRate);
            self::assertNotNull($item->minFunds);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getETFInfo
     * Get ETF Info
     * /api/v3/etf/info
     */
    public function testGetETFInfo()
    {
        $builder = GetETFInfoReq::builder();
        $builder->setCurrency("BTCUP");
        $req = $builder->build();
        $resp = $this->api->getETFInfo($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->netAsset);
            self::assertNotNull($item->targetLeverage);
            self::assertNotNull($item->actualLeverage);
            self::assertNotNull($item->issuedSize);
            self::assertNotNull($item->basket);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getMarkPriceDetail
     * Get Mark Price Detail
     * /api/v1/mark-price/{symbol}/current
     */
    public function testGetMarkPriceDetail()
    {
        $builder = GetMarkPriceDetailReq::builder();
        $builder->setSymbol('USDT-BTC');
        $req = $builder->build();
        $resp = $this->api->getMarkPriceDetail($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->timePoint);
        self::assertNotNull($resp->value);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getMarginConfig
     * Get Margin Config
     * /api/v1/margin/config
     */
    public function testGetMarginConfig()
    {
        $resp = $this->api->getMarginConfig();
        foreach ($resp->currencyList as $item) {
            self::assertNotNull($item);
        }

        self::assertNotNull($resp->maxLeverage);
        self::assertNotNull($resp->warningDebtRatio);
        self::assertNotNull($resp->liqDebtRatio);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getMarkPriceList
     * Get Mark Price List
     * /api/v3/mark-price/all-symbols
     */
    public function testGetMarkPriceList()
    {
        $resp = $this->api->getMarkPriceList();
        foreach ($resp->data as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->timePoint);
            self::assertNotNull($item->value);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getIsolatedMarginSymbols
     * Get Symbols - Isolated Margin
     * /api/v1/isolated/symbols
     */
    public function testGetIsolatedMarginSymbols()
    {
        $resp = $this->api->getIsolatedMarginSymbols();
        foreach ($resp->data as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->symbolName);
            self::assertNotNull($item->baseCurrency);
            self::assertNotNull($item->quoteCurrency);
            self::assertNotNull($item->maxLeverage);
            self::assertNotNull($item->flDebtRatio);
            self::assertNotNull($item->tradeEnable);
            self::assertNotNull($item->autoRenewMaxDebtRatio);
            self::assertNotNull($item->baseBorrowEnable);
            self::assertNotNull($item->quoteBorrowEnable);
            self::assertNotNull($item->baseTransferInEnable);
            self::assertNotNull($item->quoteTransferInEnable);
            self::assertNotNull($item->baseBorrowCoefficient);
            self::assertNotNull($item->quoteBorrowCoefficient);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

}