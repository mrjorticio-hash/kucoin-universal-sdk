import unittest
from .model_get_accounts_resp import GetAccountsResp
from .model_get_discount_rate_configs_resp import GetDiscountRateConfigsResp
from .model_get_loan_info_resp import GetLoanInfoResp
from kucoin_universal_sdk.model.common import RestResponse


class VIPLendingAPITest(unittest.TestCase):

    def test_get_discount_rate_configs_req_model(self):
        """
       get_discount_rate_configs
       Get Discount Rate Configs
       /api/v1/otc-loan/discount-rate-configs
       """

    def test_get_discount_rate_configs_resp_model(self):
        """
        get_discount_rate_configs
        Get Discount Rate Configs
        /api/v1/otc-loan/discount-rate-configs
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"currency\": \"BTC\",\n            \"usdtLevels\": [\n                {\n                    \"left\": 0,\n                    \"right\": 20000000,\n                    \"discountRate\": \"1.00000000\"\n                },\n                {\n                    \"left\": 20000000,\n                    \"right\": 50000000,\n                    \"discountRate\": \"0.95000000\"\n                },\n                {\n                    \"left\": 50000000,\n                    \"right\": 100000000,\n                    \"discountRate\": \"0.90000000\"\n                },\n                {\n                    \"left\": 100000000,\n                    \"right\": 300000000,\n                    \"discountRate\": \"0.50000000\"\n                },\n                {\n                    \"left\": 300000000,\n                    \"right\": 99999999999,\n                    \"discountRate\": \"0.00000000\"\n                }\n            ]\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetDiscountRateConfigsResp.from_dict(common_response.data)

    def test_get_loan_info_req_model(self):
        """
       get_loan_info
       Get Loan Info
       /api/v1/otc-loan/loan
       """

    def test_get_loan_info_resp_model(self):
        """
        get_loan_info
        Get Loan Info
        /api/v1/otc-loan/loan
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"parentUid\": \"1260004199\",\n        \"orders\": [{\n            \"orderId\": \"671a2be815f4140007a588e1\",\n            \"principal\": \"100\",\n            \"interest\": \"0\",\n            \"currency\": \"USDT\"\n        }],\n        \"ltv\": {\n            \"transferLtv\": \"0.6000\",\n            \"onlyClosePosLtv\": \"0.7500\",\n            \"delayedLiquidationLtv\": \"0.7500\",\n            \"instantLiquidationLtv\": \"0.8000\",\n            \"currentLtv\": \"0.1111\"\n        },\n        \"totalMarginAmount\": \"900.00000000\",\n        \"transferMarginAmount\": \"166.66666666\",\n        \"margins\": [{\n            \"marginCcy\": \"USDT\",\n            \"marginQty\": \"1000.00000000\",\n            \"marginFactor\": \"0.9000000000\"\n        }]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetLoanInfoResp.from_dict(common_response.data)

    def test_get_accounts_req_model(self):
        """
       get_accounts
       Get Accounts
       /api/v1/otc-loan/accounts
       """

    def test_get_accounts_resp_model(self):
        """
        get_accounts
        Get Accounts
        /api/v1/otc-loan/accounts
        """
        data = "\n{\n    \"code\": \"200000\",\n    \"data\": [{\n        \"uid\": \"1260004199\",\n        \"marginCcy\": \"USDT\",\n        \"marginQty\": \"900\",\n        \"marginFactor\": \"0.9000000000\",\n        \"accountType\": \"TRADE\",\n        \"isParent\": true\n    }]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetAccountsResp.from_dict(common_response.data)
