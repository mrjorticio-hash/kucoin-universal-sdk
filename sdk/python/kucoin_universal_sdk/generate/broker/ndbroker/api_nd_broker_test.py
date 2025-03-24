import unittest
from .model_add_sub_account_api_req import AddSubAccountApiReq
from .model_add_sub_account_api_resp import AddSubAccountApiResp
from .model_add_sub_account_req import AddSubAccountReq
from .model_add_sub_account_resp import AddSubAccountResp
from .model_delete_sub_account_api_req import DeleteSubAccountApiReq
from .model_delete_sub_account_api_resp import DeleteSubAccountApiResp
from .model_get_broker_info_req import GetBrokerInfoReq
from .model_get_broker_info_resp import GetBrokerInfoResp
from .model_get_deposit_detail_req import GetDepositDetailReq
from .model_get_deposit_detail_resp import GetDepositDetailResp
from .model_get_deposit_list_req import GetDepositListReq
from .model_get_deposit_list_resp import GetDepositListResp
from .model_get_rebase_req import GetRebaseReq
from .model_get_rebase_resp import GetRebaseResp
from .model_get_sub_account_api_req import GetSubAccountApiReq
from .model_get_sub_account_api_resp import GetSubAccountApiResp
from .model_get_sub_account_req import GetSubAccountReq
from .model_get_sub_account_resp import GetSubAccountResp
from .model_get_transfer_history_req import GetTransferHistoryReq
from .model_get_transfer_history_resp import GetTransferHistoryResp
from .model_get_withdraw_detail_req import GetWithdrawDetailReq
from .model_get_withdraw_detail_resp import GetWithdrawDetailResp
from .model_modify_sub_account_api_req import ModifySubAccountApiReq
from .model_modify_sub_account_api_resp import ModifySubAccountApiResp
from .model_transfer_req import TransferReq
from .model_transfer_resp import TransferResp
from kucoin_universal_sdk.model.common import RestResponse


