import os
import unittest

from kucoin_universal_sdk.api.client import DefaultClient
from kucoin_universal_sdk.extension.interceptor.logging import LoggingInterceptor
from kucoin_universal_sdk.generate.futures.fundingfees.model_get_current_funding_rate_req import \
    GetCurrentFundingRateReqBuilder
from kucoin_universal_sdk.generate.futures.fundingfees.model_get_private_funding_history_req import \
    GetPrivateFundingHistoryReqBuilder
from kucoin_universal_sdk.generate.futures.fundingfees.model_get_public_funding_history_req import \
    GetPublicFundingHistoryReqBuilder
from kucoin_universal_sdk.model.client_option import ClientOptionBuilder
from kucoin_universal_sdk.model.constants import GLOBAL_API_ENDPOINT, GLOBAL_BROKER_API_ENDPOINT, \
    GLOBAL_FUTURES_API_ENDPOINT
from kucoin_universal_sdk.model.transport_option import TransportOptionBuilder


class FuturesApiTest(unittest.TestCase):
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
        self.api = kucoin_rest_api.get_futures_service().get_funding_fees_api()

    def test_get_public_funding_history_req(self):
        """
            get_public_funding_history
            Get Public Funding History
            /api/v1/contract/funding-rates
        """

        builder = GetPublicFundingHistoryReqBuilder()
        builder.set_symbol("XBTUSDTM").set_from_(1732464000000).set_to(1732500000000)
        req = builder.build()
        try:
            resp = self.api.get_public_funding_history(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_private_funding_history_req(self):
        """
            get_private_funding_history
            Get Private Funding History
            /api/v1/funding-history
        """

        builder = GetPrivateFundingHistoryReqBuilder()
        builder.set_symbol("DOGEUSDTM").set_start_at(1732550400000).set_end_at(1732723200000).set_reverse(True).set_max_count(
            100)
        req = builder.build()
        try:
            resp = self.api.get_private_funding_history(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_current_funding_rate_req(self):
        """
            get_current_funding_rate
            Get Current Funding Rate
            /api/v1/funding-rate/{symbol}/current
        """

        builder = GetCurrentFundingRateReqBuilder()
        builder.set_symbol("XBTUSDTM")
        req = builder.build()
        try:
            resp = self.api.get_current_funding_rate(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e
