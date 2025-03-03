import logging
import os
import time
from collections import defaultdict
from datetime import datetime
from typing import Dict

from pydantic import BaseModel

from kucoin_universal_sdk.api import DefaultClient
from kucoin_universal_sdk.generate.spot.spot_public import TradeEvent
from kucoin_universal_sdk.model import ClientOptionBuilder
from kucoin_universal_sdk.model import GLOBAL_API_ENDPOINT, GLOBAL_FUTURES_API_ENDPOINT, \
    GLOBAL_BROKER_API_ENDPOINT
from kucoin_universal_sdk.model import WebSocketClientOptionBuilder


class KLine(BaseModel):
    open: float
    high: float
    low: float
    close: float
    volume: float
    start_time: int
    end_time: int


kline_data: Dict[int, Dict[str, KLine]] = defaultdict(dict)

time_interval = 60


def on_trade_data(topic: str, subject: str, trade_event: TradeEvent):
    global kline_data

    symbol = trade_event.symbol
    price = float(trade_event.price)
    size = float(trade_event.size)
    timestamp = int(trade_event.time) // 1000000000

    period_start = timestamp // time_interval * time_interval
    period_end = period_start + time_interval

    if symbol not in kline_data[period_start]:
        kline = KLine(
            open=price,
            high=price,
            low=price,
            close=price,
            volume=size,
            start_time=period_start,
            end_time=period_end,
        )
        kline_data[period_start][symbol] = kline
    else:
        kline = kline_data[period_start][symbol]
        kline.high = max(kline.high, price)
        kline.low = min(kline.low, price)
        kline.close = price
        kline.volume += size

    print(f"KLine for {symbol} @ {datetime.fromtimestamp(period_start)}: {kline}")


def print_kline_data():
    for period_start in sorted(kline_data.keys()):
        print(f"\nTime Period: {datetime.fromtimestamp(period_start)}")
        for symbol, kline in kline_data[period_start].items():
            print(f"  Symbol: {symbol}")
            print(f"    Open: {kline.open}")
            print(f"    High: {kline.high}")
            print(f"    Low: {kline.low}")
            print(f"    Close: {kline.close}")
            print(f"    Volume: {kline.volume}")
            print(f"    Start Time: {datetime.fromtimestamp(kline.start_time)}")
            print(f"    End Time: {datetime.fromtimestamp(kline.end_time)}")


if __name__ == '__main__':
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

    spot_public = client.ws_service().new_spot_public_ws()
    spot_public.start()

    sub_id = spot_public.trade(["BTC-USDT", "ETH-USDT"], on_trade_data)

    time.sleep(180)

    spot_public.unsubscribe(sub_id)
    spot_public.stop()

    print_kline_data()