class NDBrokerAPITest(unittest.TestCase):

    def test_get_broker_info_req_model(self):
        """
       get_broker_info
       Get Broker Info
       /api/v1/broker/nd/info
       """
        data = "{\"begin\": \"20240510\", \"end\": \"20241010\", \"tradeType\": \"1\"}"
        req = GetBrokerInfoReq.from_json(data)

    def test_get_broker_info_resp_model(self):
        """
        get_broker_info
        Get Broker Info
        /api/v1/broker/nd/info
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"accountSize\": 0,\n        \"maxAccountSize\": null,\n        \"level\": 0\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetBrokerInfoResp.from_dict(common_response.data)

    def test_add_sub_account_req_model(self):
        """
       add_sub_account
       Add sub-account
       /api/v1/broker/nd/account
       """
        data = "{\"accountName\": \"Account1\"}"
        req = AddSubAccountReq.from_json(data)

    def test_add_sub_account_resp_model(self):
        """
        add_sub_account
        Add sub-account
        /api/v1/broker/nd/account
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"accountName\": \"Account15\",\n        \"uid\": \"226383154\",\n        \"createdAt\": 1729819381908,\n        \"level\": 0\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = AddSubAccountResp.from_dict(common_response.data)

    def test_get_sub_account_req_model(self):
        """
       get_sub_account
       Get sub-account
       /api/v1/broker/nd/account
       """
        data = "{\"uid\": \"226383154\", \"currentPage\": 1, \"pageSize\": 20}"
        req = GetSubAccountReq.from_json(data)

    def test_get_sub_account_resp_model(self):
        """
        get_sub_account
        Get sub-account
        /api/v1/broker/nd/account
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 20,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"accountName\": \"Account15\",\n                \"uid\": \"226383154\",\n                \"createdAt\": 1729819382000,\n                \"level\": 0\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetSubAccountResp.from_dict(common_response.data)

    def test_add_sub_account_api_req_model(self):
        """
       add_sub_account_api
       Add sub-account API
       /api/v1/broker/nd/account/apikey
       """
        data = "{\"uid\": \"226383154\", \"passphrase\": \"11223344\", \"ipWhitelist\": [\"127.0.0.1\", \"123.123.123.123\"], \"permissions\": [\"general\", \"spot\"], \"label\": \"This is remarks\"}"
        req = AddSubAccountApiReq.from_json(data)

    def test_add_sub_account_api_resp_model(self):
        """
        add_sub_account_api
        Add sub-account API
        /api/v1/broker/nd/account/apikey
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"uid\": \"226383154\",\n        \"label\": \"This is remarks\",\n        \"apiKey\": \"671afb36cee20f00015cfaf1\",\n        \"secretKey\": \"d694df2******5bae05b96\",\n        \"apiVersion\": 3,\n        \"permissions\": [\n            \"General\",\n            \"Spot\"\n        ],\n        \"ipWhitelist\": [\n            \"127.0.0.1\",\n            \"123.123.123.123\"\n        ],\n        \"createdAt\": 1729821494000\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = AddSubAccountApiResp.from_dict(common_response.data)

    def test_get_sub_account_api_req_model(self):
        """
       get_sub_account_api
       Get sub-account API
       /api/v1/broker/nd/account/apikey
       """
        data = "{\"uid\": \"226383154\", \"apiKey\": \"671afb36cee20f00015cfaf1\"}"
        req = GetSubAccountApiReq.from_json(data)

    def test_get_sub_account_api_resp_model(self):
        """
        get_sub_account_api
        Get sub-account API
        /api/v1/broker/nd/account/apikey
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"uid\": \"226383154\",\n            \"label\": \"This is remarks\",\n            \"apiKey\": \"671afb36cee20f00015cfaf1\",\n            \"apiVersion\": 3,\n            \"permissions\": [\n                \"General\",\n                \"Spot\"\n            ],\n            \"ipWhitelist\": [\n                \"127.**.1\",\n                \"203.**.154\"\n            ],\n            \"createdAt\": 1729821494000\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetSubAccountApiResp.from_dict(common_response.data)

    def test_modify_sub_account_api_req_model(self):
        """
       modify_sub_account_api
       Modify sub-account API
       /api/v1/broker/nd/account/update-apikey
       """
        data = "{\"uid\": \"226383154\", \"apiKey\": \"671afb36cee20f00015cfaf1\", \"ipWhitelist\": [\"127.0.0.1\", \"123.123.123.123\"], \"permissions\": [\"general\", \"spot\"], \"label\": \"This is remarks\"}"
        req = ModifySubAccountApiReq.from_json(data)

    def test_modify_sub_account_api_resp_model(self):
        """
        modify_sub_account_api
        Modify sub-account API
        /api/v1/broker/nd/account/update-apikey
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"uid\": \"226383154\",\n        \"label\": \"This is remarks\",\n        \"apiKey\": \"671afb36cee20f00015cfaf1\",\n        \"apiVersion\": 3,\n        \"permissions\": [\n            \"General\",\n            \"Spot\"\n        ],\n        \"ipWhitelist\": [\n            \"127.**.1\",\n            \"123.**.123\"\n        ],\n        \"createdAt\": 1729821494000\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = ModifySubAccountApiResp.from_dict(common_response.data)

    def test_delete_sub_account_api_req_model(self):
        """
       delete_sub_account_api
       Delete sub-account API
       /api/v1/broker/nd/account/apikey
       """
        data = "{\"uid\": \"226383154\", \"apiKey\": \"671afb36cee20f00015cfaf1\"}"
        req = DeleteSubAccountApiReq.from_json(data)

    def test_delete_sub_account_api_resp_model(self):
        """
        delete_sub_account_api
        Delete sub-account API
        /api/v1/broker/nd/account/apikey
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": true\n}"
        common_response = RestResponse.from_json(data)
        resp = DeleteSubAccountApiResp.from_dict(common_response.data)

    def test_transfer_req_model(self):
        """
       transfer
       Transfer
       /api/v1/broker/nd/transfer
       """
        data = "{\"currency\": \"USDT\", \"amount\": \"1\", \"clientOid\": \"e6c24d23-6bc2-401b-bf9e-55e2daddfbc1\", \"direction\": \"OUT\", \"accountType\": \"MAIN\", \"specialUid\": \"226383154\", \"specialAccountType\": \"MAIN\"}"
        req = TransferReq.from_json(data)

    def test_transfer_resp_model(self):
        """
        transfer
        Transfer
        /api/v1/broker/nd/transfer
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"671b4600c1e3dd000726866d\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = TransferResp.from_dict(common_response.data)

    def test_get_transfer_history_req_model(self):
        """
       get_transfer_history
       Get Transfer History
       /api/v3/broker/nd/transfer/detail
       """
        data = "{\"orderId\": \"671b4600c1e3dd000726866d\"}"
        req = GetTransferHistoryReq.from_json(data)

    def test_get_transfer_history_resp_model(self):
        """
        get_transfer_history
        Get Transfer History
        /api/v3/broker/nd/transfer/detail
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"671b4600c1e3dd000726866d\",\n        \"currency\": \"USDT\",\n        \"amount\": \"1\",\n        \"fromUid\": 165111215,\n        \"fromAccountType\": \"MAIN\",\n        \"fromAccountTag\": \"DEFAULT\",\n        \"toUid\": 226383154,\n        \"toAccountType\": \"MAIN\",\n        \"toAccountTag\": \"DEFAULT\",\n        \"status\": \"SUCCESS\",\n        \"reason\": null,\n        \"createdAt\": 1729840640000\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetTransferHistoryResp.from_dict(common_response.data)

    def test_get_deposit_list_req_model(self):
        """
       get_deposit_list
       Get Deposit List
       /api/v1/asset/ndbroker/deposit/list
       """
        data = "{\"currency\": \"USDT\", \"status\": \"SUCCESS\", \"hash\": \"example_string_default_value\", \"startTimestamp\": 123456, \"endTimestamp\": 123456, \"limit\": 100}"
        req = GetDepositListReq.from_json(data)

    def test_get_deposit_list_resp_model(self):
        """
        get_deposit_list
        Get Deposit List
        /api/v1/asset/ndbroker/deposit/list
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"uid\": 165111215,\n            \"hash\": \"6724e363a492800007ec602b\",\n            \"address\": \"xxxxxxx@gmail.com\",\n            \"memo\": \"\",\n            \"amount\": \"3.0\",\n            \"fee\": \"0.0\",\n            \"currency\": \"USDT\",\n            \"isInner\": true,\n            \"walletTxId\": \"bbbbbbbbb\",\n            \"status\": \"SUCCESS\",\n            \"chain\": \"\",\n            \"remark\": \"\",\n            \"createdAt\": 1730470760000,\n            \"updatedAt\": 1730470760000\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetDepositListResp.from_dict(common_response.data)

    def test_get_deposit_detail_req_model(self):
        """
       get_deposit_detail
       Get Deposit Detail
       /api/v3/broker/nd/deposit/detail
       """
        data = "{\"currency\": \"USDT\", \"hash\": \"30bb0e0b***4156c5188\"}"
        req = GetDepositDetailReq.from_json(data)

    def test_get_deposit_detail_resp_model(self):
        """
        get_deposit_detail
        Get Deposit Detail
        /api/v3/broker/nd/deposit/detail
        """
        data = "{\n  \"data\" : {\n    \"chain\" : \"trx\",\n    \"hash\" : \"30bb0e0b***4156c5188\",\n    \"walletTxId\" : \"30bb0***610d1030f\",\n    \"uid\" : 201496341,\n    \"updatedAt\" : 1713429174000,\n    \"amount\" : \"8.5\",\n    \"memo\" : \"\",\n    \"fee\" : \"0.0\",\n    \"address\" : \"THLPzUrbd1o***vP7d\",\n    \"remark\" : \"Deposit\",\n    \"isInner\" : false,\n    \"currency\" : \"USDT\",\n    \"status\" : \"SUCCESS\",\n    \"createdAt\" : 1713429173000\n  },\n  \"code\" : \"200000\"\n}"
        common_response = RestResponse.from_json(data)
        resp = GetDepositDetailResp.from_dict(common_response.data)

    def test_get_withdraw_detail_req_model(self):
        """
       get_withdraw_detail
       Get Withdraw Detail
       /api/v3/broker/nd/withdraw/detail
       """
        data = "{\"withdrawalId\": \"66617a2***3c9a\"}"
        req = GetWithdrawDetailReq.from_json(data)

    def test_get_withdraw_detail_resp_model(self):
        """
        get_withdraw_detail
        Get Withdraw Detail
        /api/v3/broker/nd/withdraw/detail
        """
        data = "{\n    \"data\": {\n        \"id\": \"66617a2***3c9a\",\n        \"chain\": \"ton\",\n        \"walletTxId\": \"AJ***eRI=\",\n        \"uid\": 157267400,\n        \"amount\": \"1.00000000\",\n        \"memo\": \"7025734\",\n        \"fee\": \"0.00000000\",\n        \"address\": \"EQDn***dKbGzr\",\n        \"remark\": \"\",\n        \"isInner\": false,\n        \"currency\": \"USDT\",\n        \"status\": \"SUCCESS\",\n        \"createdAt\": 1717664288000,\n        \"updatedAt\": 1717664375000\n    },\n    \"code\": \"200000\"\n}"
        common_response = RestResponse.from_json(data)
        resp = GetWithdrawDetailResp.from_dict(common_response.data)

    def test_get_rebase_req_model(self):
        """
       get_rebase
       Get Broker Rebate
       /api/v1/broker/nd/rebase/download
       """
        data = "{\"begin\": \"20240610\", \"end\": \"20241010\", \"tradeType\": \"1\"}"
        req = GetRebaseReq.from_json(data)

    def test_get_rebase_resp_model(self):
        """
        get_rebase
        Get Broker Rebate
        /api/v1/broker/nd/rebase/download
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"url\": \"https://kc-v2-promotion.s3.ap-northeast-1.amazonaws.com/broker/671aec522593f600019766d0_file.csv?X-Amz-Security-Token=IQo*********2cd90f14efb\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetRebaseResp.from_dict(common_response.data)
