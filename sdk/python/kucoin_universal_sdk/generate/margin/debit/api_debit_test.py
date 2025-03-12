import unittest
from .model_borrow_req import BorrowReq
from .model_borrow_resp import BorrowResp
from .model_get_borrow_history_req import GetBorrowHistoryReq
from .model_get_borrow_history_resp import GetBorrowHistoryResp
from .model_get_interest_history_req import GetInterestHistoryReq
from .model_get_interest_history_resp import GetInterestHistoryResp
from .model_get_repay_history_req import GetRepayHistoryReq
from .model_get_repay_history_resp import GetRepayHistoryResp
from .model_modify_leverage_req import ModifyLeverageReq
from .model_modify_leverage_resp import ModifyLeverageResp
from .model_repay_req import RepayReq
from .model_repay_resp import RepayResp
from kucoin_universal_sdk.model.common import RestResponse


class DebitAPITest(unittest.TestCase):

    def test_borrow_req_model(self):
        """
       borrow
       Borrow
       /api/v3/margin/borrow
       """
        data = "{\"currency\": \"USDT\", \"size\": 10, \"timeInForce\": \"FOK\", \"isIsolated\": false, \"isHf\": false}"
        req = BorrowReq.from_json(data)

    def test_borrow_resp_model(self):
        """
        borrow
        Borrow
        /api/v3/margin/borrow
        """
        data = "{\"code\":\"200000\",\"data\":{\"orderNo\":\"67187162c0d6990007717b15\",\"actualSize\":\"10\"}}"
        common_response = RestResponse.from_json(data)
        resp = BorrowResp.from_dict(common_response.data)

    def test_get_borrow_history_req_model(self):
        """
       get_borrow_history
       Get Borrow History
       /api/v3/margin/borrow
       """
        data = "{\"currency\": \"BTC\", \"isIsolated\": true, \"symbol\": \"BTC-USDT\", \"orderNo\": \"example_string_default_value\", \"startTime\": 123456, \"endTime\": 123456, \"currentPage\": 1, \"pageSize\": 50}"
        req = GetBorrowHistoryReq.from_json(data)

    def test_get_borrow_history_resp_model(self):
        """
        get_borrow_history
        Get Borrow History
        /api/v3/margin/borrow
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"timestamp\": 1729657580449,\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 2,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"orderNo\": \"67187162c0d6990007717b15\",\n                \"symbol\": null,\n                \"currency\": \"USDT\",\n                \"size\": \"10\",\n                \"actualSize\": \"10\",\n                \"status\": \"SUCCESS\",\n                \"createdTime\": 1729655138000\n            },\n            {\n                \"orderNo\": \"67187155b088e70007149585\",\n                \"symbol\": null,\n                \"currency\": \"USDT\",\n                \"size\": \"0.1\",\n                \"actualSize\": \"0\",\n                \"status\": \"FAILED\",\n                \"createdTime\": 1729655125000\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetBorrowHistoryResp.from_dict(common_response.data)

    def test_repay_req_model(self):
        """
       repay
       Repay
       /api/v3/margin/repay
       """
        data = "{\"currency\": \"USDT\", \"size\": 10}"
        req = RepayReq.from_json(data)

    def test_repay_resp_model(self):
        """
        repay
        Repay
        /api/v3/margin/repay
        """
        data = "{\"code\":\"200000\",\"data\":{\"timestamp\":1729655606816,\"orderNo\":\"671873361d5bd400075096ad\",\"actualSize\":\"10\"}}"
        common_response = RestResponse.from_json(data)
        resp = RepayResp.from_dict(common_response.data)

    def test_get_repay_history_req_model(self):
        """
       get_repay_history
       Get Repay History
       /api/v3/margin/repay
       """
        data = "{\"currency\": \"BTC\", \"isIsolated\": true, \"symbol\": \"BTC-USDT\", \"orderNo\": \"example_string_default_value\", \"startTime\": 123456, \"endTime\": 123456, \"currentPage\": 1, \"pageSize\": 50}"
        req = GetRepayHistoryReq.from_json(data)

    def test_get_repay_history_resp_model(self):
        """
        get_repay_history
        Get Repay History
        /api/v3/margin/repay
        """
        data = "{\"code\":\"200000\",\"data\":{\"timestamp\":1729663471891,\"currentPage\":1,\"pageSize\":50,\"totalNum\":1,\"totalPage\":1,\"items\":[{\"orderNo\":\"671873361d5bd400075096ad\",\"symbol\":null,\"currency\":\"USDT\",\"size\":\"10\",\"principal\":\"9.99986518\",\"interest\":\"0.00013482\",\"status\":\"SUCCESS\",\"createdTime\":1729655606000}]}}"
        common_response = RestResponse.from_json(data)
        resp = GetRepayHistoryResp.from_dict(common_response.data)

    def test_get_interest_history_req_model(self):
        """
       get_interest_history
       Get Interest History.
       /api/v3/margin/interest
       """
        data = "{\"currency\": \"BTC\", \"isIsolated\": true, \"symbol\": \"BTC-USDT\", \"startTime\": 123456, \"endTime\": 123456, \"currentPage\": 1, \"pageSize\": 50}"
        req = GetInterestHistoryReq.from_json(data)

    def test_get_interest_history_resp_model(self):
        """
        get_interest_history
        Get Interest History.
        /api/v3/margin/interest
        """
        data = "{\"code\":\"200000\",\"data\":{\"timestamp\":1729665170701,\"currentPage\":1,\"pageSize\":50,\"totalNum\":3,\"totalPage\":1,\"items\":[{\"currency\":\"USDT\",\"dayRatio\":\"0.000296\",\"interestAmount\":\"0.00000001\",\"createdTime\":1729663213375},{\"currency\":\"USDT\",\"dayRatio\":\"0.000296\",\"interestAmount\":\"0.00000001\",\"createdTime\":1729659618802},{\"currency\":\"USDT\",\"dayRatio\":\"0.000296\",\"interestAmount\":\"0.00000001\",\"createdTime\":1729656028077}]}}"
        common_response = RestResponse.from_json(data)
        resp = GetInterestHistoryResp.from_dict(common_response.data)

    def test_modify_leverage_req_model(self):
        """
       modify_leverage
       Modify Leverage
       /api/v3/position/update-user-leverage
       """
        data = "{\"leverage\": \"5\"}"
        req = ModifyLeverageReq.from_json(data)

    def test_modify_leverage_resp_model(self):
        """
        modify_leverage
        Modify Leverage
        /api/v3/position/update-user-leverage
        """
        data = "{\"code\":\"200000\",\"data\":null}"
        common_response = RestResponse.from_json(data)
        resp = ModifyLeverageResp.from_dict(common_response.data)
