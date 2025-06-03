import os
import unittest

from kucoin_universal_sdk.api.client import DefaultClient
from kucoin_universal_sdk.extension.interceptor.logging import LoggingInterceptor
from kucoin_universal_sdk.generate.account.withdrawal import GetWithdrawalHistoryByIdReqBuilder
from kucoin_universal_sdk.generate.account.withdrawal.model_cancel_withdrawal_req import CancelWithdrawalReqBuilder
from kucoin_universal_sdk.generate.account.withdrawal.model_get_withdrawal_history_old_req import \
    GetWithdrawalHistoryOldReqBuilder
from kucoin_universal_sdk.generate.account.withdrawal.model_get_withdrawal_history_req import \
    GetWithdrawalHistoryReqBuilder
from kucoin_universal_sdk.generate.account.withdrawal.model_get_withdrawal_quotas_req import \
    GetWithdrawalQuotasReqBuilder
from kucoin_universal_sdk.generate.account.withdrawal.model_withdrawal_v1_req import WithdrawalV1ReqBuilder
from kucoin_universal_sdk.generate.account.withdrawal.model_withdrawal_v3_req import WithdrawalV3ReqBuilder, \
    WithdrawalV3Req
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
        self.api = kucoin_rest_api.get_account_service().get_withdrawal_api()

    # todo empty data
    def test_get_withdrawal_history_old_req(self):
        """
            get_withdrawal_history_old
            Get Withdrawal History(OLD)
            /api/v1/hist-withdrawals
        """

        builder = GetWithdrawalHistoryOldReqBuilder()
        builder.set_currency("USDT").set_start_at(1703001600000).set_end_at(1703260800000)
        req = builder.build()
        try:
            resp = self.api.get_withdrawal_history_old(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_withdrawal_history_req(self):
        """
            get_withdrawal_history
            Get Withdrawal History
            /api/v1/withdrawals
        """

        builder = GetWithdrawalHistoryReqBuilder()
        builder.set_currency("USDT").set_start_at(1727712000000).set_end_at(1732982400000)
        req = builder.build()
        try:
            resp = self.api.get_withdrawal_history(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_withdrawal_history_by_id_req(self):
        """
            get_withdrawal_history_by_id
            Get Withdrawal History By ID
            /api/v1/withdrawals/{withdrawalId}
        """

        builder = GetWithdrawalHistoryByIdReqBuilder()
        builder.set_withdrawal_id("674576dc74b2bb000778452c")
        req = builder.build()
        try:
            resp = self.api.get_withdrawal_history_by_id(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e


    def test_withdrawal_v1_req(self):
        """
            withdrawal_v1
            Withdraw(V1)
            /api/v1/withdrawals
        """

        builder = WithdrawalV1ReqBuilder()
        builder.set_currency("USDT").set_chain("bsc").set_address(
            "***********").set_amount(20).set_is_inner(False)
        req = builder.build()
        try:
            resp = self.api.withdrawal_v1(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_withdrawal_quotas_req(self):
        """
            get_withdrawal_quotas
            Get Withdrawal Quotas
            /api/v1/withdrawals/quotas
        """

        builder = GetWithdrawalQuotasReqBuilder()
        builder.set_currency("USDT").set_chain("bsc")
        req = builder.build()
        try:
            resp = self.api.get_withdrawal_quotas(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_cancel_withdrawal_req(self):
        """
            cancel_withdrawal
            Cancel Withdrawal
            /api/v1/withdrawals/{withdrawalId}
        """

        builder = CancelWithdrawalReqBuilder()
        builder.set_withdrawal_id("674fba71b6afc90007e29bd2")
        req = builder.build()
        try:
            resp = self.api.cancel_withdrawal(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_withdrawal_v3_req(self):
        """
            withdrawal_v3
            Withdraw(V3)
            /api/v3/withdrawals
        """

        builder = WithdrawalV3ReqBuilder()
        builder.set_currency("USDT").set_chain("bsc").set_amount("20").set_is_inner(False).set_to_address(
            "*************").set_withdraw_type(WithdrawalV3Req.WithdrawTypeEnum.ADDRESS)
        req = builder.build()
        try:
            resp = self.api.withdrawal_v3(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e
