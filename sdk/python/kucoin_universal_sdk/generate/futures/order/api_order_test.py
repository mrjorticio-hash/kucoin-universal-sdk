import unittest
from .model_add_order_req import AddOrderReq
from .model_add_order_resp import AddOrderResp
from .model_add_order_test_req import AddOrderTestReq
from .model_add_order_test_resp import AddOrderTestResp
from .model_add_tpsl_order_req import AddTpslOrderReq
from .model_add_tpsl_order_resp import AddTpslOrderResp
from .model_batch_add_orders_req import BatchAddOrdersReq
from .model_batch_add_orders_resp import BatchAddOrdersResp
from .model_batch_cancel_orders_req import BatchCancelOrdersReq
from .model_batch_cancel_orders_resp import BatchCancelOrdersResp
from .model_cancel_all_orders_v1_req import CancelAllOrdersV1Req
from .model_cancel_all_orders_v1_resp import CancelAllOrdersV1Resp
from .model_cancel_all_orders_v3_req import CancelAllOrdersV3Req
from .model_cancel_all_orders_v3_resp import CancelAllOrdersV3Resp
from .model_cancel_all_stop_orders_req import CancelAllStopOrdersReq
from .model_cancel_all_stop_orders_resp import CancelAllStopOrdersResp
from .model_cancel_order_by_client_oid_req import CancelOrderByClientOidReq
from .model_cancel_order_by_client_oid_resp import CancelOrderByClientOidResp
from .model_cancel_order_by_id_req import CancelOrderByIdReq
from .model_cancel_order_by_id_resp import CancelOrderByIdResp
from .model_get_open_order_value_req import GetOpenOrderValueReq
from .model_get_open_order_value_resp import GetOpenOrderValueResp
from .model_get_order_by_client_oid_req import GetOrderByClientOidReq
from .model_get_order_by_client_oid_resp import GetOrderByClientOidResp
from .model_get_order_by_order_id_req import GetOrderByOrderIdReq
from .model_get_order_by_order_id_resp import GetOrderByOrderIdResp
from .model_get_order_list_req import GetOrderListReq
from .model_get_order_list_resp import GetOrderListResp
from .model_get_recent_closed_orders_req import GetRecentClosedOrdersReq
from .model_get_recent_closed_orders_resp import GetRecentClosedOrdersResp
from .model_get_recent_trade_history_req import GetRecentTradeHistoryReq
from .model_get_recent_trade_history_resp import GetRecentTradeHistoryResp
from .model_get_stop_order_list_req import GetStopOrderListReq
from .model_get_stop_order_list_resp import GetStopOrderListResp
from .model_get_trade_history_req import GetTradeHistoryReq
from .model_get_trade_history_resp import GetTradeHistoryResp
from typing_extensions import deprecated
from kucoin_universal_sdk.model.common import RestResponse


