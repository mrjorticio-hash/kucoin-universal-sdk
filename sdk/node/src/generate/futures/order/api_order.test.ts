import { GetTradeHistoryResp } from './model_get_trade_history_resp';
import { GetOpenOrderValueResp } from './model_get_open_order_value_resp';
import { GetOrderByOrderIdResp } from './model_get_order_by_order_id_resp';
import { CancelAllStopOrdersReq } from './model_cancel_all_stop_orders_req';
import { GetOrderByClientOidResp } from './model_get_order_by_client_oid_resp';
import { CancelOrderByClientOidReq } from './model_cancel_order_by_client_oid_req';
import { GetRecentClosedOrdersReq } from './model_get_recent_closed_orders_req';
import { GetStopOrderListResp } from './model_get_stop_order_list_resp';
import { CancelOrderByIdResp } from './model_cancel_order_by_id_resp';
import { AddTPSLOrderReq } from './model_add_tpsl_order_req';
import { AddOrderReq } from './model_add_order_req';
import { CancelAllStopOrdersResp } from './model_cancel_all_stop_orders_resp';
import { CancelAllOrdersV1Req } from './model_cancel_all_orders_v1_req';
import { GetStopOrderListReq } from './model_get_stop_order_list_req';
import { AddTPSLOrderResp } from './model_add_tpsl_order_resp';
import { GetTradeHistoryReq } from './model_get_trade_history_req';
import { GetOpenOrderValueReq } from './model_get_open_order_value_req';
import { CancelAllOrdersV3Resp } from './model_cancel_all_orders_v3_resp';
import { AddOrderTestReq } from './model_add_order_test_req';
import { GetOrderListResp } from './model_get_order_list_resp';
import { GetOrderListReq } from './model_get_order_list_req';
import { BatchCancelOrdersReq } from './model_batch_cancel_orders_req';
import { CancelAllOrdersV3Req } from './model_cancel_all_orders_v3_req';
import { BatchCancelOrdersResp } from './model_batch_cancel_orders_resp';
import { CancelOrderByClientOidResp } from './model_cancel_order_by_client_oid_resp';
import { AddOrderTestResp } from './model_add_order_test_resp';
import { CancelAllOrdersV1Resp } from './model_cancel_all_orders_v1_resp';
import { GetOrderByClientOidReq } from './model_get_order_by_client_oid_req';
import { BatchAddOrdersResp } from './model_batch_add_orders_resp';
import { BatchAddOrdersReq } from './model_batch_add_orders_req';
import { AddOrderResp } from './model_add_order_resp';
import { GetRecentTradeHistoryReq } from './model_get_recent_trade_history_req';
import { GetRecentTradeHistoryResp } from './model_get_recent_trade_history_resp';
import { GetOrderByOrderIdReq } from './model_get_order_by_order_id_req';
import { CancelOrderByIdReq } from './model_cancel_order_by_id_req';
import { GetRecentClosedOrdersResp } from './model_get_recent_closed_orders_resp';
import { RestResponse } from '@model/common';

