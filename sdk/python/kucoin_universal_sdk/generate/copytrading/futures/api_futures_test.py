import unittest
from .model_add_isolated_margin_req import AddIsolatedMarginReq
from .model_add_isolated_margin_resp import AddIsolatedMarginResp
from .model_add_order_req import AddOrderReq
from .model_add_order_resp import AddOrderResp
from .model_add_order_test_req import AddOrderTestReq
from .model_add_order_test_resp import AddOrderTestResp
from .model_add_tpsl_order_req import AddTpslOrderReq
from .model_add_tpsl_order_resp import AddTpslOrderResp
from .model_cancel_order_by_client_oid_req import CancelOrderByClientOidReq
from .model_cancel_order_by_client_oid_resp import CancelOrderByClientOidResp
from .model_cancel_order_by_id_req import CancelOrderByIdReq
from .model_cancel_order_by_id_resp import CancelOrderByIdResp
from .model_get_max_open_size_req import GetMaxOpenSizeReq
from .model_get_max_open_size_resp import GetMaxOpenSizeResp
from .model_get_max_withdraw_margin_req import GetMaxWithdrawMarginReq
from .model_get_max_withdraw_margin_resp import GetMaxWithdrawMarginResp
from .model_modify_auto_deposit_status_req import ModifyAutoDepositStatusReq
from .model_modify_auto_deposit_status_resp import ModifyAutoDepositStatusResp
from .model_modify_isolated_margin_risk_limt_req import ModifyIsolatedMarginRiskLimtReq
from .model_modify_isolated_margin_risk_limt_resp import ModifyIsolatedMarginRiskLimtResp
from .model_remove_isolated_margin_req import RemoveIsolatedMarginReq
from .model_remove_isolated_margin_resp import RemoveIsolatedMarginResp
from kucoin_universal_sdk.model.common import RestResponse


