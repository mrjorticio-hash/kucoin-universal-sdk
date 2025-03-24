import os
import unittest

from kucoin_universal_sdk.api.client import DefaultClient
from kucoin_universal_sdk.extension.interceptor.logging import LoggingInterceptor
from kucoin_universal_sdk.generate.account.subaccount.model_add_sub_account_api_req import AddSubAccountApiReqBuilder, \
    AddSubAccountApiReq
from kucoin_universal_sdk.generate.account.subaccount.model_add_sub_account_futures_permission_req import \
    AddSubAccountFuturesPermissionReqBuilder
from kucoin_universal_sdk.generate.account.subaccount.model_add_sub_account_margin_permission_req import \
    AddSubAccountMarginPermissionReqBuilder
from kucoin_universal_sdk.generate.account.subaccount.model_add_sub_account_req import AddSubAccountReqBuilder, \
    AddSubAccountReq
from kucoin_universal_sdk.generate.account.subaccount.model_delete_sub_account_api_req import \
    DeleteSubAccountApiReqBuilder
from kucoin_universal_sdk.generate.account.subaccount.model_get_futures_sub_account_list_v2_req import \
    GetFuturesSubAccountListV2ReqBuilder
from kucoin_universal_sdk.generate.account.subaccount.model_get_spot_sub_account_detail_req import \
    GetSpotSubAccountDetailReqBuilder
from kucoin_universal_sdk.generate.account.subaccount.model_get_spot_sub_account_list_v2_req import \
    GetSpotSubAccountListV2ReqBuilder
from kucoin_universal_sdk.generate.account.subaccount.model_get_spot_sub_accounts_summary_v2_req import \
    GetSpotSubAccountsSummaryV2ReqBuilder
from kucoin_universal_sdk.generate.account.subaccount.model_get_sub_account_api_list_req import \
    GetSubAccountApiListReqBuilder
from kucoin_universal_sdk.generate.account.subaccount.model_modify_sub_account_api_req import \
    ModifySubAccountApiReqBuilder, ModifySubAccountApiReq
from kucoin_universal_sdk.model.client_option import ClientOptionBuilder
from kucoin_universal_sdk.model.constants import GLOBAL_API_ENDPOINT, GLOBAL_FUTURES_API_ENDPOINT, \
    GLOBAL_BROKER_API_ENDPOINT
from kucoin_universal_sdk.model.transport_option import TransportOptionBuilder


