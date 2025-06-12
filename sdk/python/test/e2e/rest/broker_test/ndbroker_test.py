import os
import unittest
import uuid

from kucoin_universal_sdk.api.client import DefaultClient
from kucoin_universal_sdk.extension.interceptor.logging import LoggingInterceptor
from kucoin_universal_sdk.generate.broker.ndbroker import SubmitKycReqBuilder, SubmitKycReq, GetKycStatusReqBuilder, \
    GetKycStatusListReqBuilder
from kucoin_universal_sdk.generate.broker.ndbroker.model_add_sub_account_api_req import AddSubAccountApiReqBuilder, \
    AddSubAccountApiReq
from kucoin_universal_sdk.generate.broker.ndbroker.model_add_sub_account_req import AddSubAccountReqBuilder
from kucoin_universal_sdk.generate.broker.ndbroker.model_delete_sub_account_api_req import DeleteSubAccountApiReqBuilder
from kucoin_universal_sdk.generate.broker.ndbroker.model_get_broker_info_req import GetBrokerInfoReqBuilder, \
    GetBrokerInfoReq
from kucoin_universal_sdk.generate.broker.ndbroker.model_get_deposit_detail_req import GetDepositDetailReqBuilder
from kucoin_universal_sdk.generate.broker.ndbroker.model_get_deposit_list_req import GetDepositListReqBuilder
from kucoin_universal_sdk.generate.broker.ndbroker.model_get_rebase_req import GetRebaseReqBuilder, GetRebaseReq
from kucoin_universal_sdk.generate.broker.ndbroker.model_get_sub_account_api_req import GetSubAccountApiReqBuilder
from kucoin_universal_sdk.generate.broker.ndbroker.model_get_sub_account_req import GetSubAccountReqBuilder
from kucoin_universal_sdk.generate.broker.ndbroker.model_get_transfer_history_req import GetTransferHistoryReqBuilder
from kucoin_universal_sdk.generate.broker.ndbroker.model_get_withdraw_detail_req import GetWithdrawDetailReqBuilder
from kucoin_universal_sdk.generate.broker.ndbroker.model_modify_sub_account_api_req import \
    ModifySubAccountApiReqBuilder, ModifySubAccountApiReq
from kucoin_universal_sdk.generate.broker.ndbroker.model_transfer_req import TransferReqBuilder, TransferReq
from kucoin_universal_sdk.model.client_option import ClientOptionBuilder
from kucoin_universal_sdk.model.constants import GLOBAL_API_ENDPOINT, GLOBAL_BROKER_API_ENDPOINT, \
    GLOBAL_FUTURES_API_ENDPOINT
from kucoin_universal_sdk.model.transport_option import TransportOptionBuilder


