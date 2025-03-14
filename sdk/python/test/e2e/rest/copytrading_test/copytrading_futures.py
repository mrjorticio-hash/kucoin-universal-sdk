import os
import unittest
import uuid

from kucoin_universal_sdk.api.client import DefaultClient
from kucoin_universal_sdk.extension.interceptor.logging import LoggingInterceptor
from kucoin_universal_sdk.generate.copytrading.futures.model_add_isolated_margin_req import AddIsolatedMarginReqBuilder
from kucoin_universal_sdk.generate.copytrading.futures.model_add_order_req import AddOrderReqBuilder, AddOrderReq
from kucoin_universal_sdk.generate.copytrading.futures.model_add_order_test_req import AddOrderTestReqBuilder, \
    AddOrderTestReq
from kucoin_universal_sdk.generate.copytrading.futures.model_add_tpsl_order_req import AddTpslOrderReqBuilder, \
    AddTpslOrderReq
from kucoin_universal_sdk.generate.copytrading.futures.model_cancel_order_by_client_oid_req import \
    CancelOrderByClientOidReqBuilder
from kucoin_universal_sdk.generate.copytrading.futures.model_cancel_order_by_id_req import CancelOrderByIdReqBuilder
from kucoin_universal_sdk.generate.copytrading.futures.model_get_max_open_size_req import GetMaxOpenSizeReqBuilder
from kucoin_universal_sdk.generate.copytrading.futures.model_get_max_withdraw_margin_req import \
    GetMaxWithdrawMarginReqBuilder
from kucoin_universal_sdk.generate.copytrading.futures.model_modify_auto_deposit_status_req import \
    ModifyAutoDepositStatusReqBuilder
from kucoin_universal_sdk.generate.copytrading.futures.model_modify_isolated_margin_risk_limt_req import \
    ModifyIsolatedMarginRiskLimtReqBuilder
from kucoin_universal_sdk.generate.copytrading.futures.model_remove_isolated_margin_req import \
    RemoveIsolatedMarginReqBuilder
