import {
    ClientOptionBuilder,
    GlobalApiEndpoint,
    GlobalBrokerApiEndpoint,
    GlobalFuturesApiEndpoint,
    TransportOptionBuilder,
} from '@model/index';
import {
    Get24hrStatsReq,
    GetAllSymbolsReq,
    GetAnnouncementsReq, GetCallAuctionInfoReq, GetCallAuctionPartOrderBookReq,
    GetCurrencyReq,
    GetFiatPriceReq,
    GetFullOrderBookReq,
    GetKlinesReq,
    GetPartOrderBookReq,
    GetSymbolReq,
    GetTickerReq,
    GetTradeHistoryReq,
    MarketAPI,
} from '@src/generate/spot/market';
import { DefaultClient } from '@api/index';

describe('Auto Test', () => {
    let api: MarketAPI;

    beforeAll(() => {
        const key = process.env.API_KEY || '';
        const secret = process.env.API_SECRET || '';
        const passphrase = process.env.API_PASSPHRASE || '';

        // Set specific options, others will fall back to default values
        const httpTransportOption = new TransportOptionBuilder()
            .setKeepAlive(true)
            .setMaxConnsPerHost(10)
            .setMaxIdleConns(10)
            .build();

        // Create a client using the specified options
        const clientOption = new ClientOptionBuilder()
            .setKey(key)
            .setSecret(secret)
            .setPassphrase(passphrase)
            .setSpotEndpoint(GlobalApiEndpoint)
            .setFuturesEndpoint(GlobalFuturesApiEndpoint)
            .setBrokerEndpoint(GlobalBrokerApiEndpoint)
            .setTransportOption(httpTransportOption)
            .build();

        const client = new DefaultClient(clientOption);

        // Get the Restful Service
        const kucoinRestService = client.restService();
        api = kucoinRestService.getSpotService().getMarketApi();
    });

    test('getAnnouncements request test', () => {
        /**
         * getAnnouncements
         * Get Announcements
         * /api/v3/announcements
         */
        let builder = GetAnnouncementsReq.builder();
        builder
            .setAnnType(GetAnnouncementsReq.AnnTypeEnum.LATEST_ANNOUNCEMENTS)
            .setLang(GetAnnouncementsReq.LangEnum.EN_US);
        let req = builder.build();
        let resp = api.getAnnouncements(req);
        return resp.then((result) => {
            expect(result.totalNum).toEqual(expect.anything());
            result.items.forEach((item) => {
                expect(item.annId).toEqual(expect.any(Number));
                expect(item.annTitle).toEqual(expect.any(String));
                expect(item.annType).toEqual(expect.any(Array));
                expect(item.annDesc).toEqual(expect.any(String));
                expect(item.cTime).toEqual(expect.any(Number));
                expect(item.language).toEqual(expect.any(String));
                expect(item.annUrl).toEqual(expect.any(String));
            });
            expect(result.currentPage).toEqual(expect.anything());
            expect(result.pageSize).toEqual(expect.anything());
            expect(result.totalPage).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getCurrency request test', () => {
        /**
         * getCurrency
         * Get Currency
         * /api/v3/currencies/{currency}
         */
        let builder = GetCurrencyReq.builder();
        builder.setCurrency('BTC').setChain('btc');
        let req = builder.build();
        let resp = api.getCurrency(req);
        return resp.then((result) => {
            expect(result.currency).toEqual(expect.anything());
            expect(result.name).toEqual(expect.anything());
            expect(result.fullName).toEqual(expect.anything());
            expect(result.precision).toEqual(expect.anything());
            expect(result.isMarginEnabled).toEqual(expect.anything());
            expect(result.isDebitEnabled).toEqual(expect.anything());
            expect(result.chains).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getAllCurrencies request test', () => {
        /**
         * getAllCurrencies
         * Get All Currencies
         * /api/v3/currencies
         */
        let resp = api.getAllCurrencies();
        return resp.then((result) => {
            result.data.forEach((item) => {
                expect(item.currency).toEqual(expect.any(String));
                expect(item.name).toEqual(expect.any(String));
                expect(item.fullName).toEqual(expect.any(String));
                expect(item.precision).toEqual(expect.any(Number));
                expect(item.isMarginEnabled).toEqual(expect.any(Boolean));
                expect(item.isDebitEnabled).toEqual(expect.any(Boolean));
            });
            console.log(resp);
        });
    });

    test('getSymbol request test', () => {
        /**
         * getSymbol
         * Get Symbol
         * /api/v2/symbols/{symbol}
         */
        let builder = GetSymbolReq.builder();
        builder.setSymbol('BTC-USDT');
        let req = builder.build();
        let resp = api.getSymbol(req);
        return resp.then((result) => {
            expect(result.symbol).toEqual(expect.anything());
            expect(result.name).toEqual(expect.anything());
            expect(result.baseCurrency).toEqual(expect.anything());
            expect(result.quoteCurrency).toEqual(expect.anything());
            expect(result.feeCurrency).toEqual(expect.anything());
            expect(result.market).toEqual(expect.anything());
            expect(result.baseMinSize).toEqual(expect.anything());
            expect(result.quoteMinSize).toEqual(expect.anything());
            expect(result.baseMaxSize).toEqual(expect.anything());
            expect(result.quoteMaxSize).toEqual(expect.anything());
            expect(result.baseIncrement).toEqual(expect.anything());
            expect(result.quoteIncrement).toEqual(expect.anything());
            expect(result.priceIncrement).toEqual(expect.anything());
            expect(result.priceLimitRate).toEqual(expect.anything());
            expect(result.minFunds).toEqual(expect.anything());
            expect(result.isMarginEnabled).toEqual(expect.anything());
            expect(result.enableTrading).toEqual(expect.anything());
            expect(result.feeCategory).toEqual(expect.anything());
            expect(result.makerFeeCoefficient).toEqual(expect.anything());
            expect(result.takerFeeCoefficient).toEqual(expect.anything());
            expect(result.st).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getAllSymbols request test', () => {
        /**
         * getAllSymbols
         * Get All Symbols
         * /api/v2/symbols
         */
        let builder = GetAllSymbolsReq.builder();
        builder.setMarket('USDS');
        let req = builder.build();
        let resp = api.getAllSymbols(req);
        return resp.then((result) => {
            result.data.forEach((item) => {
                expect(item.symbol).toEqual(expect.any(String));
                expect(item.name).toEqual(expect.any(String));
                expect(item.baseCurrency).toEqual(expect.any(String));
                expect(item.quoteCurrency).toEqual(expect.any(String));
                expect(item.feeCurrency).toEqual(expect.any(String));
                expect(item.market).toEqual(expect.any(String));
                expect(item.baseMinSize).toEqual(expect.any(String));
                expect(item.quoteMinSize).toEqual(expect.any(String));
                expect(item.baseMaxSize).toEqual(expect.any(String));
                expect(item.quoteMaxSize).toEqual(expect.any(String));
                expect(item.baseIncrement).toEqual(expect.any(String));
                expect(item.quoteIncrement).toEqual(expect.any(String));
                expect(item.priceIncrement).toEqual(expect.any(String));
                expect(item.priceLimitRate).toEqual(expect.any(String));
                expect(item.minFunds).toEqual(expect.any(String));
                expect(item.enableTrading).toEqual(expect.any(Boolean));
                expect(item.makerFeeCoefficient).toEqual(expect.any(String));
                expect(item.takerFeeCoefficient).toEqual(expect.any(String));
                expect(item.st).toEqual(expect.any(Boolean));
            });
            console.log(resp);
        });
    });

    test('getTicker request test', () => {
        /**
         * getTicker
         * Get Ticker
         * /api/v1/market/orderbook/level1
         */
        let builder = GetTickerReq.builder();
        builder.setSymbol('BTC-USDT');
        let req = builder.build();
        let resp = api.getTicker(req);
        return resp.then((result) => {
            expect(result.time).toEqual(expect.anything());
            expect(result.sequence).toEqual(expect.anything());
            expect(result.price).toEqual(expect.anything());
            expect(result.size).toEqual(expect.anything());
            expect(result.bestBid).toEqual(expect.anything());
            expect(result.bestBidSize).toEqual(expect.anything());
            expect(result.bestAsk).toEqual(expect.anything());
            expect(result.bestAskSize).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getAllTickers request test', () => {
        /**
         * getAllTickers
         * Get All Tickers
         * /api/v1/market/allTickers
         */
        let resp = api.getAllTickers();
        return resp.then((result) => {
            expect(result.time).toEqual(expect.anything());
            expect(result.ticker).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getTradeHistory request test', () => {
        /**
         * getTradeHistory
         * Get Trade History
         * /api/v1/market/histories
         */
        let builder = GetTradeHistoryReq.builder();
        builder.setSymbol('BTC-USDT');
        let req = builder.build();
        let resp = api.getTradeHistory(req);
        return resp.then((result) => {
            result.data.forEach((item) => {
                expect(item.sequence).toEqual(expect.any(String));
                expect(item.price).toEqual(expect.any(String));
                expect(item.size).toEqual(expect.any(String));
                expect(item.side).toEqual(expect.any(String));
                expect(item.time).toEqual(expect.any(Number));
            });
            console.log(resp);
        });
    });

    test('getKlines request test', () => {
        /**
         * getKlines
         * Get Klines
         * /api/v1/market/candles
         */
        let builder = GetKlinesReq.builder();
        builder
            .setSymbol('BTC-USDT')
            .setType(GetKlinesReq.TypeEnum._1MIN)
            .setStartAt(1566703297)
            .setEndAt(1566789757);
        let req = builder.build();
        let resp = api.getKlines(req);
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getPartOrderBook request test', () => {
        /**
         * getPartOrderBook
         * Get Part OrderBook
         * /api/v1/market/orderbook/level2_{size}
         */
        let builder = GetPartOrderBookReq.builder();
        builder.setSymbol('BTC-USDT').setSize('20');
        let req = builder.build();
        let resp = api.getPartOrderBook(req);
        return resp.then((result) => {
            expect(result.time).toEqual(expect.anything());
            expect(result.sequence).toEqual(expect.anything());
            expect(result.bids).toEqual(expect.anything());
            expect(result.asks).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getFullOrderBook request test', () => {
        /**
         * getFullOrderBook
         * Get Full OrderBook
         * /api/v3/market/orderbook/level2
         */
        let builder = GetFullOrderBookReq.builder();
        builder.setSymbol('BTC-USDT');
        let req = builder.build();
        let resp = api.getFullOrderBook(req);
        return resp.then((result) => {
            expect(result.time).toEqual(expect.anything());
            expect(result.sequence).toEqual(expect.anything());
            expect(result.bids).toEqual(expect.anything());
            expect(result.asks).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getFiatPrice request test', () => {
        /**
         * getFiatPrice
         * Get Fiat Price
         * /api/v1/prices
         */
        let builder = GetFiatPriceReq.builder();
        builder.setBase('USD').setCurrencies('BTC,ETH');
        let req = builder.build();
        let resp = api.getFiatPrice(req);
        return resp.then((result) => {
            expect(result.BTC).toEqual(expect.anything());
            expect(result.ETH).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getCallAuctionPartOrderBook request test', ()=> {
        /**
         * getCallAuctionPartOrderBook
         * Get Call Auction Part OrderBook
         * /api/v1/market/orderbook/callauction/level2_{size}
         */
        let builder = GetCallAuctionPartOrderBookReq.builder();
        builder.setSymbol("PAWS-USDT").setSize("20");
        let req = builder.build();
        let resp = api.getCallAuctionPartOrderBook(req);
        return resp.then(result => {
            expect(result.time).toEqual(expect.anything());
            expect(result.sequence).toEqual(expect.anything());
            expect(result.bids).toEqual(expect.anything());
            expect(result.asks).toEqual(expect.anything());
            console.log(resp);
        });
    })

    test('getCallAuctionInfo request test', ()=> {
        /**
         * getCallAuctionInfo
         * Get Call Auction Info
         * /api/v1/market/callauctionData
         */
        let builder = GetCallAuctionInfoReq.builder();
        builder.setSymbol("PAWS-USDT");
        let req = builder.build();
        let resp = api.getCallAuctionInfo(req);
        return resp.then(result => {
            expect(result.symbol).toEqual(expect.anything());
            expect(result.estimatedPrice).toEqual(expect.anything());
            expect(result.estimatedSize).toEqual(expect.anything());
            expect(result.sellOrderRangeLowPrice).toEqual(expect.anything());
            expect(result.sellOrderRangeHighPrice).toEqual(expect.anything());
            expect(result.buyOrderRangeLowPrice).toEqual(expect.anything());
            expect(result.buyOrderRangeHighPrice).toEqual(expect.anything());
            expect(result.time).toEqual(expect.anything());
            console.log(resp);
        });
    })

    test('get24hrStats request test', () => {
        /**
         * get24hrStats
         * Get 24hr Stats
         * /api/v1/market/stats
         */
        let builder = Get24hrStatsReq.builder();
        builder.setSymbol('BTC-USDT');
        let req = builder.build();
        let resp = api.get24hrStats(req);
        return resp.then((result) => {
            expect(result.time).toEqual(expect.anything());
            expect(result.symbol).toEqual(expect.anything());
            expect(result.buy).toEqual(expect.anything());
            expect(result.sell).toEqual(expect.anything());
            expect(result.changeRate).toEqual(expect.anything());
            expect(result.changePrice).toEqual(expect.anything());
            expect(result.high).toEqual(expect.anything());
            expect(result.low).toEqual(expect.anything());
            expect(result.vol).toEqual(expect.anything());
            expect(result.volValue).toEqual(expect.anything());
            expect(result.last).toEqual(expect.anything());
            expect(result.averagePrice).toEqual(expect.anything());
            expect(result.takerFeeRate).toEqual(expect.anything());
            expect(result.makerFeeRate).toEqual(expect.anything());
            expect(result.takerCoefficient).toEqual(expect.anything());
            expect(result.makerCoefficient).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getMarketList request test', () => {
        /**
         * getMarketList
         * Get Market List
         * /api/v1/markets
         */
        let resp = api.getMarketList();
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getClientIPAddress request test', ()=> {
        /**
         * getClientIPAddress
         * Get Client IP Address
         * /api/v1/my-ip
         */
        let resp = api.getClientIPAddress();
        return resp.then(result => {
            expect(result.data).toEqual(expect.anything());
            console.log(resp);
        });
    })

    test('getServerTime request test', () => {
        /**
         * getServerTime
         * Get Server Time
         * /api/v1/timestamp
         */
        let resp = api.getServerTime();
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getServiceStatus request test', () => {
        /**
         * getServiceStatus
         * Get Service Status
         * /api/v1/status
         */
        let resp = api.getServiceStatus();
        return resp.then((result) => {
            expect(result.status).toEqual(expect.anything());
            expect(result.msg).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getPublicToken request test', () => {
        /**
         * getPublicToken
         * Get Public Token - Spot/Margin
         * /api/v1/bullet-public
         */
        let resp = api.getPublicToken();
        return resp.then((result) => {
            expect(result.token).toEqual(expect.anything());
            expect(result.instanceServers).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getPrivateToken request test', () => {
        /**
         * getPrivateToken
         * Get Private Token - Spot/Margin
         * /api/v1/bullet-private
         */
        let resp = api.getPrivateToken();
        return resp.then((result) => {
            expect(result.token).toEqual(expect.anything());
            expect(result.instanceServers).toEqual(expect.anything());
            console.log(resp);
        });
    });
});
