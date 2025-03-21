import { PurchaseReq } from './model_purchase_req';
import { GetPurchaseOrdersReq } from './model_get_purchase_orders_req';
import { PurchaseResp } from './model_purchase_resp';
import { ModifyPurchaseReq } from './model_modify_purchase_req';
import { GetLoanMarketInterestRateReq } from './model_get_loan_market_interest_rate_req';
import { ModifyPurchaseResp } from './model_modify_purchase_resp';
import { GetLoanMarketInterestRateResp } from './model_get_loan_market_interest_rate_resp';
import { GetRedeemOrdersResp } from './model_get_redeem_orders_resp';
import { GetPurchaseOrdersResp } from './model_get_purchase_orders_resp';
import { GetLoanMarketResp } from './model_get_loan_market_resp';
import { RedeemResp } from './model_redeem_resp';
import { GetRedeemOrdersReq } from './model_get_redeem_orders_req';
import { RedeemReq } from './model_redeem_req';
import { GetLoanMarketReq } from './model_get_loan_market_req';
import { RestResponse } from '@model/common';

describe('Auto Test', () => {
    test('getLoanMarket request test', () => {
        /**
         * getLoanMarket
         * Get Loan Market
         * /api/v3/project/list
         */
        let data = '{"currency": "BTC"}';
        let req = GetLoanMarketReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getLoanMarket response test', () => {
        /**
         * getLoanMarket
         * Get Loan Market
         * /api/v3/project/list
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "currency": "BTC",\n            "purchaseEnable": true,\n            "redeemEnable": true,\n            "increment": "0.00000001",\n            "minPurchaseSize": "0.001",\n            "maxPurchaseSize": "40",\n            "interestIncrement": "0.0001",\n            "minInterestRate": "0.005",\n            "marketInterestRate": "0.005",\n            "maxInterestRate": "0.32",\n            "autoPurchaseEnable": false\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetLoanMarketResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getLoanMarketInterestRate request test', () => {
        /**
         * getLoanMarketInterestRate
         * Get Loan Market Interest Rate
         * /api/v3/project/marketInterestRate
         */
        let data = '{"currency": "BTC"}';
        let req = GetLoanMarketInterestRateReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getLoanMarketInterestRate response test', () => {
        /**
         * getLoanMarketInterestRate
         * Get Loan Market Interest Rate
         * /api/v3/project/marketInterestRate
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "time": "202410170000",\n            "marketInterestRate": "0.005"\n        },\n        {\n            "time": "202410170100",\n            "marketInterestRate": "0.005"\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetLoanMarketInterestRateResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('purchase request test', () => {
        /**
         * purchase
         * Purchase
         * /api/v3/purchase
         */
        let data = '{"currency": "BTC", "size": "0.001", "interestRate": "0.1"}';
        let req = PurchaseReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('purchase response test', () => {
        /**
         * purchase
         * Purchase
         * /api/v3/purchase
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "orderNo": "671bafa804c26d000773c533"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = PurchaseResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('modifyPurchase request test', () => {
        /**
         * modifyPurchase
         * Modify Purchase
         * /api/v3/lend/purchase/update
         */
        let data =
            '{"currency": "BTC", "purchaseOrderNo": "671bafa804c26d000773c533", "interestRate": "0.09"}';
        let req = ModifyPurchaseReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('modifyPurchase response test', () => {
        /**
         * modifyPurchase
         * Modify Purchase
         * /api/v3/lend/purchase/update
         */
        let data = '{\n    "code": "200000",\n    "data": null\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = ModifyPurchaseResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getPurchaseOrders request test', () => {
        /**
         * getPurchaseOrders
         * Get Purchase Orders
         * /api/v3/purchase/orders
         */
        let data =
            '{"status": "DONE", "currency": "BTC", "purchaseOrderNo": "example_string_default_value", "currentPage": 1, "pageSize": 50}';
        let req = GetPurchaseOrdersReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getPurchaseOrders response test', () => {
        /**
         * getPurchaseOrders
         * Get Purchase Orders
         * /api/v3/purchase/orders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 10,\n        "totalNum": 1,\n        "totalPage": 1,\n        "items": [\n            {\n                "currency": "BTC",\n                "purchaseOrderNo": "671bb15a3b3f930007880bae",\n                "purchaseSize": "0.001",\n                "matchSize": "0",\n                "interestRate": "0.1",\n                "incomeSize": "0",\n                "applyTime": 1729868122172,\n                "status": "PENDING"\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetPurchaseOrdersResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('redeem request test', () => {
        /**
         * redeem
         * Redeem
         * /api/v3/redeem
         */
        let data =
            '{"currency": "BTC", "size": "0.001", "purchaseOrderNo": "671bafa804c26d000773c533"}';
        let req = RedeemReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('redeem response test', () => {
        /**
         * redeem
         * Redeem
         * /api/v3/redeem
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "orderNo": "671bafa804c26d000773c533"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = RedeemResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getRedeemOrders request test', () => {
        /**
         * getRedeemOrders
         * Get Redeem Orders
         * /api/v3/redeem/orders
         */
        let data =
            '{"status": "DONE", "currency": "BTC", "redeemOrderNo": "example_string_default_value", "currentPage": 1, "pageSize": 50}';
        let req = GetRedeemOrdersReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getRedeemOrders response test', () => {
        /**
         * getRedeemOrders
         * Get Redeem Orders
         * /api/v3/redeem/orders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 10,\n        "totalNum": 1,\n        "totalPage": 1,\n        "items": [\n            {\n                "currency": "BTC",\n                "purchaseOrderNo": "671bafa804c26d000773c533",\n                "redeemOrderNo": "671bb01004c26d000773c55c",\n                "redeemSize": "0.001",\n                "receiptSize": "0.001",\n                "applyTime": null,\n                "status": "DONE"\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetRedeemOrdersResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
});
