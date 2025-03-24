import unittest
from .model_get_account_holding_req import GetAccountHoldingReq
from .model_get_account_holding_resp import GetAccountHoldingResp
from .model_get_eth_staking_products_req import GetEthStakingProductsReq
from .model_get_eth_staking_products_resp import GetEthStakingProductsResp
from .model_get_kcs_staking_products_req import GetKcsStakingProductsReq
from .model_get_kcs_staking_products_resp import GetKcsStakingProductsResp
from .model_get_promotion_products_req import GetPromotionProductsReq
from .model_get_promotion_products_resp import GetPromotionProductsResp
from .model_get_redeem_preview_req import GetRedeemPreviewReq
from .model_get_redeem_preview_resp import GetRedeemPreviewResp
from .model_get_savings_products_req import GetSavingsProductsReq
from .model_get_savings_products_resp import GetSavingsProductsResp
from .model_get_staking_products_req import GetStakingProductsReq
from .model_get_staking_products_resp import GetStakingProductsResp
from .model_purchase_req import PurchaseReq
from .model_purchase_resp import PurchaseResp
from .model_redeem_req import RedeemReq
from .model_redeem_resp import RedeemResp
from kucoin_universal_sdk.model.common import RestResponse


class EarnAPITest(unittest.TestCase):

    def test_purchase_req_model(self):
        """
       purchase
       Purchase
       /api/v1/earn/orders
       """
        data = "{\"productId\": \"2611\", \"amount\": \"1\", \"accountType\": \"TRADE\"}"
        req = PurchaseReq.from_json(data)

    def test_purchase_resp_model(self):
        """
        purchase
        Purchase
        /api/v1/earn/orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"2767291\",\n        \"orderTxId\": \"6603694\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = PurchaseResp.from_dict(common_response.data)

    def test_get_redeem_preview_req_model(self):
        """
       get_redeem_preview
       Get Redeem Preview
       /api/v1/earn/redeem-preview
       """
        data = "{\"orderId\": \"2767291\", \"fromAccountType\": \"MAIN\"}"
        req = GetRedeemPreviewReq.from_json(data)

    def test_get_redeem_preview_resp_model(self):
        """
        get_redeem_preview
        Get Redeem Preview
        /api/v1/earn/redeem-preview
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currency\": \"KCS\",\n        \"redeemAmount\": \"1\",\n        \"penaltyInterestAmount\": \"0\",\n        \"redeemPeriod\": 3,\n        \"deliverTime\": 1729518951000,\n        \"manualRedeemable\": true,\n        \"redeemAll\": false\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetRedeemPreviewResp.from_dict(common_response.data)

    def test_redeem_req_model(self):
        """
       redeem
       Redeem
       /api/v1/earn/orders
       """
        data = "{\"orderId\": \"2767291\", \"amount\": \"example_string_default_value\", \"fromAccountType\": \"MAIN\", \"confirmPunishRedeem\": \"1\"}"
        req = RedeemReq.from_json(data)

    def test_redeem_resp_model(self):
        """
        redeem
        Redeem
        /api/v1/earn/orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderTxId\": \"6603700\",\n        \"deliverTime\": 1729517805000,\n        \"status\": \"PENDING\",\n        \"amount\": \"1\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = RedeemResp.from_dict(common_response.data)

    def test_get_savings_products_req_model(self):
        """
       get_savings_products
       Get Savings Products
       /api/v1/earn/saving/products
       """
        data = "{\"currency\": \"BTC\"}"
        req = GetSavingsProductsReq.from_json(data)

    def test_get_savings_products_resp_model(self):
        """
        get_savings_products
        Get Savings Products
        /api/v1/earn/saving/products
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"2172\",\n            \"currency\": \"BTC\",\n            \"category\": \"DEMAND\",\n            \"type\": \"DEMAND\",\n            \"precision\": 8,\n            \"productUpperLimit\": \"480\",\n            \"productRemainAmount\": \"132.36153083\",\n            \"userUpperLimit\": \"20\",\n            \"userLowerLimit\": \"0.01\",\n            \"redeemPeriod\": 0,\n            \"lockStartTime\": 1644807600000,\n            \"lockEndTime\": null,\n            \"applyStartTime\": 1644807600000,\n            \"applyEndTime\": null,\n            \"returnRate\": \"0.00047208\",\n            \"incomeCurrency\": \"BTC\",\n            \"earlyRedeemSupported\": 0,\n            \"status\": \"ONGOING\",\n            \"redeemType\": \"MANUAL\",\n            \"incomeReleaseType\": \"DAILY\",\n            \"interestDate\": 1729267200000,\n            \"duration\": 0,\n            \"newUserOnly\": 0\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetSavingsProductsResp.from_dict(common_response.data)

    def test_get_promotion_products_req_model(self):
        """
       get_promotion_products
       Get Promotion Products
       /api/v1/earn/promotion/products
       """
        data = "{\"currency\": \"BTC\"}"
        req = GetPromotionProductsReq.from_json(data)

    def test_get_promotion_products_resp_model(self):
        """
        get_promotion_products
        Get Promotion Products
        /api/v1/earn/promotion/products
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"2685\",\n            \"currency\": \"BTC\",\n            \"category\": \"ACTIVITY\",\n            \"type\": \"TIME\",\n            \"precision\": 8,\n            \"productUpperLimit\": \"50\",\n            \"userUpperLimit\": \"1\",\n            \"userLowerLimit\": \"0.001\",\n            \"redeemPeriod\": 0,\n            \"lockStartTime\": 1702371601000,\n            \"lockEndTime\": 1729858405000,\n            \"applyStartTime\": 1702371600000,\n            \"applyEndTime\": null,\n            \"returnRate\": \"0.03\",\n            \"incomeCurrency\": \"BTC\",\n            \"earlyRedeemSupported\": 0,\n            \"productRemainAmount\": \"49.78203998\",\n            \"status\": \"ONGOING\",\n            \"redeemType\": \"TRANS_DEMAND\",\n            \"incomeReleaseType\": \"DAILY\",\n            \"interestDate\": 1729253605000,\n            \"duration\": 7,\n            \"newUserOnly\": 1\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetPromotionProductsResp.from_dict(common_response.data)

    def test_get_staking_products_req_model(self):
        """
       get_staking_products
       Get Staking Products
       /api/v1/earn/staking/products
       """
        data = "{\"currency\": \"BTC\"}"
        req = GetStakingProductsReq.from_json(data)

    def test_get_staking_products_resp_model(self):
        """
        get_staking_products
        Get Staking Products
        /api/v1/earn/staking/products
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"2535\",\n            \"currency\": \"STX\",\n            \"category\": \"STAKING\",\n            \"type\": \"DEMAND\",\n            \"precision\": 8,\n            \"productUpperLimit\": \"1000000\",\n            \"userUpperLimit\": \"10000\",\n            \"userLowerLimit\": \"1\",\n            \"redeemPeriod\": 14,\n            \"lockStartTime\": 1688614514000,\n            \"lockEndTime\": null,\n            \"applyStartTime\": 1688614512000,\n            \"applyEndTime\": null,\n            \"returnRate\": \"0.045\",\n            \"incomeCurrency\": \"BTC\",\n            \"earlyRedeemSupported\": 0,\n            \"productRemainAmount\": \"254032.90178701\",\n            \"status\": \"ONGOING\",\n            \"redeemType\": \"MANUAL\",\n            \"incomeReleaseType\": \"DAILY\",\n            \"interestDate\": 1729267200000,\n            \"duration\": 0,\n            \"newUserOnly\": 0\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetStakingProductsResp.from_dict(common_response.data)

    def test_get_kcs_staking_products_req_model(self):
        """
       get_kcs_staking_products
       Get KCS Staking Products
       /api/v1/earn/kcs-staking/products
       """
        data = "{\"currency\": \"BTC\"}"
        req = GetKcsStakingProductsReq.from_json(data)

    def test_get_kcs_staking_products_resp_model(self):
        """
        get_kcs_staking_products
        Get KCS Staking Products
        /api/v1/earn/kcs-staking/products
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"2611\",\n            \"currency\": \"KCS\",\n            \"category\": \"KCS_STAKING\",\n            \"type\": \"DEMAND\",\n            \"precision\": 8,\n            \"productUpperLimit\": \"100000000\",\n            \"userUpperLimit\": \"100000000\",\n            \"userLowerLimit\": \"1\",\n            \"redeemPeriod\": 3,\n            \"lockStartTime\": 1701252000000,\n            \"lockEndTime\": null,\n            \"applyStartTime\": 1701252000000,\n            \"applyEndTime\": null,\n            \"returnRate\": \"0.03471727\",\n            \"incomeCurrency\": \"KCS\",\n            \"earlyRedeemSupported\": 0,\n            \"productRemainAmount\": \"58065850.54998251\",\n            \"status\": \"ONGOING\",\n            \"redeemType\": \"MANUAL\",\n            \"incomeReleaseType\": \"DAILY\",\n            \"interestDate\": 1729267200000,\n            \"duration\": 0,\n            \"newUserOnly\": 0\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetKcsStakingProductsResp.from_dict(common_response.data)

    def test_get_eth_staking_products_req_model(self):
        """
       get_eth_staking_products
       Get ETH Staking Products
       /api/v1/earn/eth-staking/products
       """
        data = "{\"currency\": \"BTC\"}"
        req = GetEthStakingProductsReq.from_json(data)

    def test_get_eth_staking_products_resp_model(self):
        """
        get_eth_staking_products
        Get ETH Staking Products
        /api/v1/earn/eth-staking/products
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"ETH2\",\n            \"category\": \"ETH2\",\n            \"type\": \"DEMAND\",\n            \"precision\": 8,\n            \"currency\": \"ETH\",\n            \"incomeCurrency\": \"ETH2\",\n            \"returnRate\": \"0.028\",\n            \"userLowerLimit\": \"0.01\",\n            \"userUpperLimit\": \"8557.3597075\",\n            \"productUpperLimit\": \"8557.3597075\",\n            \"productRemainAmount\": \"8557.3597075\",\n            \"redeemPeriod\": 5,\n            \"redeemType\": \"MANUAL\",\n            \"incomeReleaseType\": \"DAILY\",\n            \"applyStartTime\": 1729255485000,\n            \"applyEndTime\": null,\n            \"lockStartTime\": 1729255485000,\n            \"lockEndTime\": null,\n            \"interestDate\": 1729267200000,\n            \"newUserOnly\": 0,\n            \"earlyRedeemSupported\": 0,\n            \"duration\": 0,\n            \"status\": \"ONGOING\"\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetEthStakingProductsResp.from_dict(common_response.data)

    def test_get_account_holding_req_model(self):
        """
       get_account_holding
       Get Account Holding
       /api/v1/earn/hold-assets
       """
        data = "{\"currency\": \"KCS\", \"productId\": \"example_string_default_value\", \"productCategory\": \"DEMAND\", \"currentPage\": 1, \"pageSize\": 10}"
        req = GetAccountHoldingReq.from_json(data)

    def test_get_account_holding_resp_model(self):
        """
        get_account_holding
        Get Account Holding
        /api/v1/earn/hold-assets
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"currentPage\": 1,\n        \"pageSize\": 15,\n        \"items\": [\n            {\n                \"orderId\": \"2767291\",\n                \"productId\": \"2611\",\n                \"productCategory\": \"KCS_STAKING\",\n                \"productType\": \"DEMAND\",\n                \"currency\": \"KCS\",\n                \"incomeCurrency\": \"KCS\",\n                \"returnRate\": \"0.03471727\",\n                \"holdAmount\": \"1\",\n                \"redeemedAmount\": \"0\",\n                \"redeemingAmount\": \"1\",\n                \"lockStartTime\": 1701252000000,\n                \"lockEndTime\": null,\n                \"purchaseTime\": 1729257513000,\n                \"redeemPeriod\": 3,\n                \"status\": \"REDEEMING\",\n                \"earlyRedeemSupported\": 0\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetAccountHoldingResp.from_dict(common_response.data)
