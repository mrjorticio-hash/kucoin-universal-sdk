import logging
import os
from time import sleep

from kucoin_universal_sdk.api import DefaultClient, KucoinRestService
from kucoin_universal_sdk.generate.futures.futures_public import FuturesPublicWS
from kucoin_universal_sdk.generate.spot.market import GetAllSymbolsReqBuilder
from kucoin_universal_sdk.generate.spot.spot_public import SpotPublicWS
from kucoin_universal_sdk.model import ClientOptionBuilder, TransportOptionBuilder
from kucoin_universal_sdk.model import GLOBAL_API_ENDPOINT, GLOBAL_FUTURES_API_ENDPOINT, \
    GLOBAL_BROKER_API_ENDPOINT
from kucoin_universal_sdk.model import WebSocketClientOptionBuilder


def ws_reconnect_test():
    logging.basicConfig(
        level=logging.INFO,
        format='%(asctime)s %(levelname)s - %(message)s',
        datefmt='%Y-%m-%d %H:%M:%S'
    )

    # Retrieve API secret information from environment variables
    key = os.getenv("API_KEY", "")
    secret = os.getenv("API_SECRET", "")
    passphrase = os.getenv("API_PASSPHRASE", "")

    # Set specific options, others will fall back to default values
    ws_client_option = (
        WebSocketClientOptionBuilder().build()
    )

    # Create a client using the specified options
    client_option = (
        ClientOptionBuilder()
        .set_key(key)
        .set_secret(secret)
        .set_passphrase(passphrase)
        .set_websocket_client_option(ws_client_option)
        .set_transport_option(TransportOptionBuilder().build())
        .set_spot_endpoint(GLOBAL_API_ENDPOINT)
        .set_futures_endpoint(GLOBAL_FUTURES_API_ENDPOINT)
        .set_broker_endpoint(GLOBAL_BROKER_API_ENDPOINT)
        .build()
    )
    client = DefaultClient(client_option)
    kucoin_ws_service = client.ws_service()
    symbols = get_spot_symbols(client.rest_service())
    spot_ws_example(kucoin_ws_service.new_spot_public_ws(), symbols)
    futures_ws_example(kucoin_ws_service.new_futures_public_ws())


def get_spot_symbols(rest_service: KucoinRestService):
    market_api = rest_service.get_spot_service().get_market_api()
    symbols_response = market_api.get_all_symbols(GetAllSymbolsReqBuilder().set_market("USDS").build())
    symbols = []
    for d in symbols_response.data:
        symbols.append(d.symbol)
    if len(symbols) > 50:
        symbols = symbols[:50]
    return symbols


def callback_func(topic: str, subject: str, data) -> None:
    pass


def spot_ws_example(spot_public_ws: SpotPublicWS, symbols):
    spot_public_ws.start()

    for symbol in symbols:
        spot_public_ws.trade([symbol], callback_func)

    spot_public_ws.ticker(["BTC-USDT", "ETH-USDT"], callback_func)
    logging.info("Spot subscribe [OK]")


def futures_ws_example(futures_public_ws: FuturesPublicWS):
    futures_public_ws.start()

    futures_public_ws.ticker_v2("XBTUSDTM", callback_func)
    futures_public_ws.ticker_v1("XBTUSDTM", callback_func)
    logging.info("Futures subscribe [OK]")


if __name__ == "__main__":
    ws_reconnect_test()
    logging.info("Total subscribe: 53")
    sleep(3600 * 24 * 365)
