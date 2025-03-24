import {
    ClientOptionBuilder,
    GlobalApiEndpoint,
    GlobalBrokerApiEndpoint,
    GlobalFuturesApiEndpoint,
    WebSocketClientOptionBuilder,
} from '@model/index';
import {
    AllTickersEvent,
    SpotPublicWS,
    TickerEvent,
    OrderbookIncrementEvent,
    OrderbookLevel5Event,
    KlinesEvent,
    TradeEvent,
    OrderbookLevel50Event,
    MarketSnapshotEvent,
    CallAuctionInfoEvent,
    CallAuctionOrderbookLevel50Event,
} from '@src/generate/spot/spotpublic';
import { DefaultClient } from '@api/index';

function delay(ms: number): Promise<void> {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

// Set longer timeout for WebSocket tests
jest.setTimeout(30000);

describe('Spot Public WebSocket API Tests', () => {
    let api: SpotPublicWS;
    const TEST_SYMBOL = 'BTC-USDT';
    const TEST_DURATION = 5000; // 5 seconds for each test

    beforeAll(async () => {
        const key = process.env.API_KEY || '';
        const secret = process.env.API_SECRET || '';
        const passphrase = process.env.API_PASSPHRASE || '';

        // Set specific options, others will fall back to default values
        const websocketClientOption = new WebSocketClientOptionBuilder().build();

        // Create a client using the specified options
        const clientOption = new ClientOptionBuilder()
            .setKey(key)
            .setSecret(secret)
            .setPassphrase(passphrase)
            .setSpotEndpoint(GlobalApiEndpoint)
            .setFuturesEndpoint(GlobalFuturesApiEndpoint)
            .setBrokerEndpoint(GlobalBrokerApiEndpoint)
            .setWebSocketClientOption(websocketClientOption)
            .build();

        const client = new DefaultClient(clientOption);

        // Get the Restful Service
        const wsService = client.wsService();
        api = wsService.newSpotPublicWS();
        return api.start();
    });

    afterAll(() => {
        return api.stop();
    });

    test('allTickers test', () => {
        let subid = api.allTickers((topic: string, subject: string, data: AllTickersEvent) => {
            expect(data.bestAsk).toEqual(expect.anything());
            expect(data.bestAskSize).toEqual(expect.anything());
            expect(data.bestBid).toEqual(expect.anything());
            expect(data.bestBidSize).toEqual(expect.anything());
            expect(data.price).toEqual(expect.anything());
            expect(data.sequence).toEqual(expect.anything());
            expect(data.size).toEqual(expect.anything());
            expect(data.time).toEqual(expect.anything());
            // console.log(data);
        });

        return subid
            .then(async (id) => {
                await delay(5000);
                return id;
            })
            .then((id) => {
                return api.unSubscribe(id);
            });
    });

    test('ticker test', () => {
        const subid = api.ticker(
            [TEST_SYMBOL],
            (topic: string, subject: string, data: TickerEvent) => {
                expect(data).toBeDefined();
                expect(data.sequence).toEqual(expect.anything());
                expect(data.price).toEqual(expect.anything());
                expect(data.size).toEqual(expect.anything());
                expect(data.bestAsk).toEqual(expect.anything());
                expect(data.bestAskSize).toEqual(expect.anything());
                expect(data.bestBid).toEqual(expect.anything());
                expect(data.bestBidSize).toEqual(expect.anything());
                console.log(data);
            },
        );

        return subid
            .then(async (id) => {
                await delay(5000);
                return id;
            })
            .then((id) => {
                return api.unSubscribe(id);
            });
    });

    test('orderbook increment subscription test', () => {
        const subid = api.orderbookIncrement(
            [TEST_SYMBOL],
            (topic: string, subject: string, data: OrderbookIncrementEvent) => {
                expect(data).toBeDefined();
                expect(data.sequenceStart).toEqual(expect.anything());
                expect(data.sequenceEnd).toEqual(expect.anything());
                expect(data.changes).toBeDefined();
                expect(data.time).toEqual(expect.anything());
                expect(data.symbol).toEqual(expect.anything());
                console.log(data);
            },
        );

        return subid
            .then(async (id) => {
                await delay(5000);
                return id;
            })
            .then((id) => {
                return api.unSubscribe(id);
            });
    });

    test('orderbook level5 subscription test', () => {
        const subid = api.orderbookLevel5(
            [TEST_SYMBOL],
            (topic: string, subject: string, data: OrderbookLevel5Event) => {
                expect(data).toBeDefined();
                expect(data.asks).toEqual(expect.anything());
                expect(data.bids).toEqual(expect.anything());
                expect(data.timestamp).toEqual(expect.anything());
                console.log(data);
            },
        );

        return subid
            .then(async (id) => {
                await delay(5000);
                return id;
            })
            .then((id) => {
                return api.unSubscribe(id);
            });
    });

    test('klines subscription test', () => {
        const subid = api.klines(
            TEST_SYMBOL,
            '1min',
            (topic: string, subject: string, data: KlinesEvent) => {
                expect(data).toBeDefined();
                expect(data.candles).toEqual(expect.anything());
                expect(data.candles).toEqual(expect.anything());
                expect(data.time).toEqual(expect.anything());
                console.log(data);
            },
        );

        return subid
            .then(async (id) => {
                await delay(5000);
                return id;
            })
            .then((id) => {
                return api.unSubscribe(id);
            });
    });

    test('trade subscription test', () => {
        const subid = api.trade(
            [TEST_SYMBOL],
            (topic: string, subject: string, data: TradeEvent) => {
                expect(data).toBeDefined();
                expect(data.makerOrderId).toEqual(expect.anything());
                expect(data.price).toEqual(expect.anything());
                expect(data.sequence).toEqual(expect.anything());
                expect(data.side).toEqual(expect.anything());
                expect(data.size).toEqual(expect.anything());
                expect(data.symbol).toEqual(expect.anything());
                expect(data.takerOrderId).toEqual(expect.anything());
                expect(data.time).toEqual(expect.anything());
                expect(data.type).toEqual(expect.anything());
                console.log(data);
            },
        );

        return subid
            .then(async (id) => {
                await delay(5000);
                return id;
            })
            .then((id) => {
                return api.unSubscribe(id);
            });
    });

    test('orderbook level50 subscription test', () => {
        const subid = api.orderbookLevel50(
            [TEST_SYMBOL],
            (topic: string, subject: string, data: OrderbookLevel50Event) => {
                expect(data).toBeDefined();
                expect(data.asks).toEqual(expect.anything());
                expect(data.bids).toEqual(expect.anything());
                expect(data.timestamp).toEqual(expect.anything());
                console.log(data);
            },
        );

        return subid
            .then(async (id) => {
                await delay(5000);
                return id;
            })
            .then((id) => {
                return api.unSubscribe(id);
            });
    });

    test('market snapshot subscription test', () => {
        const subid = api.marketSnapshot(
            TEST_SYMBOL,
            (topic: string, subject: string, data: MarketSnapshotEvent) => {
                expect(data).toBeDefined();
                expect(data.sequence).toEqual(expect.anything());
                expect(data.data).toEqual(expect.anything());
                console.log(data);
            },
        );

        return subid
            .then(async (id) => {
                await delay(5000);
                return id;
            })
            .then((id) => {
                return api.unSubscribe(id);
            });
    });

    test('market callAuctionInfo subscription test', () => {
        const subid = api.callAuctionInfo(
            TEST_SYMBOL,
            (topic: string, subject: string, data: CallAuctionInfoEvent) => {
                expect(data).toBeDefined();
                console.log(data);
            },
        );

        return subid
            .then(async (id) => {
                await delay(5000);
                return id;
            })
            .then((id) => {
                return api.unSubscribe(id);
            });
    });

    test('market callAuctionOrderbookLevel50 subscription test', () => {
        const subid = api.callAuctionOrderbookLevel50(
            TEST_SYMBOL,
            (topic: string, subject: string, data: CallAuctionOrderbookLevel50Event) => {
                expect(data).toBeDefined();
                console.log(data);
            },
        );

        return subid
            .then(async (id) => {
                await delay(5000);
                return id;
            })
            .then((id) => {
                return api.unSubscribe(id);
            });
    });
});
