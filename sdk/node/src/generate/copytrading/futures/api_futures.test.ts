import { CancelOrderByClientOidReq } from './model_cancel_order_by_client_oid_req';
import { RemoveIsolatedMarginReq } from './model_remove_isolated_margin_req';
import { CancelOrderByIdResp } from './model_cancel_order_by_id_resp';
import { AddTPSLOrderReq } from './model_add_tpsl_order_req';
import { AddIsolatedMarginReq } from './model_add_isolated_margin_req';
import { ModifyAutoDepositStatusReq } from './model_modify_auto_deposit_status_req';
import { RemoveIsolatedMarginResp } from './model_remove_isolated_margin_resp';
import { GetMaxOpenSizeReq } from './model_get_max_open_size_req';
import { AddOrderTestReq } from './model_add_order_test_req';
import { AddOrderReq } from './model_add_order_req';
import { GetMaxWithdrawMarginResp } from './model_get_max_withdraw_margin_resp';
import { AddIsolatedMarginResp } from './model_add_isolated_margin_resp';
import { ModifyAutoDepositStatusResp } from './model_modify_auto_deposit_status_resp';
import { CancelOrderByClientOidResp } from './model_cancel_order_by_client_oid_resp';
import { AddOrderTestResp } from './model_add_order_test_resp';
import { GetMaxOpenSizeResp } from './model_get_max_open_size_resp';
import { AddOrderResp } from './model_add_order_resp';
import { ModifyIsolatedMarginRiskLimtResp } from './model_modify_isolated_margin_risk_limt_resp';
import { ModifyIsolatedMarginRiskLimtReq } from './model_modify_isolated_margin_risk_limt_req';
import { AddTPSLOrderResp } from './model_add_tpsl_order_resp';
import { CancelOrderByIdReq } from './model_cancel_order_by_id_req';
import { GetMaxWithdrawMarginReq } from './model_get_max_withdraw_margin_req';
import { RestResponse } from '@model/common';

