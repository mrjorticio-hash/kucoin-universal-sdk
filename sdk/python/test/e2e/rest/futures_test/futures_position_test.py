import os
import unittest

from kucoin_universal_sdk.api.client import DefaultClient
from kucoin_universal_sdk.extension.interceptor.logging import LoggingInterceptor
from kucoin_universal_sdk.generate.futures.positions import BatchSwitchMarginModeReqBuilder, BatchSwitchMarginModeReq, \
    GetCrossMarginRiskLimitReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_add_isolated_margin_req import AddIsolatedMarginReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_get_cross_margin_leverage_req import \
    GetCrossMarginLeverageReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_get_isolated_margin_risk_limit_req import \
    GetIsolatedMarginRiskLimitReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_get_margin_mode_req import GetMarginModeReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_get_max_open_size_req import GetMaxOpenSizeReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_get_max_withdraw_margin_req import \
    GetMaxWithdrawMarginReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_get_position_details_req import GetPositionDetailsReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_get_position_list_req import GetPositionListReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_get_positions_history_req import \
    GetPositionsHistoryReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_modify_auto_deposit_status_req import \
    ModifyAutoDepositStatusReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_modify_isolated_margin_risk_limt_req import \
    ModifyIsolatedMarginRiskLimtReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_modify_margin_leverage_req import \
    ModifyMarginLeverageReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_remove_isolated_margin_req import \
    RemoveIsolatedMarginReqBuilder
from kucoin_universal_sdk.generate.futures.positions.model_switch_margin_mode_req import SwitchMarginModeReqBuilder, \
    SwitchMarginModeReq
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
        self.api = kucoin_rest_api.get_futures_service().get_positions_api()

    def test_get_isolated_margin_risk_limit_req(self):
        """
            get_isolated_margin_risk_limit
            Get Isolated Margin Risk Limit
            /api/v1/contracts/risk-limit/{symbol}
        """

        builder = GetIsolatedMarginRiskLimitReqBuilder()
        builder.set_symbol("XBTUSDTM")
        req = builder.build()
        try:
            resp = self.api.get_isolated_margin_risk_limit(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_positions_history_req(self):
        """
            get_positions_history
            Get Positions History
            /api/v1/history-positions
        """

        builder = GetPositionsHistoryReqBuilder()
        builder.set_symbol("DOGEUSDTM")
        req = builder.build()
        try:
            resp = self.api.get_positions_history(req)
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
            /api/v1/margin/maxWithdrawMargin
        """

        builder = GetMaxWithdrawMarginReqBuilder()
        builder.set_symbol("DOGEUSDTM")
        req = builder.build()
        try:
            resp = self.api.get_max_withdraw_margin(req)
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
            /api/v1/margin/withdrawMargin
        """

        builder = RemoveIsolatedMarginReqBuilder()
        builder.set_symbol("DOGEUSDTM").set_withdraw_amount("1")
        req = builder.build()
        try:
            resp = self.api.remove_isolated_margin(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_cross_margin_risk_limit_req(self):
        """
            get_cross_margin_risk_limit
            Get Cross Margin Risk Limit
            /api/v2/batchGetCrossOrderLimit
        """

        builder = GetCrossMarginRiskLimitReqBuilder()
        builder.set_symbol("XBTUSDTM").set_total_margin("1000").set_leverage(1)
        req = builder.build()
        try:
            resp = self.api.get_cross_margin_risk_limit(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_position_details_req(self):
        """
            get_position_details
            Get Position Details
            /api/v1/position
        """

        builder = GetPositionDetailsReqBuilder()
        builder.set_symbol("DOGEUSDTM")
        req = builder.build()
        try:
            resp = self.api.get_position_details(req)
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
            /api/v1/position/margin/auto-deposit-status
        """

        builder = ModifyAutoDepositStatusReqBuilder()
        builder.set_symbol("DOGEUSDTM").set_status(True)
        req = builder.build()
        try:
            resp = self.api.modify_auto_deposit_status(req)
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
            /api/v1/position/margin/deposit-margin
        """

        builder = AddIsolatedMarginReqBuilder()
        builder.set_symbol("DOGEUSDTM").set_margin(1.0).set_biz_no("251160679598325760")
        req = builder.build()
        try:
            resp = self.api.add_isolated_margin(req)
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
            /api/v1/position/risk-limit-level/change
        """

        builder = ModifyIsolatedMarginRiskLimtReqBuilder()
        builder.set_symbol("DOGEUSDTM").set_level(4)
        req = builder.build()
        try:
            resp = self.api.modify_isolated_margin_risk_limt(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_position_list_req(self):
        """
            get_position_list
            Get Position List
            /api/v1/positions
        """

        builder = GetPositionListReqBuilder()
        builder.set_currency("USDT")
        req = builder.build()
        try:
            resp = self.api.get_position_list(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_modify_margin_leverage_req(self):
        """
            modify_margin_leverage
            Modify Cross Margin Leverage
            /api/v2/changeCrossUserLeverage
        """

        builder = ModifyMarginLeverageReqBuilder()
        builder.set_symbol("XBTUSDTM").set_leverage("3")
        req = builder.build()
        try:
            resp = self.api.modify_margin_leverage(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_cross_margin_leverage_req(self):
        """
            get_cross_margin_leverage
            Get Cross Margin Leverage
            /api/v2/getCrossUserLeverage
        """

        builder = GetCrossMarginLeverageReqBuilder()
        builder.set_symbol("DOGEUSDTM")
        req = builder.build()
        try:
            resp = self.api.get_cross_margin_leverage(req)
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
            /api/v2/getMaxOpenSize
        """

        builder = GetMaxOpenSizeReqBuilder()
        builder.set_symbol("XBTUSDTM").set_price("10000").set_leverage(10)
        req = builder.build()
        try:
            resp = self.api.get_max_open_size(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_switch_margin_mode_req(self):
        """
            switch_margin_mode
            Switch Margin Mode
            /api/v2/position/changeMarginMode
        """

        builder = SwitchMarginModeReqBuilder()
        builder.set_symbol("XBTUSDTM").set_margin_mode(SwitchMarginModeReq.MarginModeEnum.CROSS)
        req = builder.build()
        try:
            resp = self.api.switch_margin_mode(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_batch_switch_margin_mode_req(self):
        """
            batch_switch_margin_mode
            Batch Switch Margin Mode
            /api/v2/position/batchChangeMarginMode
        """

        builder = BatchSwitchMarginModeReqBuilder()
        builder.set_margin_mode(BatchSwitchMarginModeReq.MarginModeEnum.ISOLATED).set_symbols(['XBTUSDTM', 'DOGEUSDTM'])
        req = builder.build()
        try:
            resp = self.api.batch_switch_margin_mode(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_margin_mode_req(self):
        """
            get_margin_mode
            Get Margin Mode
            /api/v2/position/getMarginMode
        """

        builder = GetMarginModeReqBuilder()
        builder.set_symbol("XBTUSDTM")
        req = builder.build()
        try:
            resp = self.api.get_margin_mode(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e
