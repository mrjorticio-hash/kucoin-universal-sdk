import logging
import os
import unittest
import uuid

from kucoin_universal_sdk.api import DefaultClient
from kucoin_universal_sdk.generate.account.fee import GetBasicFeeReqBuilder, GetBasicFeeReq
from kucoin_universal_sdk.generate.earn.earn import GetSavingsProductsReqBuilder
from kucoin_universal_sdk.generate.futures.order import AddOrderReqBuilder, AddOrderReq, \
    GetOrderByOrderIdReqBuilder as FuturesGetOrderByOrderIdReqBuilder, CancelOrderByIdReqBuilder
from kucoin_universal_sdk.generate.margin.order import AddOrderReqBuilder as MarginAddOrderReqBuilder, \
    AddOrderReq as MarginAddOrderReq, \
    GetOrderByOrderIdReqBuilder as MarginGetOrderByOrderIdReqBuilder, \
    CancelOrderByOrderIdReqBuilder as MarginCancelOrderByOrderIdReqBuilder
from kucoin_universal_sdk.generate.spot.market import Get24hrStatsReqBuilder
from kucoin_universal_sdk.generate.spot.order import AddOrderSyncReqBuilder, AddOrderSyncReq, \
    GetOrderByOrderIdReqBuilder, CancelOrderByOrderIdReqBuilder
from kucoin_universal_sdk.model import ClientOptionBuilder, RestResponse
from kucoin_universal_sdk.model import GLOBAL_API_ENDPOINT, GLOBAL_FUTURES_API_ENDPOINT, \
    GLOBAL_BROKER_API_ENDPOINT
from kucoin_universal_sdk.model import TransportOptionBuilder