describe('Auto Test', () => {
    test('addOrder request test', () => {
        /**
         * addOrder
         * Add Order
         * /api/v1/orders
         */
        let data =
            '{"clientOid": "5c52e11203aa677f33e493fb", "side": "buy", "symbol": "XBTUSDTM", "leverage": 3, "type": "limit", "remark": "order remarks", "reduceOnly": false, "marginMode": "ISOLATED", "price": "0.1", "size": 1, "timeInForce": "GTC"}';
        let req = AddOrderReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('addOrder response test', () => {
        /**
         * addOrder
         * Add Order
         * /api/v1/orders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "orderId": "234125150956625920",\n        "clientOid": "5c52e11203aa677f33e493fb"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = AddOrderResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('addOrderTest request test', () => {
        /**
         * addOrderTest
         * Add Order Test
         * /api/v1/orders/test
         */
        let data =
            '{"clientOid": "5c52e11203aa677f33e493fb", "side": "buy", "symbol": "XBTUSDTM", "leverage": 3, "type": "limit", "remark": "order remarks", "reduceOnly": false, "marginMode": "ISOLATED", "price": "0.1", "size": 1, "timeInForce": "GTC"}';
        let req = AddOrderTestReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('addOrderTest response test', () => {
        /**
         * addOrderTest
         * Add Order Test
         * /api/v1/orders/test
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "orderId": "234125150956625920",\n        "clientOid": "5c52e11203aa677f33e493fb"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = AddOrderTestResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('batchAddOrders request test', () => {
        /**
         * batchAddOrders
         * Batch Add Orders
         * /api/v1/orders/multi
         */
        let data =
            '[{"clientOid": "5c52e11203aa677f33e493fb", "side": "buy", "symbol": "XBTUSDTM", "leverage": 3, "type": "limit", "remark": "order remarks", "reduceOnly": false, "marginMode": "ISOLATED", "price": "0.1", "size": 1, "timeInForce": "GTC"}, {"clientOid": "5c52e11203aa677f33e493fc", "side": "buy", "symbol": "XBTUSDTM", "leverage": 3, "type": "limit", "remark": "order remarks", "reduceOnly": false, "marginMode": "ISOLATED", "price": "0.1", "size": 1, "timeInForce": "GTC"}]';
        let req = BatchAddOrdersReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('batchAddOrders response test', () => {
        /**
         * batchAddOrders
         * Batch Add Orders
         * /api/v1/orders/multi
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "orderId": "235919387779985408",\n            "clientOid": "5c52e11203aa677f33e493fb",\n            "symbol": "XBTUSDTM",\n            "code": "200000",\n            "msg": "success"\n        },\n        {\n            "orderId": "235919387855482880",\n            "clientOid": "5c52e11203aa677f33e493fc",\n            "symbol": "XBTUSDTM",\n            "code": "200000",\n            "msg": "success"\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = BatchAddOrdersResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('addTPSLOrder request test', () => {
        /**
         * addTPSLOrder
         * Add Take Profit And Stop Loss Order
         * /api/v1/st-orders
         */
        let data =
            '{"clientOid": "5c52e11203aa677f33e493fb", "side": "buy", "symbol": "XBTUSDTM", "leverage": 3, "type": "limit", "remark": "order remarks", "reduceOnly": false, "marginMode": "ISOLATED", "price": "0.2", "size": 1, "timeInForce": "GTC", "triggerStopUpPrice": "0.3", "triggerStopDownPrice": "0.1", "stopPriceType": "TP"}';
        let req = AddTPSLOrderReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('addTPSLOrder response test', () => {
        /**
         * addTPSLOrder
         * Add Take Profit And Stop Loss Order
         * /api/v1/st-orders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "orderId": "234125150956625920",\n        "clientOid": "5c52e11203aa677f33e493fb"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = AddTPSLOrderResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('cancelOrderById request test', () => {
        /**
         * cancelOrderById
         * Cancel Order By OrderId
         * /api/v1/orders/{orderId}
         */
        let data = '{"orderId": "example_string_default_value"}';
        let req = CancelOrderByIdReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('cancelOrderById response test', () => {
        /**
         * cancelOrderById
         * Cancel Order By OrderId
         * /api/v1/orders/{orderId}
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "cancelledOrderIds": [\n            "235303670076489728"\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = CancelOrderByIdResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('cancelOrderByClientOid request test', () => {
        /**
         * cancelOrderByClientOid
         * Cancel Order By ClientOid
         * /api/v1/orders/client-order/{clientOid}
         */
        let data = '{"symbol": "XBTUSDTM", "clientOid": "example_string_default_value"}';
        let req = CancelOrderByClientOidReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('cancelOrderByClientOid response test', () => {
        /**
         * cancelOrderByClientOid
         * Cancel Order By ClientOid
         * /api/v1/orders/client-order/{clientOid}
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "clientOid": "017485b0-2957-4681-8a14-5d46b35aee0d"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = CancelOrderByClientOidResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('batchCancelOrders request test', () => {
        /**
         * batchCancelOrders
         * Batch Cancel Orders
         * /api/v1/orders/multi-cancel
         */
        let data = '{"orderIdsList": ["250445104152670209", "250445181751463936"]}';
        let req = BatchCancelOrdersReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('batchCancelOrders response test', () => {
        /**
         * batchCancelOrders
         * Batch Cancel Orders
         * /api/v1/orders/multi-cancel
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "orderId": "250445104152670209",\n            "clientOid": null,\n            "code": "200",\n            "msg": "success"\n        },\n        {\n            "orderId": "250445181751463936",\n            "clientOid": null,\n            "code": "200",\n            "msg": "success"\n        }\n    ]\n}\n';
        let commonResp = RestResponse.fromJson(data);
        let resp = BatchCancelOrdersResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('cancelAllOrdersV3 request test', () => {
        /**
         * cancelAllOrdersV3
         * Cancel All Orders
         * /api/v3/orders
         */
        let data = '{"symbol": "XBTUSDTM"}';
        let req = CancelAllOrdersV3Req.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('cancelAllOrdersV3 response test', () => {
        /**
         * cancelAllOrdersV3
         * Cancel All Orders
         * /api/v3/orders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "cancelledOrderIds": [\n            "235919172150824960",\n            "235919172150824961"\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = CancelAllOrdersV3Resp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('cancelAllStopOrders request test', () => {
        /**
         * cancelAllStopOrders
         * Cancel All Stop orders
         * /api/v1/stopOrders
         */
        let data = '{"symbol": "XBTUSDTM"}';
        let req = CancelAllStopOrdersReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('cancelAllStopOrders response test', () => {
        /**
         * cancelAllStopOrders
         * Cancel All Stop orders
         * /api/v1/stopOrders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "cancelledOrderIds": [\n            "235919172150824960",\n            "235919172150824961"\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = CancelAllStopOrdersResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getOrderByOrderId request test', () => {
        /**
         * getOrderByOrderId
         * Get Order By OrderId
         * /api/v1/orders/{order-id}
         */
        let data = '{"order-id": "236655147005071361"}';
        let req = GetOrderByOrderIdReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getOrderByOrderId response test', () => {
        /**
         * getOrderByOrderId
         * Get Order By OrderId
         * /api/v1/orders/{order-id}
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "id": "236655147005071361",\n        "symbol": "XBTUSDTM",\n        "type": "limit",\n        "side": "buy",\n        "price": "0.1",\n        "size": 1,\n        "value": "0.0001",\n        "dealValue": "0",\n        "dealSize": 0,\n        "stp": "",\n        "stop": "",\n        "stopPriceType": "",\n        "stopTriggered": false,\n        "stopPrice": null,\n        "timeInForce": "GTC",\n        "postOnly": false,\n        "hidden": false,\n        "iceberg": false,\n        "leverage": "3",\n        "forceHold": false,\n        "closeOrder": false,\n        "visibleSize": 0,\n        "clientOid": "5c52e11203aa677f33e493fb",\n        "remark": null,\n        "tags": "",\n        "isActive": true,\n        "cancelExist": false,\n        "createdAt": 1729236185949,\n        "updatedAt": 1729236185949,\n        "endAt": null,\n        "orderTime": 1729236185885647952,\n        "settleCurrency": "USDT",\n        "marginMode": "ISOLATED",\n        "avgDealPrice": "0",\n        "filledSize": 0,\n        "filledValue": "0",\n        "status": "open",\n        "reduceOnly": false\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetOrderByOrderIdResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getOrderByClientOid request test', () => {
        /**
         * getOrderByClientOid
         * Get Order By ClientOid
         * /api/v1/orders/byClientOid
         */
        let data = '{"clientOid": "5c52e11203aa677f33e493fb"}';
        let req = GetOrderByClientOidReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getOrderByClientOid response test', () => {
        /**
         * getOrderByClientOid
         * Get Order By ClientOid
         * /api/v1/orders/byClientOid
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "id": "250444645610336256",\n        "symbol": "XRPUSDTM",\n        "type": "limit",\n        "side": "buy",\n        "price": "0.1",\n        "size": 1,\n        "value": "1",\n        "dealValue": "0",\n        "dealSize": 0,\n        "stp": "",\n        "stop": "",\n        "stopPriceType": "",\n        "stopTriggered": false,\n        "stopPrice": null,\n        "timeInForce": "GTC",\n        "postOnly": false,\n        "hidden": false,\n        "iceberg": false,\n        "leverage": "3",\n        "forceHold": false,\n        "closeOrder": false,\n        "visibleSize": 0,\n        "clientOid": "5c52e11203aa677f33e493fb",\n        "remark": null,\n        "tags": "",\n        "isActive": true,\n        "cancelExist": false,\n        "createdAt": 1732523858568,\n        "updatedAt": 1732523858568,\n        "endAt": null,\n        "orderTime": 1732523858550892322,\n        "settleCurrency": "USDT",\n        "marginMode": "ISOLATED",\n        "avgDealPrice": "0",\n        "filledSize": 0,\n        "filledValue": "0",\n        "status": "open",\n        "reduceOnly": false\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetOrderByClientOidResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getOrderList request test', () => {
        /**
         * getOrderList
         * Get Order List
         * /api/v1/orders
         */
        let data =
            '{"status": "done", "symbol": "example_string_default_value", "side": "buy", "type": "limit", "startAt": 123456, "endAt": 123456, "currentPage": 1, "pageSize": 50}';
        let req = GetOrderListReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getOrderList response test', () => {
        /**
         * getOrderList
         * Get Order List
         * /api/v1/orders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 50,\n        "totalNum": 1,\n        "totalPage": 1,\n        "items": [\n            {\n                "id": "230181737576050688",\n                "symbol": "PEOPLEUSDTM",\n                "type": "limit",\n                "side": "buy",\n                "price": "0.05",\n                "size": 10,\n                "value": "5",\n                "dealValue": "0",\n                "dealSize": 0,\n                "stp": "",\n                "stop": "",\n                "stopPriceType": "",\n                "stopTriggered": false,\n                "stopPrice": null,\n                "timeInForce": "GTC",\n                "postOnly": false,\n                "hidden": false,\n                "iceberg": false,\n                "leverage": "1",\n                "forceHold": false,\n                "closeOrder": false,\n                "visibleSize": 0,\n                "clientOid": "5a80bd847f1811ef8a7faa665a37b3d7",\n                "remark": null,\n                "tags": "",\n                "isActive": true,\n                "cancelExist": false,\n                "createdAt": 1727692804813,\n                "updatedAt": 1727692804813,\n                "endAt": null,\n                "orderTime": 1727692804808418000,\n                "settleCurrency": "USDT",\n                "marginMode": "ISOLATED",\n                "avgDealPrice": "0",\n                "filledSize": 0,\n                "filledValue": "0",\n                "status": "open",\n                "reduceOnly": false\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetOrderListResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getRecentClosedOrders request test', () => {
        /**
         * getRecentClosedOrders
         * Get Recent Closed Orders
         * /api/v1/recentDoneOrders
         */
        let data = '{"symbol": "XBTUSDTM"}';
        let req = GetRecentClosedOrdersReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getRecentClosedOrders response test', () => {
        /**
         * getRecentClosedOrders
         * Get Recent Closed Orders
         * /api/v1/recentDoneOrders
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "id": "236387137732231168",\n            "symbol": "XRPUSDTM",\n            "type": "market",\n            "side": "buy",\n            "price": "0",\n            "size": 1,\n            "value": "5.51",\n            "dealValue": "5.511",\n            "dealSize": 1,\n            "stp": "",\n            "stop": "",\n            "stopPriceType": "",\n            "stopTriggered": false,\n            "stopPrice": null,\n            "timeInForce": "GTC",\n            "postOnly": false,\n            "hidden": false,\n            "iceberg": false,\n            "leverage": "10.0",\n            "forceHold": false,\n            "closeOrder": false,\n            "visibleSize": 0,\n            "clientOid": "16698fe6-2746-4aeb-a7fa-61f633ab6090",\n            "remark": null,\n            "tags": "",\n            "isActive": false,\n            "cancelExist": false,\n            "createdAt": 1729172287496,\n            "updatedAt": 1729172287568,\n            "endAt": 1729172287568,\n            "orderTime": 1729172287496950800,\n            "settleCurrency": "USDT",\n            "marginMode": "ISOLATED",\n            "avgDealPrice": "0.5511",\n            "filledSize": 1,\n            "filledValue": "5.511",\n            "status": "done",\n            "reduceOnly": false\n        },\n        {\n            "id": "236317213710184449",\n            "symbol": "XBTUSDTM",\n            "type": "market",\n            "side": "buy",\n            "price": "0",\n            "size": 1,\n            "value": "67.4309",\n            "dealValue": "67.4309",\n            "dealSize": 1,\n            "stp": "",\n            "stop": "",\n            "stopPriceType": "",\n            "stopTriggered": false,\n            "stopPrice": null,\n            "timeInForce": "GTC",\n            "postOnly": false,\n            "hidden": false,\n            "iceberg": false,\n            "leverage": "3",\n            "forceHold": false,\n            "closeOrder": false,\n            "visibleSize": 0,\n            "clientOid": "5c52e11203aa677f33e493fb",\n            "remark": null,\n            "tags": "",\n            "isActive": false,\n            "cancelExist": false,\n            "createdAt": 1729155616310,\n            "updatedAt": 1729155616324,\n            "endAt": 1729155616324,\n            "orderTime": 1729155616310180400,\n            "settleCurrency": "USDT",\n            "marginMode": "ISOLATED",\n            "avgDealPrice": "67430.9",\n            "filledSize": 1,\n            "filledValue": "67.4309",\n            "status": "done",\n            "reduceOnly": false\n        },\n        {\n            "id": "236317094436728832",\n            "symbol": "XBTUSDTM",\n            "type": "market",\n            "side": "buy",\n            "price": "0",\n            "size": 1,\n            "value": "67.445",\n            "dealValue": "67.445",\n            "dealSize": 1,\n            "stp": "",\n            "stop": "",\n            "stopPriceType": "",\n            "stopTriggered": false,\n            "stopPrice": null,\n            "timeInForce": "GTC",\n            "postOnly": false,\n            "hidden": false,\n            "iceberg": false,\n            "leverage": "3",\n            "forceHold": false,\n            "closeOrder": false,\n            "visibleSize": 0,\n            "clientOid": "5c52e11203aa677f33e493fb",\n            "remark": null,\n            "tags": "",\n            "isActive": false,\n            "cancelExist": false,\n            "createdAt": 1729155587873,\n            "updatedAt": 1729155587946,\n            "endAt": 1729155587946,\n            "orderTime": 1729155587873332000,\n            "settleCurrency": "USDT",\n            "marginMode": "ISOLATED",\n            "avgDealPrice": "67445.0",\n            "filledSize": 1,\n            "filledValue": "67.445",\n            "status": "done",\n            "reduceOnly": false\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetRecentClosedOrdersResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getStopOrderList request test', () => {
        /**
         * getStopOrderList
         * Get Stop Order List
         * /api/v1/stopOrders
         */
        let data =
            '{"symbol": "XBTUSDTM", "side": "buy", "type": "limit", "startAt": 123456, "endAt": 123456, "currentPage": 123456, "pageSize": 50}';
        let req = GetStopOrderListReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getStopOrderList response test', () => {
        /**
         * getStopOrderList
         * Get Stop Order List
         * /api/v1/stopOrders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 50,\n        "totalNum": 1,\n        "totalPage": 1,\n        "items": [\n            {\n                "id": "230181737576050688",\n                "symbol": "PEOPLEUSDTM",\n                "type": "limit",\n                "side": "buy",\n                "price": "0.05",\n                "size": 10,\n                "value": "5",\n                "dealValue": "0",\n                "dealSize": 0,\n                "stp": "",\n                "stop": "",\n                "stopPriceType": "",\n                "stopTriggered": false,\n                "stopPrice": null,\n                "timeInForce": "GTC",\n                "postOnly": false,\n                "hidden": false,\n                "iceberg": false,\n                "leverage": "1",\n                "forceHold": false,\n                "closeOrder": false,\n                "visibleSize": 0,\n                "clientOid": "5a80bd847f1811ef8a7faa665a37b3d7",\n                "remark": null,\n                "tags": "",\n                "isActive": true,\n                "cancelExist": false,\n                "createdAt": 1727692804813,\n                "updatedAt": 1727692804813,\n                "endAt": null,\n                "orderTime": 1727692804808418000,\n                "settleCurrency": "USDT",\n                "marginMode": "ISOLATED",\n                "avgDealPrice": "0",\n                "filledSize": 0,\n                "filledValue": "0",\n                "status": "open",\n                "reduceOnly": false\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetStopOrderListResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getOpenOrderValue request test', () => {
        /**
         * getOpenOrderValue
         * Get Open Order Value
         * /api/v1/openOrderStatistics
         */
        let data = '{"symbol": "XBTUSDTM"}';
        let req = GetOpenOrderValueReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getOpenOrderValue response test', () => {
        /**
         * getOpenOrderValue
         * Get Open Order Value
         * /api/v1/openOrderStatistics
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "openOrderBuySize": 1,\n        "openOrderSellSize": 0,\n        "openOrderBuyCost": "0.0001",\n        "openOrderSellCost": "0",\n        "settleCurrency": "USDT"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetOpenOrderValueResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getRecentTradeHistory request test', () => {
        /**
         * getRecentTradeHistory
         * Get Recent Trade History
         * /api/v1/recentFills
         */
        let data = '{"symbol": "XBTUSDTM"}';
        let req = GetRecentTradeHistoryReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getRecentTradeHistory response test', () => {
        /**
         * getRecentTradeHistory
         * Get Recent Trade History
         * /api/v1/recentFills
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "symbol": "XBTUSDTM",\n            "tradeId": "1784277229880",\n            "orderId": "236317213710184449",\n            "side": "buy",\n            "liquidity": "taker",\n            "forceTaker": false,\n            "price": "67430.9",\n            "size": 1,\n            "value": "67.4309",\n            "openFeePay": "0.04045854",\n            "closeFeePay": "0",\n            "stop": "",\n            "feeRate": "0.00060",\n            "fixFee": "0",\n            "feeCurrency": "USDT",\n            "marginMode": "ISOLATED",\n            "fee": "0.04045854",\n            "settleCurrency": "USDT",\n            "orderType": "market",\n            "displayType": "market",\n            "tradeType": "trade",\n            "subTradeType": null,\n            "tradeTime": 1729155616320000000,\n            "createdAt": 1729155616493\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "tradeId": "1784277132002",\n            "orderId": "236317094436728832",\n            "side": "buy",\n            "liquidity": "taker",\n            "forceTaker": false,\n            "price": "67445",\n            "size": 1,\n            "value": "67.445",\n            "openFeePay": "0",\n            "closeFeePay": "0.040467",\n            "stop": "",\n            "feeRate": "0.00060",\n            "fixFee": "0",\n            "feeCurrency": "USDT",\n            "marginMode": "ISOLATED",\n            "fee": "0.040467",\n            "settleCurrency": "USDT",\n            "orderType": "market",\n            "displayType": "market",\n            "tradeType": "trade",\n            "subTradeType": null,\n            "tradeTime": 1729155587944000000,\n            "createdAt": 1729155588104\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetRecentTradeHistoryResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getTradeHistory request test', () => {
        /**
         * getTradeHistory
         * Get Trade History
         * /api/v1/fills
         */
        let data =
            '{"orderId": "236655147005071361", "symbol": "example_string_default_value", "side": "buy", "type": "limit", "tradeTypes": "trade", "startAt": 123456, "endAt": 123456, "currentPage": 1, "pageSize": 50}';
        let req = GetTradeHistoryReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getTradeHistory response test', () => {
        /**
         * getTradeHistory
         * Get Trade History
         * /api/v1/fills
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 50,\n        "totalNum": 2,\n        "totalPage": 1,\n        "items": [\n            {\n                "symbol": "XBTUSDTM",\n                "tradeId": "1828954878212",\n                "orderId": "284486580251463680",\n                "side": "buy",\n                "liquidity": "taker",\n                "forceTaker": false,\n                "price": "86275.1",\n                "size": 1,\n                "value": "86.2751",\n                "openFeePay": "0.05176506",\n                "closeFeePay": "0",\n                "stop": "",\n                "feeRate": "0.00060",\n                "fixFee": "0",\n                "feeCurrency": "USDT",\n                "subTradeType": null,\n                "marginMode": "CROSS",\n                "openFeeTaxPay": "0",\n                "closeFeeTaxPay": "0",\n                "displayType": "market",\n                "fee": "0.05176506",\n                "settleCurrency": "USDT",\n                "orderType": "market",\n                "tradeType": "trade",\n                "tradeTime": 1740640088244000000,\n                "createdAt": 1740640088427\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetTradeHistoryResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('cancelAllOrdersV1 request test', () => {
        /**
         * cancelAllOrdersV1
         * Cancel All Orders - V1
         * /api/v1/orders
         */
        let data = '{"symbol": "XBTUSDTM"}';
        let req = CancelAllOrdersV1Req.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('cancelAllOrdersV1 response test', () => {
        /**
         * cancelAllOrdersV1
         * Cancel All Orders - V1
         * /api/v1/orders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "cancelledOrderIds": [\n            "235919172150824960",\n            "235919172150824961"\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = CancelAllOrdersV1Resp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
});
