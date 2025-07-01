import {randomUUID} from 'crypto';
import assert from 'assert';
import {
    Account,
    ClientOptionBuilder,
    DefaultClient,
    Earn,
    Futures,
    GlobalApiEndpoint,
    GlobalBrokerApiEndpoint,
    GlobalFuturesApiEndpoint,
    Margin,
    RestResponse,
    Spot,
    TransportOptionBuilder,
} from 'kucoin-universal-sdk';

const describe = (name: string, fn: () => Promise<void> | void) => {
    console.log(`\n${name}`);
    fn();
};
const it = (name: string, fn: () => Promise<void>) =>
    fn()
        .then(() => console.log(`  ✓ ${name}`))
        .catch((e) => {
            console.error(`  ✗ ${name}`);
            console.error(e);
            process.exitCode = 1;
        });

const httpOption = new TransportOptionBuilder()
    .setKeepAlive(true)
    .setMaxConnsPerHost(10)
    .build();

const client = new DefaultClient(
    new ClientOptionBuilder()
        .setKey(process.env.API_KEY ?? '')
        .setSecret(process.env.API_SECRET ?? '')
        .setPassphrase(process.env.API_PASSPHRASE ?? '')
        .setSpotEndpoint(GlobalApiEndpoint)
        .setFuturesEndpoint(GlobalFuturesApiEndpoint)
        .setBrokerEndpoint(GlobalBrokerApiEndpoint)
        .setTransportOption(httpOption)
        .build(),
);

const rest = client.restService();

function checkCommon(resp?: RestResponse) {
    assert.ok(resp !== undefined);
    if (resp) {
        assert.strictEqual(resp.code, '200000');
        assert.ok(resp.rateLimit);
    }
}

describe('CheckAllServiceTest', () => {
    //
    // Account
    //
    it('account_service', async () => {
        const feeApi = rest.getAccountService().getFeeApi();
        const resp = await feeApi.getBasicFee(
            Account.Fee.GetBasicFeeReq.builder()
                .setCurrencyType(Account.Fee.GetBasicFeeReq.CurrencyTypeEnum._0)
                .build(),
        );
        checkCommon(resp.commonResponse);
        assert.ok(resp.makerFeeRate.length > 0);
        assert.ok(resp.takerFeeRate.length > 0);
    });

    //
    // Earn
    //
    it('earn_service', async () => {
        const earnApi = rest.getEarnService().getEarnApi();
        const resp = await earnApi.getSavingsProducts(
            Earn.Earn.GetSavingsProductsReq.builder().setCurrency('USDT').build(),
        );
        checkCommon(resp.commonResponse);
        assert.ok(resp.data.length > 0);
    });

    //
    // Margin
    //
    it('margin_service', async () => {
        const orderApi = rest.getMarginService().getOrderApi();

        const addReq = Margin.Order.AddOrderReq.builder()
            .setClientOid(randomUUID())
            .setSide(Margin.Order.AddOrderReq.SideEnum.BUY)
            .setSymbol('BTC-USDT')
            .setType(Margin.Order.AddOrderReq.TypeEnum.LIMIT)
            .setPrice('10000')
            .setSize('0.001')
            .setAutoRepay(true)
            .setAutoBorrow(true)
            .setIsIsolated(true)
            .build();
        const addResp = await orderApi.addOrder(addReq);
        checkCommon(addResp.commonResponse);
        assert.ok(addResp.orderId.length > 0);

        const queryReq = Margin.Order.GetOrderByOrderIdReq.builder()
            .setSymbol('BTC-USDT')
            .setOrderId(addResp.orderId)
            .build();
        const queryResp = await orderApi.getOrderByOrderId(queryReq);
        checkCommon(queryResp.commonResponse);
        assert.ok(queryResp.symbol.length > 0);

        const cancelReq = Margin.Order.CancelOrderByOrderIdReq.builder()
            .setOrderId(addResp.orderId)
            .setSymbol('BTC-USDT')
            .build();
        const cancelResp = await orderApi.cancelOrderByOrderId(cancelReq);
        checkCommon(cancelResp.commonResponse);
        assert.ok(cancelResp.orderId.length > 0);
    });

    //
    // Spot
    //
    it('spot_service', async () => {
        const spotService = rest.getSpotService();

        const statResp = await spotService
            .getMarketApi()
            .get24hrStats(
                Spot.Market.Get24hrStatsReq.builder().setSymbol('BTC-USDT').build(),
            );
        checkCommon(statResp.commonResponse);
        assert.ok(statResp.last.length > 0);

        const orderApi = spotService.getOrderApi();

        const addReq = Spot.Order.AddOrderSyncReq.builder()
            .setClientOid(randomUUID())
            .setSide(Spot.Order.AddOrderSyncReq.SideEnum.BUY)
            .setSymbol('BTC-USDT')
            .setType(Spot.Order.AddOrderSyncReq.TypeEnum.LIMIT)
            .setRemark('sdk_test')
            .setPrice('10000')
            .setSize('0.001')
            .build();
        const addResp = await orderApi.addOrderSync(addReq);
        checkCommon(addResp.commonResponse);
        assert.ok(addResp.orderId.length > 0);
        assert.ok(addResp.orderTime > 0);

        const queryReq = Spot.Order.GetOrderByOrderIdReq.builder()
            .setSymbol('BTC-USDT')
            .setOrderId(addResp.orderId)
            .build();
        const queryResp = await orderApi.getOrderByOrderId(queryReq);
        checkCommon(queryResp.commonResponse);
        assert.ok(queryResp.symbol.length > 0);

        const cancelReq = Spot.Order.CancelOrderByOrderIdReq.builder()
            .setOrderId(addResp.orderId)
            .setSymbol('BTC-USDT')
            .build();
        const cancelResp = await orderApi.cancelOrderByOrderId(cancelReq);
        checkCommon(cancelResp.commonResponse);
        assert.ok(cancelResp.orderId.length > 0);
    });

    //
    // Futures
    //
    it('futures_service', async () => {
        const futuresService = rest.getFuturesService();

        const statResp = await futuresService.getMarketApi().get24hrStats();
        checkCommon(statResp.commonResponse);
        assert.ok(statResp.turnoverOf24h);

        const orderApi = futuresService.getOrderApi();

        const addReq = Futures.Order.AddOrderReq.builder()
            .setClientOid(randomUUID())
            .setSide(Futures.Order.AddOrderReq.SideEnum.BUY)
            .setSymbol('XBTUSDTM')
            .setLeverage(1)
            .setType(Futures.Order.AddOrderReq.TypeEnum.LIMIT)
            .setRemark('sdk_test')
            .setMarginMode(Futures.Order.AddOrderReq.MarginModeEnum.CROSS)
            .setPrice('1')
            .setSize(1)
            .build();
        const addResp = await orderApi.addOrder(addReq);
        checkCommon(addResp.commonResponse);
        assert.ok(addResp.orderId.length > 0);

        const queryReq = Futures.Order.GetOrderByOrderIdReq.builder()
            .setOrderId(addResp.orderId)
            .build();
        const queryResp = await orderApi.getOrderByOrderId(queryReq);
        checkCommon(queryResp.commonResponse);
        assert.ok(queryResp.symbol.length > 0);

        const cancelReq = Futures.Order.CancelOrderByIdReq.builder()
            .setOrderId(addResp.orderId)
            .build();
        const cancelResp = await orderApi.cancelOrderById(cancelReq);
        checkCommon(cancelResp.commonResponse);
        assert.ok(cancelResp.cancelledOrderIds.length > 0);
    });
});