class CheckAllServiceTest(unittest.TestCase):

    def setUp(self):
        logging.basicConfig(
            level=logging.INFO,
            format='%(asctime)s %(levelname)s - %(message)s',
            datefmt='%Y-%m-%d %H:%M:%S'
        )

        #  Retrieve API secret information from environment variables
        key = os.getenv("API_KEY", "")
        secret = os.getenv("API_SECRET", "")
        passphrase = os.getenv("API_PASSPHRASE", "")

        # Set specific options, others will fall back to default values
        http_transport_option = (
            TransportOptionBuilder()
            .set_keep_alive(True)
            .set_max_pool_size(10)
            .set_max_connection_per_pool(10)
            .build()
        )

        # Create a client using the specified options
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
        self.client = DefaultClient(client_option)
        self.rest_service = self.client.rest_service()

    def check_common_response(self, common_resp: RestResponse):
        self.assertEqual(common_resp.code, '200000')
        self.assertIsNotNone(common_resp.rate_limit)

    def test_account_service(self):
        account_service = self.rest_service.get_account_service()
        fee_api = account_service.get_fee_api()
        basic_fee_response = fee_api.get_basic_fee(
            GetBasicFeeReqBuilder().set_currency_type(GetBasicFeeReq.CurrencyTypeEnum.T_0).build())
        self.check_common_response(basic_fee_response.common_response)
        self.assertTrue(len(basic_fee_response.maker_fee_rate) > 0)
        self.assertTrue(len(basic_fee_response.taker_fee_rate) > 0)

    def test_earn_service(self):
        earn_service = self.rest_service.get_earn_service()
        earn_api = earn_service.get_earn_api()
        savings_products_response = earn_api.get_savings_products(
            GetSavingsProductsReqBuilder().set_currency("USDT").build())
        self.check_common_response(savings_products_response.common_response)
        self.assertTrue(len(savings_products_response.data) > 0)

    def test_margin_service(self):
        margin_service = self.rest_service.get_margin_service()
        order_api = margin_service.get_order_api()

        # add order
        add_order_req = MarginAddOrderReqBuilder().set_client_oid(uuid.uuid4().__str__()).set_side(
            MarginAddOrderReq.SideEnum.BUY).set_symbol(
            "BTC-USDT").set_type(MarginAddOrderReq.TypeEnum.LIMIT).set_price("10000").set_size("0.001").set_auto_repay(
            True).set_auto_borrow(True).set_is_isolated(True).build()
        add_order_resp = order_api.add_order(add_order_req)
        self.check_common_response(add_order_resp.common_response)
        self.assertTrue(len(add_order_resp.order_id) > 0)

        # query order
        query_order_req = MarginGetOrderByOrderIdReqBuilder().set_symbol("BTC-USDT").set_order_id(
            add_order_resp.order_id).build()
        query_order_resp = order_api.get_order_by_order_id(query_order_req)
        self.check_common_response(query_order_resp.common_response)
        self.assertTrue(len(query_order_resp.symbol), 0)

        # cancel order
        cancel_order_req = MarginCancelOrderByOrderIdReqBuilder().set_order_id(add_order_resp.order_id).set_symbol(
            "BTC-USDT").build()
        cancel_order_resp = order_api.cancel_order_by_order_id(cancel_order_req)
        self.check_common_response(cancel_order_resp.common_response)
        self.assertTrue(len(cancel_order_resp.order_id) > 0)

    def test_spot_service(self):
        spot_service = self.rest_service.get_spot_service()

        # query market api
        market_api = spot_service.get_market_api()
        stat_response = market_api.get24hr_stats(Get24hrStatsReqBuilder().set_symbol("BTC-USDT").build())
        self.check_common_response(stat_response.common_response)
        self.assertTrue(len(stat_response.last) > 0)

        order_api = spot_service.get_order_api()

        # add order
        add_order_sync_req = AddOrderSyncReqBuilder().set_client_oid(uuid.uuid4().__str__()).set_side(
            AddOrderSyncReq.SideEnum.BUY).set_symbol(
            "BTC-USDT").set_type(
            AddOrderSyncReq.TypeEnum.LIMIT).set_remark("sdk_test").set_price("10000").set_size("0.001").build()
        add_order_sync_resp = order_api.add_order_sync(add_order_sync_req)
        self.check_common_response(add_order_sync_resp.common_response)
        self.assertTrue(len(add_order_sync_resp.order_id) > 0)
        self.assertTrue(add_order_sync_resp.order_time > 0)

        # query order
        query_order_req = GetOrderByOrderIdReqBuilder().set_symbol("BTC-USDT").set_order_id(
            add_order_sync_resp.order_id).build()
        query_order_resp = order_api.get_order_by_order_id(query_order_req)
        self.check_common_response(query_order_resp.common_response)
        self.assertTrue(len(query_order_resp.symbol), 0)

        # cancel order
        cancel_order_req = CancelOrderByOrderIdReqBuilder().set_order_id(add_order_sync_resp.order_id).set_symbol(
            "BTC-USDT").build()
        cancel_order_resp = order_api.cancel_order_by_order_id(cancel_order_req)
        self.check_common_response(cancel_order_resp.common_response)
        self.assertTrue(len(cancel_order_resp.order_id) > 0)

    def test_futures_service(self):
        futures_service = self.rest_service.get_futures_service()

        # query market api
        market_api = futures_service.get_market_api()
        stat_response = market_api.get24hr_stats()
        self.check_common_response(stat_response.common_response)
        self.assertIsNotNone(stat_response.turnover_of24h)

        order_api = futures_service.get_order_api()

        # add order
        add_order_req = AddOrderReqBuilder().set_client_oid(uuid.uuid4().__str__()).set_side(
            AddOrderReq.SideEnum.BUY).set_symbol("XBTUSDTM").set_leverage(1).set_type(
            AddOrderReq.TypeEnum.LIMIT).set_remark("sdk_test").set_margin_mode(
            AddOrderReq.MarginModeEnum.CROSS).set_price("1").set_size(1).build()
        add_order_resp = order_api.add_order(add_order_req)
        self.check_common_response(add_order_resp.common_response)
        self.assertTrue(len(add_order_resp.order_id) > 0)

        # query order
        query_order_req = FuturesGetOrderByOrderIdReqBuilder().set_order_id(add_order_resp.order_id).build()
        query_order_resp = order_api.get_order_by_order_id(query_order_req)
        self.check_common_response(query_order_resp.common_response)
        self.assertTrue(len(query_order_resp.symbol), 0)

        # cancel order
        cancel_order_req = CancelOrderByIdReqBuilder().set_order_id(add_order_resp.order_id).build()
        cancel_order_resp = order_api.cancel_order_by_id(cancel_order_req)
        self.check_common_response(cancel_order_resp.common_response)
        self.assertTrue(len(cancel_order_resp.cancelled_order_ids) > 0)
