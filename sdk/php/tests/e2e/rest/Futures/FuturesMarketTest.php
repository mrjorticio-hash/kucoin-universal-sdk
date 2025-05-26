<?php

namespace Tests\e2e\rest\Futures;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Futures\Market\GetFullOrderBookReq;
use KuCoin\UniversalSDK\Generate\Futures\Market\GetInterestRateIndexReq;
use KuCoin\UniversalSDK\Generate\Futures\Market\GetKlinesReq;
use KuCoin\UniversalSDK\Generate\Futures\Market\GetMarkPriceReq;
use KuCoin\UniversalSDK\Generate\Futures\Market\GetPartOrderBookReq;
use KuCoin\UniversalSDK\Generate\Futures\Market\GetPremiumIndexReq;
use KuCoin\UniversalSDK\Generate\Futures\Market\GetSpotIndexPriceReq;
use KuCoin\UniversalSDK\Generate\Futures\Market\GetSymbolReq;
use KuCoin\UniversalSDK\Generate\Futures\Market\GetTickerReq;
use KuCoin\UniversalSDK\Generate\Futures\Market\GetTradeHistoryReq;
use KuCoin\UniversalSDK\Generate\Futures\Market\MarketApi;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class FuturesMarketTest extends TestCase
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
        $this->api = $kucoinRestService->getFuturesService()->getMarketApi();
    }


    /**
     * getSymbol
     * Get Symbol
     * /api/v1/contracts/{symbol}
     */
    public function testGetSymbol()
    {
        $builder = GetSymbolReq::builder();
        $builder->setSymbol("XBTUSDM");
        $req = $builder->build();
        $resp = $this->api->getSymbol($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->rootSymbol);
        self::assertNotNull($resp->type);
        self::assertNotNull($resp->firstOpenDate);
        self::assertNotNull($resp->baseCurrency);
        self::assertNotNull($resp->quoteCurrency);
        self::assertNotNull($resp->settleCurrency);
        self::assertNotNull($resp->maxOrderQty);
        self::assertNotNull($resp->maxPrice);
        self::assertNotNull($resp->lotSize);
        self::assertNotNull($resp->tickSize);
        self::assertNotNull($resp->indexPriceTickSize);
        self::assertNotNull($resp->multiplier);
        self::assertNotNull($resp->initialMargin);
        self::assertNotNull($resp->maintainMargin);
        self::assertNotNull($resp->maxRiskLimit);
        self::assertNotNull($resp->minRiskLimit);
        self::assertNotNull($resp->riskStep);
        self::assertNotNull($resp->makerFeeRate);
        self::assertNotNull($resp->takerFeeRate);
        self::assertNotNull($resp->takerFixFee);
        self::assertNotNull($resp->makerFixFee);
        self::assertNotNull($resp->isDeleverage);
        self::assertNotNull($resp->isQuanto);
        self::assertNotNull($resp->isInverse);
        self::assertNotNull($resp->markMethod);
        self::assertNotNull($resp->fairMethod);
        self::assertNotNull($resp->fundingBaseSymbol);
        self::assertNotNull($resp->fundingQuoteSymbol);
        self::assertNotNull($resp->fundingRateSymbol);
        self::assertNotNull($resp->indexSymbol);
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->fundingFeeRate);
        self::assertNotNull($resp->predictedFundingFeeRate);
        self::assertNotNull($resp->fundingRateGranularity);
        self::assertNotNull($resp->openInterest);
        self::assertNotNull($resp->turnoverOf24h);
        self::assertNotNull($resp->volumeOf24h);
        self::assertNotNull($resp->markPrice);
        self::assertNotNull($resp->indexPrice);
        self::assertNotNull($resp->lastTradePrice);
        self::assertNotNull($resp->nextFundingRateTime);
        self::assertNotNull($resp->maxLeverage);
        foreach ($resp->sourceExchanges as $item) {
            self::assertNotNull($item);
        }

        self::assertNotNull($resp->premiumsSymbol1M);
        self::assertNotNull($resp->premiumsSymbol8H);
        self::assertNotNull($resp->fundingBaseSymbol1M);
        self::assertNotNull($resp->fundingQuoteSymbol1M);
        self::assertNotNull($resp->lowPrice);
        self::assertNotNull($resp->highPrice);
        self::assertNotNull($resp->priceChgPct);
        self::assertNotNull($resp->priceChg);
        self::assertNotNull($resp->k);
        self::assertNotNull($resp->m);
        self::assertNotNull($resp->f);
        self::assertNotNull($resp->mmrLimit);
        self::assertNotNull($resp->mmrLevConstant);
        self::assertNotNull($resp->supportCross);
        self::assertNotNull($resp->buyLimit);
        self::assertNotNull($resp->sellLimit);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getAllSymbols
     * Get All Symbols
     * /api/v1/contracts/active
     */
    public function testGetAllSymbols()
    {
        $resp = $this->api->getAllSymbols();
        foreach ($resp->data as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->rootSymbol);
            self::assertNotNull($item->type);
            self::assertNotNull($item->firstOpenDate);
            self::assertNotNull($item->baseCurrency);
            self::assertNotNull($item->quoteCurrency);
            self::assertNotNull($item->settleCurrency);
            self::assertNotNull($item->maxOrderQty);
            self::assertNotNull($item->maxPrice);
            self::assertNotNull($item->lotSize);
            self::assertNotNull($item->tickSize);
            self::assertNotNull($item->indexPriceTickSize);
            self::assertNotNull($item->multiplier);
            self::assertNotNull($item->initialMargin);
            self::assertNotNull($item->maintainMargin);
            self::assertNotNull($item->maxRiskLimit);
            self::assertNotNull($item->minRiskLimit);
            self::assertNotNull($item->riskStep);
            self::assertNotNull($item->makerFeeRate);
            self::assertNotNull($item->takerFeeRate);
            self::assertNotNull($item->takerFixFee);
            self::assertNotNull($item->makerFixFee);
            self::assertNotNull($item->isDeleverage);
            self::assertNotNull($item->isQuanto);
            self::assertNotNull($item->isInverse);
            self::assertNotNull($item->markMethod);
            self::assertNotNull($item->indexSymbol);
            self::assertNotNull($item->status);
            self::assertNotNull($item->openInterest);
            self::assertNotNull($item->turnoverOf24h);
            self::assertNotNull($item->volumeOf24h);
            self::assertNotNull($item->markPrice);
            self::assertNotNull($item->indexPrice);
            self::assertNotNull($item->lastTradePrice);
            self::assertNotNull($item->maxLeverage);
            self::assertNotNull($item->sourceExchanges);
            self::assertNotNull($item->premiumsSymbol1M);
            self::assertNotNull($item->premiumsSymbol8H);
            self::assertNotNull($item->lowPrice);
            self::assertNotNull($item->highPrice);
            self::assertNotNull($item->priceChgPct);
            self::assertNotNull($item->priceChg);
            self::assertNotNull($item->k);
            self::assertNotNull($item->m);
            self::assertNotNull($item->f);
            self::assertNotNull($item->mmrLimit);
            self::assertNotNull($item->mmrLevConstant);
            self::assertNotNull($item->supportCross);
            self::assertNotNull($item->buyLimit);
            self::assertNotNull($item->sellLimit);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getTicker
     * Get Ticker
     * /api/v1/ticker
     */
    public function testGetTicker()
    {
        $builder = GetTickerReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getTicker($req);
        self::assertNotNull($resp->sequence);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->side);
        self::assertNotNull($resp->size);
        self::assertNotNull($resp->tradeId);
        self::assertNotNull($resp->price);
        self::assertNotNull($resp->bestBidPrice);
        self::assertNotNull($resp->bestBidSize);
        self::assertNotNull($resp->bestAskPrice);
        self::assertNotNull($resp->bestAskSize);
        self::assertNotNull($resp->ts);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getAllTickers
     * Get All Tickers
     * /api/v1/allTickers
     */
    public function testGetAllTickers()
    {
        $resp = $this->api->getAllTickers();
        foreach ($resp->data as $item) {
            self::assertNotNull($item->sequence);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->side);
            self::assertNotNull($item->size);
            self::assertNotNull($item->tradeId);
            self::assertNotNull($item->price);
            self::assertNotNull($item->bestBidPrice);
            self::assertNotNull($item->bestBidSize);
            self::assertNotNull($item->bestAskPrice);
            self::assertNotNull($item->bestAskSize);
            self::assertNotNull($item->ts);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getFullOrderBook
     * Get Full OrderBook
     * /api/v1/level2/snapshot
     */
    public function testGetFullOrderBook()
    {
        $builder = GetFullOrderBookReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getFullOrderBook($req);
        self::assertNotNull($resp->sequence);
        self::assertNotNull($resp->symbol);
        foreach ($resp->bids as $item) {
            self::assertNotNull($item);

        }

        foreach ($resp->asks as $item) {
            self::assertNotNull($item);
        }

        self::assertNotNull($resp->ts);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPartOrderBook
     * Get Part OrderBook
     * /api/v1/level2/depth{size}
     */
    public function testGetPartOrderBook()
    {
        $builder = GetPartOrderBookReq::builder();
        $builder->setSize("100")->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getPartOrderBook($req);
        self::assertNotNull($resp->sequence);
        self::assertNotNull($resp->symbol);
        foreach ($resp->bids as $item) {
            self::assertNotNull($item);
        }

        foreach ($resp->asks as $item) {
            self::assertNotNull($item);
        }

        self::assertNotNull($resp->ts);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getTradeHistory
     * Get Trade History
     * /api/v1/trade/history
     */
    public function testGetTradeHistory()
    {
        $builder = GetTradeHistoryReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getTradeHistory($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->sequence);
            self::assertNotNull($item->contractId);
            self::assertNotNull($item->tradeId);
            self::assertNotNull($item->makerOrderId);
            self::assertNotNull($item->takerOrderId);
            self::assertNotNull($item->ts);
            self::assertNotNull($item->size);
            self::assertNotNull($item->price);
            self::assertNotNull($item->side);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getKlines
     * Get Klines
     * /api/v1/kline/query
     */
    public function testGetKlines()
    {
        $builder = GetKlinesReq::builder();
        $builder->setSymbol("XBTUSDTM")->setGranularity(1)->setFrom(1738339200000)->setTo(1738425600000);
        $req = $builder->build();
        $resp = $this->api->getKlines($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getMarkPrice
     * Get Mark Price
     * /api/v1/mark-price/{symbol}/current
     */
    public function testGetMarkPrice()
    {
        $builder = GetMarkPriceReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getMarkPrice($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->granularity);
        self::assertNotNull($resp->timePoint);
        self::assertNotNull($resp->value);
        self::assertNotNull($resp->indexPrice);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSpotIndexPrice
     * Get Spot Index Price
     * /api/v1/index/query
     */
    public function testGetSpotIndexPrice()
    {
        $builder = GetSpotIndexPriceReq::builder();
        $builder->setSymbol(".KXBTUSDT")->setStartAt(1738339200000)->setEndAt(1738425600000)->setMaxCount(10);
        $req = $builder->build();
        $resp = $this->api->getSpotIndexPrice($req);
        foreach ($resp->dataList as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->granularity);
            self::assertNotNull($item->timePoint);
            self::assertNotNull($item->value);
            self::assertNotNull($item->decomposionList);
        }

        self::assertNotNull($resp->hasMore);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getInterestRateIndex
     * Get Interest Rate Index
     * /api/v1/interest/query
     */
    public function testGetInterestRateIndex()
    {
        $builder = GetInterestRateIndexReq::builder();
        $builder->setSymbol(".XBTINT")->setStartAt(1738339200000)->setEndAt(1738425600000);
        $req = $builder->build();
        $resp = $this->api->getInterestRateIndex($req);
        foreach ($resp->dataList as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->granularity);
            self::assertNotNull($item->timePoint);
            self::assertNotNull($item->value);
        }

        self::assertNotNull($resp->hasMore);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPremiumIndex
     * Get Premium Index
     * /api/v1/premium/query
     */
    public function testGetPremiumIndex()
    {
        $builder = GetPremiumIndexReq::builder();
        $builder->setSymbol('.XBTUSDTMPI')->setStartAt(1746028800000)->setEndAt(1746547200000);
        $req = $builder->build();
        $resp = $this->api->getPremiumIndex($req);
        foreach ($resp->dataList as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->granularity);
            self::assertNotNull($item->timePoint);
            self::assertNotNull($item->value);
        }

        self::assertNotNull($resp->hasMore);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * get24hrStats
     * Get 24hr stats
     * /api/v1/trade-statistics
     */
    public function testGet24hrStats()
    {
        $resp = $this->api->get24hrStats();
        self::assertNotNull($resp->turnoverOf24h);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getServerTime
     * Get Server Time
     * /api/v1/timestamp
     */
    public function testGetServerTime()
    {
        $resp = $this->api->getServerTime();
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getServiceStatus
     * Get Service Status
     * /api/v1/status
     */
    public function testGetServiceStatus()
    {
        $resp = $this->api->getServiceStatus();
        self::assertNotNull($resp->msg);
        self::assertNotNull($resp->status);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPublicToken
     * Get Public Token - Futures
     * /api/v1/bullet-public
     */
    public function testGetPublicToken()
    {
        $resp = $this->api->getPublicToken();
        self::assertNotNull($resp->token);
        foreach ($resp->instanceServers as $item) {
            self::assertNotNull($item->endpoint);
            self::assertNotNull($item->encrypt);
            self::assertNotNull($item->protocol);
            self::assertNotNull($item->pingInterval);
            self::assertNotNull($item->pingTimeout);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPrivateToken
     * Get Private Token - Futures
     * /api/v1/bullet-private
     */
    public function testGetPrivateToken()
    {
        $resp = $this->api->getPrivateToken();
        self::assertNotNull($resp->token);
        foreach ($resp->instanceServers as $item) {
            self::assertNotNull($item->endpoint);
            self::assertNotNull($item->encrypt);
            self::assertNotNull($item->protocol);
            self::assertNotNull($item->pingInterval);
            self::assertNotNull($item->pingTimeout);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }


}