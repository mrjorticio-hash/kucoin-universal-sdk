import {
    ClientOptionBuilder,
    GlobalApiEndpoint,
    GlobalBrokerApiEndpoint,
    GlobalFuturesApiEndpoint,
    TransportOptionBuilder,
} from '@model/index';
import {
    AddOrderReq,
    AddOrderTestReq,
    AddTPSLOrderReq,
    BatchAddOrdersItem,
    BatchAddOrdersReq,
    BatchCancelOrdersClientOidsList,
    BatchCancelOrdersReq,
    CancelAllOrdersV1Req,
    CancelAllOrdersV3Req,
    CancelAllStopOrdersReq,
    CancelOrderByClientOidReq,
    CancelOrderByIdReq,
    GetOpenOrderValueReq,
    GetOrderByClientOidReq,
    GetOrderByOrderIdReq,
    GetOrderListReq,
    GetRecentClosedOrdersReq, GetRecentTradeHistoryData,
    GetRecentTradeHistoryReq,
    GetStopOrderListReq,
    GetTradeHistoryReq,
    OrderAPI,
} from '@src/generate/futures/order';
import { DefaultClient } from '@api/index';
import { randomUUID } from 'crypto';

describe('Auto Test', () => {
    let api: OrderAPI;

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
        api = kucoinRestService.getFuturesService().getOrderApi();
    });

    test('addOrder request test', () => {
        /**
         * addOrder
         * Add Order
         * /api/v1/orders
         */
        let builder = AddOrderReq.builder();
        builder
            .setClientOid(randomUUID())
            .setSide(AddOrderReq.SideEnum.BUY)
            .setSymbol('XBTUSDTM')
            .setLeverage(3.0)
            .setType(AddOrderReq.TypeEnum.LIMIT)
            .setRemark('order_test')
            .setMarginMode(AddOrderReq.MarginModeEnum.ISOLATED)
            .setPrice('1')
            .setSize(1);
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
         * /api/v1/orders/test
         */
        let builder = AddOrderTestReq.builder();
        builder
            .setClientOid(randomUUID())
            .setSide(AddOrderTestReq.SideEnum.BUY)
            .setSymbol('XBTUSDTM')
            .setLeverage(3.0)
            .setType(AddOrderTestReq.TypeEnum.LIMIT)
            .setRemark('order_test')
            .setMarginMode(AddOrderTestReq.MarginModeEnum.ISOLATED)
            .setPrice('1')
            .setSize(1);
        let req = builder.build();
        let resp = api.addOrderTest(req);
        return resp.then((result) => {
            expect(result.orderId).toEqual(expect.anything());
            expect(result.clientOid).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('batchAddOrders request test', () => {
        /**
         * batchAddOrders
         * Batch Add Orders
         * /api/v1/orders/multi
         */
        let builder = BatchAddOrdersReq.builder();

        let order1 = BatchAddOrdersItem.builder()
            .setClientOid(randomUUID())
            .setSide(BatchAddOrdersItem.SideEnum.BUY)
            .setSymbol('XBTUSDTM')
            .setLeverage(3.0)
            .setType(BatchAddOrdersItem.TypeEnum.LIMIT)
            .setRemark('order_test')
            .setMarginMode(BatchAddOrdersItem.MarginModeEnum.ISOLATED)
            .setPrice('1')
            .setSize(1)
            .build();
        let order2 = BatchAddOrdersItem.builder()
            .setClientOid(randomUUID())
            .setSide(BatchAddOrdersItem.SideEnum.BUY)
            .setSymbol('XBTUSDTM')
            .setLeverage(3.0)
            .setType(BatchAddOrdersItem.TypeEnum.LIMIT)
            .setRemark('order_test')
            .setMarginMode(BatchAddOrdersItem.MarginModeEnum.ISOLATED)
            .setPrice('1')
            .setSize(1)
            .build();

        builder.setItems([order1, order2]);
        let req = builder.build();
        let resp = api.batchAddOrders(req);
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(resp);
            result.data.forEach((item) => {
                console.log(item.orderId);
                console.log(item.clientOid);
            })
        });
    });

    test('addTPSLOrder request test', () => {
        /**
         * addTPSLOrder
         * Add Take Profit And Stop Loss Order
         * /api/v1/st-orders
         */
        let builder = AddTPSLOrderReq.builder();
        builder
            .setClientOid(randomUUID())
            .setSide(AddTPSLOrderReq.SideEnum.BUY)
            .setSymbol('XBTUSDTM')
            .setLeverage(3.0)
            .setType(AddTPSLOrderReq.TypeEnum.LIMIT)
            .setRemark('order_test')
            .setStopPriceType(AddTPSLOrderReq.StopPriceTypeEnum.TRADE_PRICE)
            .setMarginMode(AddTPSLOrderReq.MarginModeEnum.ISOLATED)
            .setPrice('10000')
            .setSize(1)
            .setTriggerStopUpPrice('8000')
            .setTriggerStopDownPrice('12000')
            .build();
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
         * /api/v1/orders/{orderId}
         */
        let builder = CancelOrderByIdReq.builder();
        builder.setOrderId('278696338965737472');
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
         * /api/v1/orders/client-order/{clientOid}
         */
        let builder = CancelOrderByClientOidReq.builder();
        builder.setSymbol('XBTUSDTM').setClientOid('ab78cba6-55f7-42d7-a49f-fee676b265a4');
        let req = builder.build();
        let resp = api.cancelOrderByClientOid(req);
        return resp.then((result) => {
            expect(result.clientOid).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('batchCancelOrders request test', () => {
        /**
         * batchCancelOrders
         * Batch Cancel Orders
         * /api/v1/orders/multi-cancel
         */
        let builder = BatchCancelOrdersReq.builder();
        builder
            .setOrderIdsList(['278697532131983364', '278697532211675136'])
            .setClientOidsList([
                BatchCancelOrdersClientOidsList.create({ symbol: 'XBTUSDTM', clientOid: '5f888dfe-8fec-4691-a36d-d2cfb9f050b0' }),
                BatchCancelOrdersClientOidsList.create({ symbol: 'XBTUSDTM', clientOid: '48d63d98-2369-43c1-a599-db4ceba41fbf' }),
            ]);
        let req = builder.build();
        let resp = api.batchCancelOrders(req);
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('cancelAllOrdersV3 request test', () => {
        /**
         * cancelAllOrdersV3
         * Cancel All Orders
         * /api/v3/orders
         */
        let builder = CancelAllOrdersV3Req.builder();
        builder.setSymbol('XBTUSDTM');
        let req = builder.build();
        let resp = api.cancelAllOrdersV3(req);
        return resp.then((result) => {
            expect(result.cancelledOrderIds).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('cancelAllStopOrders request test', () => {
        /**
         * cancelAllStopOrders
         * Cancel All Stop orders
         * /api/v1/stopOrders
         */
        let builder = CancelAllStopOrdersReq.builder();
        builder.setSymbol('XBTUSDTM');
        let req = builder.build();
        let resp = api.cancelAllStopOrders(req);
        return resp.then((result) => {
            expect(result.cancelledOrderIds).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getOrderByOrderId request test', () => {
        /**
         * getOrderByOrderId
         * Get Order By OrderId
         * /api/v1/orders/{order-id}
         */
        let builder = GetOrderByOrderIdReq.builder();
        builder.setOrderId('278700272115560449');
        let req = builder.build();
        let resp = api.getOrderByOrderId(req);
        return resp.then((result) => {
            expect(result.id).toEqual(expect.anything());
            expect(result.symbol).toEqual(expect.anything());
            expect(result.type).toEqual(expect.anything());
            expect(result.side).toEqual(expect.anything());
            expect(result.price).toEqual(expect.anything());
            expect(result.size).toEqual(expect.anything());
            expect(result.value).toEqual(expect.anything());
            expect(result.dealValue).toEqual(expect.anything());
            expect(result.dealSize).toEqual(expect.anything());
            expect(result.stp).toEqual(expect.anything());
            expect(result.stop).toEqual(expect.anything());
            expect(result.stopPriceType).toEqual(expect.anything());
            expect(result.stopTriggered).toEqual(expect.anything());
            expect(result.timeInForce).toEqual(expect.anything());
            expect(result.postOnly).toEqual(expect.anything());
            expect(result.hidden).toEqual(expect.anything());
            expect(result.iceberg).toEqual(expect.anything());
            expect(result.leverage).toEqual(expect.anything());
            expect(result.forceHold).toEqual(expect.anything());
            expect(result.closeOrder).toEqual(expect.anything());
            expect(result.visibleSize).toEqual(expect.anything());
            expect(result.clientOid).toEqual(expect.anything());
            expect(result.isActive).toEqual(expect.anything());
            expect(result.cancelExist).toEqual(expect.anything());
            expect(result.createdAt).toEqual(expect.anything());
            expect(result.updatedAt).toEqual(expect.anything());
            expect(result.orderTime).toEqual(expect.anything());
            expect(result.settleCurrency).toEqual(expect.anything());
            expect(result.marginMode).toEqual(expect.anything());
            expect(result.avgDealPrice).toEqual(expect.anything());
            expect(result.filledSize).toEqual(expect.anything());
            expect(result.filledValue).toEqual(expect.anything());
            expect(result.status).toEqual(expect.anything());
            expect(result.reduceOnly).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getOrderByClientOid request test', () => {
        /**
         * getOrderByClientOid
         * Get Order By ClientOid
         * /api/v1/orders/byClientOid
         */
        let builder = GetOrderByClientOidReq.builder();
        builder.setClientOid('4595de36-25e9-434c-ab13-fb93670ade2a');
        let req = builder.build();
        let resp = api.getOrderByClientOid(req);
        return resp.then((result) => {
            expect(result.id).toEqual(expect.anything());
            expect(result.symbol).toEqual(expect.anything());
            expect(result.type).toEqual(expect.anything());
            expect(result.side).toEqual(expect.anything());
            expect(result.price).toEqual(expect.anything());
            expect(result.size).toEqual(expect.anything());
            expect(result.value).toEqual(expect.anything());
            expect(result.dealValue).toEqual(expect.anything());
            expect(result.dealSize).toEqual(expect.anything());
            expect(result.stp).toEqual(expect.anything());
            expect(result.stop).toEqual(expect.anything());
            expect(result.stopPriceType).toEqual(expect.anything());
            expect(result.stopTriggered).toEqual(expect.anything());
            expect(result.timeInForce).toEqual(expect.anything());
            expect(result.postOnly).toEqual(expect.anything());
            expect(result.hidden).toEqual(expect.anything());
            expect(result.iceberg).toEqual(expect.anything());
            expect(result.leverage).toEqual(expect.anything());
            expect(result.forceHold).toEqual(expect.anything());
            expect(result.closeOrder).toEqual(expect.anything());
            expect(result.visibleSize).toEqual(expect.anything());
            expect(result.clientOid).toEqual(expect.anything());
            expect(result.isActive).toEqual(expect.anything());
            expect(result.cancelExist).toEqual(expect.anything());
            expect(result.createdAt).toEqual(expect.anything());
            expect(result.updatedAt).toEqual(expect.anything());
            expect(result.orderTime).toEqual(expect.anything());
            expect(result.settleCurrency).toEqual(expect.anything());
            expect(result.marginMode).toEqual(expect.anything());
            expect(result.avgDealPrice).toEqual(expect.anything());
            expect(result.filledSize).toEqual(expect.anything());
            expect(result.filledValue).toEqual(expect.anything());
            expect(result.status).toEqual(expect.anything());
            expect(result.reduceOnly).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getOrderList request test', () => {
        /**
         * getOrderList
         * Get Order List
         * /api/v1/orders
         */
        let builder = GetOrderListReq.builder();
        builder
            .setStatus(GetOrderListReq.StatusEnum.ACTIVE)
            .setSymbol('XBTUSDTM')
            .setSide(GetOrderListReq.SideEnum.BUY);
        let req = builder.build();
        let resp = api.getOrderList(req);
        return resp.then((result) => {
            expect(result.currentPage).toEqual(expect.anything());
            expect(result.pageSize).toEqual(expect.anything());
            expect(result.totalNum).toEqual(expect.anything());
            expect(result.totalPage).toEqual(expect.anything());
            result.items.forEach((item) => {
                expect(item.id).toEqual(expect.any(String));
                expect(item.symbol).toEqual(expect.any(String));
                expect(item.type).toEqual(expect.any(String));
                expect(item.side).toEqual(expect.any(String));
                expect(item.price).toEqual(expect.any(String));
                expect(item.size).toEqual(expect.any(Number));
                expect(item.value).toEqual(expect.any(String));
                expect(item.dealValue).toEqual(expect.any(String));
                expect(item.dealSize).toEqual(expect.any(Number));
                expect(item.stp).toEqual(expect.any(String));
                expect(item.stop).toEqual(expect.any(String));
                expect(item.stopPriceType).toEqual(expect.any(String));
                expect(item.stopTriggered).toEqual(expect.any(Boolean));
                expect(item.timeInForce).toEqual(expect.any(String));
                expect(item.postOnly).toEqual(expect.any(Boolean));
                expect(item.hidden).toEqual(expect.any(Boolean));
                expect(item.iceberg).toEqual(expect.any(Boolean));
                expect(item.leverage).toEqual(expect.any(String));
                expect(item.forceHold).toEqual(expect.any(Boolean));
                expect(item.closeOrder).toEqual(expect.any(Boolean));
                expect(item.visibleSize).toEqual(expect.any(Number));
                expect(item.clientOid).toEqual(expect.any(String));
                expect(item.isActive).toEqual(expect.any(Boolean));
                expect(item.cancelExist).toEqual(expect.any(Boolean));
                expect(item.createdAt).toEqual(expect.any(Number));
                expect(item.updatedAt).toEqual(expect.any(Number));
                expect(item.orderTime).toEqual(expect.any(Number));
                expect(item.settleCurrency).toEqual(expect.any(String));
                expect(item.marginMode).toEqual(expect.any(String));
                expect(item.avgDealPrice).toEqual(expect.any(String));
                expect(item.status).toEqual(expect.any(String));
                expect(item.filledSize).toEqual(expect.any(Number));
                expect(item.filledValue).toEqual(expect.any(String));
                expect(item.reduceOnly).toEqual(expect.any(Boolean));
            })
            console.log(resp);
        });
    });

    test('getRecentClosedOrders request test', () => {
        /**
         * getRecentClosedOrders
         * Get Recent Closed Orders
         * /api/v1/recentDoneOrders
         */
        let builder = GetRecentClosedOrdersReq.builder();
        builder.setSymbol('XBTUSDTM');
        let req = builder.build();
        let resp = api.getRecentClosedOrders(req);
        return resp.then((result) => {
            result.data.forEach(item=> {
                expect(item.id).toEqual(expect.any(String));
                expect(item.symbol).toEqual(expect.any(String));
                expect(item.type).toEqual(expect.any(String));
                expect(item.side).toEqual(expect.any(String));
                expect(item.price).toEqual(expect.any(String));
                expect(item.size).toEqual(expect.any(Number));
                expect(item.value).toEqual(expect.any(String));
                expect(item.dealValue).toEqual(expect.any(String));
                expect(item.dealSize).toEqual(expect.any(Number));
                expect(item.stp).toEqual(expect.any(String));
                expect(item.stop).toEqual(expect.any(String));
                expect(item.stopPriceType).toEqual(expect.any(String));
                expect(item.stopTriggered).toEqual(expect.any(Boolean));
                expect(item.timeInForce).toEqual(expect.any(String));
                expect(item.postOnly).toEqual(expect.any(Boolean));
                expect(item.hidden).toEqual(expect.any(Boolean));
                expect(item.iceberg).toEqual(expect.any(Boolean));
                expect(item.leverage).toEqual(expect.any(String));
                expect(item.forceHold).toEqual(expect.any(Boolean));
                expect(item.closeOrder).toEqual(expect.any(Boolean));
                expect(item.visibleSize).toEqual(expect.any(Number));
                expect(item.clientOid).toEqual(expect.any(String));
                expect(item.isActive).toEqual(expect.any(Boolean));
                expect(item.cancelExist).toEqual(expect.any(Boolean));
                expect(item.createdAt).toEqual(expect.any(Number));
                expect(item.updatedAt).toEqual(expect.any(Number));
                expect(item.endAt).toEqual(expect.any(Number));
                expect(item.orderTime).toEqual(expect.any(Number));
                expect(item.settleCurrency).toEqual(expect.any(String));
                expect(item.marginMode).toEqual(expect.any(String));
                expect(item.avgDealPrice).toEqual(expect.any(String));
                expect(item.status).toEqual(expect.any(String));
                expect(item.filledSize).toEqual(expect.any(Number));
                expect(item.filledValue).toEqual(expect.any(String));
                expect(item.reduceOnly).toEqual(expect.any(Boolean));
            })
            console.log(resp);
        });
    });

    test('getStopOrderList request test', () => {
        /**
         * getStopOrderList
         * Get Stop Order List
         * /api/v1/stopOrders
         */
        let builder = GetStopOrderListReq.builder();
        builder.setSymbol('XBTUSDTM');
        let req = builder.build();
        let resp = api.getStopOrderList(req);
        return resp.then((result) => {
            expect(result.currentPage).toEqual(expect.anything());
            expect(result.pageSize).toEqual(expect.anything());
            expect(result.totalNum).toEqual(expect.anything());
            expect(result.totalPage).toEqual(expect.anything());
            expect(result.items).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getOpenOrderValue request test', () => {
        /**
         * getOpenOrderValue
         * Get Open Order Value
         * /api/v1/openOrderStatistics
         */
        let builder = GetOpenOrderValueReq.builder();
        builder.setSymbol('XBTUSDTM');
        let req = builder.build();
        let resp = api.getOpenOrderValue(req);
        return resp.then((result) => {
            expect(result.openOrderBuySize).toEqual(expect.anything());
            expect(result.openOrderSellSize).toEqual(expect.anything());
            expect(result.openOrderBuyCost).toEqual(expect.anything());
            expect(result.openOrderSellCost).toEqual(expect.anything());
            expect(result.settleCurrency).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getRecentTradeHistory request test', () => {
        /**
         * getRecentTradeHistory
         * Get Recent Trade History
         * /api/v1/recentFills
         */
        let builder = GetRecentTradeHistoryReq.builder();
        // builder.setSymbol('XBTUSDTM');
        let req = builder.build();
        let resp = api.getRecentTradeHistory(req);
        return resp.then((result) => {
            result.data.forEach(item=> {
                expect(item.symbol).toEqual(expect.any(String));
                expect(item.tradeId).toEqual(expect.any(String));
                expect(item.orderId).toEqual(expect.any(String));
                expect(item.side).toEqual(expect.any(String));
                expect(item.liquidity).toEqual(expect.any(String));
                expect(item.forceTaker).toEqual(expect.any(Boolean));
                expect(item.price).toEqual(expect.any(String));
                expect(item.size).toEqual(expect.any(Number));
                expect(item.value).toEqual(expect.any(String));
                expect(item.openFeePay).toEqual(expect.any(String));
                expect(item.closeFeePay).toEqual(expect.any(String));
                expect(item.stop).toEqual(expect.any(String));
                expect(item.feeRate).toEqual(expect.any(String));
                expect(item.fixFee).toEqual(expect.any(String));
                expect(item.feeCurrency).toEqual(expect.any(String));
                expect(item.tradeTime).toEqual(expect.any(Number));
                expect(item.marginMode).toEqual(expect.any(String));
                expect(item.displayType).toEqual(expect.any(String));
                expect(item.fee).toEqual(expect.any(String));
                expect(item.settleCurrency).toEqual(expect.any(String));
                expect(item.orderType).toEqual(expect.any(String));
                expect(item.tradeType).toEqual(expect.any(String));
                expect(item.createdAt).toEqual(expect.any(Number));
            })
            console.log(resp);
        });
    });

    test('getTradeHistory request test', () => {
        /**
         * getTradeHistory
         * Get Trade History
         * /api/v1/fills
         */
        let builder = GetTradeHistoryReq.builder();
        builder.setType(GetTradeHistoryReq.TypeEnum.MARKET).setSide(GetTradeHistoryReq.SideEnum.BUY);
        let req = builder.build();
        let resp = api.getTradeHistory(req);
        return resp.then((result) => {
            expect(result.currentPage).toEqual(expect.anything());
            expect(result.pageSize).toEqual(expect.anything());
            expect(result.totalNum).toEqual(expect.anything());
            expect(result.totalPage).toEqual(expect.anything());
            result.items.forEach(item=> {
                expect(item.symbol).toEqual(expect.any(String));
                expect(item.tradeId).toEqual(expect.any(String));
                expect(item.orderId).toEqual(expect.any(String));
                expect(item.side).toEqual(expect.any(String));
                expect(item.liquidity).toEqual(expect.any(String));
                expect(item.forceTaker).toEqual(expect.any(Boolean));
                expect(item.price).toEqual(expect.any(String));
                expect(item.size).toEqual(expect.any(Number));
                expect(item.value).toEqual(expect.any(String));
                expect(item.openFeePay).toEqual(expect.any(String));
                expect(item.closeFeePay).toEqual(expect.any(String));
                expect(item.stop).toEqual(expect.any(String));
                expect(item.feeRate).toEqual(expect.any(String));
                expect(item.fixFee).toEqual(expect.any(String));
                expect(item.feeCurrency).toEqual(expect.any(String));
                expect(item.tradeTime).toEqual(expect.any(Number));
                expect(item.marginMode).toEqual(expect.any(String));
                expect(item.displayType).toEqual(expect.any(String));
                expect(item.fee).toEqual(expect.any(String));
                expect(item.settleCurrency).toEqual(expect.any(String));
                expect(item.orderType).toEqual(expect.any(String));
                expect(item.tradeType).toEqual(expect.any(String));
                expect(item.createdAt).toEqual(expect.any(Number));
            })
            console.log(resp);
        });
    });

    test('cancelAllOrdersV1 request test', () => {
        /**
         * cancelAllOrdersV1
         * Cancel All Orders - V1
         * /api/v1/orders
         */
        let builder = CancelAllOrdersV1Req.builder();
        builder.setSymbol('XBTUSDTM');
        let req = builder.build();
        let resp = api.cancelAllOrdersV1(req);
        return resp.then((result) => {
            expect(result.cancelledOrderIds).toEqual(expect.anything());
            console.log(resp);
        });
    });
});
