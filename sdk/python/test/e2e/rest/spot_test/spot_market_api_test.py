import os
import unittest

from kucoin_universal_sdk.api.client import DefaultClient
from kucoin_universal_sdk.extension.interceptor.logging import LoggingInterceptor
from kucoin_universal_sdk.generate.spot.market import GetCallAuctionPartOrderBookReqBuilder, \
    GetCallAuctionInfoReqBuilder
from kucoin_universal_sdk.generate.spot.market.model_get24hr_stats_req import Get24hrStatsReqBuilder
from kucoin_universal_sdk.generate.spot.market.model_get_all_symbols_req import GetAllSymbolsReqBuilder
from kucoin_universal_sdk.generate.spot.market.model_get_announcements_req import GetAnnouncementsReqBuilder, \
    GetAnnouncementsReq
from kucoin_universal_sdk.generate.spot.market.model_get_currency_req import GetCurrencyReqBuilder
from kucoin_universal_sdk.generate.spot.market.model_get_fiat_price_req import GetFiatPriceReqBuilder
from kucoin_universal_sdk.generate.spot.market.model_get_full_order_book_req import GetFullOrderBookReqBuilder
from kucoin_universal_sdk.generate.spot.market.model_get_klines_req import GetKlinesReqBuilder, GetKlinesReq
from kucoin_universal_sdk.generate.spot.market.model_get_part_order_book_req import GetPartOrderBookReqBuilder
from kucoin_universal_sdk.generate.spot.market.model_get_symbol_req import GetSymbolReqBuilder
from kucoin_universal_sdk.generate.spot.market.model_get_ticker_req import GetTickerReqBuilder
from kucoin_universal_sdk.generate.spot.market.model_get_trade_history_req import GetTradeHistoryReqBuilder
from kucoin_universal_sdk.model.client_option import ClientOptionBuilder
from kucoin_universal_sdk.model.constants import GLOBAL_API_ENDPOINT, GLOBAL_BROKER_API_ENDPOINT, \
    GLOBAL_FUTURES_API_ENDPOINT
from kucoin_universal_sdk.model.transport_option import TransportOptionBuilder


