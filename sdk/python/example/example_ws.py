import logging
import os
import time

from kucoin_universal_sdk.api import DefaultClient
from kucoin_universal_sdk.generate.futures.futures_public import FuturesPublicWS
from kucoin_universal_sdk.generate.futures.futures_public import TickerV2Event
from kucoin_universal_sdk.generate.spot.spot_public import SpotPublicWS
from kucoin_universal_sdk.generate.spot.spot_public import TickerEvent
from kucoin_universal_sdk.model import ClientOptionBuilder
from kucoin_universal_sdk.model import GLOBAL_API_ENDPOINT, GLOBAL_FUTURES_API_ENDPOINT, \
    GLOBAL_BROKER_API_ENDPOINT
from kucoin_universal_sdk.model import WebSocketClientOptionBuilder


def ws_example():
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
        .set_spot_endpoint(GLOBAL_API_ENDPOINT)
        .set_futures_endpoint(GLOBAL_FUTURES_API_ENDPOINT)
        .set_broker_endpoint(GLOBAL_BROKER_API_ENDPOINT)
        .build()
    )
    client = DefaultClient(client_option)
    # Get the websocket service
    kucoin_ws_service = client.ws_service()
    spot_ws_example(kucoin_ws_service.new_spot_public_ws())
    futures_ws_example(kucoin_ws_service.new_futures_public_ws())


def spot_ws_example(spot_public_ws: SpotPublicWS):
    try:
        # Start WebSocket
        spot_public_ws.start()
    except Exception as e:
        logging.error(f"failed to start spot public websocket: {e}")
        return

    try:
        def ticker_event_callback(topic: str, subject: str, data: TickerEvent) -> None:
            # Process logic
            logging.info(
                f"received ticker event {topic} {subject} {data.sequence} {data.price} {data.time} {data.size}")

        # Get ticker
        try:
            sub_id = spot_public_ws.ticker(["BTC-USDT"], ticker_event_callback)
        except Exception as e:
            logging.fatal(f"subscribe error: {e}")
            return

        # Triggered when certain conditions are met
        time.sleep(10)

        # Unsubscribe by sub id
        try:
            spot_public_ws.unsubscribe(sub_id)
        except Exception as e:
            logging.fatal(f"unsubscribe error: {e}, id: {sub_id}")
    finally:
        spot_public_ws.stop()


def futures_ws_example(futures_public_ws: FuturesPublicWS):
    try:
        # Start WebSocket
        futures_public_ws.start()
    except Exception as e:
        logging.fatal(f"failed to start futures public websocket: {e}")
        return

    try:
        def ticker_event_v2_callback(topic: str, subject: str, data: TickerV2Event) -> None:
            logging.info(f"received ticker event {data.to_json()}")

        # Get Ticker
        try:
            sub_id = futures_public_ws.ticker_v2("XBTUSDTM", ticker_event_v2_callback)
        except Exception as e:
            logging.fatal(f"subscribe error: {e}")
            return
        # Triggered when certain conditions are met
        time.sleep(10)

        # Unsubscribe
        try:
            futures_public_ws.unsubscribe(sub_id)
        except Exception as e:
            logging.fatal(f"unsubscribe error: {e}, id: {sub_id}")
    finally:
        futures_public_ws.stop()


if __name__ == "__main__":
    ws_example()
