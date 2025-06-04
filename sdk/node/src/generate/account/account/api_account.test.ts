import { GetFuturesLedgerReq } from './model_get_futures_ledger_req';
import { GetCrossMarginAccountReq } from './model_get_cross_margin_account_req';
import { GetAccountInfoResp } from './model_get_account_info_resp';
import { GetIsolatedMarginAccountListV1Req } from './model_get_isolated_margin_account_list_v1_req';
import { GetFuturesAccountReq } from './model_get_futures_account_req';
import { GetCrossMarginAccountResp } from './model_get_cross_margin_account_resp';
import { GetSpotLedgerReq } from './model_get_spot_ledger_req';
import { GetSpotAccountListReq } from './model_get_spot_account_list_req';
import { GetSpotAccountDetailReq } from './model_get_spot_account_detail_req';
import { GetMarginHFLedgerResp } from './model_get_margin_hf_ledger_resp';
import { GetFuturesAccountResp } from './model_get_futures_account_resp';
import { GetIsolatedMarginAccountReq } from './model_get_isolated_margin_account_req';
import { GetIsolatedMarginAccountDetailV1Req } from './model_get_isolated_margin_account_detail_v1_req';
import { GetSpotHFLedgerResp } from './model_get_spot_hf_ledger_resp';
import { GetSpotLedgerResp } from './model_get_spot_ledger_resp';
import { GetIsolatedMarginAccountDetailV1Resp } from './model_get_isolated_margin_account_detail_v1_resp';
import { GetIsolatedMarginAccountResp } from './model_get_isolated_margin_account_resp';
import { GetSpotHFLedgerReq } from './model_get_spot_hf_ledger_req';
import { GetFuturesLedgerResp } from './model_get_futures_ledger_resp';
import { GetApikeyInfoResp } from './model_get_apikey_info_resp';
import { GetSpotAccountListResp } from './model_get_spot_account_list_resp';
import { GetMarginAccountDetailResp } from './model_get_margin_account_detail_resp';
import { GetIsolatedMarginAccountListV1Resp } from './model_get_isolated_margin_account_list_v1_resp';
import { GetMarginHFLedgerReq } from './model_get_margin_hf_ledger_req';
import { GetSpotAccountDetailResp } from './model_get_spot_account_detail_resp';
import { GetSpotAccountTypeResp } from './model_get_spot_account_type_resp';
import { RestResponse } from '@model/common';