from kucoin_universal_sdk.model.client_option import ClientOptionBuilder
from kucoin_universal_sdk.model.constants import GLOBAL_API_ENDPOINT, GLOBAL_FUTURES_API_ENDPOINT, \
    GLOBAL_BROKER_API_ENDPOINT
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
        self.api = kucoin_rest_api.get_copytrading_service().get_futures_api()

    def test_add_order_req(self):
        """
            add_order
            Add Order
            /api/v1/copy-trade/futures/orders
        """

        builder = AddOrderReqBuilder()
        builder.set_client_oid(uuid.uuid4().__str__()).set_side(AddOrderReq.SideEnum.BUY).set_symbol(
            'XBTUSDTM').set_leverage(3).set_type(AddOrderReq.TypeEnum.MARKET).set_size(1)
        req = builder.build()
        try:
            resp = self.api.add_order(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_add_order_test_req(self):
        """
            add_order_test
            Add Order Test
            /api/v1/copy-trade/futures/orders/test
        """

        builder = AddOrderTestReqBuilder()
        builder.set_client_oid(uuid.uuid4().__str__()).set_side(AddOrderTestReq.SideEnum.BUY).set_symbol(
            'XBTUSDTM').set_leverage(3).set_type(AddOrderTestReq.TypeEnum.LIMIT).set_remark(
            'order remarks').set_reduce_only(False).set_margin_mode(AddOrderTestReq.MarginModeEnum.ISOLATED).set_price(
            '0.1').set_size(1).set_time_in_force(AddOrderTestReq.TimeInForceEnum.GOOD_TILL_CANCELED)
        req = builder.build()
        try:
            resp = self.api.add_order_test(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_add_tpsl_order_req(self):
        """
            add_tpsl_order
            Add Take Profit And Stop Loss Order
            /api/v1/copy-trade/futures/st-orders
        """

        builder = AddTpslOrderReqBuilder()
        builder.set_client_oid(uuid.uuid4().__str__()).set_side(AddTpslOrderReq.SideEnum.BUY).set_symbol(
            'XBTUSDTM').set_leverage(3).set_type(AddTpslOrderReq.TypeEnum.LIMIT).set_reduce_only(False).set_margin_mode(AddTpslOrderReq.MarginModeEnum.ISOLATED).set_price(
            '0.1').set_size(1).set_time_in_force(
            AddTpslOrderReq.TimeInForceEnum.GOOD_TILL_CANCELED).set_trigger_stop_up_price(
            '0.3').set_trigger_stop_down_price('0.1').set_stop_price_type(AddTpslOrderReq.StopPriceTypeEnum.TRADE_PRICE)
        req = builder.build()
        try:
            resp = self.api.add_tpsl_order(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_cancel_order_by_id_req(self):
        """
            cancel_order_by_id
            Cancel Order By OrderId
            /api/v1/copy-trade/futures/orders
        """

        builder = CancelOrderByIdReqBuilder()
        builder.set_order_id("268924170417500160")
        req = builder.build()
        try:
            resp = self.api.cancel_order_by_id(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_cancel_order_by_client_oid_req(self):
        """
            cancel_order_by_client_oid
            Cancel Order By ClientOid
            /api/v1/copy-trade/futures/orders/client-order
        """

        builder = CancelOrderByClientOidReqBuilder()
        builder.set_symbol('XBTUSDTM').set_client_oid('2603cb0a-c227-4522-9652-701eb77237bb')
        req = builder.build()
        try:
            resp = self.api.cancel_order_by_client_oid(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_max_open_size_req(self):
        """
            get_max_open_size
            Get Max Open Size
            /api/v1/copy-trade/futures/get-max-open-size
        """

        builder = GetMaxOpenSizeReqBuilder()
        builder.set_symbol('XBTUSDTM').set_price(0.1).set_leverage(10)
        req = builder.build()
        try:
            resp = self.api.get_max_open_size(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_max_withdraw_margin_req(self):
        """
            get_max_withdraw_margin
            Get Max Withdraw Margin
            /api/v1/copy-trade/futures/position/margin/max-withdraw-margin
        """

        builder = GetMaxWithdrawMarginReqBuilder()
        builder.set_symbol('XBTUSDTM')
        req = builder.build()
        try:
            resp = self.api.get_max_withdraw_margin(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_add_isolated_margin_req(self):
        """
            add_isolated_margin
            Add Isolated Margin
            /api/v1/copy-trade/futures/position/margin/deposit-margin
        """

        builder = AddIsolatedMarginReqBuilder()
        builder.set_symbol("XBTUSDTM").set_margin(3).set_biz_no(uuid.uuid4().__str__())
        req = builder.build()
        try:
            resp = self.api.add_isolated_margin(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_remove_isolated_margin_req(self):
        """
            remove_isolated_margin
            Remove Isolated Margin
            /api/v1/copy-trade/futures/position/margin/withdraw-margin
        """

        builder = RemoveIsolatedMarginReqBuilder()
        builder.set_symbol('XBTUSDTM').set_withdraw_amount(0.0000001)
        req = builder.build()
        try:
            resp = self.api.remove_isolated_margin(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_modify_isolated_margin_risk_limt_req(self):
        """
            modify_isolated_margin_risk_limt
            Modify Isolated Margin Risk Limit
            /api/v1/copy-trade/futures/position/risk-limit-level/change
        """

        builder = ModifyIsolatedMarginRiskLimtReqBuilder()
        builder.set_symbol('XBTUSDTM').set_level(1)
        req = builder.build()
        try:
            resp = self.api.modify_isolated_margin_risk_limt(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_modify_auto_deposit_status_req(self):
        """
            modify_auto_deposit_status
            Modify Isolated Margin Auto-Deposit Status
            /api/v1/copy-trade/futures/position/margin/auto-deposit-status
        """

        builder = ModifyAutoDepositStatusReqBuilder()
        builder.set_symbol("XBTUSDTM").set_status(True)
        req = builder.build()
        try:
            resp = self.api.modify_auto_deposit_status(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e
