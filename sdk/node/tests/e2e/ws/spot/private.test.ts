import {
    ClientOptionBuilder,
    GlobalApiEndpoint,
    GlobalBrokerApiEndpoint,
    GlobalFuturesApiEndpoint,
    WebSocketClientOptionBuilder,
} from '@model/index';
import {
    AccountEvent,
    OrderV1Event,
    OrderV2Event,
    SpotPrivateWS,
    StopOrderEvent,
} from '@src/generate/spot/spotprivate';
import { DefaultClient } from '@api/index';

jest.setTimeout(300000);

describe('Spot Private WebSocket API Tests', () => {
    let api: SpotPrivateWS;

    beforeAll(async () => {
        const key = process.env.API_KEY || '';
        const secret = process.env.API_SECRET || '';
        const passphrase = process.env.API_PASSPHRASE || '';

        const websocketClientOption = new WebSocketClientOptionBuilder().build();

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
        const wsService = client.wsService();
        api = wsService.newSpotPrivateWS();
        await api.start();
    });

    afterAll(() => {
        return api.stop();
    });

    test('account subscription test', (done) => {
        (async () => {
            let subid = await api.account(
                async (topic: string, subject: string, item: AccountEvent) => {
                    console.log(item);
                    expect(item.accountId).toEqual(expect.any(String));
                    expect(item.available).toEqual(expect.any(String));
                    expect(item.availableChange).toEqual(expect.any(String));
                    expect(item.currency).toEqual(expect.any(String));
                    expect(item.hold).toEqual(expect.any(String));
                    expect(item.holdChange).toEqual(expect.any(String));
                    expect(item.relationContext).toEqual(expect.any(Object));
                    expect(item.relationEvent).toEqual(expect.any(String));
                    expect(item.relationEventId).toEqual(expect.any(String));
                    expect(item.time).toEqual(expect.any(String));
                    expect(item.total).toEqual(expect.any(String));
                    api.unSubscribe(subid).then(() => {
                        done();
                    });
                },
            );

            console.log(`subscribe id: ${subid}`);
        })();
    });

    test('orderV1 subscription test', (done) => {
        (async () => {
            let subid = await api.orderV1(
                async (topic: string, subject: string, item: OrderV1Event) => {
                    console.log(item);
                    try {
                        expect(item.filledSize).toEqual(expect.any(String));
                        expect(item.orderId).toEqual(expect.any(String));
                        expect(item.orderTime).toEqual(expect.any(Number));
                        expect(item.orderType).toEqual(expect.any(String));
                        expect(item.price).toEqual(expect.any(String));
                    } catch (e) {
                        done(e);
                    }
                    api.unSubscribe(subid).then(() => {
                        done();
                    });
                },
            );

            console.log(`subscribe id: ${subid}`);
        })();
    });

    test('orderV2 subscription test', (done) => {
        (async () => {
            let subid = await api.orderV2(
                async (topic: string, subject: string, item: OrderV2Event) => {
                    console.log(item);
                    expect(item.filledSize).toEqual(expect.any(String));
                    expect(item.orderId).toEqual(expect.any(String));
                    expect(item.orderTime).toEqual(expect.any(Number));
                    expect(item.orderType).toEqual(expect.any(String));
                    expect(item.price).toEqual(expect.any(String));
                    api.unSubscribe(subid).then(() => {
                        done();
                    });
                },
            );

            console.log(`subscribe id: ${subid}`);
        })();
    });

    test('stopOrder subscription test', (done) => {
        (async () => {
            let subid = await api.stopOrder(
                async (topic: string, subject: string, item: StopOrderEvent) => {
                    console.log(item);
                    expect(item.createdAt).toEqual(expect.any(Number));
                    expect(item.orderId).toEqual(expect.any(String));
                    expect(item.orderPrice).toEqual(expect.any(Number));
                    expect(item.orderType).toEqual(expect.any(String));
                    expect(item.side).toEqual(expect.any(String));
                    expect(item.size).toEqual(expect.any(String));
                    expect(item.stop).toEqual(expect.any(String));
                    expect(item.stopPrice).toEqual(expect.any(String));
                    expect(item.symbol).toEqual(expect.any(String));
                    expect(item.tradeType).toEqual(expect.any(String));
                    expect(item.ts).toEqual(expect.any(Number));
                    expect(item.type).toEqual(expect.any(String));
                    api.unSubscribe(subid).then(() => {
                        done();
                    });
                },
            );

            console.log(`subscribe id: ${subid}`);
        })();
    });
});
