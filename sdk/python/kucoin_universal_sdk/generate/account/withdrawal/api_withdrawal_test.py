import unittest
from .model_cancel_withdrawal_req import CancelWithdrawalReq
from .model_cancel_withdrawal_resp import CancelWithdrawalResp
from .model_get_withdrawal_history_by_id_req import GetWithdrawalHistoryByIdReq
from .model_get_withdrawal_history_by_id_resp import GetWithdrawalHistoryByIdResp
from .model_get_withdrawal_history_old_req import GetWithdrawalHistoryOldReq
from .model_get_withdrawal_history_old_resp import GetWithdrawalHistoryOldResp
from .model_get_withdrawal_history_req import GetWithdrawalHistoryReq
from .model_get_withdrawal_history_resp import GetWithdrawalHistoryResp
from .model_get_withdrawal_quotas_req import GetWithdrawalQuotasReq
from .model_get_withdrawal_quotas_resp import GetWithdrawalQuotasResp
from .model_withdrawal_v1_req import WithdrawalV1Req
from .model_withdrawal_v1_resp import WithdrawalV1Resp
from .model_withdrawal_v3_req import WithdrawalV3Req
from .model_withdrawal_v3_resp import WithdrawalV3Resp
from typing_extensions import deprecated
from kucoin_universal_sdk.model.common import RestResponse


