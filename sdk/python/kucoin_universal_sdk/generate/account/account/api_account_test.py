import unittest
from .model_get_account_info_resp import GetAccountInfoResp
from .model_get_apikey_info_resp import GetApikeyInfoResp
from .model_get_cross_margin_account_req import GetCrossMarginAccountReq
from .model_get_cross_margin_account_resp import GetCrossMarginAccountResp
from .model_get_futures_account_req import GetFuturesAccountReq
from .model_get_futures_account_resp import GetFuturesAccountResp
from .model_get_futures_ledger_req import GetFuturesLedgerReq
from .model_get_futures_ledger_resp import GetFuturesLedgerResp
from .model_get_isolated_margin_account_detail_v1_req import GetIsolatedMarginAccountDetailV1Req
from .model_get_isolated_margin_account_detail_v1_resp import GetIsolatedMarginAccountDetailV1Resp
from .model_get_isolated_margin_account_list_v1_req import GetIsolatedMarginAccountListV1Req
from .model_get_isolated_margin_account_list_v1_resp import GetIsolatedMarginAccountListV1Resp
from .model_get_isolated_margin_account_req import GetIsolatedMarginAccountReq
from .model_get_isolated_margin_account_resp import GetIsolatedMarginAccountResp
from .model_get_margin_account_detail_resp import GetMarginAccountDetailResp
from .model_get_margin_hf_ledger_req import GetMarginHfLedgerReq
from .model_get_margin_hf_ledger_resp import GetMarginHfLedgerResp
from .model_get_spot_account_detail_req import GetSpotAccountDetailReq
from .model_get_spot_account_detail_resp import GetSpotAccountDetailResp
from .model_get_spot_account_list_req import GetSpotAccountListReq
from .model_get_spot_account_list_resp import GetSpotAccountListResp
from .model_get_spot_account_type_resp import GetSpotAccountTypeResp
from .model_get_spot_hf_ledger_req import GetSpotHfLedgerReq
from .model_get_spot_hf_ledger_resp import GetSpotHfLedgerResp
from .model_get_spot_ledger_req import GetSpotLedgerReq
from .model_get_spot_ledger_resp import GetSpotLedgerResp
from typing_extensions import deprecated
from kucoin_universal_sdk.model.common import RestResponse