describe('Auto Test', () => {
    test('addOrder request test', () => {
        /**
         * addOrder
         * Add Order
         * /api/v1/copy-trade/futures/orders
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
         * /api/v1/copy-trade/futures/orders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "orderId": "263485113055133696",\n        "clientOid": "5c52e11203aa677f331e493fb"\n    }\n}';
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
         * /api/v1/copy-trade/futures/orders/test
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
         * /api/v1/copy-trade/futures/orders/test
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
    test('addTPSLOrder request test', () => {
        /**
         * addTPSLOrder
         * Add Take Profit And Stop Loss Order
         * /api/v1/copy-trade/futures/st-orders
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
         * /api/v1/copy-trade/futures/st-orders
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
         * /api/v1/copy-trade/futures/orders
         */
        let data = '{"orderId": "263485113055133696"}';
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
         * /api/v1/copy-trade/futures/orders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "cancelledOrderIds": [\n            "263485113055133696"\n        ]\n    }\n}';
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
         * /api/v1/copy-trade/futures/orders/client-order
         */
        let data = '{"symbol": "XBTUSDTM", "clientOid": "5c52e11203aa677f331e493fb"}';
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
         * /api/v1/copy-trade/futures/orders/client-order
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "clientOid": "5c52e11203aa677f331e4913fb"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = CancelOrderByClientOidResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getMaxOpenSize request test', () => {
        /**
         * getMaxOpenSize
         * Get Max Open Size
         * /api/v1/copy-trade/futures/get-max-open-size
         */
        let data = '{"symbol": "XBTUSDTM", "price": 123456.0, "leverage": 123456}';
        let req = GetMaxOpenSizeReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getMaxOpenSize response test', () => {
        /**
         * getMaxOpenSize
         * Get Max Open Size
         * /api/v1/copy-trade/futures/get-max-open-size
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "symbol": "XBTUSDTM",\n        "maxBuyOpenSize": "1000000",\n        "maxSellOpenSize": "51"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetMaxOpenSizeResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getMaxWithdrawMargin request test', () => {
        /**
         * getMaxWithdrawMargin
         * Get Max Withdraw Margin
         * /api/v1/copy-trade/futures/position/margin/max-withdraw-margin
         */
        let data = '{"symbol": "example_string_default_value"}';
        let req = GetMaxWithdrawMarginReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getMaxWithdrawMargin response test', () => {
        /**
         * getMaxWithdrawMargin
         * Get Max Withdraw Margin
         * /api/v1/copy-trade/futures/position/margin/max-withdraw-margin
         */
        let data = '{\n    "code": "200000",\n    "data": "21.1135719252"\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetMaxWithdrawMarginResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('addIsolatedMargin request test', () => {
        /**
         * addIsolatedMargin
         * Add Isolated Margin
         * /api/v1/copy-trade/futures/position/margin/deposit-margin
         */
        let data = '{"symbol": "XBTUSDTM", "margin": 3, "bizNo": "112233"}';
        let req = AddIsolatedMarginReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('addIsolatedMargin response test', () => {
        /**
         * addIsolatedMargin
         * Add Isolated Margin
         * /api/v1/copy-trade/futures/position/margin/deposit-margin
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "id": "400000000000974886",\n        "symbol": "XBTUSDTM",\n        "autoDeposit": true,\n        "maintMarginReq": "0.004",\n        "riskLimit": 100000,\n        "realLeverage": "1.83",\n        "crossMode": false,\n        "marginMode": "",\n        "positionSide": "",\n        "leverage": "1.83",\n        "delevPercentage": 0.2,\n        "openingTimestamp": 1736932881164,\n        "currentTimestamp": 1736933530230,\n        "currentQty": 1,\n        "currentCost": "97.302",\n        "currentComm": "0.0583812",\n        "unrealisedCost": "97.302",\n        "realisedGrossCost": "0.0000000000",\n        "realisedCost": "0.0583812000",\n        "isOpen": true,\n        "markPrice": "96939.98",\n        "markValue": "96.9399800000",\n        "posCost": "97.302",\n        "posCross": "20.9874",\n        "posInit": "32.4339999967",\n        "posComm": "0.0904415999",\n        "posLoss": "0",\n        "posMargin": "53.5118415966",\n        "posMaint": "0.4796495999",\n        "maintMargin": "53.1498215966",\n        "realisedGrossPnl": "0.0000000000",\n        "realisedPnl": "-0.0583812000",\n        "unrealisedPnl": "-0.3620200000",\n        "unrealisedPnlPcnt": "-0.0037",\n        "unrealisedRoePcnt": "-0.0112",\n        "avgEntryPrice": "97302.00",\n        "liquidationPrice": "44269.81",\n        "bankruptPrice": "43880.61",\n        "settleCurrency": "USDT"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = AddIsolatedMarginResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('removeIsolatedMargin request test', () => {
        /**
         * removeIsolatedMargin
         * Remove Isolated Margin
         * /api/v1/copy-trade/futures/position/margin/withdraw-margin
         */
        let data = '{"symbol": "XBTUSDTM", "withdrawAmount": 1e-07}';
        let req = RemoveIsolatedMarginReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('removeIsolatedMargin response test', () => {
        /**
         * removeIsolatedMargin
         * Remove Isolated Margin
         * /api/v1/copy-trade/futures/position/margin/withdraw-margin
         */
        let data = '{\n    "code": "200000",\n    "data": "0.1"\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = RemoveIsolatedMarginResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('modifyIsolatedMarginRiskLimt request test', () => {
        /**
         * modifyIsolatedMarginRiskLimt
         * Modify Isolated Margin Risk Limit
         * /api/v1/copy-trade/futures/position/risk-limit-level/change
         */
        let data = '{"symbol": "XBTUSDTM", "level": 1}';
        let req = ModifyIsolatedMarginRiskLimtReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('modifyIsolatedMarginRiskLimt response test', () => {
        /**
         * modifyIsolatedMarginRiskLimt
         * Modify Isolated Margin Risk Limit
         * /api/v1/copy-trade/futures/position/risk-limit-level/change
         */
        let data = '{\n    "code": "200000",\n    "data": true\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = ModifyIsolatedMarginRiskLimtResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('modifyAutoDepositStatus request test', () => {
        /**
         * modifyAutoDepositStatus
         * Modify Isolated Margin Auto-Deposit Status
         * /api/v1/copy-trade/futures/position/margin/auto-deposit-status
         */
        let data = '{"symbol": "XBTUSDTM", "status": true}';
        let req = ModifyAutoDepositStatusReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('modifyAutoDepositStatus response test', () => {
        /**
         * modifyAutoDepositStatus
         * Modify Isolated Margin Auto-Deposit Status
         * /api/v1/copy-trade/futures/position/margin/auto-deposit-status
         */
        let data = '{\n    "code": "200000",\n    "data": true\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = ModifyAutoDepositStatusResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
});
