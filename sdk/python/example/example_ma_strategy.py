"""
DISCLAIMER:
This strategy is provided for educational and illustrative purposes only. It is not intended to be used as financial
or investment advice. Trading cryptocurrencies involves significant risk, and you should carefully consider your
investment objectives, level of experience, and risk appetite. Past performance of any trading strategy is not
indicative of future results.

The authors and contributors of this example are not responsible for any financial losses or damages that may occur
from using this code. Use it at your own discretion and consult with a professional financial advisor if necessary.
"""

import logging
import os
import time
import uuid
from datetime import datetime
from enum import Enum

from kucoin_universal_sdk.api import DefaultClient
from kucoin_universal_sdk.generate.account.account import AccountAPI
from kucoin_universal_sdk.generate.account.account import GetSpotAccountListReqBuilder, \
    GetSpotAccountListReq
from kucoin_universal_sdk.generate.spot.market import Get24hrStatsReqBuilder
from kucoin_universal_sdk.generate.spot.market import GetKlinesReqBuilder, GetKlinesReq
from kucoin_universal_sdk.generate.spot.market import MarketAPI
from kucoin_universal_sdk.generate.spot.order import AddOrderSyncReqBuilder, AddOrderSyncReq
from kucoin_universal_sdk.generate.spot.order import CancelAllOrdersBySymbolReqBuilder
from kucoin_universal_sdk.generate.spot.order import GetOpenOrdersReqBuilder
from kucoin_universal_sdk.generate.spot.order import OrderAPI
from kucoin_universal_sdk.generate.spot.order import SetDcpReqBuilder
from kucoin_universal_sdk.model import ClientOptionBuilder
from kucoin_universal_sdk.model import GLOBAL_API_ENDPOINT, GLOBAL_FUTURES_API_ENDPOINT, \
    GLOBAL_BROKER_API_ENDPOINT
from kucoin_universal_sdk.model import TransportOptionBuilder


class Action(Enum):
    """
    Represents the possible trading actions.

    Attributes:
        BUY: Indicates a buy action.
        SELL: Indicates a sell action.
        SKIP: Indicates no action (skipping the trade).
    """
    BUY = "buy"
    SELL = "sell"
    SKIP = "skip"


def simple_moving_average_strategy(api: MarketAPI, symbol: str, short_window: int, long_window: int,
                                   end_time: int) -> Action:
    """
    A simple moving average crossover strategy example.

    Args:
        api (MarketAPI): The market API client.
        symbol (str): The trading pair symbol (e.g., 'BTC-USDT').
        short_window (int): The window size for the short-term moving average.
        long_window (int): The window size for the long-term moving average.
        end_time (int): The end time in seconds

    Returns:
        Action: The recommended trading action based on the moving average crossover logic.


    Strategy Logic:
        - Calculates two moving averages: short-term and long-term.
        - If the short-term moving average crosses above the long-term average, it signals a "buy".
        - If the short-term moving average crosses below the long-term average, it signals a "sell".
    """

    # Fetch historical prices for the given symbol
    start_time = end_time - long_window * 60
    logging.info(f"Query kline data start Time: {datetime.fromtimestamp(start_time).strftime('%Y-%m-%d %H:%M:%S')}, "
                 f"end Time: {datetime.fromtimestamp(end_time).strftime('%Y-%m-%d %H:%M:%S')}")
    get_kline_req = GetKlinesReqBuilder().set_symbol(symbol).set_type(GetKlinesReq.TypeEnum.T_1MIN).set_start_at(
        start_time).set_end_at(end_time).build()
    kline_resp = api.get_klines(get_kline_req)

    prices = []
    for kline_data in kline_resp.data:
        # use close data for prices
        prices.append(float(kline_data[2]))

    # Calculate the short-term and long-term moving averages
    short_ma = sum(prices[-short_window:]) / short_window
    long_ma = sum(prices[-long_window:]) / long_window

    # logging.info calculated moving averages for debugging
    logging.info(f"Short MA: {short_ma}, Long MA: {long_ma}")

    # Determine trading signals
    if short_ma > long_ma:
        logging.info(f"{symbol}: Short MA > Long MA. Should place a BUY order.")
        return Action.BUY
    elif short_ma < long_ma:
        logging.info(f"{symbol}: Short MA < Long MA. Should place a SELL order.")
        return Action.SELL
    else:
        return Action.SKIP


def get_last_trade_price(market_api: MarketAPI, symbol: str) -> float:
    """
    Fetches the last traded price of a specific symbol

    Args:
        market_api (MarketAPI): The market API client instance used to fetch data.
        symbol (str): The trading pair symbol (e.g., 'BTC-USDT').
    Returns:
        float: The last traded price of the symbol as a float.
    """
    get_stat_req = Get24hrStatsReqBuilder().set_symbol(symbol).build()
    stat_resp = market_api.get24hr_stats(get_stat_req)
    return float(stat_resp.last)