class BrokerApiTest(unittest.TestCase):
    def setUp(self):
        key = os.getenv("API_KEY")
        secret = os.getenv("API_SECRET")
        passphrase = os.getenv("API_PASSPHRASE")
        broker_name = os.getenv("BROKER_NAME")
        broker_key = os.getenv("BROKER_KEY")
        broker_partner = os.getenv("BROKER_PARTNER")

        # create http transport option
        http_transport_option = (
            TransportOptionBuilder()
            .set_interceptors([LoggingInterceptor()])
            .build()
        )

        # create client option
        client_option = (
            ClientOptionBuilder()
            .set_key(key)
            .set_secret(secret)
            .set_passphrase(passphrase)
            .set_spot_endpoint(GLOBAL_API_ENDPOINT)
            .set_futures_endpoint(GLOBAL_FUTURES_API_ENDPOINT)
            .set_broker_endpoint(GLOBAL_BROKER_API_ENDPOINT)
            .set_broker_name(broker_name)
            .set_broker_partner(broker_partner)
            .set_broker_key(broker_key)
            .set_transport_option(http_transport_option)
            .build()
        )

        # create API client
        client = DefaultClient(client_option)
        kucoin_rest_api = client.rest_service()
        self.api = kucoin_rest_api.get_broker_service().get_nd_broker_api()

    def test_submit_kyc_req(self):
        """
            submit_kyc
            Submit KYC
            /api/kyc/ndBroker/proxyClient/submit
        """

        builder = SubmitKycReqBuilder()
        builder.set_client_uid("226383154").set_first_name("Kaylah").set_last_name("Padberg").set_issue_country(
            "JP").set_birth_date(
            "2000-01-01").set_identity_type(SubmitKycReq.IdentityTypeEnum.PASSPORT).set_identity_number(
            "55").set_expire_date("2030-01-01").set_front_photo("****").set_backend_photo("***").set_face_photo("***")
        req = builder.build()
        try:
            resp = self.api.submit_kyc(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_kyc_status_req(self):
        """
            get_kyc_status
            Get KYC Status
            /api/kyc/ndBroker/proxyClient/status/list
        """

        builder = GetKycStatusReqBuilder()
        builder.set_client_uids("226383154")
        req = builder.build()
        try:
            resp = self.api.get_kyc_status(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_kyc_status_list_req(self):
        """
            get_kyc_status_list
            Get KYC Status List
            /api/kyc/ndBroker/proxyClient/status/page
        """

        builder = GetKycStatusListReqBuilder()
        builder.set_page_number(1).set_page_size(100)
        req = builder.build()
        try:
            resp = self.api.get_kyc_status_list(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_deposit_list_req(self):
        """
            get_deposit_list
            Get Deposit List
            /api/v1/asset/ndbroker/deposit/list
        """

        builder = GetDepositListReqBuilder()
        builder.set_currency("USDT")
        req = builder.build()
        try:
            resp = self.api.get_deposit_list(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_delete_sub_account_api_req(self):
        """
            delete_sub_account_api
            Delete SubAccount API
            /api/v1/broker/nd/account/apikey
        """

        builder = DeleteSubAccountApiReqBuilder()
        builder.set_uid("229317507").set_api_key("674ff687ad1e6600015aa2b2")
        req = builder.build()
        try:
            resp = self.api.delete_sub_account_api(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_sub_account_api_req(self):
        """
            get_sub_account_api
            Get SubAccount API
            /api/v1/broker/nd/account/apikey
        """

        builder = GetSubAccountApiReqBuilder()
        builder.set_uid("229317507").set_api_key("674ff687ad1e6600015aa2b2")
        req = builder.build()
        try:
            resp = self.api.get_sub_account_api(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_add_sub_account_api_req(self):
        """
            add_sub_account_api
            Add SubAccount API
            /api/v1/broker/nd/account/apikey
        """

        builder = AddSubAccountApiReqBuilder()
        builder.set_uid("229317507").set_passphrase("11223344").set_ip_whitelist(
            ["127.0.0.1", "192.168.1.1"]).set_permissions(
            [AddSubAccountApiReq.PermissionsEnum.GENERAL, AddSubAccountApiReq.PermissionsEnum.SPOT]).set_label(
            "This is remarks")
        req = builder.build()
        try:
            resp = self.api.add_sub_account_api(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_sub_account_req(self):
        """
            get_sub_account
            Get SubAccount
            /api/v1/broker/nd/account
        """

        builder = GetSubAccountReqBuilder()
        builder.set_uid("229317507").set_current_page(1).set_page_size(10)
        req = builder.build()
        try:
            resp = self.api.get_sub_account(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_add_sub_account_req(self):
        """
            add_sub_account
            Add SubAccount
            /api/v1/broker/nd/account
        """

        builder = AddSubAccountReqBuilder()
        builder.set_account_name("sdk_test_2")
        req = builder.build()
        try:
            resp = self.api.add_sub_account(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_modify_sub_account_api_req(self):
        """
            modify_sub_account_api
            Modify SubAccount API
            /api/v1/broker/nd/account/update-apikey
        """

        builder = ModifySubAccountApiReqBuilder()
        builder.set_uid("229317507").set_ip_whitelist(["127.0.0.1"]).set_permissions(
            [ModifySubAccountApiReq.PermissionsEnum.SPOT]).set_label("label").set_api_key("674ff687ad1e6600015aa2b2")
        req = builder.build()
        try:
            resp = self.api.modify_sub_account_api(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_broker_info_req(self):
        """
            get_broker_info
            Get Broker Info
            /api/v1/broker/nd/info
        """

        builder = GetBrokerInfoReqBuilder()
        builder.set_trade_type(GetBrokerInfoReq.TradeTypeEnum.T_1)
        req = builder.build()
        try:
            resp = self.api.get_broker_info(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_rebase_req(self):
        """
            get_rebase
            Get Broker Rebate
            /api/v1/broker/nd/rebase/download
        """

        builder = GetRebaseReqBuilder()
        builder.set_begin("20240610").set_end("20241010").set_trade_type(GetRebaseReq.TradeTypeEnum.T_1)
        req = builder.build()
        try:
            resp = self.api.get_rebase(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_transfer_req(self):
        """
            transfer
            Transfer
            /api/v1/broker/nd/transfer
        """

        builder = TransferReqBuilder()
        builder.set_currency("USDT").set_amount("0.01").set_direction(
            TransferReq.DirectionEnum.OUT).set_account_type(TransferReq.AccountTypeEnum.TRADE).set_special_uid(
            "229317507").set_special_account_type(TransferReq.SpecialAccountTypeEnum.MAIN).set_client_oid(
            uuid.uuid4().__str__())
        req = builder.build()
        try:
            resp = self.api.transfer(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_deposit_detail_req(self):
        """
            get_deposit_detail
            Get Deposit Detail
            /api/v3/broker/nd/deposit/detail
        """

        builder = GetDepositDetailReqBuilder()
        builder.set_currency("USDT").set_hash("6724e363a492800007ec602b")
        req = builder.build()
        try:
            resp = self.api.get_deposit_detail(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_transfer_history_req(self):
        """
            get_transfer_history
            Get Transfer History
            /api/v3/broker/nd/transfer/detail
        """

        builder = GetTransferHistoryReqBuilder()
        builder.set_order_id("671b4600c1e3dd000726866d")
        req = builder.build()
        try:
            resp = self.api.get_transfer_history(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_withdraw_detail_req(self):
        """
            get_withdraw_detail
            Get Withdraw Detail
            /api/v3/broker/nd/withdraw/detail
        """

        builder = GetWithdrawDetailReqBuilder()
        builder.set_withdrawal_id("674686fa1ac01f0007b25768")
        req = builder.build()
        try:
            resp = self.api.get_withdraw_detail(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e
