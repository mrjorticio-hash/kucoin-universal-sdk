import {setTimeout as sleep} from 'timers/promises';
import {
    ClientOptionBuilder,
    DefaultClient,
    GlobalApiEndpoint,
    GlobalBrokerApiEndpoint,
    GlobalFuturesApiEndpoint,
    Spot,
    TransportOptionBuilder,
    WebSocketClientOptionBuilder,
} from 'kucoin-universal-sdk';

const SLEEP_SECONDS = 5;

class Runner {
    private client;
    private restService;
    private wsService;
    private marketApi;
    private marketErrorCounter = 0;
    private wsStartStopErrorCounter = 0;
    private wsMessageCounter = 0;

    constructor() {
        const key = process.env.API_KEY ?? '';
        const secret = process.env.API_SECRET ?? '';
        const passphrase = process.env.API_PASSPHRASE ?? '';

        const httpTransportOption = new TransportOptionBuilder()
            .setKeepAlive(true)
            .build();

        const wsTransportOption = new WebSocketClientOptionBuilder().build();

        const clientOption = new ClientOptionBuilder()
            .setKey(key)
            .setSecret(secret)
            .setPassphrase(passphrase)
            .setSpotEndpoint(GlobalApiEndpoint)
            .setFuturesEndpoint(GlobalFuturesApiEndpoint)
            .setBrokerEndpoint(GlobalBrokerApiEndpoint)
            .setTransportOption(httpTransportOption)
            .setWebSocketClientOption(wsTransportOption)
            .build();

        this.client = new DefaultClient(clientOption);
        this.restService = this.client.restService();
        this.wsService = this.client.wsService();
        this.marketApi = this.restService.getSpotService().getMarketApi();
    }

    async run_ws_service_star_stop_forever(): Promise<void> {
        const print_cb = (_topic: string, _subject: string, data: any): void => {
            JSON.stringify(data);
        };

        while (true) {
            await sleep(SLEEP_SECONDS * 1000);
            const spotPublic = this.wsService.newSpotPublicWS();
            spotPublic.start().then(() => {
                return spotPublic.ticker(['ETH-USDT', 'BTC-USDT'], print_cb);
            }).then(async (id) => {
                await sleep(SLEEP_SECONDS * 1000);
                return id;
            }).then((id) => {
                return spotPublic.unSubscribe(id);
            }).then(() => {
                return spotPublic.stop();
            }).then(() => {
                console.info('WS STAR/STOP: [OK]');
            }).catch((e) => {
                console.error('WS STAR/STOP: [ERROR]');
                console.error(e);
                this.wsStartStopErrorCounter += 1;
            });
        }
    }

    async run_ws_service_forever(): Promise<void> {
        const print_cb = (_topic: string, _subject: string, data: any): void => {
            JSON.stringify(data);
            this.wsMessageCounter += 1;
        };

        const spotPublic = this.wsService.newSpotPublicWS();
        spotPublic.start().then(async () => {
            return spotPublic.orderbookLevel50(['ETH-USDT', 'BTC-USDT'], print_cb);
        }).then(async (id) => {
            console.info('WS: [OK]');
        }).catch((e) => {
            console.error('WS [ERROR]', e);
        });
        await new Promise(()=>{});
    }

    async run_market_api_forever(): Promise<void> {
        while (true) {
            await sleep(SLEEP_SECONDS * 1000);
            try {
                const req = Spot.Market.GetFullOrderBookReq.builder()
                    .setSymbol('BTC-USDT')
                    .build();
                const resp = await this.marketApi.getFullOrderBook(req);
                console.info('MARKET API: [OK] %d %d', resp.bids.length, resp.asks.length);
            } catch (e) {
                console.error('MARKET API: [ERROR]');
                console.error(e);
                this.marketErrorCounter += 1;
            }
        }
    }

    async print_error(): Promise<void> {
        while (true) {
            await sleep(SLEEP_SECONDS * 1000);
            console.info(
                'Stat, Market_ERROR:[%d], WS_SS_ERROR:[%d], WS_MESSAGE:[%d]',
                this.marketErrorCounter,
                this.wsStartStopErrorCounter,
                this.wsMessageCounter,
            );
        }
    }

    run_forever(): void {
        this.run_market_api_forever();
        this.run_ws_service_forever();
        this.run_ws_service_star_stop_forever();
        this.print_error();
    }
}

new Runner().run_forever();


