import { AddDepositAddressV3Resp } from './model_add_deposit_address_v3_resp';
import { GetDepositAddressV1Resp } from './model_get_deposit_address_v1_resp';
import { GetDepositHistoryOldResp } from './model_get_deposit_history_old_resp';
import { AddDepositAddressV3Req } from './model_add_deposit_address_v3_req';
import { AddDepositAddressV1Req } from './model_add_deposit_address_v1_req';
import { GetDepositHistoryOldReq } from './model_get_deposit_history_old_req';
import { GetDepositAddressV2Req } from './model_get_deposit_address_v2_req';
import { GetDepositHistoryResp } from './model_get_deposit_history_resp';
import { GetDepositHistoryReq } from './model_get_deposit_history_req';
import { GetDepositAddressV3Resp } from './model_get_deposit_address_v3_resp';
import { GetDepositAddressV3Req } from './model_get_deposit_address_v3_req';
import { AddDepositAddressV1Resp } from './model_add_deposit_address_v1_resp';
import { GetDepositAddressV1Req } from './model_get_deposit_address_v1_req';
import { GetDepositAddressV2Resp } from './model_get_deposit_address_v2_resp';
import { RestResponse } from '@model/common';

describe('Auto Test', () => {
    test('addDepositAddressV3 request test', () => {
        /**
         * addDepositAddressV3
         * Add Deposit Address (V3)
         * /api/v3/deposit-address/create
         */
        let data = '{"currency": "TON", "chain": "ton", "to": "trade"}';
        let req = AddDepositAddressV3Req.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('addDepositAddressV3 response test', () => {
        /**
         * addDepositAddressV3
         * Add Deposit Address (V3)
         * /api/v3/deposit-address/create
         */
        let data =
            '{"code":"200000","data":{"address":"EQCA1BI4QRZ8qYmskSRDzJmkucGodYRTZCf_b9hckjla6dZl","memo":"2090821203","chainId":"ton","to":"TRADE","expirationDate":0,"currency":"TON","chainName":"TON"}}';
        let commonResp = RestResponse.fromJson(data);
        let resp = AddDepositAddressV3Resp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getDepositAddressV3 request test', () => {
        /**
         * getDepositAddressV3
         * Get Deposit Address (V3)
         * /api/v3/deposit-addresses
         */
        let data = '{"currency": "BTC", "amount": "example_string_default_value", "chain": "eth"}';
        let req = GetDepositAddressV3Req.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getDepositAddressV3 response test', () => {
        /**
         * getDepositAddressV3
         * Get Deposit Address (V3)
         * /api/v3/deposit-addresses
         */
        let data =
            '{"code":"200000","data":[{"address":"TSv3L1fS7yA3SxzKD8c1qdX4nLP6rqNxYz","memo":"","chainId":"trx","to":"TRADE","expirationDate":0,"currency":"USDT","contractAddress":"TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t","chainName":"TRC20"},{"address":"0x551e823a3b36865e8c5dc6e6ac6cc0b00d98533e","memo":"","chainId":"kcc","to":"TRADE","expirationDate":0,"currency":"USDT","contractAddress":"0x0039f574ee5cc39bdd162e9a88e3eb1f111baf48","chainName":"KCC"},{"address":"EQCA1BI4QRZ8qYmskSRDzJmkucGodYRTZCf_b9hckjla6dZl","memo":"2085202643","chainId":"ton","to":"TRADE","expirationDate":0,"currency":"USDT","contractAddress":"EQCxE6mUtQJKFnGfaROTKOt1lZbDiiX1kCixRv7Nw2Id_sDs","chainName":"TON"},{"address":"0x0a2586d5a901c8e7e68f6b0dc83bfd8bd8600ff5","memo":"","chainId":"eth","to":"MAIN","expirationDate":0,"currency":"USDT","contractAddress":"0xdac17f958d2ee523a2206206994597c13d831ec7","chainName":"ERC20"}]}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetDepositAddressV3Resp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getDepositHistory request test', () => {
        /**
         * getDepositHistory
         * Get Deposit History
         * /api/v1/deposits
         */
        let data =
            '{"currency": "BTC", "status": "SUCCESS", "startAt": 1728663338000, "endAt": 1728692138000, "currentPage": 1, "pageSize": 50}';
        let req = GetDepositHistoryReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getDepositHistory response test', () => {
        /**
         * getDepositHistory
         * Get Deposit History
         * /api/v1/deposits
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 50,\n        "totalNum": 5,\n        "totalPage": 1,\n        "items": [\n            {\n                "currency": "USDT",\n                "chain": "",\n                "status": "SUCCESS",\n                "address": "a435*****@gmail.com",\n                "memo": "",\n                "isInner": true,\n                "amount": "1.00000000",\n                "fee": "0.00000000",\n                "walletTxId": null,\n                "createdAt": 1728555875000,\n                "updatedAt": 1728555875000,\n                "remark": "",\n                "arrears": false\n            },\n            {\n                "currency": "USDT",\n                "chain": "trx",\n                "status": "SUCCESS",\n                "address": "TSv3L1fS7******X4nLP6rqNxYz",\n                "memo": "",\n                "isInner": true,\n                "amount": "6.00000000",\n                "fee": "0.00000000",\n                "walletTxId": null,\n                "createdAt": 1721730920000,\n                "updatedAt": 1721730920000,\n                "remark": "",\n                "arrears": false\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetDepositHistoryResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getDepositAddressV2 request test', () => {
        /**
         * getDepositAddressV2
         * Get Deposit Addresses (V2)
         * /api/v2/deposit-addresses
         */
        let data = '{"currency": "BTC", "chain": "eth"}';
        let req = GetDepositAddressV2Req.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getDepositAddressV2 response test', () => {
        /**
         * getDepositAddressV2
         * Get Deposit Addresses (V2)
         * /api/v2/deposit-addresses
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "address": "0x02028456*****87ede7a73d7c",\n            "memo": "",\n            "chain": "ERC20",\n            "chainId": "eth",\n            "to": "MAIN",\n            "currency": "ETH",\n            "contractAddress": ""\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetDepositAddressV2Resp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getDepositAddressV1 request test', () => {
        /**
         * getDepositAddressV1
         * Get Deposit Addresses - V1
         * /api/v1/deposit-addresses
         */
        let data = '{"currency": "BTC", "chain": "eth"}';
        let req = GetDepositAddressV1Req.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getDepositAddressV1 response test', () => {
        /**
         * getDepositAddressV1
         * Get Deposit Addresses - V1
         * /api/v1/deposit-addresses
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "address": "0xea220bf61c3c2b0adc2cfa29fec3d2677745a379",\n        "memo": "",\n        "chain": "ERC20",\n        "chainId": "eth",\n        "to": "MAIN",\n        "currency": "USDT"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetDepositAddressV1Resp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getDepositHistoryOld request test', () => {
        /**
         * getDepositHistoryOld
         * Get Deposit History - Old
         * /api/v1/hist-deposits
         */
        let data =
            '{"currency": "BTC", "status": "SUCCESS", "startAt": 1728663338000, "endAt": 1728692138000}';
        let req = GetDepositHistoryOldReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getDepositHistoryOld response test', () => {
        /**
         * getDepositHistoryOld
         * Get Deposit History - Old
         * /api/v1/hist-deposits
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 50,\n        "totalNum": 0,\n        "totalPage": 0,\n        "items": [\n            {\n                "currency": "BTC",\n                "createAt": 1528536998,\n                "amount": "0.03266638",\n                "walletTxId": "55c643bc2c68d6f17266383ac1be9e454038864b929ae7cee0bc408cc5c869e8@12ffGWmMMD1zA1WbFm7Ho3JZ1w6NYXjpFk@234",\n                "isInner": false,\n                "status": "SUCCESS"\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetDepositHistoryOldResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('addDepositAddressV1 request test', () => {
        /**
         * addDepositAddressV1
         * Add Deposit Address - V1
         * /api/v1/deposit-addresses
         */
        let data = '{"currency": "ETH", "chain": "eth", "to": "MAIN"}';
        let req = AddDepositAddressV1Req.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('addDepositAddressV1 response test', () => {
        /**
         * addDepositAddressV1
         * Add Deposit Address - V1
         * /api/v1/deposit-addresses
         */
        let data =
            '{"code":"200000","data":{"address":"0x02028456f38e78609904e8a002c787ede7a73d7c","memo":null,"chain":"ERC20","chainId":"eth","to":"MAIN","currency":"ETH"}}';
        let commonResp = RestResponse.fromJson(data);
        let resp = AddDepositAddressV1Resp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
});