class OrderAPITest(unittest.TestCase):

    def test_add_order_req_model(self):
        """
       add_order
       Add Order
       /api/v1/orders
       """
        data = "{\"clientOid\": \"5c52e11203aa677f33e493fb\", \"side\": \"buy\", \"symbol\": \"XBTUSDTM\", \"leverage\": 3, \"type\": \"limit\", \"remark\": \"order remarks\", \"reduceOnly\": false, \"marginMode\": \"ISOLATED\", \"price\": \"0.1\", \"size\": 1, \"timeInForce\": \"GTC\"}"
        req = AddOrderReq.from_json(data)

    def test_add_order_resp_model(self):
        """
        add_order
        Add Order
        /api/v1/orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"234125150956625920\",\n        \"clientOid\": \"5c52e11203aa677f33e493fb\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = AddOrderResp.from_dict(common_response.data)

    def test_add_order_test_req_model(self):
        """
       add_order_test
       Add Order Test
       /api/v1/orders/test
       """
        data = "{\"clientOid\": \"5c52e11203aa677f33e493fb\", \"side\": \"buy\", \"symbol\": \"XBTUSDTM\", \"leverage\": 3, \"type\": \"limit\", \"remark\": \"order remarks\", \"reduceOnly\": false, \"marginMode\": \"ISOLATED\", \"price\": \"0.1\", \"size\": 1, \"timeInForce\": \"GTC\"}"
        req = AddOrderTestReq.from_json(data)

    def test_add_order_test_resp_model(self):
        """
        add_order_test
        Add Order Test
        /api/v1/orders/test
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"234125150956625920\",\n        \"clientOid\": \"5c52e11203aa677f33e493fb\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = AddOrderTestResp.from_dict(common_response.data)

    def test_batch_add_orders_req_model(self):
        """
       batch_add_orders
       Batch Add Orders
       /api/v1/orders/multi
       """
        data = "[{\"clientOid\": \"5c52e11203aa677f33e493fb\", \"side\": \"buy\", \"symbol\": \"XBTUSDTM\", \"leverage\": 3, \"type\": \"limit\", \"remark\": \"order remarks\", \"reduceOnly\": false, \"marginMode\": \"ISOLATED\", \"price\": \"0.1\", \"size\": 1, \"timeInForce\": \"GTC\"}, {\"clientOid\": \"5c52e11203aa677f33e493fc\", \"side\": \"buy\", \"symbol\": \"XBTUSDTM\", \"leverage\": 3, \"type\": \"limit\", \"remark\": \"order remarks\", \"reduceOnly\": false, \"marginMode\": \"ISOLATED\", \"price\": \"0.1\", \"size\": 1, \"timeInForce\": \"GTC\"}]"
        req = BatchAddOrdersReq.from_json(data)

    def test_batch_add_orders_resp_model(self):
        """
        batch_add_orders
        Batch Add Orders
        /api/v1/orders/multi
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"orderId\": \"235919387779985408\",\n            \"clientOid\": \"5c52e11203aa677f33e493fb\",\n            \"symbol\": \"XBTUSDTM\",\n            \"code\": \"200000\",\n            \"msg\": \"success\"\n        },\n        {\n            \"orderId\": \"235919387855482880\",\n            \"clientOid\": \"5c52e11203aa677f33e493fc\",\n            \"symbol\": \"XBTUSDTM\",\n            \"code\": \"200000\",\n            \"msg\": \"success\"\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = BatchAddOrdersResp.from_dict(common_response.data)

    def test_add_tpsl_order_req_model(self):
        """
       add_tpsl_order
       Add Take Profit And Stop Loss Order
       /api/v1/st-orders
       """
        data = "{\"clientOid\": \"5c52e11203aa677f33e493fb\", \"side\": \"buy\", \"symbol\": \"XBTUSDTM\", \"leverage\": 3, \"type\": \"limit\", \"remark\": \"order remarks\", \"reduceOnly\": false, \"marginMode\": \"ISOLATED\", \"price\": \"0.2\", \"size\": 1, \"timeInForce\": \"GTC\", \"triggerStopUpPrice\": \"0.3\", \"triggerStopDownPrice\": \"0.1\", \"stopPriceType\": \"TP\"}"
        req = AddTpslOrderReq.from_json(data)

    def test_add_tpsl_order_resp_model(self):
        """
        add_tpsl_order
        Add Take Profit And Stop Loss Order
        /api/v1/st-orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"234125150956625920\",\n        \"clientOid\": \"5c52e11203aa677f33e493fb\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = AddTpslOrderResp.from_dict(common_response.data)

    def test_cancel_order_by_id_req_model(self):
        """
       cancel_order_by_id
       Cancel Order By OrderId
       /api/v1/orders/{orderId}
       """
        data = "{\"orderId\": \"example_string_default_value\"}"
        req = CancelOrderByIdReq.from_json(data)

    def test_cancel_order_by_id_resp_model(self):
        """
        cancel_order_by_id
        Cancel Order By OrderId
        /api/v1/orders/{orderId}
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"cancelledOrderIds\": [\n            \"235303670076489728\"\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = CancelOrderByIdResp.from_dict(common_response.data)

    def test_cancel_order_by_client_oid_req_model(self):
        """
       cancel_order_by_client_oid
       Cancel Order By ClientOid
       /api/v1/orders/client-order/{clientOid}
       """
        data = "{\"symbol\": \"XBTUSDTM\", \"clientOid\": \"example_string_default_value\"}"
        req = CancelOrderByClientOidReq.from_json(data)

    def test_cancel_order_by_client_oid_resp_model(self):
        """
        cancel_order_by_client_oid
        Cancel Order By ClientOid
        /api/v1/orders/client-order/{clientOid}
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"clientOid\": \"017485b0-2957-4681-8a14-5d46b35aee0d\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = CancelOrderByClientOidResp.from_dict(common_response.data)

    def test_batch_cancel_orders_req_model(self):
        """
       batch_cancel_orders
       Batch Cancel Orders
       /api/v1/orders/multi-cancel
       """
        data = "{\"orderIdsList\": [\"250445104152670209\", \"250445181751463936\"]}"
        req = BatchCancelOrdersReq.from_json(data)

    def test_batch_cancel_orders_resp_model(self):
        """
        batch_cancel_orders
        Batch Cancel Orders
        /api/v1/orders/multi-cancel
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"orderId\": \"250445104152670209\",\n            \"clientOid\": null,\n            \"code\": \"200\",\n            \"msg\": \"success\"\n        },\n        {\n            \"orderId\": \"250445181751463936\",\n            \"clientOid\": null,\n            \"code\": \"200\",\n            \"msg\": \"success\"\n        }\n    ]\n}\n"
        common_response = RestResponse.from_json(data)
        resp = BatchCancelOrdersResp.from_dict(common_response.data)

    def test_cancel_all_orders_v3_req_model(self):
        """
       cancel_all_orders_v3
       Cancel All Orders
       /api/v3/orders
       """
        data = "{\"symbol\": \"XBTUSDTM\"}"
        req = CancelAllOrdersV3Req.from_json(data)

    def test_cancel_all_orders_v3_resp_model(self):
        """
        cancel_all_orders_v3
        Cancel All Orders
        /api/v3/orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"cancelledOrderIds\": [\n            \"235919172150824960\",\n            \"235919172150824961\"\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = CancelAllOrdersV3Resp.from_dict(common_response.data)

    def test_cancel_all_stop_orders_req_model(self):
        """
       cancel_all_stop_orders
       Cancel All Stop orders
       /api/v1/stopOrders
       """
        data = "{\"symbol\": \"XBTUSDTM\"}"
        req = CancelAllStopOrdersReq.from_json(data)

    def test_cancel_all_stop_orders_resp_model(self):
        """
        cancel_all_stop_orders
        Cancel All Stop orders
        /api/v1/stopOrders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"cancelledOrderIds\": [\n            \"235919172150824960\",\n            \"235919172150824961\"\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = CancelAllStopOrdersResp.from_dict(common_response.data)

    def test_get_order_by_order_id_req_model(self):
        """
       get_order_by_order_id
       Get Order By OrderId
       /api/v1/orders/{order-id}
       """
        data = "{\"order-id\": \"236655147005071361\"}"
        req = GetOrderByOrderIdReq.from_json(data)

    def test_get_order_by_order_id_resp_model(self):
        """
        get_order_by_order_id
        Get Order By OrderId
        /api/v1/orders/{order-id}
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"id\": \"236655147005071361\",\n        \"symbol\": \"XBTUSDTM\",\n        \"type\": \"limit\",\n        \"side\": \"buy\",\n        \"price\": \"0.1\",\n        \"size\": 1,\n        \"value\": \"0.0001\",\n        \"dealValue\": \"0\",\n        \"dealSize\": 0,\n        \"stp\": \"\",\n        \"stop\": \"\",\n        \"stopPriceType\": \"\",\n        \"stopTriggered\": false,\n        \"stopPrice\": null,\n        \"timeInForce\": \"GTC\",\n        \"postOnly\": false,\n        \"hidden\": false,\n        \"iceberg\": false,\n        \"leverage\": \"3\",\n        \"forceHold\": false,\n        \"closeOrder\": false,\n        \"visibleSize\": 0,\n        \"clientOid\": \"5c52e11203aa677f33e493fb\",\n        \"remark\": null,\n        \"tags\": \"\",\n        \"isActive\": true,\n        \"cancelExist\": false,\n        \"createdAt\": 1729236185949,\n        \"updatedAt\": 1729236185949,\n        \"endAt\": null,\n        \"orderTime\": 1729236185885647952,\n        \"settleCurrency\": \"USDT\",\n        \"marginMode\": \"ISOLATED\",\n        \"avgDealPrice\": \"0\",\n        \"filledSize\": 0,\n        \"filledValue\": \"0\",\n        \"status\": \"open\",\n        \"reduceOnly\": false\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetOrderByOrderIdResp.from_dict(common_response.data)

    def test_get_order_by_client_oid_req_model(self):
        """
       get_order_by_client_oid
       Get Order By ClientOid
       /api/v1/orders/byClientOid
       """
        data = "{\"clientOid\": \"5c52e11203aa677f33e493fb\"}"
        req = GetOrderByClientOidReq.from_json(data)

    def test_get_order_by_client_oid_resp_model(self):
        """
        get_order_by_client_oid
        Get Order By ClientOid
        /api/v1/orders/byClientOid
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"id\": \"250444645610336256\",\n        \"symbol\": \"XRPUSDTM\",\n        \"type\": \"limit\",\n        \"side\": \"buy\",\n        \"price\": \"0.1\",\n        \"size\": 1,\n        \"value\": \"1\",\n        \"dealValue\": \"0\",\n        \"dealSize\": 0,\n        \"stp\": \"\",\n        \"stop\": \"\",\n        \"stopPriceType\": \"\",\n        \"stopTriggered\": false,\n        \"stopPrice\": null,\n        \"timeInForce\": \"GTC\",\n        \"postOnly\": false,\n        \"hidden\": false,\n        \"iceberg\": false,\n        \"leverage\": \"3\",\n        \"forceHold\": false,\n        \"closeOrder\": false,\n        \"visibleSize\": 0,\n        \"clientOid\": \"5c52e11203aa677f33e493fb\",\n        \"remark\": null,\n        \"tags\": \"\",\n        \"isActive\": true,\n        \"cancelExist\": false,\n        \"createdAt\": 1732523858568,\n        \"updatedAt\": 1732523858568,\n        \"endAt\": null,\n        \"orderTime\": 1732523858550892322,\n        \"settleCurrency\": \"USDT\",\n        \"marginMode\": \"ISOLATED\",\n        \"avgDealPrice\": \"0\",\n        \"filledSize\": 0,\n        \"filledValue\": \"0\",\n        \"status\": \"open\",\n        \"reduceOnly\": false\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetOrderByClientOidResp.from_dict(common_response.data)

    def test_get_order_list_req_model(self):
        """
       get_order_list
       Get Order List
       /api/v1/orders
       """
        data = "{\"status\": \"done\", \"symbol\": \"example_string_default_value\", \"side\": \"buy\", \"type\": \"limit\", \"startAt\": 123456, \"endAt\": 123456, \"currentPage\": 1, \"pageSize\": 50}"
        req = GetOrderListReq.from_json(data)

    def test_get_order_list_resp_model(self):
        """
        get_order_list
        Get Order List
        /api/v1/orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"id\": \"230181737576050688\",\n                \"symbol\": \"PEOPLEUSDTM\",\n                \"type\": \"limit\",\n                \"side\": \"buy\",\n                \"price\": \"0.05\",\n                \"size\": 10,\n                \"value\": \"5\",\n                \"dealValue\": \"0\",\n                \"dealSize\": 0,\n                \"stp\": \"\",\n                \"stop\": \"\",\n                \"stopPriceType\": \"\",\n                \"stopTriggered\": false,\n                \"stopPrice\": null,\n                \"timeInForce\": \"GTC\",\n                \"postOnly\": false,\n                \"hidden\": false,\n                \"iceberg\": false,\n                \"leverage\": \"1\",\n                \"forceHold\": false,\n                \"closeOrder\": false,\n                \"visibleSize\": 0,\n                \"clientOid\": \"5a80bd847f1811ef8a7faa665a37b3d7\",\n                \"remark\": null,\n                \"tags\": \"\",\n                \"isActive\": true,\n                \"cancelExist\": false,\n                \"createdAt\": 1727692804813,\n                \"updatedAt\": 1727692804813,\n                \"endAt\": null,\n                \"orderTime\": 1727692804808418000,\n                \"settleCurrency\": \"USDT\",\n                \"marginMode\": \"ISOLATED\",\n                \"avgDealPrice\": \"0\",\n                \"filledSize\": 0,\n                \"filledValue\": \"0\",\n                \"status\": \"open\",\n                \"reduceOnly\": false\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetOrderListResp.from_dict(common_response.data)

    def test_get_recent_closed_orders_req_model(self):
        """
       get_recent_closed_orders
       Get Recent Closed Orders
       /api/v1/recentDoneOrders
       """
        data = "{\"symbol\": \"XBTUSDTM\"}"
        req = GetRecentClosedOrdersReq.from_json(data)

    def test_get_recent_closed_orders_resp_model(self):
        """
        get_recent_closed_orders
        Get Recent Closed Orders
        /api/v1/recentDoneOrders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"236387137732231168\",\n            \"symbol\": \"XRPUSDTM\",\n            \"type\": \"market\",\n            \"side\": \"buy\",\n            \"price\": \"0\",\n            \"size\": 1,\n            \"value\": \"5.51\",\n            \"dealValue\": \"5.511\",\n            \"dealSize\": 1,\n            \"stp\": \"\",\n            \"stop\": \"\",\n            \"stopPriceType\": \"\",\n            \"stopTriggered\": false,\n            \"stopPrice\": null,\n            \"timeInForce\": \"GTC\",\n            \"postOnly\": false,\n            \"hidden\": false,\n            \"iceberg\": false,\n            \"leverage\": \"10.0\",\n            \"forceHold\": false,\n            \"closeOrder\": false,\n            \"visibleSize\": 0,\n            \"clientOid\": \"16698fe6-2746-4aeb-a7fa-61f633ab6090\",\n            \"remark\": null,\n            \"tags\": \"\",\n            \"isActive\": false,\n            \"cancelExist\": false,\n            \"createdAt\": 1729172287496,\n            \"updatedAt\": 1729172287568,\n            \"endAt\": 1729172287568,\n            \"orderTime\": 1729172287496950800,\n            \"settleCurrency\": \"USDT\",\n            \"marginMode\": \"ISOLATED\",\n            \"avgDealPrice\": \"0.5511\",\n            \"filledSize\": 1,\n            \"filledValue\": \"5.511\",\n            \"status\": \"done\",\n            \"reduceOnly\": false\n        },\n        {\n            \"id\": \"236317213710184449\",\n            \"symbol\": \"XBTUSDTM\",\n            \"type\": \"market\",\n            \"side\": \"buy\",\n            \"price\": \"0\",\n            \"size\": 1,\n            \"value\": \"67.4309\",\n            \"dealValue\": \"67.4309\",\n            \"dealSize\": 1,\n            \"stp\": \"\",\n            \"stop\": \"\",\n            \"stopPriceType\": \"\",\n            \"stopTriggered\": false,\n            \"stopPrice\": null,\n            \"timeInForce\": \"GTC\",\n            \"postOnly\": false,\n            \"hidden\": false,\n            \"iceberg\": false,\n            \"leverage\": \"3\",\n            \"forceHold\": false,\n            \"closeOrder\": false,\n            \"visibleSize\": 0,\n            \"clientOid\": \"5c52e11203aa677f33e493fb\",\n            \"remark\": null,\n            \"tags\": \"\",\n            \"isActive\": false,\n            \"cancelExist\": false,\n            \"createdAt\": 1729155616310,\n            \"updatedAt\": 1729155616324,\n            \"endAt\": 1729155616324,\n            \"orderTime\": 1729155616310180400,\n            \"settleCurrency\": \"USDT\",\n            \"marginMode\": \"ISOLATED\",\n            \"avgDealPrice\": \"67430.9\",\n            \"filledSize\": 1,\n            \"filledValue\": \"67.4309\",\n            \"status\": \"done\",\n            \"reduceOnly\": false\n        },\n        {\n            \"id\": \"236317094436728832\",\n            \"symbol\": \"XBTUSDTM\",\n            \"type\": \"market\",\n            \"side\": \"buy\",\n            \"price\": \"0\",\n            \"size\": 1,\n            \"value\": \"67.445\",\n            \"dealValue\": \"67.445\",\n            \"dealSize\": 1,\n            \"stp\": \"\",\n            \"stop\": \"\",\n            \"stopPriceType\": \"\",\n            \"stopTriggered\": false,\n            \"stopPrice\": null,\n            \"timeInForce\": \"GTC\",\n            \"postOnly\": false,\n            \"hidden\": false,\n            \"iceberg\": false,\n            \"leverage\": \"3\",\n            \"forceHold\": false,\n            \"closeOrder\": false,\n            \"visibleSize\": 0,\n            \"clientOid\": \"5c52e11203aa677f33e493fb\",\n            \"remark\": null,\n            \"tags\": \"\",\n            \"isActive\": false,\n            \"cancelExist\": false,\n            \"createdAt\": 1729155587873,\n            \"updatedAt\": 1729155587946,\n            \"endAt\": 1729155587946,\n            \"orderTime\": 1729155587873332000,\n            \"settleCurrency\": \"USDT\",\n            \"marginMode\": \"ISOLATED\",\n            \"avgDealPrice\": \"67445.0\",\n            \"filledSize\": 1,\n            \"filledValue\": \"67.445\",\n            \"status\": \"done\",\n            \"reduceOnly\": false\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetRecentClosedOrdersResp.from_dict(common_response.data)

    def test_get_stop_order_list_req_model(self):
        """
       get_stop_order_list
       Get Stop Order List
       /api/v1/stopOrders
       """
        data = "{\"symbol\": \"XBTUSDTM\", \"side\": \"buy\", \"type\": \"limit\", \"startAt\": 123456, \"endAt\": 123456, \"currentPage\": 123456, \"pageSize\": 50}"
        req = GetStopOrderListReq.from_json(data)

    def test_get_stop_order_list_resp_model(self):
        """
        get_stop_order_list
        Get Stop Order List
        /api/v1/stopOrders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"id\": \"230181737576050688\",\n                \"symbol\": \"PEOPLEUSDTM\",\n                \"type\": \"limit\",\n                \"side\": \"buy\",\n                \"price\": \"0.05\",\n                \"size\": 10,\n                \"value\": \"5\",\n                \"dealValue\": \"0\",\n                \"dealSize\": 0,\n                \"stp\": \"\",\n                \"stop\": \"\",\n                \"stopPriceType\": \"\",\n                \"stopTriggered\": false,\n                \"stopPrice\": null,\n                \"timeInForce\": \"GTC\",\n                \"postOnly\": false,\n                \"hidden\": false,\n                \"iceberg\": false,\n                \"leverage\": \"1\",\n                \"forceHold\": false,\n                \"closeOrder\": false,\n                \"visibleSize\": 0,\n                \"clientOid\": \"5a80bd847f1811ef8a7faa665a37b3d7\",\n                \"remark\": null,\n                \"tags\": \"\",\n                \"isActive\": true,\n                \"cancelExist\": false,\n                \"createdAt\": 1727692804813,\n                \"updatedAt\": 1727692804813,\n                \"endAt\": null,\n                \"orderTime\": 1727692804808418000,\n                \"settleCurrency\": \"USDT\",\n                \"marginMode\": \"ISOLATED\",\n                \"avgDealPrice\": \"0\",\n                \"filledSize\": 0,\n                \"filledValue\": \"0\",\n                \"status\": \"open\",\n                \"reduceOnly\": false\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetStopOrderListResp.from_dict(common_response.data)

    def test_get_open_order_value_req_model(self):
        """
       get_open_order_value
       Get Open Order Value
       /api/v1/openOrderStatistics
       """
        data = "{\"symbol\": \"XBTUSDTM\"}"
        req = GetOpenOrderValueReq.from_json(data)

    def test_get_open_order_value_resp_model(self):
        """
        get_open_order_value
        Get Open Order Value
        /api/v1/openOrderStatistics
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"openOrderBuySize\": 1,\n        \"openOrderSellSize\": 0,\n        \"openOrderBuyCost\": \"0.0001\",\n        \"openOrderSellCost\": \"0\",\n        \"settleCurrency\": \"USDT\"\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetOpenOrderValueResp.from_dict(common_response.data)

    def test_get_recent_trade_history_req_model(self):
        """
       get_recent_trade_history
       Get Recent Trade History
       /api/v1/recentFills
       """
        data = "{\"symbol\": \"XBTUSDTM\"}"
        req = GetRecentTradeHistoryReq.from_json(data)

    def test_get_recent_trade_history_resp_model(self):
        """
        get_recent_trade_history
        Get Recent Trade History
        /api/v1/recentFills
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"tradeId\": \"1784277229880\",\n            \"orderId\": \"236317213710184449\",\n            \"side\": \"buy\",\n            \"liquidity\": \"taker\",\n            \"forceTaker\": false,\n            \"price\": \"67430.9\",\n            \"size\": 1,\n            \"value\": \"67.4309\",\n            \"openFeePay\": \"0.04045854\",\n            \"closeFeePay\": \"0\",\n            \"stop\": \"\",\n            \"feeRate\": \"0.00060\",\n            \"fixFee\": \"0\",\n            \"feeCurrency\": \"USDT\",\n            \"marginMode\": \"ISOLATED\",\n            \"fee\": \"0.04045854\",\n            \"settleCurrency\": \"USDT\",\n            \"orderType\": \"market\",\n            \"displayType\": \"market\",\n            \"tradeType\": \"trade\",\n            \"subTradeType\": null,\n            \"tradeTime\": 1729155616320000000,\n            \"createdAt\": 1729155616493\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"tradeId\": \"1784277132002\",\n            \"orderId\": \"236317094436728832\",\n            \"side\": \"buy\",\n            \"liquidity\": \"taker\",\n            \"forceTaker\": false,\n            \"price\": \"67445\",\n            \"size\": 1,\n            \"value\": \"67.445\",\n            \"openFeePay\": \"0\",\n            \"closeFeePay\": \"0.040467\",\n            \"stop\": \"\",\n            \"feeRate\": \"0.00060\",\n            \"fixFee\": \"0\",\n            \"feeCurrency\": \"USDT\",\n            \"marginMode\": \"ISOLATED\",\n            \"fee\": \"0.040467\",\n            \"settleCurrency\": \"USDT\",\n            \"orderType\": \"market\",\n            \"displayType\": \"market\",\n            \"tradeType\": \"trade\",\n            \"subTradeType\": null,\n            \"tradeTime\": 1729155587944000000,\n            \"createdAt\": 1729155588104\n        }\n    ]\n}"
        common_response = RestResponse.from_json(data)
        resp = GetRecentTradeHistoryResp.from_dict(common_response.data)

    def test_get_trade_history_req_model(self):
        """
       get_trade_history
       Get Trade History
       /api/v1/fills
       """
        data = "{\"orderId\": \"236655147005071361\", \"symbol\": \"example_string_default_value\", \"side\": \"buy\", \"type\": \"limit\", \"tradeTypes\": \"trade\", \"startAt\": 123456, \"endAt\": 123456, \"currentPage\": 1, \"pageSize\": 50}"
        req = GetTradeHistoryReq.from_json(data)

    def test_get_trade_history_resp_model(self):
        """
        get_trade_history
        Get Trade History
        /api/v1/fills
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 2,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"symbol\": \"XBTUSDTM\",\n                \"tradeId\": \"1828954878212\",\n                \"orderId\": \"284486580251463680\",\n                \"side\": \"buy\",\n                \"liquidity\": \"taker\",\n                \"forceTaker\": false,\n                \"price\": \"86275.1\",\n                \"size\": 1,\n                \"value\": \"86.2751\",\n                \"openFeePay\": \"0.05176506\",\n                \"closeFeePay\": \"0\",\n                \"stop\": \"\",\n                \"feeRate\": \"0.00060\",\n                \"fixFee\": \"0\",\n                \"feeCurrency\": \"USDT\",\n                \"subTradeType\": null,\n                \"marginMode\": \"CROSS\",\n                \"openFeeTaxPay\": \"0\",\n                \"closeFeeTaxPay\": \"0\",\n                \"displayType\": \"market\",\n                \"fee\": \"0.05176506\",\n                \"settleCurrency\": \"USDT\",\n                \"orderType\": \"market\",\n                \"tradeType\": \"trade\",\n                \"tradeTime\": 1740640088244000000,\n                \"createdAt\": 1740640088427\n            }\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = GetTradeHistoryResp.from_dict(common_response.data)

    def test_cancel_all_orders_v1_req_model(self):
        """
       cancel_all_orders_v1
       Cancel All Orders - V1
       /api/v1/orders
       """
        data = "{\"symbol\": \"XBTUSDTM\"}"
        req = CancelAllOrdersV1Req.from_json(data)

    def test_cancel_all_orders_v1_resp_model(self):
        """
        cancel_all_orders_v1
        Cancel All Orders - V1
        /api/v1/orders
        """
        data = "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"cancelledOrderIds\": [\n            \"235919172150824960\",\n            \"235919172150824961\"\n        ]\n    }\n}"
        common_response = RestResponse.from_json(data)
        resp = CancelAllOrdersV1Resp.from_dict(common_response.data)
