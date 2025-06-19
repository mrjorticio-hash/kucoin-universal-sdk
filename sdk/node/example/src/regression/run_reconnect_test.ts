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

function callbackFunc(_topic: string, _subject: string, _data: any): void {
}

async function getSpotSymbols(restService: any): Promise<string[]> {
    const marketApi = restService.getSpotService().getMarketApi();
    const resp = await marketApi.getAllSymbols(
        Spot.Market.GetAllSymbolsReq.builder().setMarket('USDS').build(),
    );
    const symbols: string[] = resp.data.map((d: any) => d.symbol);
    return symbols.length > 100 ? symbols.slice(0, 100) : symbols;
}

async function spotWsExample(ws: any, symbols: string[]): Promise<void> {
    let p = ws.start();
    for (const s of symbols) {
        p = p.then(() => {
            return ws.trade([s], callbackFunc);
        });
    }
    p = p.then(() => {
        return ws.ticker(['BTC-USDT', 'ETH-USDT'], callbackFunc);
    });

    return p.then(() => {
        console.info('Spot subscribe [OK]');
    }).catch((e: any) => {
        console.error('Spot subscribe [Error]', e);
    });
}

async function futuresWsExample(ws: any): Promise<void> {
    return ws.start().then(() => {
        return ws.tickerV2('XBTUSDTM', callbackFunc);
    }).then(() => {
        return ws.tickerV1('XBTUSDTM', callbackFunc);
    }).then(() => {
        console.info('Futures subscribe [OK]');
    }).catch((e: any) => {
        console.error('Futures subscribe [Error]', e);
    })
}

async function wsReconnectTest(): Promise<void> {
    const key = process.env.API_KEY ?? '';
    const secret = process.env.API_SECRET ?? '';
    const passphrase = process.env.API_PASSPHRASE ?? '';

    const wsOption = new WebSocketClientOptionBuilder().build();

    const clientOption = new ClientOptionBuilder()
        .setKey(key)
        .setSecret(secret)
        .setPassphrase(passphrase)
        .setWebSocketClientOption(wsOption)
        .setTransportOption(new TransportOptionBuilder().build())
        .setSpotEndpoint(GlobalApiEndpoint)
        .setFuturesEndpoint(GlobalFuturesApiEndpoint)
        .setBrokerEndpoint(GlobalBrokerApiEndpoint)
        .build();

    const client = new DefaultClient(clientOption);
    const wsService = client.wsService();

    const symbols = await getSpotSymbols(client.restService());
    await spotWsExample(wsService.newSpotPublicWS(), symbols);
    await futuresWsExample(wsService.newFuturesPublicWS());
}

wsReconnectTest().then(() => {
    console.info('Total subscribe: 103');
});