class AccountAPITest(unittest.TestCase):

    def test_get_account_info_req_model(self):
        """
       get_account_info
       Get Account Summary Info
       /api/v2/user-info
       """

    def test_get_account_info_resp_model(self):
        """
        get_account_info
        Get Account Summary Info
        /api/v2/user-info
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"level\": 0,\n        \"subQuantity\": 3,\n        \"spotSubQuantity\": 3,\n        \"marginSubQuantity\": 2,\n        \"futuresSubQuantity\": 2,\n        \"optionSubQuantity\": 0,\n        \"maxSubQuantity\": 5,\n        \"maxDefaultSubQuantity\": 5,\n        \"maxSpotSubQuantity\": 0,\n        \"maxMarginSubQuantity\": 0,\n        \"maxFuturesSubQuantity\": 0,\n        \"maxOptionSubQuantity\": 0\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetAccountInfoResp.from_dict(common_response.data)

    def test_get_apikey_info_req_model(self):
        """
       get_apikey_info
       Get Apikey Info
       /api/v1/user/api-key
       """

    def test_get_apikey_info_resp_model(self):
        """
        get_apikey_info
        Get Apikey Info
        /api/v1/user/api-key
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"remark\": \"account1\",\n        \"apiKey\": \"6705f5c311545b000157d3eb\",\n        \"apiVersion\": 3,\n        \"permission\": \"General,Futures,Spot,Earn,InnerTransfer,Transfer,Margin\",\n        \"ipWhitelist\": \"203.**.154,103.**.34\",\n        \"createdAt\": 1728443843000,\n        \"uid\": 165111215,\n        \"isMaster\": true\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetApikeyInfoResp.from_dict(common_response.data)

    def test_get_spot_account_type_req_model(self):
        """
       get_spot_account_type
       Get Account Type - Spot 
       /api/v1/hf/accounts/opened
       """

    def test_get_spot_account_type_resp_model(self):
        """
        get_spot_account_type
        Get Account Type - Spot 
        /api/v1/hf/accounts/opened
        """
        data = "{\"code\":\"200000\",\"data\":false}"
        common_response = RestResponse.from_json(data)
        resp = GetSpotAccountTypeResp.from_dict(common_response.data)

    def test_get_spot_account_list_req_model(self):
        """
       get_spot_account_list
       Get Account List - Spot
       /api/v1/accounts
       """
        data = "{\"currency\": \"USDT\", \"type\": \"main\"}"
        req = GetSpotAccountListReq.from_json(data)

    def test_get_spot_account_list_resp_model(self):
        """
        get_spot_account_list
        Get Account List - Spot
        /api/v1/accounts
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"548674591753\",\n            \"currency\": \"USDT\",\n            \"type\": \"trade\",\n            \"balance\": \"26.66759503\",\n            \"available\": \"26.66759503\",\n            \"holds\": \"0\"\n        },\n        {\n            \"id\": \"63355cd156298d0001b66e61\",\n            \"currency\": \"USDT\",\n            \"type\": \"main\",\n            \"balance\": \"0.01\",\n            \"available\": \"0.01\",\n            \"holds\": \"0\"\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetSpotAccountListResp.from_dict(common_response.data)

    def test_get_spot_account_detail_req_model(self):
        """
       get_spot_account_detail
       Get Account Detail - Spot
       /api/v1/accounts/{accountId}
       """
        data = "{\"accountId\": \"548674591753\"}"
        req = GetSpotAccountDetailReq.from_json(data)

    def test_get_spot_account_detail_resp_model(self):
        """
        get_spot_account_detail
        Get Account Detail - Spot
        /api/v1/accounts/{accountId}
        """
        data = "{\"code\":\"200000\",\"data\":{\"currency\":\"USDT\",\"balance\":\"26.66759503\",\"available\":\"26.66759503\",\"holds\":\"0\"}}"
        common_response = RestResponse.from_json(data)
        resp = GetSpotAccountDetailResp.from_dict(common_response.data)

    def test_get_cross_margin_account_req_model(self):
        """
       get_cross_margin_account
       Get Account - Cross Margin
       /api/v3/margin/accounts
       """
        data = "{\"quoteCurrency\": \"USDT\", \"queryType\": \"MARGIN\"}"
        req = GetCrossMarginAccountReq.from_json(data)

    def test_get_cross_margin_account_resp_model(self):
        """
        get_cross_margin_account
        Get Account - Cross Margin
        /api/v3/margin/accounts
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"totalAssetOfQuoteCurrency\": \"40.8648372\",\n        \"totalLiabilityOfQuoteCurrency\": \"0\",\n        \"debtRatio\": \"0\",\n        \"status\": \"EFFECTIVE\",\n        \"accounts\": [\n            {\n                \"currency\": \"USDT\",\n                \"total\": \"38.68855864\",\n                \"available\": \"20.01916691\",\n                \"hold\": \"18.66939173\",\n                \"liability\": \"0\",\n                \"liabilityPrincipal\": \"0\",\n                \"liabilityInterest\": \"0\",\n                \"maxBorrowSize\": \"163\",\n                \"borrowEnabled\": true,\n                \"transferInEnabled\": true\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetCrossMarginAccountResp.from_dict(common_response.data)

    def test_get_isolated_margin_account_req_model(self):
        """
       get_isolated_margin_account
       Get Account - Isolated Margin
       /api/v3/isolated/accounts
       """
        data = "{\"symbol\": \"BTC-USDT\", \"quoteCurrency\": \"USDT\", \"queryType\": \"ISOLATED\"}"
        req = GetIsolatedMarginAccountReq.from_json(data)

    def test_get_isolated_margin_account_resp_model(self):
        """
        get_isolated_margin_account
        Get Account - Isolated Margin
        /api/v3/isolated/accounts
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"totalAssetOfQuoteCurrency\": \"4.97047372\",\n        \"totalLiabilityOfQuoteCurrency\": \"0.00038891\",\n        \"timestamp\": 1747303659773,\n        \"assets\": [\n            {\n                \"symbol\": \"BTC-USDT\",\n                \"status\": \"EFFECTIVE\",\n                \"debtRatio\": \"0\",\n                \"baseAsset\": {\n                    \"currency\": \"BTC\",\n                    \"borrowEnabled\": true,\n                    \"transferInEnabled\": true,\n                    \"liability\": \"0\",\n                    \"liabilityPrincipal\": \"0\",\n                    \"liabilityInterest\": \"0\",\n                    \"total\": \"0\",\n                    \"available\": \"0\",\n                    \"hold\": \"0\",\n                    \"maxBorrowSize\": \"0\"\n                },\n                \"quoteAsset\": {\n                    \"currency\": \"USDT\",\n                    \"borrowEnabled\": true,\n                    \"transferInEnabled\": true,\n                    \"liability\": \"0.00038891\",\n                    \"liabilityPrincipal\": \"0.00038888\",\n                    \"liabilityInterest\": \"0.00000003\",\n                    \"total\": \"4.97047372\",\n                    \"available\": \"4.97047372\",\n                    \"hold\": \"0\",\n                    \"maxBorrowSize\": \"44\"\n                }\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetIsolatedMarginAccountResp.from_dict(common_response.data)

    def test_get_futures_account_req_model(self):
        """
       get_futures_account
       Get Account - Futures
       /api/v1/account-overview
       """
        data = "{\"currency\": \"USDT\"}"
        req = GetFuturesAccountReq.from_json(data)

    def test_get_futures_account_resp_model(self):
        """
        get_futures_account
        Get Account - Futures
        /api/v1/account-overview
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"accountEquity\": 394.439280806,\n        \"unrealisedPNL\": 20.15278,\n        \"marginBalance\": 371.394298816,\n        \"positionMargin\": 102.20664159,\n        \"orderMargin\": 10.06002012,\n        \"frozenFunds\": 0.0,\n        \"availableBalance\": 290.326799096,\n        \"currency\": \"USDT\",\n        \"riskRatio\": 0.0065289525,\n        \"maxWithdrawAmount\": 290.326419096\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetFuturesAccountResp.from_dict(common_response.data)

    def test_get_spot_ledger_req_model(self):
        """
       get_spot_ledger
       Get Account Ledgers - Spot/Margin
       /api/v1/accounts/ledgers
       """
        data = "{\"currency\": \"BTC\", \"direction\": \"in\", \"bizType\": \"TRANSFER\", \"startAt\": 1728663338000, \"endAt\": 1728692138000, \"currentPage\": 1, \"pageSize\": 50}"
        req = GetSpotLedgerReq.from_json(data)

    def test_get_spot_ledger_resp_model(self):
        """
        get_spot_ledger
        Get Account Ledgers - Spot/Margin
        /api/v1/accounts/ledgers
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"id\": \"265329987780896\",\n                \"currency\": \"USDT\",\n                \"amount\": \"0.01\",\n                \"fee\": \"0\",\n                \"balance\": \"0\",\n                \"accountType\": \"TRADE\",\n                \"bizType\": \"SUB_TRANSFER\",\n                \"direction\": \"out\",\n                \"createdAt\": 1728658481484,\n                \"context\": \"\"\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetSpotLedgerResp.from_dict(common_response.data)

    def test_get_spot_hf_ledger_req_model(self):
        """
       get_spot_hf_ledger
       Get Account Ledgers - Trade_hf
       /api/v1/hf/accounts/ledgers
       """
        data = "{\"currency\": \"BTC\", \"direction\": \"in\", \"bizType\": \"TRANSFER\", \"lastId\": 254062248624417, \"limit\": 100, \"startAt\": 1728663338000, \"endAt\": 1728692138000}"
        req = GetSpotHfLedgerReq.from_json(data)

    def test_get_spot_hf_ledger_resp_model(self):
        """
        get_spot_hf_ledger
        Get Account Ledgers - Trade_hf
        /api/v1/hf/accounts/ledgers
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"254062248624417\",\n            \"currency\": \"USDT\",\n            \"amount\": \"1.59760080\",\n            \"fee\": \"0.00159920\",\n            \"tax\": \"0\",\n            \"balance\": \"26.73759503\",\n            \"accountType\": \"TRADE_HF\",\n            \"bizType\": \"TRADE_EXCHANGE\",\n            \"direction\": \"in\",\n            \"createdAt\": \"1728443957539\",\n            \"context\": \"{\\\"symbol\\\":\\\"KCS-USDT\\\",\\\"orderId\\\":\\\"6705f6350dc7210007d6a36d\\\",\\\"tradeId\\\":\\\"10046097631627265\\\"}\"\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetSpotHfLedgerResp.from_dict(common_response.data)

    def test_get_margin_hf_ledger_req_model(self):
        """
       get_margin_hf_ledger
       Get Account Ledgers - Margin_hf
       /api/v3/hf/margin/account/ledgers
       """
        data = "{\"currency\": \"BTC\", \"direction\": \"in\", \"bizType\": \"TRANSFER\", \"lastId\": 254062248624417, \"limit\": 100, \"startAt\": 1728663338000, \"endAt\": 1728692138000}"
        req = GetMarginHfLedgerReq.from_json(data)

    def test_get_margin_hf_ledger_resp_model(self):
        """
        get_margin_hf_ledger
        Get Account Ledgers - Margin_hf
        /api/v3/hf/margin/account/ledgers
        """
        data = "{\"code\":\"200000\",\"data\":[{\"id\":1949641706720,\"currency\":\"USDT\",\"amount\":\"0.01000000\",\"fee\":\"0.00000000\",\"balance\":\"0.01000000\",\"accountType\":\"MARGIN_V2\",\"bizType\":\"TRANSFER\",\"direction\":\"in\",\"createdAt\":1728664091208,\"context\":\"{}\",\"tax\":\"0.00000000\"}]}"
        common_response = RestResponse.from_json(data)
        resp = GetMarginHfLedgerResp.from_dict(common_response.data)

    def test_get_futures_ledger_req_model(self):
        """
       get_futures_ledger
       Get Account Ledgers - Futures
       /api/v1/transaction-history
       """
        data = "{\"currency\": \"XBT\", \"type\": \"Transferin\", \"offset\": 254062248624417, \"forward\": true, \"maxCount\": 50, \"startAt\": 1728663338000, \"endAt\": 1728692138000}"
        req = GetFuturesLedgerReq.from_json(data)

    def test_get_futures_ledger_resp_model(self):
        """
        get_futures_ledger
        Get Account Ledgers - Futures
        /api/v1/transaction-history
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"dataList\": [\n            {\n                \"time\": 1728665747000,\n                \"type\": \"TransferIn\",\n                \"amount\": 0.01,\n                \"fee\": 0.0,\n                \"accountEquity\": 14.02924938,\n                \"status\": \"Completed\",\n                \"remark\": \"Transferred from High-Frequency Trading Account\",\n                \"offset\": 51360793,\n                \"currency\": \"USDT\"\n            },\n            {\n                \"time\": 1728648000000,\n                \"type\": \"RealisedPNL\",\n                \"amount\": 0.00630042,\n                \"fee\": 0.0,\n                \"accountEquity\": 20.0,\n                \"status\": \"Completed\",\n                \"remark\": \"XBTUSDTM\",\n                \"offset\": 51352430,\n                \"currency\": \"USDT\"\n            }\n        ],\n        \"hasMore\": false\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetFuturesLedgerResp.from_dict(common_response.data)

    def test_get_margin_account_detail_req_model(self):
        """
       get_margin_account_detail
       Get Account Detail - Margin
       /api/v1/margin/account
       """

    def test_get_margin_account_detail_resp_model(self):
        """
        get_margin_account_detail
        Get Account Detail - Margin
        /api/v1/margin/account
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"debtRatio\": \"0\",\n        \"accounts\": [\n            {\n                \"currency\": \"USDT\",\n                \"totalBalance\": \"0.03\",\n                \"availableBalance\": \"0.02\",\n                \"holdBalance\": \"0.01\",\n                \"liability\": \"0\",\n                \"maxBorrowSize\": \"0\"\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetMarginAccountDetailResp.from_dict(common_response.data)

    def test_get_isolated_margin_account_list_v1_req_model(self):
        """
       get_isolated_margin_account_list_v1
       Get Account List - Isolated Margin - V1
       /api/v1/isolated/accounts
       """
        data = "{\"balanceCurrency\": \"USDT\"}"
        req = GetIsolatedMarginAccountListV1Req.from_json(data)

    def test_get_isolated_margin_account_list_v1_resp_model(self):
        """
        get_isolated_margin_account_list_v1
        Get Account List - Isolated Margin - V1
        /api/v1/isolated/accounts
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"totalConversionBalance\": \"0.01\",\n        \"liabilityConversionBalance\": \"0\",\n        \"assets\": [\n            {\n                \"symbol\": \"BTC-USDT\",\n                \"status\": \"CLEAR\",\n                \"debtRatio\": \"0\",\n                \"baseAsset\": {\n                    \"currency\": \"BTC\",\n                    \"totalBalance\": \"0\",\n                    \"holdBalance\": \"0\",\n                    \"availableBalance\": \"0\",\n                    \"liability\": \"0\",\n                    \"interest\": \"0\",\n                    \"borrowableAmount\": \"0\"\n                },\n                \"quoteAsset\": {\n                    \"currency\": \"USDT\",\n                    \"totalBalance\": \"0.01\",\n                    \"holdBalance\": \"0\",\n                    \"availableBalance\": \"0.01\",\n                    \"liability\": \"0\",\n                    \"interest\": \"0\",\n                    \"borrowableAmount\": \"0\"\n                }\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetIsolatedMarginAccountListV1Resp.from_dict(
            common_response.data)

    def test_get_isolated_margin_account_detail_v1_req_model(self):
        """
       get_isolated_margin_account_detail_v1
       Get Account Detail - Isolated Margin - V1
       /api/v1/isolated/account/{symbol}
       """
        data = "{\"symbol\": \"example_string_default_value\"}"
        req = GetIsolatedMarginAccountDetailV1Req.from_json(data)

    def test_get_isolated_margin_account_detail_v1_resp_model(self):
        """
        get_isolated_margin_account_detail_v1
        Get Account Detail - Isolated Margin - V1
        /api/v1/isolated/account/{symbol}
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"symbol\": \"BTC-USDT\",\n        \"status\": \"CLEAR\",\n        \"debtRatio\": \"0\",\n        \"baseAsset\": {\n            \"currency\": \"BTC\",\n            \"totalBalance\": \"0\",\n            \"holdBalance\": \"0\",\n            \"availableBalance\": \"0\",\n            \"liability\": \"0\",\n            \"interest\": \"0\",\n            \"borrowableAmount\": \"0\"\n        },\n        \"quoteAsset\": {\n            \"currency\": \"USDT\",\n            \"totalBalance\": \"0.01\",\n            \"holdBalance\": \"0\",\n            \"availableBalance\": \"0.01\",\n            \"liability\": \"0\",\n            \"interest\": \"0\",\n            \"borrowableAmount\": \"0\"\n        }\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetIsolatedMarginAccountDetailV1Resp.from_dict(
            common_response.data)
