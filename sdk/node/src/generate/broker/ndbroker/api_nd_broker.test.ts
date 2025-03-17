import { GetSubAccountResp } from './model_get_sub_account_resp';
import { GetRebaseResp } from './model_get_rebase_resp';
import { AddSubAccountApiResp } from './model_add_sub_account_api_resp';
import { AddSubAccountApiReq } from './model_add_sub_account_api_req';
import { GetSubAccountAPIReq } from './model_get_sub_account_api_req';
import { GetRebaseReq } from './model_get_rebase_req';
import { DeleteSubAccountAPIReq } from './model_delete_sub_account_api_req';
import { GetWithdrawDetailReq } from './model_get_withdraw_detail_req';
import { GetDepositDetailResp } from './model_get_deposit_detail_resp';
import { GetDepositDetailReq } from './model_get_deposit_detail_req';
import { TransferResp } from './model_transfer_resp';
import { GetWithdrawDetailResp } from './model_get_withdraw_detail_resp';
import { DeleteSubAccountAPIResp } from './model_delete_sub_account_api_resp';
import { GetTransferHistoryResp } from './model_get_transfer_history_resp';
import { GetDepositListResp } from './model_get_deposit_list_resp';
import { ModifySubAccountApiResp } from './model_modify_sub_account_api_resp';
import { AddSubAccountReq } from './model_add_sub_account_req';
import { GetSubAccountReq } from './model_get_sub_account_req';
import { GetSubAccountAPIResp } from './model_get_sub_account_api_resp';
import { GetBrokerInfoResp } from './model_get_broker_info_resp';
import { GetTransferHistoryReq } from './model_get_transfer_history_req';
import { AddSubAccountResp } from './model_add_sub_account_resp';
import { GetDepositListReq } from './model_get_deposit_list_req';
import { GetBrokerInfoReq } from './model_get_broker_info_req';
import { ModifySubAccountApiReq } from './model_modify_sub_account_api_req';
import { TransferReq } from './model_transfer_req';
import { RestResponse } from '@model/common';

