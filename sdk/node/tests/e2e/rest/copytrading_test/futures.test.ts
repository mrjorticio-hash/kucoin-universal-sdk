import {
    ClientOptionBuilder,
    GlobalApiEndpoint,
    GlobalBrokerApiEndpoint,
    GlobalFuturesApiEndpoint,
    TransportOptionBuilder,
} from '@model/index';
import {
    AddIsolatedMarginReq,
    AddOrderReq,
    AddOrderTestReq,
    AddTPSLOrderReq,
    CancelOrderByClientOidReq,
    CancelOrderByIdReq,
    FuturesAPI,
    GetMaxOpenSizeReq,
    GetMaxWithdrawMarginReq,
    ModifyAutoDepositStatusReq,
    ModifyIsolatedMarginRiskLimtReq,
    RemoveIsolatedMarginReq,
} from '@src/generate/copytrading/futures';
import { DefaultClient } from '@api/index';
import { randomUUID } from 'crypto';

describe('Auto Test', () => {
    let api: FuturesAPI;

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
        api = kucoinRestService.getCopyTradingService().getFuturesApi();
    });

    test('addOrder request test', () => {
        /**
         * addOrder
         * Add Order
         * /api/v1/copy-trade/futures/orders
         */
        let builder = AddOrderReq.builder();
        builder
            .setClientOid(randomUUID())
            .setSide(AddOrderReq.SideEnum.BUY)
            .setSymbol('XBTUSDTM')
            .setLeverage(3)
            .setType(AddOrderReq.TypeEnum.LIMIT)
            .setReduceOnly(false)
            .setMarginMode(AddOrderReq.MarginModeEnum.ISOLATED)
            .setPrice('0.1')
            .setSize(1)
            .setTimeInForce(AddOrderReq.TimeInForceEnum.GOOD_TILL_CANCELED);
        let req = builder.build();
        let resp = api.addOrder(req);
        return resp.then((result) => {
            expect(result.orderId).toEqual(expect.anything());
            expect(result.clientOid).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('addOrderTest request test', () => {
        /**
         * addOrderTest
         * Add Order Test
         * /api/v1/copy-trade/futures/orders/test
         */
        let builder = AddOrderTestReq.builder();
        builder
            .setClientOid(randomUUID())
            .setSide(AddOrderReq.SideEnum.BUY)
            .setSymbol('XBTUSDTM')
            .setLeverage(3)
            .setType(AddOrderTestReq.TypeEnum.LIMIT)
            .setReduceOnly(false)
            .setMarginMode(AddOrderTestReq.MarginModeEnum.ISOLATED)
            .setPrice('0.1')
            .setSize(1)
            .setTimeInForce(AddOrderTestReq.TimeInForceEnum.GOOD_TILL_CANCELED);
        let req = builder.build();
        let resp = api.addOrderTest(req);
        return resp.then((result) => {
            expect(result.orderId).toEqual(expect.anything());
            expect(result.clientOid).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('addTPSLOrder request test', () => {
        /**
         * addTPSLOrder
         * Add Take Profit And Stop Loss Order
         * /api/v1/copy-trade/futures/st-orders
         */
        let builder = AddTPSLOrderReq.builder();
        builder
            .setClientOid(randomUUID())
            .setSide(AddTPSLOrderReq.SideEnum.BUY)
            .setSymbol('XBTUSDTM')
            .setLeverage(3)
            .setType(AddTPSLOrderReq.TypeEnum.LIMIT)
            .setReduceOnly(false)
            .setMarginMode(AddTPSLOrderReq.MarginModeEnum.ISOLATED)
            .setPrice('0.1')
            .setSize(1)
            .setTimeInForce(AddTPSLOrderReq.TimeInForceEnum.GOOD_TILL_CANCELED)
            .setTriggerStopUpPrice('0.3')
            .setTriggerStopDownPrice('0.1')
            .setStopPriceType(AddTPSLOrderReq.StopPriceTypeEnum.TRADE_PRICE);
        let req = builder.build();
        let resp = api.addTPSLOrder(req);
        return resp.then((result) => {
            expect(result.orderId).toEqual(expect.anything());
            expect(result.clientOid).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('cancelOrderById request test', () => {
        /**
         * cancelOrderById
         * Cancel Order By OrderId
         * /api/v1/copy-trade/futures/orders
         */
        let builder = CancelOrderByIdReq.builder();
        builder.setOrderId('278750197184520192');
        let req = builder.build();
        let resp = api.cancelOrderById(req);
        return resp.then((result) => {
            expect(result.cancelledOrderIds).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('cancelOrderByClientOid request test', () => {
        /**
         * cancelOrderByClientOid
         * Cancel Order By ClientOid
         * /api/v1/copy-trade/futures/orders/client-order
         */
        let builder = CancelOrderByClientOidReq.builder();
        builder.setSymbol('XBTUSDTM').setClientOid('52d18a28-32d8-4a1d-b183-46fa5cd09780');
        let req = builder.build();
        let resp = api.cancelOrderByClientOid(req);
        return resp.then((result) => {
            expect(result.clientOid).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getMaxOpenSize request test', () => {
        /**
         * getMaxOpenSize
         * Get Max Open Size
         * /api/v1/copy-trade/futures/get-max-open-size
         */
        let builder = GetMaxOpenSizeReq.builder();
        builder.setSymbol('XBTUSDTM').setPrice(0.1).setLeverage(10);
        let req = builder.build();
        let resp = api.getMaxOpenSize(req);
        return resp.then((result) => {
            expect(result.symbol).toEqual(expect.anything());
            expect(result.maxBuyOpenSize).toEqual(expect.anything());
            expect(result.maxSellOpenSize).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getMaxWithdrawMargin request test', () => {
        /**
         * getMaxWithdrawMargin
         * Get Max Withdraw Margin
         * /api/v1/copy-trade/futures/position/margin/max-withdraw-margin
         */
        let builder = GetMaxWithdrawMarginReq.builder();
        builder.setSymbol('XBTUSDTM');
        let req = builder.build();
        let resp = api.getMaxWithdrawMargin(req);
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('addIsolatedMargin request test', () => {
        /**
         * addIsolatedMargin
         * Add Isolated Margin
         * /api/v1/copy-trade/futures/position/margin/deposit-margin
         */
        let builder = AddIsolatedMarginReq.builder();
        builder.setSymbol('XBTUSDTM').setMargin(3).setBizNo(randomUUID());
        let req = builder.build();
        let resp = api.addIsolatedMargin(req);
        return resp.then((result) => {
            expect(result.id).toEqual(expect.anything());
            expect(result.symbol).toEqual(expect.anything());
            expect(result.autoDeposit).toEqual(expect.anything());
            expect(result.maintMarginReq).toEqual(expect.anything());
            expect(result.riskLimit).toEqual(expect.anything());
            expect(result.realLeverage).toEqual(expect.anything());
            expect(result.crossMode).toEqual(expect.anything());
            expect(result.delevPercentage).toEqual(expect.anything());
            expect(result.openingTimestamp).toEqual(expect.anything());
            expect(result.currentTimestamp).toEqual(expect.anything());
            expect(result.currentQty).toEqual(expect.anything());
            expect(result.currentCost).toEqual(expect.anything());
            expect(result.currentComm).toEqual(expect.anything());
            expect(result.unrealisedCost).toEqual(expect.anything());
            expect(result.realisedGrossCost).toEqual(expect.anything());
            expect(result.realisedCost).toEqual(expect.anything());
            expect(result.isOpen).toEqual(expect.anything());
            expect(result.markPrice).toEqual(expect.anything());
            expect(result.markValue).toEqual(expect.anything());
            expect(result.posCost).toEqual(expect.anything());
            expect(result.posCross).toEqual(expect.anything());
            expect(result.posInit).toEqual(expect.anything());
            expect(result.posComm).toEqual(expect.anything());
            expect(result.posLoss).toEqual(expect.anything());
            expect(result.posMargin).toEqual(expect.anything());
            expect(result.posMaint).toEqual(expect.anything());
            expect(result.maintMargin).toEqual(expect.anything());
            expect(result.realisedGrossPnl).toEqual(expect.anything());
            expect(result.realisedPnl).toEqual(expect.anything());
            expect(result.unrealisedPnl).toEqual(expect.anything());
            expect(result.unrealisedPnlPcnt).toEqual(expect.anything());
            expect(result.unrealisedRoePcnt).toEqual(expect.anything());
            expect(result.avgEntryPrice).toEqual(expect.anything());
            expect(result.liquidationPrice).toEqual(expect.anything());
            expect(result.bankruptPrice).toEqual(expect.anything());
            expect(result.settleCurrency).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('removeIsolatedMargin request test', () => {
        /**
         * removeIsolatedMargin
         * Remove Isolated Margin
         * /api/v1/copy-trade/futures/position/margin/withdraw-margin
         */
        let builder = RemoveIsolatedMarginReq.builder();
        builder.setSymbol('XBTUSDTM').setWithdrawAmount(0.0000001);
        let req = builder.build();
        let resp = api.removeIsolatedMargin(req);
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('modifyIsolatedMarginRiskLimt request test', () => {
        /**
         * modifyIsolatedMarginRiskLimt
         * Modify Isolated Margin Risk Limit
         * /api/v1/copy-trade/futures/position/risk-limit-level/change
         */
        let builder = ModifyIsolatedMarginRiskLimtReq.builder();
        builder.setSymbol('XBTUSDTM').setLevel(1);
        let req = builder.build();
        let resp = api.modifyIsolatedMarginRiskLimt(req);
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('modifyAutoDepositStatus request test', () => {
        /**
         * modifyAutoDepositStatus
         * Modify Isolated Margin Auto-Deposit Status
         * /api/v1/copy-trade/futures/position/margin/auto-deposit-status
         */
        let builder = ModifyAutoDepositStatusReq.builder();
        builder.setSymbol('XBTUSDTM').setStatus(true);
        let req = builder.build();
        let resp = api.modifyAutoDepositStatus(req);
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(resp);
        });
    });
});
