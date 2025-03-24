import { GetStakingProductsReq } from './model_get_staking_products_req';
import { GetPromotionProductsReq } from './model_get_promotion_products_req';
import { GetSavingsProductsReq } from './model_get_savings_products_req';
import { GetPromotionProductsResp } from './model_get_promotion_products_resp';
import { PurchaseReq } from './model_purchase_req';
import { GetAccountHoldingResp } from './model_get_account_holding_resp';
import { GetETHStakingProductsResp } from './model_get_eth_staking_products_resp';
import { GetAccountHoldingReq } from './model_get_account_holding_req';
import { PurchaseResp } from './model_purchase_resp';
import { GetStakingProductsResp } from './model_get_staking_products_resp';
import { GetETHStakingProductsReq } from './model_get_eth_staking_products_req';
import { GetRedeemPreviewResp } from './model_get_redeem_preview_resp';
import { GetKcsStakingProductsReq } from './model_get_kcs_staking_products_req';
import { RedeemResp } from './model_redeem_resp';
import { GetRedeemPreviewReq } from './model_get_redeem_preview_req';
import { GetSavingsProductsResp } from './model_get_savings_products_resp';
import { RedeemReq } from './model_redeem_req';
import { GetKcsStakingProductsResp } from './model_get_kcs_staking_products_resp';
import { RestResponse } from '@model/common';

