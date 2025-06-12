import { WithdrawalV1Req } from './model_withdrawal_v1_req';
import { GetWithdrawalHistoryByIdResp } from './model_get_withdrawal_history_by_id_resp';
import { CancelWithdrawalResp } from './model_cancel_withdrawal_resp';
import { WithdrawalV3Req } from './model_withdrawal_v3_req';
import { WithdrawalV3Resp } from './model_withdrawal_v3_resp';
import { GetWithdrawalQuotasReq } from './model_get_withdrawal_quotas_req';
import { WithdrawalV1Resp } from './model_withdrawal_v1_resp';
import { GetWithdrawalHistoryOldResp } from './model_get_withdrawal_history_old_resp';
import { GetWithdrawalQuotasResp } from './model_get_withdrawal_quotas_resp';
import { GetWithdrawalHistoryOldReq } from './model_get_withdrawal_history_old_req';
import { CancelWithdrawalReq } from './model_cancel_withdrawal_req';
import { GetWithdrawalHistoryByIdReq } from './model_get_withdrawal_history_by_id_req';
import { GetWithdrawalHistoryReq } from './model_get_withdrawal_history_req';
import { GetWithdrawalHistoryResp } from './model_get_withdrawal_history_resp';
import { RestResponse } from '@model/common';

describe('Auto Test', () => {
    test('getWithdrawalQuotas request test', () => {
        /**
         * getWithdrawalQuotas
         * Get Withdrawal Quotas
         * /api/v1/withdrawals/quotas
         */
        let data = '{"currency": "BTC", "chain": "eth"}';
        let req = GetWithdrawalQuotasReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getWithdrawalQuotas response test', () => {
        /**
         * getWithdrawalQuotas
         * Get Withdrawal Quotas
         * /api/v1/withdrawals/quotas
         */
        let data =
            '{"code":"200000","data":{"currency":"BTC","limitBTCAmount":"15.79590095","usedBTCAmount":"0.00000000","quotaCurrency":"USDT","limitQuotaCurrencyAmount":"999999.00000000","usedQuotaCurrencyAmount":"0","remainAmount":"15.79590095","availableAmount":"0","withdrawMinFee":"0.0005","innerWithdrawMinFee":"0","withdrawMinSize":"0.001","isWithdrawEnabled":true,"precision":8,"chain":"BTC","reason":null,"lockedAmount":"0"}}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetWithdrawalQuotasResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('withdrawalV3 request test', () => {
        /**
         * withdrawalV3
         * Withdraw (V3)
         * /api/v3/withdrawals
         */
        let data =
            '{"currency": "USDT", "toAddress": "TKFRQXSDcY****GmLrjJggwX8", "amount": "3", "withdrawType": "ADDRESS", "chain": "trx", "isInner": true, "remark": "this is Remark"}';
        let req = WithdrawalV3Req.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('withdrawalV3 response test', () => {
        /**
         * withdrawalV3
         * Withdraw (V3)
         * /api/v3/withdrawals
         */
        let data = '{"code":"200000","data":{"withdrawalId":"670deec84d64da0007d7c946"}}';
        let commonResp = RestResponse.fromJson(data);
        let resp = WithdrawalV3Resp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('cancelWithdrawal request test', () => {
        /**
         * cancelWithdrawal
         * Cancel Withdrawal
         * /api/v1/withdrawals/{withdrawalId}
         */
        let data = '{"withdrawalId": "670b891f7e0f440007730692"}';
        let req = CancelWithdrawalReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('cancelWithdrawal response test', () => {
        /**
         * cancelWithdrawal
         * Cancel Withdrawal
         * /api/v1/withdrawals/{withdrawalId}
         */
        let data = '{"code":"200000","data":null}';
        let commonResp = RestResponse.fromJson(data);
        let resp = CancelWithdrawalResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getWithdrawalHistory request test', () => {
        /**
         * getWithdrawalHistory
         * Get Withdrawal History
         * /api/v1/withdrawals
         */
        let data =
            '{"currency": "BTC", "status": "SUCCESS", "startAt": 1728663338000, "endAt": 1728692138000, "currentPage": 1, "pageSize": 50}';
        let req = GetWithdrawalHistoryReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getWithdrawalHistory response test', () => {
        /**
         * getWithdrawalHistory
         * Get Withdrawal History
         * /api/v1/withdrawals
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 50,\n        "totalNum": 5,\n        "totalPage": 1,\n        "items": [\n            {\n                "currency": "USDT",\n                "chain": "",\n                "status": "SUCCESS",\n                "address": "a435*****@gmail.com",\n                "memo": "",\n                "isInner": true,\n                "amount": "1.00000000",\n                "fee": "0.00000000",\n                "walletTxId": null,\n                "createdAt": 1728555875000,\n                "updatedAt": 1728555875000,\n                "remark": "",\n                "arrears": false\n            },\n            {\n                "currency": "USDT",\n                "chain": "trx",\n                "status": "SUCCESS",\n                "address": "TSv3L1fS7******X4nLP6rqNxYz",\n                "memo": "",\n                "isInner": true,\n                "amount": "6.00000000",\n                "fee": "0.00000000",\n                "walletTxId": null,\n                "createdAt": 1721730920000,\n                "updatedAt": 1721730920000,\n                "remark": "",\n                "arrears": false\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetWithdrawalHistoryResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getWithdrawalHistoryById request test', () => {
        /**
         * getWithdrawalHistoryById
         * Get Withdrawal History By ID
         * /api/v1/withdrawals/{withdrawalId}
         */
        let data = '{"withdrawalId": "67e6515f7960ba0007b42025"}';
        let req = GetWithdrawalHistoryByIdReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getWithdrawalHistoryById response test', () => {
        /**
         * getWithdrawalHistoryById
         * Get Withdrawal History By ID
         * /api/v1/withdrawals/{withdrawalId}
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "id": "67e6515f7960ba0007b42025",\n        "uid": 165111215,\n        "currency": "USDT",\n        "chainId": "trx",\n        "chainName": "TRC20",\n        "currencyName": "USDT",\n        "status": "SUCCESS",\n        "failureReason": "",\n        "failureReasonMsg": null,\n        "address": "TKFRQXSDcY4kd3QLzw7uK16GmLrjJggwX8",\n        "memo": "",\n        "isInner": true,\n        "amount": "3.00000000",\n        "fee": "0.00000000",\n        "walletTxId": null,\n        "addressRemark": null,\n        "remark": "this is Remark",\n        "createdAt": 1743147359000,\n        "cancelType": "NON_CANCELABLE",\n        "taxes": null,\n        "taxDescription": null,\n        "returnStatus": "NOT_RETURN",\n        "returnAmount": null,\n        "returnCurrency": "KCS"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetWithdrawalHistoryByIdResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getWithdrawalHistoryOld request test', () => {
        /**
         * getWithdrawalHistoryOld
         * Get Withdrawal History - Old
         * /api/v1/hist-withdrawals
         */
        let data =
            '{"currency": "BTC", "status": "SUCCESS", "startAt": 1728663338000, "endAt": 1728692138000, "currentPage": 1, "pageSize": 50}';
        let req = GetWithdrawalHistoryOldReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getWithdrawalHistoryOld response test', () => {
        /**
         * getWithdrawalHistoryOld
         * Get Withdrawal History - Old
         * /api/v1/hist-withdrawals
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 50,\n        "totalNum": 1,\n        "totalPage": 1,\n        "items": [\n            {\n                "currency": "BTC",\n                "createAt": 1526723468,\n                "amount": "0.534",\n                "address": "33xW37ZSW4tQvg443Pc7NLCAs167Yc2XUV",\n                "walletTxId": "aeacea864c020acf58e51606169240e96774838dcd4f7ce48acf38e3651323f4",\n                "isInner": false,\n                "status": "SUCCESS"\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetWithdrawalHistoryOldResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('withdrawalV1 request test', () => {
        /**
         * withdrawalV1
         * Withdraw - V1
         * /api/v1/withdrawals
         */
        let data =
            '{"currency": "USDT", "address": "TKFRQXSDc****16GmLrjJggwX8", "amount": 3, "chain": "trx", "isInner": true}';
        let req = WithdrawalV1Req.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('withdrawalV1 response test', () => {
        /**
         * withdrawalV1
         * Withdraw - V1
         * /api/v1/withdrawals
         */
        let data = '{"code":"200000","data":{"withdrawalId":"670a973cf07b3800070e216c"}}';
        let commonResp = RestResponse.fromJson(data);
        let resp = WithdrawalV1Resp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
});