describe('Auto Test', () => {
    test('getBrokerInfo request test', () => {
        /**
         * getBrokerInfo
         * Get Broker Info
         * /api/v1/broker/nd/info
         */
        let data = '{"begin": "20240510", "end": "20241010", "tradeType": "1"}';
        let req = GetBrokerInfoReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getBrokerInfo response test', () => {
        /**
         * getBrokerInfo
         * Get Broker Info
         * /api/v1/broker/nd/info
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "accountSize": 0,\n        "maxAccountSize": null,\n        "level": 0\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetBrokerInfoResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('addSubAccount request test', () => {
        /**
         * addSubAccount
         * Add sub-account
         * /api/v1/broker/nd/account
         */
        let data = '{"accountName": "Account1"}';
        let req = AddSubAccountReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('addSubAccount response test', () => {
        /**
         * addSubAccount
         * Add sub-account
         * /api/v1/broker/nd/account
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "accountName": "Account15",\n        "uid": "226383154",\n        "createdAt": 1729819381908,\n        "level": 0\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = AddSubAccountResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getSubAccount request test', () => {
        /**
         * getSubAccount
         * Get sub-account
         * /api/v1/broker/nd/account
         */
        let data = '{"uid": "226383154", "currentPage": 1, "pageSize": 20}';
        let req = GetSubAccountReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getSubAccount response test', () => {
        /**
         * getSubAccount
         * Get sub-account
         * /api/v1/broker/nd/account
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 20,\n        "totalNum": 1,\n        "totalPage": 1,\n        "items": [\n            {\n                "accountName": "Account15",\n                "uid": "226383154",\n                "createdAt": 1729819382000,\n                "level": 0\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetSubAccountResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('addSubAccountApi request test', () => {
        /**
         * addSubAccountApi
         * Add sub-account API
         * /api/v1/broker/nd/account/apikey
         */
        let data =
            '{"uid": "226383154", "passphrase": "11223344", "ipWhitelist": ["127.0.0.1", "123.123.123.123"], "permissions": ["general", "spot"], "label": "This is remarks"}';
        let req = AddSubAccountApiReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('addSubAccountApi response test', () => {
        /**
         * addSubAccountApi
         * Add sub-account API
         * /api/v1/broker/nd/account/apikey
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "uid": "226383154",\n        "label": "This is remarks",\n        "apiKey": "671afb36cee20f00015cfaf1",\n        "secretKey": "d694df2******5bae05b96",\n        "apiVersion": 3,\n        "permissions": [\n            "General",\n            "Spot"\n        ],\n        "ipWhitelist": [\n            "127.0.0.1",\n            "123.123.123.123"\n        ],\n        "createdAt": 1729821494000\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = AddSubAccountApiResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getSubAccountAPI request test', () => {
        /**
         * getSubAccountAPI
         * Get sub-account API
         * /api/v1/broker/nd/account/apikey
         */
        let data = '{"uid": "226383154", "apiKey": "671afb36cee20f00015cfaf1"}';
        let req = GetSubAccountAPIReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getSubAccountAPI response test', () => {
        /**
         * getSubAccountAPI
         * Get sub-account API
         * /api/v1/broker/nd/account/apikey
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "uid": "226383154",\n            "label": "This is remarks",\n            "apiKey": "671afb36cee20f00015cfaf1",\n            "apiVersion": 3,\n            "permissions": [\n                "General",\n                "Spot"\n            ],\n            "ipWhitelist": [\n                "127.**.1",\n                "203.**.154"\n            ],\n            "createdAt": 1729821494000\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetSubAccountAPIResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('modifySubAccountApi request test', () => {
        /**
         * modifySubAccountApi
         * Modify sub-account API
         * /api/v1/broker/nd/account/update-apikey
         */
        let data =
            '{"uid": "226383154", "apiKey": "671afb36cee20f00015cfaf1", "ipWhitelist": ["127.0.0.1", "123.123.123.123"], "permissions": ["general", "spot"], "label": "This is remarks"}';
        let req = ModifySubAccountApiReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('modifySubAccountApi response test', () => {
        /**
         * modifySubAccountApi
         * Modify sub-account API
         * /api/v1/broker/nd/account/update-apikey
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "uid": "226383154",\n        "label": "This is remarks",\n        "apiKey": "671afb36cee20f00015cfaf1",\n        "apiVersion": 3,\n        "permissions": [\n            "General",\n            "Spot"\n        ],\n        "ipWhitelist": [\n            "127.**.1",\n            "123.**.123"\n        ],\n        "createdAt": 1729821494000\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = ModifySubAccountApiResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('deleteSubAccountAPI request test', () => {
        /**
         * deleteSubAccountAPI
         * Delete sub-account API
         * /api/v1/broker/nd/account/apikey
         */
        let data = '{"uid": "226383154", "apiKey": "671afb36cee20f00015cfaf1"}';
        let req = DeleteSubAccountAPIReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('deleteSubAccountAPI response test', () => {
        /**
         * deleteSubAccountAPI
         * Delete sub-account API
         * /api/v1/broker/nd/account/apikey
         */
        let data = '{\n    "code": "200000",\n    "data": true\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = DeleteSubAccountAPIResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('transfer request test', () => {
        /**
         * transfer
         * Transfer
         * /api/v1/broker/nd/transfer
         */
        let data =
            '{"currency": "USDT", "amount": "1", "clientOid": "e6c24d23-6bc2-401b-bf9e-55e2daddfbc1", "direction": "OUT", "accountType": "MAIN", "specialUid": "226383154", "specialAccountType": "MAIN"}';
        let req = TransferReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('transfer response test', () => {
        /**
         * transfer
         * Transfer
         * /api/v1/broker/nd/transfer
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "orderId": "671b4600c1e3dd000726866d"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = TransferResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getTransferHistory request test', () => {
        /**
         * getTransferHistory
         * Get Transfer History
         * /api/v3/broker/nd/transfer/detail
         */
        let data = '{"orderId": "671b4600c1e3dd000726866d"}';
        let req = GetTransferHistoryReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getTransferHistory response test', () => {
        /**
         * getTransferHistory
         * Get Transfer History
         * /api/v3/broker/nd/transfer/detail
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "orderId": "671b4600c1e3dd000726866d",\n        "currency": "USDT",\n        "amount": "1",\n        "fromUid": 165111215,\n        "fromAccountType": "MAIN",\n        "fromAccountTag": "DEFAULT",\n        "toUid": 226383154,\n        "toAccountType": "MAIN",\n        "toAccountTag": "DEFAULT",\n        "status": "SUCCESS",\n        "reason": null,\n        "createdAt": 1729840640000\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetTransferHistoryResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getDepositList request test', () => {
        /**
         * getDepositList
         * Get Deposit List
         * /api/v1/asset/ndbroker/deposit/list
         */
        let data =
            '{"currency": "USDT", "status": "SUCCESS", "hash": "example_string_default_value", "startTimestamp": 123456, "endTimestamp": 123456, "limit": 100}';
        let req = GetDepositListReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getDepositList response test', () => {
        /**
         * getDepositList
         * Get Deposit List
         * /api/v1/asset/ndbroker/deposit/list
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "uid": 165111215,\n            "hash": "6724e363a492800007ec602b",\n            "address": "xxxxxxx@gmail.com",\n            "memo": "",\n            "amount": "3.0",\n            "fee": "0.0",\n            "currency": "USDT",\n            "isInner": true,\n            "walletTxId": "bbbbbbbbb",\n            "status": "SUCCESS",\n            "chain": "",\n            "remark": "",\n            "createdAt": 1730470760000,\n            "updatedAt": 1730470760000\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetDepositListResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getDepositDetail request test', () => {
        /**
         * getDepositDetail
         * Get Deposit Detail
         * /api/v3/broker/nd/deposit/detail
         */
        let data = '{"currency": "USDT", "hash": "30bb0e0b***4156c5188"}';
        let req = GetDepositDetailReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getDepositDetail response test', () => {
        /**
         * getDepositDetail
         * Get Deposit Detail
         * /api/v3/broker/nd/deposit/detail
         */
        let data =
            '{\n  "data" : {\n    "chain" : "trx",\n    "hash" : "30bb0e0b***4156c5188",\n    "walletTxId" : "30bb0***610d1030f",\n    "uid" : 201496341,\n    "updatedAt" : 1713429174000,\n    "amount" : "8.5",\n    "memo" : "",\n    "fee" : "0.0",\n    "address" : "THLPzUrbd1o***vP7d",\n    "remark" : "Deposit",\n    "isInner" : false,\n    "currency" : "USDT",\n    "status" : "SUCCESS",\n    "createdAt" : 1713429173000\n  },\n  "code" : "200000"\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetDepositDetailResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getWithdrawDetail request test', () => {
        /**
         * getWithdrawDetail
         * Get Withdraw Detail
         * /api/v3/broker/nd/withdraw/detail
         */
        let data = '{"withdrawalId": "66617a2***3c9a"}';
        let req = GetWithdrawDetailReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getWithdrawDetail response test', () => {
        /**
         * getWithdrawDetail
         * Get Withdraw Detail
         * /api/v3/broker/nd/withdraw/detail
         */
        let data =
            '{\n    "data": {\n        "id": "66617a2***3c9a",\n        "chain": "ton",\n        "walletTxId": "AJ***eRI=",\n        "uid": 157267400,\n        "amount": "1.00000000",\n        "memo": "7025734",\n        "fee": "0.00000000",\n        "address": "EQDn***dKbGzr",\n        "remark": "",\n        "isInner": false,\n        "currency": "USDT",\n        "status": "SUCCESS",\n        "createdAt": 1717664288000,\n        "updatedAt": 1717664375000\n    },\n    "code": "200000"\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetWithdrawDetailResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getRebase request test', () => {
        /**
         * getRebase
         * Get Broker Rebate
         * /api/v1/broker/nd/rebase/download
         */
        let data = '{"begin": "20240610", "end": "20241010", "tradeType": "1"}';
        let req = GetRebaseReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getRebase response test', () => {
        /**
         * getRebase
         * Get Broker Rebate
         * /api/v1/broker/nd/rebase/download
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "url": "https://kc-v2-promotion.s3.ap-northeast-1.amazonaws.com/broker/671aec522593f600019766d0_file.csv?X-Amz-Security-Token=IQo*********2cd90f14efb"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetRebaseResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
});