describe('Auto Test', () => {
    test('purchase request test', () => {
        /**
         * purchase
         * Purchase
         * /api/v1/earn/orders
         */
        let data = '{"productId": "2611", "amount": "1", "accountType": "TRADE"}';
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
         * /api/v1/earn/orders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "orderId": "2767291",\n        "orderTxId": "6603694"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = PurchaseResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getRedeemPreview request test', () => {
        /**
         * getRedeemPreview
         * Get Redeem Preview
         * /api/v1/earn/redeem-preview
         */
        let data = '{"orderId": "2767291", "fromAccountType": "MAIN"}';
        let req = GetRedeemPreviewReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getRedeemPreview response test', () => {
        /**
         * getRedeemPreview
         * Get Redeem Preview
         * /api/v1/earn/redeem-preview
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currency": "KCS",\n        "redeemAmount": "1",\n        "penaltyInterestAmount": "0",\n        "redeemPeriod": 3,\n        "deliverTime": 1729518951000,\n        "manualRedeemable": true,\n        "redeemAll": false\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetRedeemPreviewResp.fromObject(commonResp.data);
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
         * /api/v1/earn/orders
         */
        let data =
            '{"orderId": "2767291", "amount": "example_string_default_value", "fromAccountType": "MAIN", "confirmPunishRedeem": "1"}';
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
         * /api/v1/earn/orders
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "orderTxId": "6603700",\n        "deliverTime": 1729517805000,\n        "status": "PENDING",\n        "amount": "1"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = RedeemResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getSavingsProducts request test', () => {
        /**
         * getSavingsProducts
         * Get Savings Products
         * /api/v1/earn/saving/products
         */
        let data = '{"currency": "BTC"}';
        let req = GetSavingsProductsReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getSavingsProducts response test', () => {
        /**
         * getSavingsProducts
         * Get Savings Products
         * /api/v1/earn/saving/products
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "id": "2172",\n            "currency": "BTC",\n            "category": "DEMAND",\n            "type": "DEMAND",\n            "precision": 8,\n            "productUpperLimit": "480",\n            "productRemainAmount": "132.36153083",\n            "userUpperLimit": "20",\n            "userLowerLimit": "0.01",\n            "redeemPeriod": 0,\n            "lockStartTime": 1644807600000,\n            "lockEndTime": null,\n            "applyStartTime": 1644807600000,\n            "applyEndTime": null,\n            "returnRate": "0.00047208",\n            "incomeCurrency": "BTC",\n            "earlyRedeemSupported": 0,\n            "status": "ONGOING",\n            "redeemType": "MANUAL",\n            "incomeReleaseType": "DAILY",\n            "interestDate": 1729267200000,\n            "duration": 0,\n            "newUserOnly": 0\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetSavingsProductsResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getPromotionProducts request test', () => {
        /**
         * getPromotionProducts
         * Get Promotion Products
         * /api/v1/earn/promotion/products
         */
        let data = '{"currency": "BTC"}';
        let req = GetPromotionProductsReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getPromotionProducts response test', () => {
        /**
         * getPromotionProducts
         * Get Promotion Products
         * /api/v1/earn/promotion/products
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "id": "2685",\n            "currency": "BTC",\n            "category": "ACTIVITY",\n            "type": "TIME",\n            "precision": 8,\n            "productUpperLimit": "50",\n            "userUpperLimit": "1",\n            "userLowerLimit": "0.001",\n            "redeemPeriod": 0,\n            "lockStartTime": 1702371601000,\n            "lockEndTime": 1729858405000,\n            "applyStartTime": 1702371600000,\n            "applyEndTime": null,\n            "returnRate": "0.03",\n            "incomeCurrency": "BTC",\n            "earlyRedeemSupported": 0,\n            "productRemainAmount": "49.78203998",\n            "status": "ONGOING",\n            "redeemType": "TRANS_DEMAND",\n            "incomeReleaseType": "DAILY",\n            "interestDate": 1729253605000,\n            "duration": 7,\n            "newUserOnly": 1\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetPromotionProductsResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getStakingProducts request test', () => {
        /**
         * getStakingProducts
         * Get Staking Products
         * /api/v1/earn/staking/products
         */
        let data = '{"currency": "BTC"}';
        let req = GetStakingProductsReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getStakingProducts response test', () => {
        /**
         * getStakingProducts
         * Get Staking Products
         * /api/v1/earn/staking/products
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "id": "2535",\n            "currency": "STX",\n            "category": "STAKING",\n            "type": "DEMAND",\n            "precision": 8,\n            "productUpperLimit": "1000000",\n            "userUpperLimit": "10000",\n            "userLowerLimit": "1",\n            "redeemPeriod": 14,\n            "lockStartTime": 1688614514000,\n            "lockEndTime": null,\n            "applyStartTime": 1688614512000,\n            "applyEndTime": null,\n            "returnRate": "0.045",\n            "incomeCurrency": "BTC",\n            "earlyRedeemSupported": 0,\n            "productRemainAmount": "254032.90178701",\n            "status": "ONGOING",\n            "redeemType": "MANUAL",\n            "incomeReleaseType": "DAILY",\n            "interestDate": 1729267200000,\n            "duration": 0,\n            "newUserOnly": 0\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetStakingProductsResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getKcsStakingProducts request test', () => {
        /**
         * getKcsStakingProducts
         * Get KCS Staking Products
         * /api/v1/earn/kcs-staking/products
         */
        let data = '{"currency": "BTC"}';
        let req = GetKcsStakingProductsReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getKcsStakingProducts response test', () => {
        /**
         * getKcsStakingProducts
         * Get KCS Staking Products
         * /api/v1/earn/kcs-staking/products
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "id": "2611",\n            "currency": "KCS",\n            "category": "KCS_STAKING",\n            "type": "DEMAND",\n            "precision": 8,\n            "productUpperLimit": "100000000",\n            "userUpperLimit": "100000000",\n            "userLowerLimit": "1",\n            "redeemPeriod": 3,\n            "lockStartTime": 1701252000000,\n            "lockEndTime": null,\n            "applyStartTime": 1701252000000,\n            "applyEndTime": null,\n            "returnRate": "0.03471727",\n            "incomeCurrency": "KCS",\n            "earlyRedeemSupported": 0,\n            "productRemainAmount": "58065850.54998251",\n            "status": "ONGOING",\n            "redeemType": "MANUAL",\n            "incomeReleaseType": "DAILY",\n            "interestDate": 1729267200000,\n            "duration": 0,\n            "newUserOnly": 0\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetKcsStakingProductsResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getETHStakingProducts request test', () => {
        /**
         * getETHStakingProducts
         * Get ETH Staking Products
         * /api/v1/earn/eth-staking/products
         */
        let data = '{"currency": "BTC"}';
        let req = GetETHStakingProductsReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getETHStakingProducts response test', () => {
        /**
         * getETHStakingProducts
         * Get ETH Staking Products
         * /api/v1/earn/eth-staking/products
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "id": "ETH2",\n            "category": "ETH2",\n            "type": "DEMAND",\n            "precision": 8,\n            "currency": "ETH",\n            "incomeCurrency": "ETH2",\n            "returnRate": "0.028",\n            "userLowerLimit": "0.01",\n            "userUpperLimit": "8557.3597075",\n            "productUpperLimit": "8557.3597075",\n            "productRemainAmount": "8557.3597075",\n            "redeemPeriod": 5,\n            "redeemType": "MANUAL",\n            "incomeReleaseType": "DAILY",\n            "applyStartTime": 1729255485000,\n            "applyEndTime": null,\n            "lockStartTime": 1729255485000,\n            "lockEndTime": null,\n            "interestDate": 1729267200000,\n            "newUserOnly": 0,\n            "earlyRedeemSupported": 0,\n            "duration": 0,\n            "status": "ONGOING"\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetETHStakingProductsResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getAccountHolding request test', () => {
        /**
         * getAccountHolding
         * Get Account Holding
         * /api/v1/earn/hold-assets
         */
        let data =
            '{"currency": "KCS", "productId": "example_string_default_value", "productCategory": "DEMAND", "currentPage": 1, "pageSize": 10}';
        let req = GetAccountHoldingReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getAccountHolding response test', () => {
        /**
         * getAccountHolding
         * Get Account Holding
         * /api/v1/earn/hold-assets
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "totalNum": 1,\n        "totalPage": 1,\n        "currentPage": 1,\n        "pageSize": 15,\n        "items": [\n            {\n                "orderId": "2767291",\n                "productId": "2611",\n                "productCategory": "KCS_STAKING",\n                "productType": "DEMAND",\n                "currency": "KCS",\n                "incomeCurrency": "KCS",\n                "returnRate": "0.03471727",\n                "holdAmount": "1",\n                "redeemedAmount": "0",\n                "redeemingAmount": "1",\n                "lockStartTime": 1701252000000,\n                "lockEndTime": null,\n                "purchaseTime": 1729257513000,\n                "redeemPeriod": 3,\n                "status": "REDEEMING",\n                "earlyRedeemSupported": 0\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetAccountHoldingResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
});
