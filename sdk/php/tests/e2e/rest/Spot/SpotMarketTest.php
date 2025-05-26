<?php

namespace Tests\e2e\rest\Spot;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Spot\Market\Get24hrStatsReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetAllSymbolsReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetAnnouncementsReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetCallAuctionInfoReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetCallAuctionPartOrderBookReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetCurrencyReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetFiatPriceReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetFullOrderBookReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetKlinesReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetPartOrderBookReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetSymbolReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetTickerReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetTradeHistoryReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\MarketApi;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class SpotMarketTest extends TestCase
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
        $this->api = $kucoinRestService->getSpotService()->getMarketApi();
    }


    /**
     * getAnnouncements
     * Get Announcements
     * /api/v3/announcements
     */
    public function testGetAnnouncements()
    {
        $builder = GetAnnouncementsReq::builder();
        $builder->setAnnType("latest-announcements")->setLang("en_US");
        $req = $builder->build();
        $resp = $this->api->getAnnouncements($req);
        self::assertNotNull($resp->totalNum);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->annId);
            self::assertNotNull($item->annTitle);
            self::assertNotNull($item->annType);
            self::assertNotNull($item->annDesc);
            self::assertNotNull($item->cTime);
            self::assertNotNull($item->language);
            self::assertNotNull($item->annUrl);
        }

        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalPage);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getCurrency
     * Get Currency
     * /api/v3/currencies/{currency}
     */
    public function testGetCurrency()
    {
        $builder = GetCurrencyReq::builder();
        $builder->setCurrency("BTC")->setChain("btc");
        $req = $builder->build();
        $resp = $this->api->getCurrency($req);
        self::assertNotNull($resp->currency);
        self::assertNotNull($resp->name);
        self::assertNotNull($resp->fullName);
        self::assertNotNull($resp->precision);
        self::assertNotNull($resp->isMarginEnabled);
        self::assertNotNull($resp->isDebitEnabled);
        foreach ($resp->chains as $item) {
            self::assertNotNull($item->chainName);
            self::assertNotNull($item->withdrawalMinSize);
            self::assertNotNull($item->depositMinSize);
            self::assertNotNull($item->withdrawFeeRate);
            self::assertNotNull($item->withdrawalMinFee);
            self::assertNotNull($item->isWithdrawEnabled);
            self::assertNotNull($item->isDepositEnabled);
            self::assertNotNull($item->confirms);
            self::assertNotNull($item->preConfirms);
            self::assertNotNull($item->contractAddress);
            self::assertNotNull($item->withdrawPrecision);
            self::assertNotNull($item->needTag);
            self::assertNotNull($item->chainId);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getAllCurrencies
     * Get All Currencies
     * /api/v3/currencies
     */
    public function testGetAllCurrencies()
    {
        $resp = $this->api->getAllCurrencies();
        foreach ($resp->data as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->name);
            self::assertNotNull($item->fullName);
            self::assertNotNull($item->precision);
            self::assertNotNull($item->isMarginEnabled);
            self::assertNotNull($item->isDebitEnabled);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSymbol
     * Get Symbol
     * /api/v2/symbols/{symbol}
     */
    public function testGetSymbol()
    {
        $builder = GetSymbolReq::builder();
        $builder->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->getSymbol($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->name);
        self::assertNotNull($resp->baseCurrency);
        self::assertNotNull($resp->quoteCurrency);
        self::assertNotNull($resp->feeCurrency);
        self::assertNotNull($resp->market);
        self::assertNotNull($resp->baseMinSize);
        self::assertNotNull($resp->quoteMinSize);
        self::assertNotNull($resp->baseMaxSize);
        self::assertNotNull($resp->quoteMaxSize);
        self::assertNotNull($resp->baseIncrement);
        self::assertNotNull($resp->quoteIncrement);
        self::assertNotNull($resp->priceIncrement);
        self::assertNotNull($resp->priceLimitRate);
        self::assertNotNull($resp->minFunds);
        self::assertNotNull($resp->isMarginEnabled);
        self::assertNotNull($resp->enableTrading);
        self::assertNotNull($resp->feeCategory);
        self::assertNotNull($resp->makerFeeCoefficient);
        self::assertNotNull($resp->takerFeeCoefficient);
        self::assertNotNull($resp->st);
        self::assertNotNull($resp->callauctionIsEnabled);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getAllSymbols
     * Get All Symbols
     * /api/v2/symbols
     */
    public function testGetAllSymbols()
    {
        $builder = GetAllSymbolsReq::builder();
        $builder->setMarket("USDS");
        $req = $builder->build();
        $resp = $this->api->getAllSymbols($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->name);
            self::assertNotNull($item->baseCurrency);
            self::assertNotNull($item->quoteCurrency);
            self::assertNotNull($item->feeCurrency);
            self::assertNotNull($item->market);
            self::assertNotNull($item->baseMinSize);
            self::assertNotNull($item->quoteMinSize);
            self::assertNotNull($item->baseMaxSize);
            self::assertNotNull($item->quoteMaxSize);
            self::assertNotNull($item->baseIncrement);
            self::assertNotNull($item->quoteIncrement);
            self::assertNotNull($item->priceIncrement);
            self::assertNotNull($item->priceLimitRate);
            self::assertNotNull($item->minFunds);
            self::assertNotNull($item->isMarginEnabled);
            self::assertNotNull($item->enableTrading);
            self::assertNotNull($item->feeCategory);
            self::assertNotNull($item->makerFeeCoefficient);
            self::assertNotNull($item->takerFeeCoefficient);
            self::assertNotNull($item->st);
            self::assertNotNull($item->callauctionIsEnabled);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getTicker
     * Get Ticker
     * /api/v1/market/orderbook/level1
     */
    public function testGetTicker()
    {
        $builder = GetTickerReq::builder();
        $builder->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->getTicker($req);
        self::assertNotNull($resp->time);
        self::assertNotNull($resp->sequence);
        self::assertNotNull($resp->price);
        self::assertNotNull($resp->size);
        self::assertNotNull($resp->bestBid);
        self::assertNotNull($resp->bestBidSize);
        self::assertNotNull($resp->bestAsk);
        self::assertNotNull($resp->bestAskSize);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getAllTickers
     * Get All Tickers
     * /api/v1/market/allTickers
     */
    public function testGetAllTickers()
    {
        $resp = $this->api->getAllTickers();
        self::assertNotNull($resp->time);
        foreach ($resp->ticker as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->symbolName);
            self::assertNotNull($item->changeRate);
            self::assertNotNull($item->high);
            self::assertNotNull($item->low);
            self::assertNotNull($item->vol);
            self::assertNotNull($item->volValue);
            self::assertNotNull($item->takerFeeRate);
            self::assertNotNull($item->makerFeeRate);
            self::assertNotNull($item->takerCoefficient);
            self::assertNotNull($item->makerCoefficient);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getTradeHistory
     * Get Trade History
     * /api/v1/market/histories
     */
    public function testGetTradeHistory()
    {
        $builder = GetTradeHistoryReq::builder();
        $builder->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->getTradeHistory($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->sequence);
            self::assertNotNull($item->price);
            self::assertNotNull($item->size);
            self::assertNotNull($item->side);
            self::assertNotNull($item->time);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getKlines
     * Get Klines
     * /api/v1/market/candles
     */
    public function testGetKlines()
    {
        $builder = GetKlinesReq::builder();
        $builder->setSymbol("BTC-USDT")->setType("1min")->setStartAt(1566703297)->setEndAt(1566789757);
        $req = $builder->build();
        $resp = $this->api->getKlines($req);
        foreach ($resp->data as $item) {
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPartOrderBook
     * Get Part OrderBook
     * /api/v1/market/orderbook/level2_{size}
     */
    public function testGetPartOrderBook()
    {
        $builder = GetPartOrderBookReq::builder();
        $builder->setSymbol("BTC-USDT")->setSize('20');
        $req = $builder->build();
        $resp = $this->api->getPartOrderBook($req);
        self::assertNotNull($resp->time);
        self::assertNotNull($resp->sequence);
        foreach ($resp->bids as $item) {
            self::assertNotNull($item);
        }

        foreach ($resp->asks as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getFullOrderBook
     * Get Full OrderBook
     * /api/v3/market/orderbook/level2
     */
    public function testGetFullOrderBook()
    {
        $builder = GetFullOrderBookReq::builder();
        $builder->setSymbol("BTC-USDT");
        $req = $builder->build();
        $resp = $this->api->getFullOrderBook($req);
        self::assertNotNull($resp->time);
        self::assertNotNull($resp->sequence);
        foreach ($resp->bids as $item) {
            self::assertNotNull($item);
        }

        foreach ($resp->asks as $item)
            self::assertNotNull($item);
        {
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getCallAuctionPartOrderBook
     * Get Call Auction Part OrderBook
     * /api/v1/market/orderbook/callauction/level2_{size}
     */
    public function testGetCallAuctionPartOrderBook()
    {
        $builder = GetCallAuctionPartOrderBookReq::builder();
        $builder->setSymbol('NXPC-USDT')->setSize("20");
        $req = $builder->build();
        $resp = $this->api->getCallAuctionPartOrderBook($req);
        self::assertNotNull($resp->time);
        self::assertNotNull($resp->sequence);
        foreach ($resp->bids as $item) {
            self::assertNotNull($item);
        }

        foreach ($resp->asks as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getCallAuctionInfo
     * Get Call Auction Info
     * /api/v1/market/callauctionData
     */
    public function testGetCallAuctionInfo()
    {
        $builder = GetCallAuctionInfoReq::builder();
        $builder->setSymbol("NXPC-USDT");
        $req = $builder->build();
        $resp = $this->api->getCallAuctionInfo($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->sellOrderRangeLowPrice);
        self::assertNotNull($resp->sellOrderRangeHighPrice);
        self::assertNotNull($resp->buyOrderRangeLowPrice);
        self::assertNotNull($resp->buyOrderRangeHighPrice);
        self::assertNotNull($resp->time);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getFiatPrice
     * Get Fiat Price
     * /api/v1/prices
     */
    public function testGetFiatPrice()
    {
        $builder = GetFiatPriceReq::builder();
        $builder->setBase('USD')->setCurrencies('BTC,ETH');
        $req = $builder->build();
        $resp = $this->api->getFiatPrice($req);
        self::assertNotNull($resp->bTC);
        self::assertNotNull($resp->eTH);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * get24hrStats
     * Get 24hr Stats
     * /api/v1/market/stats
     */
    public function testGet24hrStats()
    {
        $builder = Get24hrStatsReq::builder();
        $builder->setSymbol('BTC-USDT');
        $req = $builder->build();
        $resp = $this->api->get24hrStats($req);
        self::assertNotNull($resp->time);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->buy);
        self::assertNotNull($resp->sell);
        self::assertNotNull($resp->changeRate);
        self::assertNotNull($resp->changePrice);
        self::assertNotNull($resp->high);
        self::assertNotNull($resp->low);
        self::assertNotNull($resp->vol);
        self::assertNotNull($resp->volValue);
        self::assertNotNull($resp->last);
        self::assertNotNull($resp->averagePrice);
        self::assertNotNull($resp->takerFeeRate);
        self::assertNotNull($resp->makerFeeRate);
        self::assertNotNull($resp->takerCoefficient);
        self::assertNotNull($resp->makerCoefficient);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getMarketList
     * Get Market List
     * /api/v1/markets
     */
    public function testGetMarketList()
    {
        $resp = $this->api->getMarketList();
        foreach ($resp->data as $item) {
            self::assertNotNull($item);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getClientIPAddress
     * Get Client IP Address
     * /api/v1/my-ip
     */
    public function testGetClientIPAddress()
    {
        $resp = $this->api->getClientIPAddress();
        self::assertNotNull($resp->data);
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
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->msg);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPublicToken
     * Get Public Token - Spot/Margin
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
     * Get Private Token - Spot/Margin
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