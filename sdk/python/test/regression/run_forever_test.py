import logging
import os
import threading
from time import sleep

from kucoin_universal_sdk.api import DefaultClient
from kucoin_universal_sdk.generate.spot.market import GetAllSymbolsReqBuilder
from kucoin_universal_sdk.model import ClientOptionBuilder, WebSocketClientOptionBuilder
from kucoin_universal_sdk.model import GLOBAL_API_ENDPOINT, GLOBAL_FUTURES_API_ENDPOINT, \
    GLOBAL_BROKER_API_ENDPOINT
from kucoin_universal_sdk.model import TransportOptionBuilder

SLEEP_SECONDS = 5
SLEEP_FOREVER = 3600 * 24 * 30 * 12


class Runner():

    def __init__(self):
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

        ws_transport_option = (
            WebSocketClientOptionBuilder().build()
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
            .set_websocket_client_option(ws_transport_option)
            .build()
        )
        self.client = DefaultClient(client_option)
        self.rest_service = self.client.rest_service()
        self.ws_service = self.client.ws_service()
        self.market_api = self.rest_service.get_spot_service().get_market_api()

        self.market_error_counter = 0
        self.ws_start_stop_error_counter = 0
        self.ws_message_counter = 0

    def run_ws_service_star_stop_forever(self):
        def print_cb(topic, subject, data):
            len(data.to_json())

        while True:
            sleep(SLEEP_SECONDS)
            try:
                spot_public = self.ws_service.new_spot_public_ws()
                spot_public.start()
                sub_id = spot_public.ticker(["ETH-USDT", "BTC-USDT"], print_cb)
                sleep(SLEEP_SECONDS)
                spot_public.unsubscribe(sub_id)
                spot_public.stop()
                logging.info("WS STAR/STOP: [OK]")
            except Exception as e:
                logging.error("WS STAR/STOP: [ERROR]")
                logging.error(e)
                self.ws_start_stop_error_counter = self.ws_start_stop_error_counter + 1

    def run_ws_service_forever(self):
        def print_cb(topic, subject, data):
            len(data.to_json())
            self.ws_message_counter = self.ws_message_counter + 1

        spot_public = self.ws_service.new_spot_public_ws()
        spot_public.start()
        spot_public.orderbook_level50(["ETH-USDT", "BTC-USDT"], print_cb)
        logging.info("WS: [OK]")
        sleep(SLEEP_FOREVER)

    def run_market_api_forever(self):
        while True:
            sleep(SLEEP_SECONDS)
            try:
                get_all_symbols_req = GetAllSymbolsReqBuilder().set_market("USDS").build()
                get_all_symbols_resp = self.market_api.get_all_symbols(get_all_symbols_req)
                logging.info("MARKET API: [OK] %d ", len(get_all_symbols_resp.data))
            except Exception as e:
                logging.error("MARKET API: [ERROR]")
                logging.error(e)
                self.market_error_counter = self.market_error_counter + 1

    def print_error(self):
        while True:
            sleep(SLEEP_SECONDS)
            logging.info("Stat, Market_ERROR:[%d], WS_SS_ERROR:[%d], WS_MESSAGE:[%d]", self.market_error_counter,
                         self.ws_start_stop_error_counter, self.ws_message_counter)

    def run_forever(self):
        threading.Thread(target=self.run_market_api_forever, daemon=True).start()
        threading.Thread(target=self.run_ws_service_forever, daemon=True).start()
        threading.Thread(target=self.run_ws_service_star_stop_forever, daemon=True).start()
        threading.Thread(target=self.print_error, daemon=True).start()


if __name__ == '__main__':
    r = Runner()
    r.run_forever()
    sleep(SLEEP_FOREVER)
