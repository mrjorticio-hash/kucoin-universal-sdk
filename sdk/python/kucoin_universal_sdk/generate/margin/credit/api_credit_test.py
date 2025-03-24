import unittest
from .model_get_loan_market_interest_rate_req import GetLoanMarketInterestRateReq
from .model_get_loan_market_interest_rate_resp import GetLoanMarketInterestRateResp
from .model_get_loan_market_req import GetLoanMarketReq
from .model_get_loan_market_resp import GetLoanMarketResp
from .model_get_purchase_orders_req import GetPurchaseOrdersReq
from .model_get_purchase_orders_resp import GetPurchaseOrdersResp
from .model_get_redeem_orders_req import GetRedeemOrdersReq
from .model_get_redeem_orders_resp import GetRedeemOrdersResp
from .model_modify_purchase_req import ModifyPurchaseReq
from .model_modify_purchase_resp import ModifyPurchaseResp
from .model_purchase_req import PurchaseReq
from .model_purchase_resp import PurchaseResp
from .model_redeem_req import RedeemReq
from .model_redeem_resp import RedeemResp
from kucoin_universal_sdk.model.common import RestResponse


class CreditAPITest(unittest.TestCase):

    def test_get_loan_market_req_model(self):
        """
       get_loan_market
       Get Loan Market
       /api/v3/project/list
       """
        data = "{\"currency\": \"BTC\"}"
        req = GetLoanMarketReq.from_json(data)

    def test_get_loan_market_resp_model(self):
        """
        get_loan_market
        Get Loan Market
        /api/v3/project/list
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"currency\": \"BTC\",\n            \"purchaseEnable\": true,\n            \"redeemEnable\": true,\n            \"increment\": \"0.00000001\",\n            \"minPurchaseSize\": \"0.001\",\n            \"maxPurchaseSize\": \"40\",\n            \"interestIncrement\": \"0.0001\",\n            \"minInterestRate\": \"0.005\",\n            \"marketInterestRate\": \"0.005\",\n            \"maxInterestRate\": \"0.32\",\n            \"autoPurchaseEnable\": false\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetLoanMarketResp.from_dict(common_response.data)

    def test_get_loan_market_interest_rate_req_model(self):
        """
       get_loan_market_interest_rate
       Get Loan Market Interest Rate
       /api/v3/project/marketInterestRate
       """
        data = "{\"currency\": \"BTC\"}"
        req = GetLoanMarketInterestRateReq.from_json(data)

    def test_get_loan_market_interest_rate_resp_model(self):
        """
        get_loan_market_interest_rate
        Get Loan Market Interest Rate
        /api/v3/project/marketInterestRate
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"time\": \"202410170000\",\n            \"marketInterestRate\": \"0.005\"\n        },\n        {\n            \"time\": \"202410170100\",\n            \"marketInterestRate\": \"0.005\"\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetLoanMarketInterestRateResp.from_dict(common_response.data)

    def test_purchase_req_model(self):
        """
       purchase
       Purchase
       /api/v3/purchase
       """
        data = "{\"currency\": \"BTC\", \"size\": \"0.001\", \"interestRate\": \"0.1\"}"
        req = PurchaseReq.from_json(data)

    def test_purchase_resp_model(self):
        """
        purchase
        Purchase
        /api/v3/purchase
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderNo\": \"671bafa804c26d000773c533\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = PurchaseResp.from_dict(common_response.data)

    def test_modify_purchase_req_model(self):
        """
       modify_purchase
       Modify Purchase
       /api/v3/lend/purchase/update
       """
        data = "{\"currency\": \"BTC\", \"purchaseOrderNo\": \"671bafa804c26d000773c533\", \"interestRate\": \"0.09\"}"
        req = ModifyPurchaseReq.from_json(data)

    def test_modify_purchase_resp_model(self):
        """
        modify_purchase
        Modify Purchase
        /api/v3/lend/purchase/update
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": null\n}"
        common_response = RestResponse.from_json(data)
        resp = ModifyPurchaseResp.from_dict(common_response.data)

    def test_get_purchase_orders_req_model(self):
        """
       get_purchase_orders
       Get Purchase Orders
       /api/v3/purchase/orders
       """
        data = "{\"status\": \"DONE\", \"currency\": \"BTC\", \"purchaseOrderNo\": \"example_string_default_value\", \"currentPage\": 1, \"pageSize\": 50}"
        req = GetPurchaseOrdersReq.from_json(data)

    def test_get_purchase_orders_resp_model(self):
        """
        get_purchase_orders
        Get Purchase Orders
        /api/v3/purchase/orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 10,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"currency\": \"BTC\",\n                \"purchaseOrderNo\": \"671bb15a3b3f930007880bae\",\n                \"purchaseSize\": \"0.001\",\n                \"matchSize\": \"0\",\n                \"interestRate\": \"0.1\",\n                \"incomeSize\": \"0\",\n                \"applyTime\": 1729868122172,\n                \"status\": \"PENDING\"\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetPurchaseOrdersResp.from_dict(common_response.data)

    def test_redeem_req_model(self):
        """
       redeem
       Redeem
       /api/v3/redeem
       """
        data = "{\"currency\": \"BTC\", \"size\": \"0.001\", \"purchaseOrderNo\": \"671bafa804c26d000773c533\"}"
        req = RedeemReq.from_json(data)

    def test_redeem_resp_model(self):
        """
        redeem
        Redeem
        /api/v3/redeem
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderNo\": \"671bafa804c26d000773c533\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = RedeemResp.from_dict(common_response.data)

    def test_get_redeem_orders_req_model(self):
        """
       get_redeem_orders
       Get Redeem Orders
       /api/v3/redeem/orders
       """
        data = "{\"status\": \"DONE\", \"currency\": \"BTC\", \"redeemOrderNo\": \"example_string_default_value\", \"currentPage\": 1, \"pageSize\": 50}"
        req = GetRedeemOrdersReq.from_json(data)

    def test_get_redeem_orders_resp_model(self):
        """
        get_redeem_orders
        Get Redeem Orders
        /api/v3/redeem/orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 10,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"currency\": \"BTC\",\n                \"purchaseOrderNo\": \"671bafa804c26d000773c533\",\n                \"redeemOrderNo\": \"671bb01004c26d000773c55c\",\n                \"redeemSize\": \"0.001\",\n                \"receiptSize\": \"0.001\",\n                \"applyTime\": null,\n                \"status\": \"DONE\"\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetRedeemOrdersResp.from_dict(common_response.data)