class SpotApiTest(unittest.TestCase):
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
        self.api = kucoin_rest_api.get_spot_service().get_market_api()

    def test_get_private_token_req(self):
        """
            get_private_token
            Get Private Token - Spot/Margin
            /api/v1/bullet-private
        """

        try:
            resp = self.api.get_private_token()
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_public_token_req(self):
        """
            get_public_token
            Get Public Token - Spot/Margin
            /api/v1/bullet-public
        """

        try:
            resp = self.api.get_public_token()
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_all_tickers_req(self):
        """
            get_all_tickers
            Get All Tickers
            /api/v1/market/allTickers
        """

        try:
            resp = self.api.get_all_tickers()
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_klines_req(self):
        """
            get_klines
            Get Klines
            /api/v1/market/candles
        """

        builder = GetKlinesReqBuilder()
        builder.set_symbol("BTC-USDT").set_type(GetKlinesReq.TypeEnum.T_1MIN).set_start_at(1566703297).set_end_at(
            1566789757)
        req = builder.build()
        try:
            resp = self.api.get_klines(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_trade_history_req(self):
        """
            get_trade_history
            Get Trade History
            /api/v1/market/histories
        """

        builder = GetTradeHistoryReqBuilder()
        builder.set_symbol("BTC-USDT")
        req = builder.build()
        try:
            resp = self.api.get_trade_history(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_ticker_req(self):
        """
            get_ticker
            Get Ticker
            /api/v1/market/orderbook/level1
        """

        builder = GetTickerReqBuilder()
        builder.set_symbol("BTC-USDT")
        req = builder.build()
        try:
            resp = self.api.get_ticker(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_part_order_book_req(self):
        """
            get_part_order_book
            Get Part OrderBook
            /api/v1/market/orderbook/level2_{size}
        """

        builder = GetPartOrderBookReqBuilder()
        builder.set_symbol("BTC-USDT").set_size("20")
        req = builder.build()
        try:
            resp = self.api.get_part_order_book(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get24hr_stats_req(self):
        """
            get24hr_stats
            Get 24hr Stats
            /api/v1/market/stats
        """

        builder = Get24hrStatsReqBuilder()
        builder.set_symbol("BTC-USDT")
        req = builder.build()
        try:
            resp = self.api.get24hr_stats(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_market_list_req(self):
        """
            get_market_list
            Get Market List
            /api/v1/markets
        """

        try:
            resp = self.api.get_market_list()
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_fiat_price_req(self):
        """
            get_fiat_price
            Get Fiat Price
            /api/v1/prices
        """

        builder = GetFiatPriceReqBuilder()
        builder.set_base("USD").set_currencies("BTC,ETH")
        req = builder.build()
        try:
            resp = self.api.get_fiat_price(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_service_status_req(self):
        """
            get_service_status
            Get Service Status
            /api/v1/status
        """

        try:
            resp = self.api.get_service_status()
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_server_time_req(self):
        """
            get_server_time
            Get Server Time
            /api/v1/timestamp
        """

        try:
            resp = self.api.get_server_time()
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_all_symbols_req(self):
        """
            get_all_symbols
            Get All Symbols
            /api/v2/symbols
        """

        builder = GetAllSymbolsReqBuilder()
        builder.set_market("USDS")
        req = builder.build()
        try:
            resp = self.api.get_all_symbols(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_symbol_req(self):
        """
            get_symbol
            Get Symbol
            /api/v2/symbols/{symbol}
        """

        builder = GetSymbolReqBuilder()
        builder.set_symbol("BTC-USDT")
        req = builder.build()
        try:
            resp = self.api.get_symbol(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_announcements_req(self):
        """
            get_announcements
            Get Announcements
            /api/v3/announcements
        """

        builder = GetAnnouncementsReqBuilder()
        builder.set_ann_type(GetAnnouncementsReq.AnnTypeEnum.LATEST_ANNOUNCEMENTS).set_lang(GetAnnouncementsReq.LangEnum.EN_US)
        req = builder.build()
        try:
            resp = self.api.get_announcements(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_currency_req(self):
        """
            get_currency
            Get Currency
            /api/v3/currencies/{currency}
        """

        builder = GetCurrencyReqBuilder()
        builder.set_currency("BTC").set_chain("btc")
        req = builder.build()
        try:
            resp = self.api.get_currency(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_all_currencies_req(self):
        """
            get_all_currencies
            Get All Currencies
            /api/v3/currencies
        """

        try:
            resp = self.api.get_all_currencies()
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_full_order_book_req(self):
        """
            get_full_order_book
            Get Full OrderBook
            /api/v3/market/orderbook/level2
        """

        builder = GetFullOrderBookReqBuilder()
        builder.set_symbol("BTC-USDT")
        req = builder.build()
        try:
            resp = self.api.get_full_order_book(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_call_auction_part_order_book_req(self):
        """
            get_call_auction_part_order_book
            Get Call Auction Part OrderBook
            /api/v1/market/orderbook/callauction/level2_{size}
        """

        builder = GetCallAuctionPartOrderBookReqBuilder()
        builder.set_symbol("PAWS-USDT").set_size("20")
        req = builder.build()
        try:
            resp = self.api.get_call_auction_part_order_book(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_call_auction_info_req(self):
        """
            get_call_auction_info
            Get Call Auction Info
            /api/v1/market/callauctionData
        """

        builder = GetCallAuctionInfoReqBuilder()
        builder.set_symbol("PAWS-USDT")
        req = builder.build()
        try:
            resp = self.api.get_call_auction_info(req)
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e

    def test_get_client_ip_address_req(self):
        """
            get_client_ip_address
            Get Client IP Address
            /api/v1/my-ip
        """

        try:
            resp = self.api.get_client_ip_address()
            print("code: ", resp.common_response.code)
            print("msg: ", resp.common_response.message)
            print("data: ", resp.to_json())
        except Exception as e:
            print("error: ", e)
            raise e