describe('Auto Test', () => {
    test('getAccountInfo request test', () => {
        /**
         * getAccountInfo
         * Get Account Summary Info
         * /api/v2/user-info
         */
    });

    test('getAccountInfo response test', () => {
        /**
         * getAccountInfo
         * Get Account Summary Info
         * /api/v2/user-info
         */
    });
    test('getApikeyInfo request test', () => {
        /**
         * getApikeyInfo
         * Get Apikey Info
         * /api/v1/user/api-key
         */
    });

    test('getApikeyInfo response test', () => {
        /**
         * getApikeyInfo
         * Get Apikey Info
         * /api/v1/user/api-key
         */
    });
    test('getSpotAccountType request test', () => {
        /**
         * getSpotAccountType
         * Get Account Type - Spot
         * /api/v1/hf/accounts/opened
         */
    });

    test('getSpotAccountType response test', () => {
        /**
         * getSpotAccountType
         * Get Account Type - Spot
         * /api/v1/hf/accounts/opened
         */
    });
    test('getSpotAccountList request test', () => {
        /**
         * getSpotAccountList
         * Get Account List - Spot
         * /api/v1/accounts
         */
        let data = '{"currency": "USDT", "type": "main"}';
        let req = GetSpotAccountListReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getSpotAccountList response test', () => {
        /**
         * getSpotAccountList
         * Get Account List - Spot
         * /api/v1/accounts
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "id": "548674591753",\n            "currency": "USDT",\n            "type": "trade",\n            "balance": "26.66759503",\n            "available": "26.66759503",\n            "holds": "0"\n        },\n        {\n            "id": "63355cd156298d0001b66e61",\n            "currency": "USDT",\n            "type": "main",\n            "balance": "0.01",\n            "available": "0.01",\n            "holds": "0"\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetSpotAccountListResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getSpotAccountDetail request test', () => {
        /**
         * getSpotAccountDetail
         * Get Account Detail - Spot
         * /api/v1/accounts/{accountId}
         */
        let data = '{"accountId": "548674591753"}';
        let req = GetSpotAccountDetailReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getSpotAccountDetail response test', () => {
        /**
         * getSpotAccountDetail
         * Get Account Detail - Spot
         * /api/v1/accounts/{accountId}
         */
        let data =
            '{"code":"200000","data":{"currency":"USDT","balance":"26.66759503","available":"26.66759503","holds":"0"}}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetSpotAccountDetailResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getCrossMarginAccount request test', () => {
        /**
         * getCrossMarginAccount
         * Get Account - Cross Margin
         * /api/v3/margin/accounts
         */
        let data = '{"quoteCurrency": "USDT", "queryType": "MARGIN"}';
        let req = GetCrossMarginAccountReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getCrossMarginAccount response test', () => {
        /**
         * getCrossMarginAccount
         * Get Account - Cross Margin
         * /api/v3/margin/accounts
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "totalAssetOfQuoteCurrency": "40.8648372",\n        "totalLiabilityOfQuoteCurrency": "0",\n        "debtRatio": "0",\n        "status": "EFFECTIVE",\n        "accounts": [\n            {\n                "currency": "USDT",\n                "total": "38.68855864",\n                "available": "20.01916691",\n                "hold": "18.66939173",\n                "liability": "0",\n                "liabilityPrincipal": "0",\n                "liabilityInterest": "0",\n                "maxBorrowSize": "163",\n                "borrowEnabled": true,\n                "transferInEnabled": true\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetCrossMarginAccountResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getIsolatedMarginAccount request test', () => {
        /**
         * getIsolatedMarginAccount
         * Get Account - Isolated Margin
         * /api/v3/isolated/accounts
         */
        let data = '{"symbol": "BTC-USDT", "quoteCurrency": "USDT", "queryType": "ISOLATED"}';
        let req = GetIsolatedMarginAccountReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getIsolatedMarginAccount response test', () => {
        /**
         * getIsolatedMarginAccount
         * Get Account - Isolated Margin
         * /api/v3/isolated/accounts
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "totalAssetOfQuoteCurrency": "4.97047372",\n        "totalLiabilityOfQuoteCurrency": "0.00038891",\n        "timestamp": 1747303659773,\n        "assets": [\n            {\n                "symbol": "BTC-USDT",\n                "status": "EFFECTIVE",\n                "debtRatio": "0",\n                "baseAsset": {\n                    "currency": "BTC",\n                    "borrowEnabled": true,\n                    "transferInEnabled": true,\n                    "liability": "0",\n                    "liabilityPrincipal": "0",\n                    "liabilityInterest": "0",\n                    "total": "0",\n                    "available": "0",\n                    "hold": "0",\n                    "maxBorrowSize": "0"\n                },\n                "quoteAsset": {\n                    "currency": "USDT",\n                    "borrowEnabled": true,\n                    "transferInEnabled": true,\n                    "liability": "0.00038891",\n                    "liabilityPrincipal": "0.00038888",\n                    "liabilityInterest": "0.00000003",\n                    "total": "4.97047372",\n                    "available": "4.97047372",\n                    "hold": "0",\n                    "maxBorrowSize": "44"\n                }\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetIsolatedMarginAccountResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getFuturesAccount request test', () => {
        /**
         * getFuturesAccount
         * Get Account - Futures
         * /api/v1/account-overview
         */
        let data = '{"currency": "USDT"}';
        let req = GetFuturesAccountReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getFuturesAccount response test', () => {
        /**
         * getFuturesAccount
         * Get Account - Futures
         * /api/v1/account-overview
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "accountEquity": 394.439280806,\n        "unrealisedPNL": 20.15278,\n        "marginBalance": 371.394298816,\n        "positionMargin": 102.20664159,\n        "orderMargin": 10.06002012,\n        "frozenFunds": 0.0,\n        "availableBalance": 290.326799096,\n        "currency": "USDT",\n        "riskRatio": 0.0065289525,\n        "maxWithdrawAmount": 290.326419096\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetFuturesAccountResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getSpotLedger request test', () => {
        /**
         * getSpotLedger
         * Get Account Ledgers - Spot/Margin
         * /api/v1/accounts/ledgers
         */
        let data =
            '{"currency": "BTC", "direction": "in", "bizType": "TRANSFER", "startAt": 1728663338000, "endAt": 1728692138000, "currentPage": 1, "pageSize": 50}';
        let req = GetSpotLedgerReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getSpotLedger response test', () => {
        /**
         * getSpotLedger
         * Get Account Ledgers - Spot/Margin
         * /api/v1/accounts/ledgers
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 50,\n        "totalNum": 1,\n        "totalPage": 1,\n        "items": [\n            {\n                "id": "265329987780896",\n                "currency": "USDT",\n                "amount": "0.01",\n                "fee": "0",\n                "balance": "0",\n                "accountType": "TRADE",\n                "bizType": "SUB_TRANSFER",\n                "direction": "out",\n                "createdAt": 1728658481484,\n                "context": ""\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetSpotLedgerResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getSpotHFLedger request test', () => {
        /**
         * getSpotHFLedger
         * Get Account Ledgers - Trade_hf
         * /api/v1/hf/accounts/ledgers
         */
        let data =
            '{"currency": "BTC", "direction": "in", "bizType": "TRANSFER", "lastId": 254062248624417, "limit": 100, "startAt": 1728663338000, "endAt": 1728692138000}';
        let req = GetSpotHFLedgerReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getSpotHFLedger response test', () => {
        /**
         * getSpotHFLedger
         * Get Account Ledgers - Trade_hf
         * /api/v1/hf/accounts/ledgers
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "id": "254062248624417",\n            "currency": "USDT",\n            "amount": "1.59760080",\n            "fee": "0.00159920",\n            "tax": "0",\n            "balance": "26.73759503",\n            "accountType": "TRADE_HF",\n            "bizType": "TRADE_EXCHANGE",\n            "direction": "in",\n            "createdAt": "1728443957539",\n            "context": "{\\"symbol\\":\\"KCS-USDT\\",\\"orderId\\":\\"6705f6350dc7210007d6a36d\\",\\"tradeId\\":\\"10046097631627265\\"}"\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetSpotHFLedgerResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getMarginHFLedger request test', () => {
        /**
         * getMarginHFLedger
         * Get Account Ledgers - Margin_hf
         * /api/v3/hf/margin/account/ledgers
         */
        let data =
            '{"currency": "BTC", "direction": "in", "bizType": "TRANSFER", "lastId": 254062248624417, "limit": 100, "startAt": 1728663338000, "endAt": 1728692138000}';
        let req = GetMarginHFLedgerReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getMarginHFLedger response test', () => {
        /**
         * getMarginHFLedger
         * Get Account Ledgers - Margin_hf
         * /api/v3/hf/margin/account/ledgers
         */
        let data =
            '{"code":"200000","data":[{"id":1949641706720,"currency":"USDT","amount":"0.01000000","fee":"0.00000000","balance":"0.01000000","accountType":"MARGIN_V2","bizType":"TRANSFER","direction":"in","createdAt":1728664091208,"context":"{}","tax":"0.00000000"}]}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetMarginHFLedgerResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getFuturesLedger request test', () => {
        /**
         * getFuturesLedger
         * Get Account Ledgers - Futures
         * /api/v1/transaction-history
         */
        let data =
            '{"currency": "XBT", "type": "Transferin", "offset": 254062248624417, "forward": true, "maxCount": 50, "startAt": 1728663338000, "endAt": 1728692138000}';
        let req = GetFuturesLedgerReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getFuturesLedger response test', () => {
        /**
         * getFuturesLedger
         * Get Account Ledgers - Futures
         * /api/v1/transaction-history
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "dataList": [\n            {\n                "time": 1728665747000,\n                "type": "TransferIn",\n                "amount": 0.01,\n                "fee": 0.0,\n                "accountEquity": 14.02924938,\n                "status": "Completed",\n                "remark": "Transferred from High-Frequency Trading Account",\n                "offset": 51360793,\n                "currency": "USDT"\n            },\n            {\n                "time": 1728648000000,\n                "type": "RealisedPNL",\n                "amount": 0.00630042,\n                "fee": 0.0,\n                "accountEquity": 20.0,\n                "status": "Completed",\n                "remark": "XBTUSDTM",\n                "offset": 51352430,\n                "currency": "USDT"\n            }\n        ],\n        "hasMore": false\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetFuturesLedgerResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getMarginAccountDetail request test', () => {
        /**
         * getMarginAccountDetail
         * Get Account Detail - Margin
         * /api/v1/margin/account
         */
    });

    test('getMarginAccountDetail response test', () => {
        /**
         * getMarginAccountDetail
         * Get Account Detail - Margin
         * /api/v1/margin/account
         */
    });
    test('getIsolatedMarginAccountListV1 request test', () => {
        /**
         * getIsolatedMarginAccountListV1
         * Get Account List - Isolated Margin - V1
         * /api/v1/isolated/accounts
         */
        let data = '{"balanceCurrency": "USDT"}';
        let req = GetIsolatedMarginAccountListV1Req.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getIsolatedMarginAccountListV1 response test', () => {
        /**
         * getIsolatedMarginAccountListV1
         * Get Account List - Isolated Margin - V1
         * /api/v1/isolated/accounts
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "totalConversionBalance": "0.01",\n        "liabilityConversionBalance": "0",\n        "assets": [\n            {\n                "symbol": "BTC-USDT",\n                "status": "CLEAR",\n                "debtRatio": "0",\n                "baseAsset": {\n                    "currency": "BTC",\n                    "totalBalance": "0",\n                    "holdBalance": "0",\n                    "availableBalance": "0",\n                    "liability": "0",\n                    "interest": "0",\n                    "borrowableAmount": "0"\n                },\n                "quoteAsset": {\n                    "currency": "USDT",\n                    "totalBalance": "0.01",\n                    "holdBalance": "0",\n                    "availableBalance": "0.01",\n                    "liability": "0",\n                    "interest": "0",\n                    "borrowableAmount": "0"\n                }\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetIsolatedMarginAccountListV1Resp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getIsolatedMarginAccountDetailV1 request test', () => {
        /**
         * getIsolatedMarginAccountDetailV1
         * Get Account Detail - Isolated Margin - V1
         * /api/v1/isolated/account/{symbol}
         */
        let data = '{"symbol": "example_string_default_value"}';
        let req = GetIsolatedMarginAccountDetailV1Req.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getIsolatedMarginAccountDetailV1 response test', () => {
        /**
         * getIsolatedMarginAccountDetailV1
         * Get Account Detail - Isolated Margin - V1
         * /api/v1/isolated/account/{symbol}
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "symbol": "BTC-USDT",\n        "status": "CLEAR",\n        "debtRatio": "0",\n        "baseAsset": {\n            "currency": "BTC",\n            "totalBalance": "0",\n            "holdBalance": "0",\n            "availableBalance": "0",\n            "liability": "0",\n            "interest": "0",\n            "borrowableAmount": "0"\n        },\n        "quoteAsset": {\n            "currency": "USDT",\n            "totalBalance": "0.01",\n            "holdBalance": "0",\n            "availableBalance": "0.01",\n            "liability": "0",\n            "interest": "0",\n            "borrowableAmount": "0"\n        }\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetIsolatedMarginAccountDetailV1Resp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
});
