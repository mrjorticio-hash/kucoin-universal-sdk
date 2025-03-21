import logging
import os
import threading
import unittest

from kucoin_universal_sdk.api.client import DefaultClient
from kucoin_universal_sdk.model.client_option import ClientOptionBuilder
from kucoin_universal_sdk.model.constants import GLOBAL_API_ENDPOINT, GLOBAL_FUTURES_API_ENDPOINT, \
    GLOBAL_BROKER_API_ENDPOINT
from kucoin_universal_sdk.model.websocket_option import WebSocketClientOptionBuilder

logging.basicConfig(level=logging.DEBUG)


class SpotPublicWsTest(unittest.TestCase):
    def setUp(self):
        key = os.getenv("API_KEY")
        secret = os.getenv("API_SECRET")
        passphrase = os.getenv("API_PASSPHRASE")

        if not key or not secret or not passphrase:
            self.fail("Environment variables API_KEY, API_SECRET, and API_PASSPHRASE must be set.")

        # Create client option
        ws_client_option = WebSocketClientOptionBuilder().build()
        client_option = (
            ClientOptionBuilder()
            .set_key(key)
            .set_secret(secret)
            .set_passphrase(passphrase)
            .set_websocket_client_option(ws_client_option)
            .set_spot_endpoint(GLOBAL_API_ENDPOINT)
            .set_futures_endpoint(GLOBAL_FUTURES_API_ENDPOINT)
            .set_broker_endpoint(GLOBAL_BROKER_API_ENDPOINT)
            .build()
        )

        # Create WebSocket client
        client = DefaultClient(client_option)
        kucoin_ws_client = client.ws_service()
        self.api = kucoin_ws_client.new_spot_public_ws()
        self.api.start()

    def tearDown(self):
        self.api.stop()

    def _test_subscription(self, subscribe_method, *args):
        message_count = 0
        event = threading.Event()

        def callback(topic: str, subject: str, data):
            nonlocal message_count
            message_count += 1
            print(f"Received message {message_count} on topic: {topic}, subject: {subject}, data: {data.to_json()}")
            if message_count == 10:
                print("Received 10 messages. Unsubscribe...")
                event.set()

        subscription_id = subscribe_method(*args, callback)
        try:
            event.wait()
        finally:
            self.api.unsubscribe(subscription_id)

    def test_public_all_tickers(self):
        self._test_subscription(self.api.all_tickers)

    def test_public_tickers(self):
        symbols = ["BTC-USDT", "ETH-USDT"]
        self._test_subscription(self.api.ticker, symbols)

    def test_public_klines(self):
        self._test_subscription(self.api.klines, "BTC-USDT", "1min")

    def test_public_trade(self):
        symbols = ["BTC-USDT", "ETH-USDT"]
        self._test_subscription(self.api.trade, symbols)

    def test_public_market_snapshot(self):
        symbols = "BTC-USDT"
        self._test_subscription(self.api.market_snapshot, symbols)

    def test_public_symbol_snapshot(self):
        symbols = "BTC-USDT"
        self._test_subscription(self.api.symbol_snapshot, symbols)

    def test_public_orderbook_lever1(self):
        symbols = ["BTC-USDT", "ETH-USDT"]
        self._test_subscription(self.api.orderbook_level1, symbols)

    def test_public_orderbook_lever5(self):
        symbols = ["BTC-USDT", "ETH-USDT"]
        self._test_subscription(self.api.orderbook_level5, symbols)

    def test_public_orderbook_lever50(self):
        symbols = ["BTC-USDT", "ETH-USDT"]
        self._test_subscription(self.api.orderbook_level50, symbols)

    def test_public_orderbook_increment(self):
        symbols = ["BTC-USDT", "ETH-USDT"]
        self._test_subscription(self.api.orderbook_increment, symbols)

    def test_public_call_auction_info(self):
        symbols = "PAWS-USDT"
        self._test_subscription(self.api.call_auction_info, symbols)

    def test_public_call_auction_orderbook_level50(self):
        symbols = "PAWS-USDT"
        self._test_subscription(self.api.call_auction_orderbook_level50, symbols)