def check_available_balance(account_api: AccountAPI, last_trade_price: float, amount: float, action: Action) -> bool:
    """
    Checks if the available balance is sufficient for the trade.

    Args:
        account_api (AccountAPI): The account API client instance.
        last_trade_price (float): The last traded price of the symbol.
        amount (float): The amount of the asset to trade.
        action (Action): The trading action (BUY or SELL).

    Returns:
        bool: True if the balance is sufficient, False otherwise.

    Logic:
        - For a BUY action, checks the USDT balance to cover the trade value.
        - For a SELL action, checks the balance of the asset being sold.
    """
    # Calculate the total value of the trade
    trade_value = last_trade_price * amount

    # Determine the currency to check based on the trading action
    currency = "USDT" if action == Action.BUY else "DOGE"
    logging.info(f"Checking balance for currency: {currency}")

    # Build and send the account request for the specified currency
    get_account_req = (
        GetSpotAccountListReqBuilder()
        .set_type(GetSpotAccountListReq.TypeEnum.TRADE)
        .set_currency(currency)
        .build()
    )
    get_account_resp = account_api.get_spot_account_list(get_account_req)

    # Calculate the total available balance for the specified currency
    available_balance = sum(float(account_data.available) for account_data in get_account_resp.data)
    logging.info(f"Available {currency} balance: {available_balance:.2f}")

    # Return whether the available balance is sufficient for the trade
    if action == Action.BUY:
        if trade_value <= available_balance:
            logging.info(f"Balance is sufficient for the trade: {trade_value:.2f} {currency} required.")
            return True
        else:
            logging.warning(
                f"Insufficient balance: {trade_value:.2f} {currency} required, but only {available_balance:.2f} available.")
            return False
    else:
        return amount <= available_balance


def place_order(order_api: OrderAPI, symbol: str, action: Action, last_trade_price: float, amount: float,
                price_delta: float):
    """
    Places a limit order after handling any existing open orders.

    Args:
        order_api (OrderAPI): The order API client instance.
        symbol (str): The trading pair symbol (e.g., 'BTC-USDT').
        action (Action): The trading action (BUY or SELL).
        last_trade_price (float): The last traded price of the symbol.
        amount (float): The amount to trade.
        price_delta (float): The price adjustment percentage (relative to last_trade_price).

    Logic:
        1. Fetches all open orders for the symbol.
        2. Cancels any open orders before placing a new order.
        3. Places a limit order at a price adjusted by the `price_delta`.
        4. Sets a time-based cancellation policy (DCP) for the order.
    """

    # Fetch all open orders for the given symbol
    open_order_req = GetOpenOrdersReqBuilder().set_symbol(symbol).build()
    open_order_resp = order_api.get_open_orders(open_order_req)

    # Collect open order IDs
    if open_order_resp.data:
        open_order_ids = [order.id for order in open_order_resp.data]
        logging.info(f"Found {len(open_order_ids)} open order(s) for {symbol}.")

        # Cancel all open orders if any exist
        if open_order_ids:
            cancel_all_req = CancelAllOrdersBySymbolReqBuilder().set_symbol(symbol).build()
            cancel_all_resp = order_api.cancel_all_orders_by_symbol(cancel_all_req)
            logging.info(f"Canceled all open orders: {cancel_all_resp.data}")

    # Calculate the order price based on action and price_delta
    price = last_trade_price * (1 - price_delta)
    side = AddOrderSyncReq.SideEnum.BUY
    if action == Action.SELL:
        side = AddOrderSyncReq.SideEnum.SELL
        price = last_trade_price * (1 + price_delta)

    logging.info(
        f"Placing a {'BUY' if side == AddOrderSyncReq.SideEnum.BUY else 'SELL'} order at {price:.2f} for {symbol}.")

    # Place a limit order
    add_order_sync_req = (
        AddOrderSyncReqBuilder()
        .set_client_oid(str(uuid.uuid4()))
        .set_side(side)
        .set_symbol(symbol)
        .set_type(AddOrderSyncReq.TypeEnum.LIMIT)
        .set_remark("ma")
        .set_price(f"{price:.2f}")
        .set_size(f"{amount:.8f}")
        .build()
    )
    add_order_sync_resp = order_api.add_order_sync(add_order_sync_req)
    logging.info(f"Order placed successfully with ID: {add_order_sync_resp.order_id}")

    # Set a time-based cancelation policy (DCP) for the order
    set_dcp_req = SetDcpReqBuilder().set_symbols(symbol).set_timeout(30).build()
    set_dcp_resp = order_api.set_dcp(set_dcp_req)
    logging.info(f"DCP set: current_time={set_dcp_resp.current_time}, trigger_time={set_dcp_resp.trigger_time}")


if __name__ == '__main__':
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
    client = DefaultClient(client_option)

    # Get the Restful Service
    kucoin_rest_service = client.rest_service()

    spot_market_api = kucoin_rest_service.get_spot_service().get_market_api()
    spot_order_api = kucoin_rest_service.get_spot_service().get_order_api()
    account_api = kucoin_rest_service.get_account_service().get_account_api()
    current_time = int(time.time()) // 60 * 60

    SYMBOL = "DOGE-USDT"
    ORDER_AMOUNT = 10
    PRICE_DELTA = 0.1

    # Run the strategy in a loop with a 1-minute interval
    try:
        logging.info("Starting the moving average strategy using K-line data. Press Ctrl+C to stop.")
        while True:
            action = simple_moving_average_strategy(spot_market_api, SYMBOL, 10, 30, current_time)
            if action != Action.SKIP:
                last_price = get_last_trade_price(spot_market_api, SYMBOL)
                logging.info(f"Last trade price for {SYMBOL}: {last_price}")
                sufficient_balance = check_available_balance(account_api, last_price, ORDER_AMOUNT, action)
                if sufficient_balance:
                    logging.info("Sufficient balance available. Attempting to place the order...")
                    place_order(spot_order_api, SYMBOL, action, last_price, ORDER_AMOUNT, PRICE_DELTA)
                else:
                    logging.info("Insufficient balance. Skipping the trade...")

            logging.info("Waiting for 1 minute before the next run...")
            time.sleep(60)
            current_time = current_time + 60
    except KeyboardInterrupt:
        logging.info("Strategy stopped by user.")
