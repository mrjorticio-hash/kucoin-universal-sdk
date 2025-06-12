import { GetCrossMarginRiskLimitReq } from './model_get_cross_margin_risk_limit_req';
import { RemoveIsolatedMarginReq } from './model_remove_isolated_margin_req';
import { AddIsolatedMarginReq } from './model_add_isolated_margin_req';
import { ModifyAutoDepositStatusReq } from './model_modify_auto_deposit_status_req';
import { GetMaxOpenSizeReq } from './model_get_max_open_size_req';
import { GetMaxWithdrawMarginResp } from './model_get_max_withdraw_margin_resp';
import { AddIsolatedMarginResp } from './model_add_isolated_margin_resp';
import { GetCrossMarginLeverageResp } from './model_get_cross_margin_leverage_resp';
import { GetMarginModeReq } from './model_get_margin_mode_req';
import { SwitchMarginModeResp } from './model_switch_margin_mode_resp';
import { GetPositionsHistoryResp } from './model_get_positions_history_resp';
import { GetCrossMarginRiskLimitResp } from './model_get_cross_margin_risk_limit_resp';
import { GetPositionListReq } from './model_get_position_list_req';
import { GetIsolatedMarginRiskLimitReq } from './model_get_isolated_margin_risk_limit_req';
import { GetIsolatedMarginRiskLimitResp } from './model_get_isolated_margin_risk_limit_resp';
import { GetCrossMarginLeverageReq } from './model_get_cross_margin_leverage_req';
import { GetMarginModeResp } from './model_get_margin_mode_resp';
import { BatchSwitchMarginModeReq } from './model_batch_switch_margin_mode_req';
import { ModifyMarginLeverageReq } from './model_modify_margin_leverage_req';
import { RemoveIsolatedMarginResp } from './model_remove_isolated_margin_resp';
import { ModifyMarginLeverageResp } from './model_modify_margin_leverage_resp';
import { GetPositionDetailsReq } from './model_get_position_details_req';
import { ModifyAutoDepositStatusResp } from './model_modify_auto_deposit_status_resp';
import { BatchSwitchMarginModeResp } from './model_batch_switch_margin_mode_resp';
import { SwitchMarginModeReq } from './model_switch_margin_mode_req';
import { GetPositionListResp } from './model_get_position_list_resp';
import { GetMaxOpenSizeResp } from './model_get_max_open_size_resp';
import { GetPositionDetailsResp } from './model_get_position_details_resp';
import { ModifyIsolatedMarginRiskLimtResp } from './model_modify_isolated_margin_risk_limt_resp';
import { ModifyIsolatedMarginRiskLimtReq } from './model_modify_isolated_margin_risk_limt_req';
import { GetPositionsHistoryReq } from './model_get_positions_history_req';
import { GetMaxWithdrawMarginReq } from './model_get_max_withdraw_margin_req';
import { RestResponse } from '@model/common';