class AccountApiTest(unittest.TestCase):
    def setUp(self):
        key = os.getenv("API_KEY")
        secret = os.getenv("API_SECRET")
        passphrase = os.getenv("API_PASSPHRASE")

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
            .set_transport_option(http_transport_option)
            .build()
        )

        # create API client
        client = DefaultClient(client_option)
        kucoin_rest_api = client.rest_service()
        self.api = kucoin_rest_api.get_account_service().get_sub_account_api()

    def test_get_futures_sub_account_list_v2_req(self):
        """
            get_futures_sub_account_list_v2
            Get SubAccount List - Futures Balance(V2)
            /api/v1/account-overview-all
        """

        builder = GetFuturesSubAccountListV2ReqBuilder()
        builder.set_currency("XBT")
        req = builder.build()
        try:
            resp = self.api.get_futures_sub_account_list_v2(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_spot_sub_account_list_v1_req(self):
        """
            get_spot_sub_account_list_v1
            Get SubAccount List - Spot Balance(V1)
            /api/v1/sub-accounts
        """

        try:
            resp = self.api.get_spot_sub_account_list_v1()
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_spot_sub_account_detail_req(self):
        """
            get_spot_sub_account_detail
            Get SubAccount Detail - Balance
            /api/v1/sub-accounts/{subUserId}
        """

        builder = GetSpotSubAccountDetailReqBuilder()
        builder.set_sub_user_id("6745b7bc890ba20001911363").set_include_base_amount(False).set_base_amount("USDT").set_base_amount("0.1")
        req = builder.build()
        try:
            resp = self.api.get_spot_sub_account_detail(req)
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
           /api/v1/sub/api-key
       """

       builder = DeleteSubAccountApiReqBuilder()
       builder.set_api_key("6745b6ba87eb0e000107ea6c").set_sub_name("**********").set_passphrase("**********")
       req = builder.build()
       try:
           resp = self.api.delete_sub_account_api(req)
           print("code: ", resp.common_response.code)
           print("msg: ", resp.common_response.message)
           print("data: ", resp.to_json())
       except Exception as e:
           print("error: ", e)
           raise e

    def test_get_sub_account_api_list_req(self):
        """
            get_sub_account_api_list
            Get SubAccount API List
            /api/v1/sub/api-key
        """

        builder = GetSubAccountApiListReqBuilder()
        builder.set_api_key("6745b6ba87eb0e000107ea6c").set_sub_name("**********")
        req = builder.build()
        try:
            resp = self.api.get_sub_account_api_list(req)
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
            /api/v1/sub/api-key
        """

        builder = AddSubAccountApiReqBuilder()
        builder.set_passphrase("**********").set_remark("**********").set_permission(
            "General,Spot").set_expire(
            AddSubAccountApiReq.ExpireEnum.T_30).set_sub_name("**********")
        req = builder.build()
        try:
            resp = self.api.add_sub_account_api(req)
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
            /api/v1/sub/api-key/update
        """

        builder = ModifySubAccountApiReqBuilder()
        builder.set_passphrase("**********").set_permission(
            "General").set_expire(
            ModifySubAccountApiReq.ExpireEnum.T_30).set_sub_name("**********").set_api_key(
            "6745b6ba87eb0e000107ea6c")
        req = builder.build()
        try:
            resp = self.api.modify_sub_account_api(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_spot_sub_accounts_summary_v1_req(self):
        """
            get_spot_sub_accounts_summary_v1
            Get SubAccount List - Summary Info(V1)
            /api/v1/sub/user
        """

        try:
            resp = self.api.get_spot_sub_accounts_summary_v1()
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_spot_sub_account_list_v2_req(self):
        """
            get_spot_sub_account_list_v2
            Get SubAccount List - Spot Balance(V2)
            /api/v2/sub-accounts
        """

        builder = GetSpotSubAccountListV2ReqBuilder()
        builder.set_current_page(1).set_page_size(10)
        req = builder.build()
        try:
            resp = self.api.get_spot_sub_account_list_v2(req)
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
            /api/v2/sub/user/created
        """

        builder = AddSubAccountReqBuilder()
        (builder.set_password("**********@123").set_remarks("**********@123").
         set_sub_name("sdkTestIntegration2").set_access(AddSubAccountReq.AccessEnum.SPOT))
        req = builder.build()
        try:
            resp = self.api.add_sub_account(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_spot_sub_accounts_summary_v2_req(self):
       """
           get_spot_sub_accounts_summary_v2
           Get SubAccount List - Summary Info
           /api/v2/sub/user
       """

       builder = GetSpotSubAccountsSummaryV2ReqBuilder()
       builder.set_current_page(1).set_page_size(10)
       req = builder.build()
       try:
           resp = self.api.get_spot_sub_accounts_summary_v2(req)
           print("code: ", resp.common_response.code)
           print("msg: ", resp.common_response.message)
           print("data: ", resp.to_json())
       except Exception as e:
           print("error: ", e)
           raise e

    def test_add_sub_account_futures_permission_req(self):
       """
           add_sub_account_futures_permission
           Add SubAccount Futures Permission
           /api/v3/sub/user/futures/enable
       """

       builder = AddSubAccountFuturesPermissionReqBuilder()
       builder.set_uid("229290616")
       req = builder.build()
       try:
           resp = self.api.add_sub_account_futures_permission(req)
           print("code: ", resp.common_response.code)
           print("msg: ", resp.common_response.message)
           print("data: ", resp.to_json())
       except Exception as e:
           print("error: ", e)
           raise e

    def test_add_sub_account_margin_permission_req(self):
       """
           add_sub_account_margin_permission
           Add SubAccount Margin Permission
           /api/v3/sub/user/margin/enable
       """

       builder = AddSubAccountMarginPermissionReqBuilder()
       builder.set_uid("229290616")
       req = builder.build()
       try:
           resp = self.api.add_sub_account_margin_permission(req)
           print("code: ", resp.common_response.code)
           print("msg: ", resp.common_response.message)
           print("data: ", resp.to_json())
       except Exception as e:
           print("error: ", e)
           raise e