class WithdrawalAPITest(unittest.TestCase):

    def test_get_withdrawal_quotas_req_model(self):
        """
       get_withdrawal_quotas
       Get Withdrawal Quotas
       /api/v1/withdrawals/quotas
       """
        data = "{\"currency\": \"BTC\", \"chain\": \"eth\"}"
        req = GetWithdrawalQuotasReq.from_json(data)

    def test_get_withdrawal_quotas_resp_model(self):
        """
        get_withdrawal_quotas
        Get Withdrawal Quotas
        /api/v1/withdrawals/quotas
        """
        data = "{\"code\":\"200000\",\"data\":{\"currency\":\"BTC\",\"limitBTCAmount\":\"15.79590095\",\"usedBTCAmount\":\"0.00000000\",\"quotaCurrency\":\"USDT\",\"limitQuotaCurrencyAmount\":\"999999.00000000\",\"usedQuotaCurrencyAmount\":\"0\",\"remainAmount\":\"15.79590095\",\"availableAmount\":\"0\",\"withdrawMinFee\":\"0.0005\",\"innerWithdrawMinFee\":\"0\",\"withdrawMinSize\":\"0.001\",\"isWithdrawEnabled\":true,\"precision\":8,\"chain\":\"BTC\",\"reason\":null,\"lockedAmount\":\"0\"}}"
        common_response = RestResponse.from_json(data)
        resp = GetWithdrawalQuotasResp.from_dict(common_response.data)

    def test_withdrawal_v3_req_model(self):
        """
       withdrawal_v3
       Withdraw (V3)
       /api/v3/withdrawals
       """
        data = "{\"currency\": \"USDT\", \"toAddress\": \"TKFRQXSDcY****GmLrjJggwX8\", \"amount\": \"3\", \"withdrawType\": \"ADDRESS\", \"chain\": \"trx\", \"isInner\": true, \"remark\": \"this is Remark\"}"
        req = WithdrawalV3Req.from_json(data)

    def test_withdrawal_v3_resp_model(self):
        """
        withdrawal_v3
        Withdraw (V3)
        /api/v3/withdrawals
        """
        data = "{\"code\":\"200000\",\"data\":{\"withdrawalId\":\"670deec84d64da0007d7c946\"}}"
        common_response = RestResponse.from_json(data)
        resp = WithdrawalV3Resp.from_dict(common_response.data)

    def test_cancel_withdrawal_req_model(self):
        """
       cancel_withdrawal
       Cancel Withdrawal
       /api/v1/withdrawals/{withdrawalId}
       """
        data = "{\"withdrawalId\": \"670b891f7e0f440007730692\"}"
        req = CancelWithdrawalReq.from_json(data)

    def test_cancel_withdrawal_resp_model(self):
        """
        cancel_withdrawal
        Cancel Withdrawal
        /api/v1/withdrawals/{withdrawalId}
        """
        data = "{\"code\":\"200000\",\"data\":null}"
        common_response = RestResponse.from_json(data)
        resp = CancelWithdrawalResp.from_dict(common_response.data)

    def test_get_withdrawal_history_req_model(self):
        """
       get_withdrawal_history
       Get Withdrawal History
       /api/v1/withdrawals
       """
        data = "{\"currency\": \"BTC\", \"status\": \"SUCCESS\", \"startAt\": 1728663338000, \"endAt\": 1728692138000, \"currentPage\": 1, \"pageSize\": 50}"
        req = GetWithdrawalHistoryReq.from_json(data)

    def test_get_withdrawal_history_resp_model(self):
        """
        get_withdrawal_history
        Get Withdrawal History
        /api/v1/withdrawals
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 5,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"currency\": \"USDT\",\n                \"chain\": \"\",\n                \"status\": \"SUCCESS\",\n                \"address\": \"a435*****@gmail.com\",\n                \"memo\": \"\",\n                \"isInner\": true,\n                \"amount\": \"1.00000000\",\n                \"fee\": \"0.00000000\",\n                \"walletTxId\": null,\n                \"createdAt\": 1728555875000,\n                \"updatedAt\": 1728555875000,\n                \"remark\": \"\",\n                \"arrears\": false\n            },\n            {\n                \"currency\": \"USDT\",\n                \"chain\": \"trx\",\n                \"status\": \"SUCCESS\",\n                \"address\": \"TSv3L1fS7******X4nLP6rqNxYz\",\n                \"memo\": \"\",\n                \"isInner\": true,\n                \"amount\": \"6.00000000\",\n                \"fee\": \"0.00000000\",\n                \"walletTxId\": null,\n                \"createdAt\": 1721730920000,\n                \"updatedAt\": 1721730920000,\n                \"remark\": \"\",\n                \"arrears\": false\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetWithdrawalHistoryResp.from_dict(common_response.data)

    def test_get_withdrawal_history_by_id_req_model(self):
        """
       get_withdrawal_history_by_id
       Get Withdrawal History By ID
       /api/v1/withdrawals/{withdrawalId}
       """
        data = "{\"withdrawalId\": \"67e6515f7960ba0007b42025\"}"
        req = GetWithdrawalHistoryByIdReq.from_json(data)

    def test_get_withdrawal_history_by_id_resp_model(self):
        """
        get_withdrawal_history_by_id
        Get Withdrawal History By ID
        /api/v1/withdrawals/{withdrawalId}
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"id\": \"67e6515f7960ba0007b42025\",\n        \"uid\": 165111215,\n        \"currency\": \"USDT\",\n        \"chainId\": \"trx\",\n        \"chainName\": \"TRC20\",\n        \"currencyName\": \"USDT\",\n        \"status\": \"SUCCESS\",\n        \"failureReason\": \"\",\n        \"failureReasonMsg\": null,\n        \"address\": \"TKFRQXSDcY4kd3QLzw7uK16GmLrjJggwX8\",\n        \"memo\": \"\",\n        \"isInner\": true,\n        \"amount\": \"3.00000000\",\n        \"fee\": \"0.00000000\",\n        \"walletTxId\": null,\n        \"addressRemark\": null,\n        \"remark\": \"this is Remark\",\n        \"createdAt\": 1743147359000,\n        \"cancelType\": \"NON_CANCELABLE\",\n        \"taxes\": null,\n        \"taxDescription\": null,\n        \"returnStatus\": \"NOT_RETURN\",\n        \"returnAmount\": null,\n        \"returnCurrency\": \"KCS\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetWithdrawalHistoryByIdResp.from_dict(common_response.data)

    def test_get_withdrawal_history_old_req_model(self):
        """
       get_withdrawal_history_old
       Get Withdrawal History - Old
       /api/v1/hist-withdrawals
       """
        data = "{\"currency\": \"BTC\", \"status\": \"SUCCESS\", \"startAt\": 1728663338000, \"endAt\": 1728692138000, \"currentPage\": 1, \"pageSize\": 50}"
        req = GetWithdrawalHistoryOldReq.from_json(data)

    def test_get_withdrawal_history_old_resp_model(self):
        """
        get_withdrawal_history_old
        Get Withdrawal History - Old
        /api/v1/hist-withdrawals
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"currency\": \"BTC\",\n                \"createAt\": 1526723468,\n                \"amount\": \"0.534\",\n                \"address\": \"33xW37ZSW4tQvg443Pc7NLCAs167Yc2XUV\",\n                \"walletTxId\": \"aeacea864c020acf58e51606169240e96774838dcd4f7ce48acf38e3651323f4\",\n                \"isInner\": false,\n                \"status\": \"SUCCESS\"\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetWithdrawalHistoryOldResp.from_dict(common_response.data)

    def test_withdrawal_v1_req_model(self):
        """
       withdrawal_v1
       Withdraw - V1
       /api/v1/withdrawals
       """
        data = "{\"currency\": \"USDT\", \"address\": \"TKFRQXSDc****16GmLrjJggwX8\", \"amount\": 3, \"chain\": \"trx\", \"isInner\": true}"
        req = WithdrawalV1Req.from_json(data)

    def test_withdrawal_v1_resp_model(self):
        """
        withdrawal_v1
        Withdraw - V1
        /api/v1/withdrawals
        """
        data = "{\"code\":\"200000\",\"data\":{\"withdrawalId\":\"670a973cf07b3800070e216c\"}}"
        common_response = RestResponse.from_json(data)
        resp = WithdrawalV1Resp.from_dict(common_response.data)
