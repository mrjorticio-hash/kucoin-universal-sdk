import { ModifyLeverageReq } from './model_modify_leverage_req';
import { GetBorrowHistoryReq } from './model_get_borrow_history_req';
import { GetRepayHistoryResp } from './model_get_repay_history_resp';
import { RepayReq } from './model_repay_req';
import { GetInterestHistoryReq } from './model_get_interest_history_req';
import { RepayResp } from './model_repay_resp';
import { BorrowResp } from './model_borrow_resp';
import { BorrowReq } from './model_borrow_req';
import { GetRepayHistoryReq } from './model_get_repay_history_req';
import { ModifyLeverageResp } from './model_modify_leverage_resp';
import { GetInterestHistoryResp } from './model_get_interest_history_resp';
import { GetBorrowHistoryResp } from './model_get_borrow_history_resp';
import { RestResponse } from '@model/common';

describe('Auto Test', () => {
    test('borrow request test', () => {
        /**
         * borrow
         * Borrow
         * /api/v3/margin/borrow
         */
        let data =
            '{"currency": "USDT", "size": 10, "timeInForce": "FOK", "isIsolated": false, "isHf": false}';
        let req = BorrowReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('borrow response test', () => {
        /**
         * borrow
         * Borrow
         * /api/v3/margin/borrow
         */
        let data =
            '{"code":"200000","data":{"orderNo":"67187162c0d6990007717b15","actualSize":"10"}}';
        let commonResp = RestResponse.fromJson(data);
        let resp = BorrowResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getBorrowHistory request test', () => {
        /**
         * getBorrowHistory
         * Get Borrow History
         * /api/v3/margin/borrow
         */
        let data =
            '{"currency": "BTC", "isIsolated": true, "symbol": "BTC-USDT", "orderNo": "example_string_default_value", "startTime": 123456, "endTime": 123456, "currentPage": 1, "pageSize": 50}';
        let req = GetBorrowHistoryReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getBorrowHistory response test', () => {
        /**
         * getBorrowHistory
         * Get Borrow History
         * /api/v3/margin/borrow
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "timestamp": 1729657580449,\n        "currentPage": 1,\n        "pageSize": 50,\n        "totalNum": 2,\n        "totalPage": 1,\n        "items": [\n            {\n                "orderNo": "67187162c0d6990007717b15",\n                "symbol": null,\n                "currency": "USDT",\n                "size": "10",\n                "actualSize": "10",\n                "status": "SUCCESS",\n                "createdTime": 1729655138000\n            },\n            {\n                "orderNo": "67187155b088e70007149585",\n                "symbol": null,\n                "currency": "USDT",\n                "size": "0.1",\n                "actualSize": "0",\n                "status": "FAILED",\n                "createdTime": 1729655125000\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetBorrowHistoryResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('repay request test', () => {
        /**
         * repay
         * Repay
         * /api/v3/margin/repay
         */
        let data = '{"currency": "USDT", "size": 10}';
        let req = RepayReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('repay response test', () => {
        /**
         * repay
         * Repay
         * /api/v3/margin/repay
         */
        let data =
            '{"code":"200000","data":{"timestamp":1729655606816,"orderNo":"671873361d5bd400075096ad","actualSize":"10"}}';
        let commonResp = RestResponse.fromJson(data);
        let resp = RepayResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getRepayHistory request test', () => {
        /**
         * getRepayHistory
         * Get Repay History
         * /api/v3/margin/repay
         */
        let data =
            '{"currency": "BTC", "isIsolated": true, "symbol": "BTC-USDT", "orderNo": "example_string_default_value", "startTime": 123456, "endTime": 123456, "currentPage": 1, "pageSize": 50}';
        let req = GetRepayHistoryReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getRepayHistory response test', () => {
        /**
         * getRepayHistory
         * Get Repay History
         * /api/v3/margin/repay
         */
        let data =
            '{"code":"200000","data":{"timestamp":1729663471891,"currentPage":1,"pageSize":50,"totalNum":1,"totalPage":1,"items":[{"orderNo":"671873361d5bd400075096ad","symbol":null,"currency":"USDT","size":"10","principal":"9.99986518","interest":"0.00013482","status":"SUCCESS","createdTime":1729655606000}]}}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetRepayHistoryResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getInterestHistory request test', () => {
        /**
         * getInterestHistory
         * Get Interest History.
         * /api/v3/margin/interest
         */
        let data =
            '{"currency": "BTC", "isIsolated": true, "symbol": "BTC-USDT", "startTime": 123456, "endTime": 123456, "currentPage": 1, "pageSize": 50}';
        let req = GetInterestHistoryReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getInterestHistory response test', () => {
        /**
         * getInterestHistory
         * Get Interest History.
         * /api/v3/margin/interest
         */
        let data =
            '{"code":"200000","data":{"timestamp":1729665170701,"currentPage":1,"pageSize":50,"totalNum":3,"totalPage":1,"items":[{"currency":"USDT","dayRatio":"0.000296","interestAmount":"0.00000001","createdTime":1729663213375},{"currency":"USDT","dayRatio":"0.000296","interestAmount":"0.00000001","createdTime":1729659618802},{"currency":"USDT","dayRatio":"0.000296","interestAmount":"0.00000001","createdTime":1729656028077}]}}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetInterestHistoryResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('modifyLeverage request test', () => {
        /**
         * modifyLeverage
         * Modify Leverage
         * /api/v3/position/update-user-leverage
         */
        let data = '{"leverage": "5"}';
        let req = ModifyLeverageReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('modifyLeverage response test', () => {
        /**
         * modifyLeverage
         * Modify Leverage
         * /api/v3/position/update-user-leverage
         */
        let data = '{"code":"200000","data":null}';
        let commonResp = RestResponse.fromJson(data);
        let resp = ModifyLeverageResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
});