class FuturesAPITest(unittest.TestCase):

    def test_add_order_req_model(self):
        """
       add_order
       Add Order
       /api/v1/copy-trade/futures/orders
       """
        data = "{\"clientOid\": \"5c52e11203aa677f33e493fb\", \"side\": \"buy\", \"symbol\": \"XBTUSDTM\", \"leverage\": 3, \"type\": \"limit\", \"remark\": \"order remarks\", \"reduceOnly\": false, \"marginMode\": \"ISOLATED\", \"price\": \"0.1\", \"size\": 1, \"timeInForce\": \"GTC\"}"
        req = AddOrderReq.from_json(data)

    def test_add_order_resp_model(self):
        """
        add_order
        Add Order
        /api/v1/copy-trade/futures/orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"263485113055133696\",\n        \"clientOid\": \"5c52e11203aa677f331e493fb\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = AddOrderResp.from_dict(common_response.data)

    def test_add_order_test_req_model(self):
        """
       add_order_test
       Add Order Test
       /api/v1/copy-trade/futures/orders/test
       """
        data = "{\"clientOid\": \"5c52e11203aa677f33e493fb\", \"side\": \"buy\", \"symbol\": \"XBTUSDTM\", \"leverage\": 3, \"type\": \"limit\", \"remark\": \"order remarks\", \"reduceOnly\": false, \"marginMode\": \"ISOLATED\", \"price\": \"0.1\", \"size\": 1, \"timeInForce\": \"GTC\"}"
        req = AddOrderTestReq.from_json(data)

    def test_add_order_test_resp_model(self):
        """
        add_order_test
        Add Order Test
        /api/v1/copy-trade/futures/orders/test
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"234125150956625920\",\n        \"clientOid\": \"5c52e11203aa677f33e493fb\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = AddOrderTestResp.from_dict(common_response.data)

    def test_add_tpsl_order_req_model(self):
        """
       add_tpsl_order
       Add Take Profit And Stop Loss Order
       /api/v1/copy-trade/futures/st-orders
       """
        data = "{\"clientOid\": \"5c52e11203aa677f33e493fb\", \"side\": \"buy\", \"symbol\": \"XBTUSDTM\", \"leverage\": 3, \"type\": \"limit\", \"remark\": \"order remarks\", \"reduceOnly\": false, \"marginMode\": \"ISOLATED\", \"price\": \"0.2\", \"size\": 1, \"timeInForce\": \"GTC\", \"triggerStopUpPrice\": \"0.3\", \"triggerStopDownPrice\": \"0.1\", \"stopPriceType\": \"TP\"}"
        req = AddTpslOrderReq.from_json(data)

    def test_add_tpsl_order_resp_model(self):
        """
        add_tpsl_order
        Add Take Profit And Stop Loss Order
        /api/v1/copy-trade/futures/st-orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"234125150956625920\",\n        \"clientOid\": \"5c52e11203aa677f33e493fb\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = AddTpslOrderResp.from_dict(common_response.data)

    def test_cancel_order_by_id_req_model(self):
        """
       cancel_order_by_id
       Cancel Order By OrderId
       /api/v1/copy-trade/futures/orders
       """
        data = "{\"orderId\": \"263485113055133696\"}"
        req = CancelOrderByIdReq.from_json(data)

    def test_cancel_order_by_id_resp_model(self):
        """
        cancel_order_by_id
        Cancel Order By OrderId
        /api/v1/copy-trade/futures/orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"cancelledOrderIds\": [\n            \"263485113055133696\"\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = CancelOrderByIdResp.from_dict(common_response.data)

    def test_cancel_order_by_client_oid_req_model(self):
        """
       cancel_order_by_client_oid
       Cancel Order By ClientOid
       /api/v1/copy-trade/futures/orders/client-order
       """
        data = "{\"symbol\": \"XBTUSDTM\", \"clientOid\": \"5c52e11203aa677f331e493fb\"}"
        req = CancelOrderByClientOidReq.from_json(data)

    def test_cancel_order_by_client_oid_resp_model(self):
        """
        cancel_order_by_client_oid
        Cancel Order By ClientOid
        /api/v1/copy-trade/futures/orders/client-order
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"clientOid\": \"5c52e11203aa677f331e4913fb\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = CancelOrderByClientOidResp.from_dict(common_response.data)

    def test_get_max_open_size_req_model(self):
        """
       get_max_open_size
       Get Max Open Size
       /api/v1/copy-trade/futures/get-max-open-size
       """
        data = "{\"symbol\": \"XBTUSDTM\", \"price\": 123456.0, \"leverage\": 123456}"
        req = GetMaxOpenSizeReq.from_json(data)

    def test_get_max_open_size_resp_model(self):
        """
        get_max_open_size
        Get Max Open Size
        /api/v1/copy-trade/futures/get-max-open-size
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"symbol\": \"XBTUSDTM\",\n        \"maxBuyOpenSize\": \"1000000\",\n        \"maxSellOpenSize\": \"51\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetMaxOpenSizeResp.from_dict(common_response.data)

    def test_get_max_withdraw_margin_req_model(self):
        """
       get_max_withdraw_margin
       Get Max Withdraw Margin
       /api/v1/copy-trade/futures/position/margin/max-withdraw-margin
       """
        data = "{\"symbol\": \"example_string_default_value\"}"
        req = GetMaxWithdrawMarginReq.from_json(data)

    def test_get_max_withdraw_margin_resp_model(self):
        """
        get_max_withdraw_margin
        Get Max Withdraw Margin
        /api/v1/copy-trade/futures/position/margin/max-withdraw-margin
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": \"21.1135719252\"\n}"
        common_response = RestResponse.from_json(data)
        resp = GetMaxWithdrawMarginResp.from_dict(common_response.data)

    def test_add_isolated_margin_req_model(self):
        """
       add_isolated_margin
       Add Isolated Margin
       /api/v1/copy-trade/futures/position/margin/deposit-margin
       """
        data = "{\"symbol\": \"XBTUSDTM\", \"margin\": 3, \"bizNo\": \"112233\"}"
        req = AddIsolatedMarginReq.from_json(data)

    def test_add_isolated_margin_resp_model(self):
        """
        add_isolated_margin
        Add Isolated Margin
        /api/v1/copy-trade/futures/position/margin/deposit-margin
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"id\": \"400000000000974886\",\n        \"symbol\": \"XBTUSDTM\",\n        \"autoDeposit\": true,\n        \"maintMarginReq\": \"0.004\",\n        \"riskLimit\": 100000,\n        \"realLeverage\": \"1.83\",\n        \"crossMode\": false,\n        \"marginMode\": \"\",\n        \"positionSide\": \"\",\n        \"leverage\": \"1.83\",\n        \"delevPercentage\": 0.2,\n        \"openingTimestamp\": 1736932881164,\n        \"currentTimestamp\": 1736933530230,\n        \"currentQty\": 1,\n        \"currentCost\": \"97.302\",\n        \"currentComm\": \"0.0583812\",\n        \"unrealisedCost\": \"97.302\",\n        \"realisedGrossCost\": \"0.0000000000\",\n        \"realisedCost\": \"0.0583812000\",\n        \"isOpen\": true,\n        \"markPrice\": \"96939.98\",\n        \"markValue\": \"96.9399800000\",\n        \"posCost\": \"97.302\",\n        \"posCross\": \"20.9874\",\n        \"posInit\": \"32.4339999967\",\n        \"posComm\": \"0.0904415999\",\n        \"posLoss\": \"0\",\n        \"posMargin\": \"53.5118415966\",\n        \"posMaint\": \"0.4796495999\",\n        \"maintMargin\": \"53.1498215966\",\n        \"realisedGrossPnl\": \"0.0000000000\",\n        \"realisedPnl\": \"-0.0583812000\",\n        \"unrealisedPnl\": \"-0.3620200000\",\n        \"unrealisedPnlPcnt\": \"-0.0037\",\n        \"unrealisedRoePcnt\": \"-0.0112\",\n        \"avgEntryPrice\": \"97302.00\",\n        \"liquidationPrice\": \"44269.81\",\n        \"bankruptPrice\": \"43880.61\",\n        \"settleCurrency\": \"USDT\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = AddIsolatedMarginResp.from_dict(common_response.data)

    def test_remove_isolated_margin_req_model(self):
        """
       remove_isolated_margin
       Remove Isolated Margin
       /api/v1/copy-trade/futures/position/margin/withdraw-margin
       """
        data = "{\"symbol\": \"XBTUSDTM\", \"withdrawAmount\": 1e-07}"
        req = RemoveIsolatedMarginReq.from_json(data)

    def test_remove_isolated_margin_resp_model(self):
        """
        remove_isolated_margin
        Remove Isolated Margin
        /api/v1/copy-trade/futures/position/margin/withdraw-margin
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": \"0.1\"\n}"
        common_response = RestResponse.from_json(data)
        resp = RemoveIsolatedMarginResp.from_dict(common_response.data)

    def test_modify_isolated_margin_risk_limt_req_model(self):
        """
       modify_isolated_margin_risk_limt
       Modify Isolated Margin Risk Limit
       /api/v1/copy-trade/futures/position/risk-limit-level/change
       """
        data = "{\"symbol\": \"XBTUSDTM\", \"level\": 1}"
        req = ModifyIsolatedMarginRiskLimtReq.from_json(data)

    def test_modify_isolated_margin_risk_limt_resp_model(self):
        """
        modify_isolated_margin_risk_limt
        Modify Isolated Margin Risk Limit
        /api/v1/copy-trade/futures/position/risk-limit-level/change
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": true\n}"
        common_response = RestResponse.from_json(data)
        resp = ModifyIsolatedMarginRiskLimtResp.from_dict(common_response.data)

    def test_modify_auto_deposit_status_req_model(self):
        """
       modify_auto_deposit_status
       Modify Isolated Margin Auto-Deposit Status
       /api/v1/copy-trade/futures/position/margin/auto-deposit-status
       """
        data = "{\"symbol\": \"XBTUSDTM\", \"status\": true}"
        req = ModifyAutoDepositStatusReq.from_json(data)

    def test_modify_auto_deposit_status_resp_model(self):
        """
        modify_auto_deposit_status
        Modify Isolated Margin Auto-Deposit Status
        /api/v1/copy-trade/futures/position/margin/auto-deposit-status
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": true\n}"
        common_response = RestResponse.from_json(data)
        resp = ModifyAutoDepositStatusResp.from_dict(common_response.data)