describe('Auto Test', () => {
    test('getMarginMode request test', () => {
        /**
         * getMarginMode
         * Get Margin Mode
         * /api/v2/position/getMarginMode
         */
        let data = '{"symbol": "XBTUSDTM"}';
        let req = GetMarginModeReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getMarginMode response test', () => {
        /**
         * getMarginMode
         * Get Margin Mode
         * /api/v2/position/getMarginMode
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "symbol": "XBTUSDTM",\n        "marginMode": "ISOLATED"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetMarginModeResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('switchMarginMode request test', () => {
        /**
         * switchMarginMode
         * Switch Margin Mode
         * /api/v2/position/changeMarginMode
         */
        let data = '{"symbol": "XBTUSDTM", "marginMode": "ISOLATED"}';
        let req = SwitchMarginModeReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('switchMarginMode response test', () => {
        /**
         * switchMarginMode
         * Switch Margin Mode
         * /api/v2/position/changeMarginMode
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "symbol": "XBTUSDTM",\n        "marginMode": "ISOLATED"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = SwitchMarginModeResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('batchSwitchMarginMode request test', () => {
        /**
         * batchSwitchMarginMode
         * Batch Switch Margin Mode
         * /api/v2/position/batchChangeMarginMode
         */
        let data = '{"marginMode": "ISOLATED", "symbols": ["XBTUSDTM", "ETHUSDTM"]}';
        let req = BatchSwitchMarginModeReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('batchSwitchMarginMode response test', () => {
        /**
         * batchSwitchMarginMode
         * Batch Switch Margin Mode
         * /api/v2/position/batchChangeMarginMode
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "marginMode": {\n            "ETHUSDTM": "ISOLATED",\n            "XBTUSDTM": "CROSS"\n        },\n        "errors": [\n            {\n                "code": "50002",\n                "msg": "exist.order.or.position",\n                "symbol": "XBTUSDTM"\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = BatchSwitchMarginModeResp.fromObject(commonResp.data);
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
         * /api/v2/getMaxOpenSize
         */
        let data =
            '{"symbol": "XBTUSDTM", "price": "example_string_default_value", "leverage": 123456}';
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
         * /api/v2/getMaxOpenSize
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "symbol": "XBTUSDTM",\n        "maxBuyOpenSize": 0,\n        "maxSellOpenSize": 0\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetMaxOpenSizeResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getPositionDetails request test', () => {
        /**
         * getPositionDetails
         * Get Position Details
         * /api/v1/position
         */
        let data = '{"symbol": "example_string_default_value"}';
        let req = GetPositionDetailsReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getPositionDetails response test', () => {
        /**
         * getPositionDetails
         * Get Position Details
         * /api/v1/position
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "id": "500000000000988255",\n        "symbol": "XBTUSDTM",\n        "autoDeposit": false,\n        "crossMode": false,\n        "maintMarginReq": 0.005,\n        "riskLimit": 500000,\n        "realLeverage": 2.88,\n        "delevPercentage": 0.18,\n        "openingTimestamp": 1729155616322,\n        "currentTimestamp": 1729482542135,\n        "currentQty": 1,\n        "currentCost": 67.4309,\n        "currentComm": 0.01925174,\n        "unrealisedCost": 67.4309,\n        "realisedGrossCost": 0.0,\n        "realisedCost": 0.01925174,\n        "isOpen": true,\n        "markPrice": 68900.7,\n        "markValue": 68.9007,\n        "posCost": 67.4309,\n        "posCross": 0.01645214,\n        "posCrossMargin": 0,\n        "posInit": 22.4769666644,\n        "posComm": 0.0539546299,\n        "posCommCommon": 0.0539447199,\n        "posLoss": 0.03766885,\n        "posMargin": 22.5097045843,\n        "posFunding": -0.0212068,\n        "posMaint": 0.3931320569,\n        "maintMargin": 23.9795045843,\n        "realisedGrossPnl": 0.0,\n        "realisedPnl": -0.06166534,\n        "unrealisedPnl": 1.4698,\n        "unrealisedPnlPcnt": 0.0218,\n        "unrealisedRoePcnt": 0.0654,\n        "avgEntryPrice": 67430.9,\n        "liquidationPrice": 45314.33,\n        "bankruptPrice": 44975.16,\n        "settleCurrency": "USDT",\n        "maintainMargin": 0.005,\n        "riskLimitLevel": 2,\n        "marginMode": "ISOLATED",\n        "positionSide": "BOTH",\n        "leverage": 2.88\n    }\n}\n';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetPositionDetailsResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getPositionList request test', () => {
        /**
         * getPositionList
         * Get Position List
         * /api/v1/positions
         */
        let data = '{"currency": "USDT"}';
        let req = GetPositionListReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getPositionList response test', () => {
        /**
         * getPositionList
         * Get Position List
         * /api/v1/positions
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "id": "500000000001046430",\n            "symbol": "ETHUSDM",\n            "crossMode": true,\n            "delevPercentage": 0.71,\n            "openingTimestamp": 1730635780702,\n            "currentTimestamp": 1730636040926,\n            "currentQty": 1,\n            "currentCost": -4.069805E-4,\n            "currentComm": 2.441E-7,\n            "unrealisedCost": -4.069805E-4,\n            "realisedGrossCost": 0.0,\n            "realisedCost": 2.441E-7,\n            "isOpen": true,\n            "markPrice": 2454.12,\n            "markValue": -4.07478E-4,\n            "posCost": -4.069805E-4,\n            "posInit": 4.06981E-5,\n            "posMargin": 4.07478E-5,\n            "realisedGrossPnl": 0.0,\n            "realisedPnl": -2.441E-7,\n            "unrealisedPnl": -4.975E-7,\n            "unrealisedPnlPcnt": -0.0012,\n            "unrealisedRoePcnt": -0.0122,\n            "avgEntryPrice": 2457.12,\n            "liquidationPrice": 1429.96,\n            "bankruptPrice": 1414.96,\n            "settleCurrency": "ETH",\n            "isInverse": true,\n            "marginMode": "CROSS",\n            "positionSide": "BOTH",\n            "leverage": 10\n        },\n        {\n            "id": "500000000000988255",\n            "symbol": "XBTUSDTM",\n            "autoDeposit": true,\n            "crossMode": false,\n            "maintMarginReq": 0.005,\n            "riskLimit": 500000,\n            "realLeverage": 2.97,\n            "delevPercentage": 0.5,\n            "openingTimestamp": 1729155616322,\n            "currentTimestamp": 1730636040926,\n            "currentQty": 1,\n            "currentCost": 67.4309,\n            "currentComm": -0.15936162,\n            "unrealisedCost": 67.4309,\n            "realisedGrossCost": 0.0,\n            "realisedCost": -0.15936162,\n            "isOpen": true,\n            "markPrice": 68323.06,\n            "markValue": 68.32306,\n            "posCost": 67.4309,\n            "posCross": 0.06225152,\n            "posCrossMargin": 0,\n            "posInit": 22.2769666644,\n            "posComm": 0.0539821899,\n            "posCommCommon": 0.0539447199,\n            "posLoss": 0.26210915,\n            "posMargin": 22.1310912243,\n            "posFunding": -0.19982016,\n            "posMaint": 0.4046228699,\n            "maintMargin": 23.0232512243,\n            "realisedGrossPnl": 0.0,\n            "realisedPnl": -0.2402787,\n            "unrealisedPnl": 0.89216,\n            "unrealisedPnlPcnt": 0.0132,\n            "unrealisedRoePcnt": 0.04,\n            "avgEntryPrice": 67430.9,\n            "liquidationPrice": 45704.44,\n            "bankruptPrice": 45353.8,\n            "settleCurrency": "USDT",\n            "isInverse": false,\n            "maintainMargin": 0.005,\n            "marginMode": "ISOLATED",\n            "positionSide": "BOTH",\n            "leverage": 2.97\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetPositionListResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getPositionsHistory request test', () => {
        /**
         * getPositionsHistory
         * Get Positions History
         * /api/v1/history-positions
         */
        let data =
            '{"symbol": "example_string_default_value", "from": 123456, "to": 123456, "limit": 10, "pageId": 1}';
        let req = GetPositionsHistoryReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getPositionsHistory response test', () => {
        /**
         * getPositionsHistory
         * Get Positions History
         * /api/v1/history-positions
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "currentPage": 1,\n        "pageSize": 10,\n        "totalNum": 1,\n        "totalPage": 1,\n        "items": [\n            {\n                "closeId": "500000000036305465",\n                "userId": "633559791e1cbc0001f319bc",\n                "symbol": "XBTUSDTM",\n                "settleCurrency": "USDT",\n                "leverage": "1.0",\n                "type": "CLOSE_LONG",\n                "pnl": "0.51214413",\n                "realisedGrossCost": "-0.5837",\n                "realisedGrossCostNew": "-0.5837",\n                "withdrawPnl": "0.0",\n                "tradeFee": "0.03766066",\n                "fundingFee": "-0.03389521",\n                "openTime": 1735549162120,\n                "closeTime": 1735589352069,\n                "openPrice": "93859.8",\n                "closePrice": "94443.5",\n                "marginMode": "CROSS",\n                "tax": "0.0",\n                "roe": null,\n                "liquidAmount": null,\n                "liquidPrice": null,\n                "side": "LONG"\n            }\n        ]\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetPositionsHistoryResp.fromObject(commonResp.data);
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
         * /api/v1/margin/maxWithdrawMargin
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
         * /api/v1/margin/maxWithdrawMargin
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
    test('getCrossMarginLeverage request test', () => {
        /**
         * getCrossMarginLeverage
         * Get Cross Margin Leverage
         * /api/v2/getCrossUserLeverage
         */
        let data = '{"symbol": "XBTUSDTM"}';
        let req = GetCrossMarginLeverageReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getCrossMarginLeverage response test', () => {
        /**
         * getCrossMarginLeverage
         * Get Cross Margin Leverage
         * /api/v2/getCrossUserLeverage
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "symbol": "XBTUSDTM",\n        "leverage": "3"\n    }\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetCrossMarginLeverageResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('modifyMarginLeverage request test', () => {
        /**
         * modifyMarginLeverage
         * Modify Cross Margin Leverage
         * /api/v2/changeCrossUserLeverage
         */
        let data = '{"symbol": "XBTUSDTM", "leverage": "10"}';
        let req = ModifyMarginLeverageReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('modifyMarginLeverage response test', () => {
        /**
         * modifyMarginLeverage
         * Modify Cross Margin Leverage
         * /api/v2/changeCrossUserLeverage
         */
        let data = '{\n    "code": "200000",\n    "data": true\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = ModifyMarginLeverageResp.fromObject(commonResp.data);
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
         * /api/v1/position/margin/deposit-margin
         */
        let data = '{"symbol": "string", "margin": 0, "bizNo": "string"}';
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
         * /api/v1/position/margin/deposit-margin
         */
        let data =
            '{\n    "code": "200000",\n    "data": {\n        "id": "6200c9b83aecfb000152ddcd",\n        "symbol": "XBTUSDTM",\n        "autoDeposit": false,\n        "maintMarginReq": 0.005,\n        "riskLimit": 500000,\n        "realLeverage": 18.72,\n        "crossMode": false,\n        "delevPercentage": 0.66,\n        "openingTimestamp": 1646287090131,\n        "currentTimestamp": 1646295055021,\n        "currentQty": 1,\n        "currentCost": 43.388,\n        "currentComm": 0.0260328,\n        "unrealisedCost": 43.388,\n        "realisedGrossCost": 0,\n        "realisedCost": 0.0260328,\n        "isOpen": true,\n        "markPrice": 43536.65,\n        "markValue": 43.53665,\n        "posCost": 43.388,\n        "posCross": 0.000024985,\n        "posInit": 2.1694,\n        "posComm": 0.02733446,\n        "posLoss": 0,\n        "posMargin": 2.19675944,\n        "posMaint": 0.24861326,\n        "maintMargin": 2.34540944,\n        "realisedGrossPnl": 0,\n        "realisedPnl": -0.0260328,\n        "unrealisedPnl": 0.14865,\n        "unrealisedPnlPcnt": 0.0034,\n        "unrealisedRoePcnt": 0.0685,\n        "avgEntryPrice": 43388,\n        "liquidationPrice": 41440,\n        "bankruptPrice": 41218,\n        "userId": 1234321123,\n        "settleCurrency": "USDT"\n    }\n}';
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
         * /api/v1/margin/withdrawMargin
         */
        let data = '{"symbol": "XBTUSDTM", "withdrawAmount": "0.0000001"}';
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
         * /api/v1/margin/withdrawMargin
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
    test('getCrossMarginRiskLimit request test', () => {
        /**
         * getCrossMarginRiskLimit
         * Get Cross Margin Risk Limit
         * /api/v2/batchGetCrossOrderLimit
         */
        let data =
            '{"symbol": "XBTUSDTM", "totalMargin": "example_string_default_value", "leverage": 123456}';
        let req = GetCrossMarginRiskLimitReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getCrossMarginRiskLimit response test', () => {
        /**
         * getCrossMarginRiskLimit
         * Get Cross Margin Risk Limit
         * /api/v2/batchGetCrossOrderLimit
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "symbol": "XBTUSDTM",\n            "maxOpenSize": 12102,\n            "maxOpenValue": "1234549.2240000000",\n            "totalMargin": "10000",\n            "price": "102012",\n            "leverage": "125.00",\n            "mmr": "0.00416136",\n            "imr": "0.008",\n            "currency": "USDT"\n        },\n        {\n            "symbol": "ETHUSDTM",\n            "maxOpenSize": 38003,\n            "maxOpenValue": "971508.6920000000",\n            "totalMargin": "10000",\n            "price": "2556.4",\n            "leverage": "100.00",\n            "mmr": "0.0054623236",\n            "imr": "0.01",\n            "currency": "USDT"\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetCrossMarginRiskLimitResp.fromObject(commonResp.data);
        if (commonResp.data !== null) {
            expect(
                Object.values(resp).every((value) => value === null || value === undefined),
            ).toBe(false);
            console.log(resp);
        }
    });
    test('getIsolatedMarginRiskLimit request test', () => {
        /**
         * getIsolatedMarginRiskLimit
         * Get Isolated Margin Risk Limit
         * /api/v1/contracts/risk-limit/{symbol}
         */
        let data = '{"symbol": "XBTUSDTM"}';
        let req = GetIsolatedMarginRiskLimitReq.fromJson(data);
        expect(Object.values(req).every((value) => value === null || value === undefined)).toBe(
            false,
        );
        console.log(req);
    });

    test('getIsolatedMarginRiskLimit response test', () => {
        /**
         * getIsolatedMarginRiskLimit
         * Get Isolated Margin Risk Limit
         * /api/v1/contracts/risk-limit/{symbol}
         */
        let data =
            '{\n    "code": "200000",\n    "data": [\n        {\n            "symbol": "XBTUSDTM",\n            "level": 1,\n            "maxRiskLimit": 100000,\n            "minRiskLimit": 0,\n            "maxLeverage": 125,\n            "initialMargin": 0.008,\n            "maintainMargin": 0.004\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "level": 2,\n            "maxRiskLimit": 500000,\n            "minRiskLimit": 100000,\n            "maxLeverage": 100,\n            "initialMargin": 0.01,\n            "maintainMargin": 0.005\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "level": 3,\n            "maxRiskLimit": 1000000,\n            "minRiskLimit": 500000,\n            "maxLeverage": 75,\n            "initialMargin": 0.014,\n            "maintainMargin": 0.007\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "level": 4,\n            "maxRiskLimit": 2000000,\n            "minRiskLimit": 1000000,\n            "maxLeverage": 50,\n            "initialMargin": 0.02,\n            "maintainMargin": 0.01\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "level": 5,\n            "maxRiskLimit": 3000000,\n            "minRiskLimit": 2000000,\n            "maxLeverage": 30,\n            "initialMargin": 0.034,\n            "maintainMargin": 0.017\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "level": 6,\n            "maxRiskLimit": 5000000,\n            "minRiskLimit": 3000000,\n            "maxLeverage": 20,\n            "initialMargin": 0.05,\n            "maintainMargin": 0.025\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "level": 7,\n            "maxRiskLimit": 8000000,\n            "minRiskLimit": 5000000,\n            "maxLeverage": 10,\n            "initialMargin": 0.1,\n            "maintainMargin": 0.05\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "level": 8,\n            "maxRiskLimit": 12000000,\n            "minRiskLimit": 8000000,\n            "maxLeverage": 5,\n            "initialMargin": 0.2,\n            "maintainMargin": 0.1\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "level": 9,\n            "maxRiskLimit": 20000000,\n            "minRiskLimit": 12000000,\n            "maxLeverage": 4,\n            "initialMargin": 0.25,\n            "maintainMargin": 0.125\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "level": 10,\n            "maxRiskLimit": 30000000,\n            "minRiskLimit": 20000000,\n            "maxLeverage": 3,\n            "initialMargin": 0.334,\n            "maintainMargin": 0.167\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "level": 11,\n            "maxRiskLimit": 40000000,\n            "minRiskLimit": 30000000,\n            "maxLeverage": 2,\n            "initialMargin": 0.5,\n            "maintainMargin": 0.25\n        },\n        {\n            "symbol": "XBTUSDTM",\n            "level": 12,\n            "maxRiskLimit": 50000000,\n            "minRiskLimit": 40000000,\n            "maxLeverage": 1,\n            "initialMargin": 1.0,\n            "maintainMargin": 0.5\n        }\n    ]\n}';
        let commonResp = RestResponse.fromJson(data);
        let resp = GetIsolatedMarginRiskLimitResp.fromObject(commonResp.data);
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
         * /api/v1/position/risk-limit-level/change
         */
        let data = '{"symbol": "XBTUSDTM", "level": 2}';
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
         * /api/v1/position/risk-limit-level/change
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
         * /api/v1/position/margin/auto-deposit-status
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
         * /api/v1/position/margin/auto-deposit-status
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
