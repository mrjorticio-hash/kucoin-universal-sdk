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
import math
import os
import time
import uuid
from datetime import datetime, timedelta
from enum import Enum

from kucoin_universal_sdk.api import DefaultClient
from kucoin_universal_sdk.generate.account.account import GetCrossMarginAccountReqBuilder, GetCrossMarginAccountReq
from kucoin_universal_sdk.generate.account.account import GetFuturesAccountReqBuilder
from kucoin_universal_sdk.generate.account.account import GetSpotAccountListReqBuilder, GetSpotAccountListReq
from kucoin_universal_sdk.generate.futures.fundingfees import GetCurrentFundingRateReqBuilder
from kucoin_universal_sdk.generate.futures.market import GetSymbolReqBuilder
from kucoin_universal_sdk.generate.futures.order import AddOrderReqBuilder, AddOrderReq
from kucoin_universal_sdk.generate.futures.order import CancelOrderByIdReqBuilder
from kucoin_universal_sdk.generate.futures.order import \
    GetOrderByOrderIdReqBuilder as FuturesGetOrderByOrderIdReqBuilder
from kucoin_universal_sdk.generate.futures.order import GetOrderByOrderIdResp
from kucoin_universal_sdk.generate.margin.order import \
    AddOrderReqBuilder as MarginAddOrderReqBuilder, AddOrderReq as MarginAddOrderReq
from kucoin_universal_sdk.generate.margin.order import CancelOrderByOrderIdReqBuilder
from kucoin_universal_sdk.generate.margin.order import \
    GetOrderByOrderIdReqBuilder as MarginGetOrderByOrderIdReqBuilder
from kucoin_universal_sdk.generate.service import AccountService
from kucoin_universal_sdk.generate.service import FuturesService
from kucoin_universal_sdk.generate.service import MarginService
from kucoin_universal_sdk.generate.service import SpotService
from kucoin_universal_sdk.generate.spot.market import GetTickerReqBuilder
from kucoin_universal_sdk.generate.spot.order import AddOrderSyncReqBuilder, AddOrderSyncReq
from kucoin_universal_sdk.generate.spot.order import CancelOrderByOrderIdSyncReqBuilder
from kucoin_universal_sdk.generate.spot.order import CancelOrderByOrderIdSyncResp
from kucoin_universal_sdk.generate.spot.order.model_get_order_by_order_id_req import GetOrderByOrderIdReqBuilder
from kucoin_universal_sdk.model import ClientOptionBuilder
from kucoin_universal_sdk.model import GLOBAL_API_ENDPOINT, GLOBAL_FUTURES_API_ENDPOINT, \
    GLOBAL_BROKER_API_ENDPOINT
from kucoin_universal_sdk.model import TransportOptionBuilder

SPOT_SYMBOL = "DOGE-USDT"
FUTURES_SYMBOL = "DOGEUSDTM"
BASE_CURRENCY = "USDT"
# Maximum time to wait for an order to fill
MAX_PLACE_ORDER_WAIT_TIME_SEC = 15


class MarketSide(Enum):
    BUY = "buy"
    SELL = "sell"


class MarketType(Enum):
    SPOT = "SPOT"
    MARGIN = "MARGIN"
    FUTURES = "FUTURES"


def check_available_balance(account_service: AccountService, price: float, amount: float,
                            marketType: MarketType) -> bool:
    """
    Checks if the available balance is sufficient for the trade.

    Args:
        account_service (AccountService): The account API client instance.
        price (float): The price of the asset.
        amount (float): The amount of the asset to trade.
        marketType (MarketType): The type of the market.

    Returns:
        bool: True if the balance is sufficient, False otherwise.
    """
    trade_value = price * amount

    if marketType == MarketType.SPOT:
        # check spot account
        get_spot_account_resp = account_service.get_account_api().get_spot_account_list(GetSpotAccountListReqBuilder()
                                                                                        .set_type(
            GetSpotAccountListReq.TypeEnum.TRADE)
                                                                                        .set_currency(BASE_CURRENCY)
                                                                                        .build())
        spot_available_balance = sum(float(account_data.available) for account_data in get_spot_account_resp.data)
        logging.info(
            f"[SPOT] Available {BASE_CURRENCY} balance: {spot_available_balance:.2f}, Required: {trade_value:.2f}")

        return spot_available_balance >= trade_value
    elif marketType == MarketType.FUTURES:
        # check futures account
        futures_available_balance = account_service.get_account_api().get_futures_account(GetFuturesAccountReqBuilder()
                                                                                          .set_currency(BASE_CURRENCY)
                                                                                          .build()).available_balance
        logging.info(
            f"[FUTURES] Available {BASE_CURRENCY} balance: {futures_available_balance:.2f}, Required: {trade_value:.2f}")
        return futures_available_balance >= trade_value
    else:
        # check margin account
        margin_available_balance = float(account_service.get_account_api().get_cross_margin_account(
            GetCrossMarginAccountReqBuilder().set_query_type(
                GetCrossMarginAccountReq.QueryTypeEnum.MARGIN).set_quote_currency(
                GetCrossMarginAccountReq.QuoteCurrencyEnum.USDT).build()).total_asset_of_quote_currency)
        logging.info(
            f"[MARGIN] Available {BASE_CURRENCY} balance: {margin_available_balance:.2f}, Required: {trade_value:.2f}")
        return margin_available_balance >= trade_value


def get_last_traded_price(spot_service: SpotService, futures_service: FuturesService):
    """
    Fetches the last traded price for the specified spot and futures symbols.

    Args:
        spot_service (SpotService): The API client for spot market.
        futures_service (FuturesService): The API client for futures market.

    Returns:
        tuple[float, float]: The last traded price for spot and futures markets.
    """
    spot_price = float(spot_service.get_market_api().get_ticker(
        GetTickerReqBuilder().set_symbol(SPOT_SYMBOL).build()).price)
    futures_price = float(futures_service.get_market_api().get_symbol(
        GetSymbolReqBuilder().set_symbol(FUTURES_SYMBOL).build()).last_trade_price)
    logging.info(f"[PRICE] Spot Price: {spot_price:.5f}, Futures Price: {futures_price:.5f}")

    return spot_price, futures_price


def funding_rate_arbitrage_strategy(futures_service: FuturesService, spot_service: SpotService,
                                    margin_service: MarginService, account_service: AccountService,
                                    amount: float, threshold: float) -> None:
    """
    Funding Rate Arbitrage Strategy.

    This strategy exploits differences in funding rates between futures and spot/margin markets.
    It operates by identifying arbitrage opportunities based on funding rates and executing corresponding
    long or short positions in the appropriate markets.

    Strategy Logic:
        1. Fetch the funding rate for the specified futures symbol.
        2. If the funding rate's absolute value is below the specified threshold, exit the strategy.
        3. If the funding rate is positive:
            - LONG the spot market and SHORT the futures market.
        4. If the funding rate is negative:
            - SHORT the margin market and LONG the futures market.
        5. Ensure sufficient balance in each account before placing orders.
        6. Monitor order status, and cancel unfilled orders after a timeout.

    Args:
        futures_service (FuturesService): The API client for the futures market.
        spot_service (SpotService): The API client for the spot market.
        margin_service (MarginService): The API client for the margin market.
        account_service (AccountService): The API client for account management.
        amount (float): The amount to trade for arbitrage.
        threshold (float): The minimum funding rate threshold to trigger the strategy.

    Returns:
        None
    """
    try:
        # Step 1: Fetch funding rate
        funding_rate_req = GetCurrentFundingRateReqBuilder().set_symbol(FUTURES_SYMBOL).build()
        funding_rate = futures_service.get_funding_fees_api().get_current_funding_rate(funding_rate_req).value * 100
        logging.info(f"[STRATEGY] Funding rate for {FUTURES_SYMBOL}: {funding_rate:.6f}%")

        # Step 2: Check if funding rate meets the threshold for arbitrage
        if abs(funding_rate) < threshold:
            logging.info(
                f"[STRATEGY] No arbitrage opportunity: Funding rate ({funding_rate:.6f}%) below threshold ({threshold}%).")
            return

        # Step 3: Fetch the latest spot and futures prices
        spot_price, futures_price = get_last_traded_price(spot_service, futures_service)

        # Calculate futures order amount in contracts
        futures_multiplier = futures_service.get_market_api().get_symbol(
            GetSymbolReqBuilder().set_symbol(FUTURES_SYMBOL).build()).multiplier
        futures_amount = math.ceil(amount / futures_multiplier)

        # Step 4: Execute arbitrage based on funding rate direction
        if funding_rate > 0:
            logging.info("[STRATEGY] Positive funding rate detected. Executing LONG spot and SHORT futures arbitrage.")

            # Ensure sufficient balance for the spot and futures accounts
            if not check_available_balance(account_service, spot_price, amount, MarketType.SPOT):
                logging.warning("[STRATEGY] Insufficient balance in spot account.")
                return
            if not check_available_balance(account_service, futures_price, amount, MarketType.FUTURES):
                logging.warning("[STRATEGY] Insufficient balance in futures account.")
                return

            # Execute orders
            if not add_spot_order_wait_fill(spot_service, SPOT_SYMBOL, MarketSide.BUY, amount, spot_price):
                logging.warning("[STRATEGY] Failed to execute spot order.")
                return
            if not add_futures_order_wait_fill(futures_service, FUTURES_SYMBOL, MarketSide.SELL, futures_amount,
                                               futures_price):
                logging.warning("[STRATEGY] Failed to execute futures order.")
                return
        else:
            logging.info(
                "[STRATEGY] Negative funding rate detected. Executing SHORT margin and LONG futures arbitrage.")

            # Ensure sufficient balance for the margin and futures accounts
            if not check_available_balance(account_service, spot_price, amount, MarketType.MARGIN):
                logging.warning("[STRATEGY] Insufficient balance in margin account.")
                return
            if not check_available_balance(account_service, futures_price, amount, MarketType.FUTURES):
                logging.warning("[STRATEGY] Insufficient balance in futures account.")
                return

            # Execute orders
            if not add_margin_order_wait_fill(margin_service, SPOT_SYMBOL, amount, spot_price):
                logging.warning("[STRATEGY] Failed to execute margin order.")
                return
            if not add_futures_order_wait_fill(futures_service, FUTURES_SYMBOL, MarketSide.BUY, futures_amount,
                                               futures_price):
                logging.warning("[STRATEGY] Failed to execute futures order.")
                return

        logging.info("[STRATEGY] Arbitrage execution completed successfully.")

    except Exception as e:
        logging.error(f"[STRATEGY] Error executing arbitrage strategy: {e}")


def add_spot_order_wait_fill(spot_service: SpotService, symbol: str, side: MarketSide, amount: float,
                             price: float) -> bool:
    """
    Places a spot order and waits for it to be filled within a specified timeout.

    Args:
        spot_service (SpotService): The API client for the spot market.
        symbol (str): The trading pair symbol (e.g., "BTC-USDT").
        side (MarketSide): The side of the trade (MarketSide.BUY or MarketSide.SELL).
        amount (float): The amount of the asset to trade.
        price (float): The price per unit of the asset.

    Returns:
        bool: True if the order is filled successfully, False if the order times out or fails.
    """
    # Build and place the spot order
    add_order_sync_req = (
        AddOrderSyncReqBuilder()
        .set_client_oid(str(uuid.uuid4()))
        .set_side(AddOrderSyncReq.SideEnum.BUY if side == MarketSide.BUY else AddOrderSyncReq.SideEnum.SELL)
        .set_symbol(symbol)
        .set_type(AddOrderSyncReq.TypeEnum.LIMIT)
        .set_remark("arbitrage")
        .set_price(f"{price:.4f}")
        .set_size(f"{amount:.4f}")
        .build()
    )
    order = spot_service.get_order_api().add_order_sync(add_order_sync_req)
    logging.info(f"[SPOT ORDER] Placed {side.value.upper()} order for {amount} {symbol} at {price:.6f}. "
                 f"Order ID: {order.order_id}")

    # Wait for the order to be filled within the timeout period
    deadline = datetime.now() + timedelta(seconds=MAX_PLACE_ORDER_WAIT_TIME_SEC)
    while datetime.now() < deadline:
        order_detail = spot_service.get_order_api().get_order_by_order_id(
            GetOrderByOrderIdReqBuilder().set_symbol(symbol).set_order_id(order.order_id).build())
        if not order_detail.active:
            logging.info(f"[SPOT ORDER] Order filled successfully: {side.value.upper()} {amount} {symbol}. "
                         f"Order ID: {order.order_id}")
            return True

        # Wait before checking order status again
        logging.info(f"[SPOT ORDER] Checking order status in 1 seconds...")
        time.sleep(1)

    # Cancel the order if it was not filled within the timeout period
    logging.warning(
        f"[SPOT ORDER] Order not filled within {MAX_PLACE_ORDER_WAIT_TIME_SEC} seconds. Cancelling order...")
    cancel_resp = spot_service.get_order_api().cancel_order_by_order_id_sync(
        CancelOrderByOrderIdSyncReqBuilder().set_order_id(order.order_id).set_symbol(symbol).build())
    if cancel_resp.status != CancelOrderByOrderIdSyncResp.StatusEnum.DONE:
        raise RuntimeError(f"[SPOT ORDER] Failed to cancel order. Order ID: {order.order_id}")

    logging.info(f"[SPOT ORDER] Order cancelled successfully. Order ID: {order.order_id}")
    return False


def add_futures_order_wait_fill(futures_service: FuturesService, symbol: str, side: MarketSide, amount: int,
                                price: float) -> bool:
    """
    Places a futures order and waits for it to be filled within a specified timeout.

    Args:
        futures_service (FuturesService): The API client for the futures market.
        symbol (str): The trading pair symbol (e.g., "BTCUSDTM").
        side (MarketSide): The side of the trade (MarketSide.BUY or MarketSide.SELL).
        amount (int): The size of the futures contract to trade.
        price (float): The price per contract.

    Returns:
        bool: True if the order is filled successfully, False if the order times out or fails.
    """
    # Build and place the futures order
    add_order_sync_req = (
        AddOrderReqBuilder()
        .set_client_oid(str(uuid.uuid4()))
        .set_side(AddOrderReq.SideEnum.BUY if side == MarketSide.BUY else AddOrderReq.SideEnum.SELL)
        .set_symbol(symbol)
        .set_type(AddOrderReq.TypeEnum.LIMIT)
        .set_remark("arbitrage")
        .set_price(f"{price:.4f}")
        .set_leverage(1)  # Default leverage set to 1
        .set_size(amount)
        .build()
    )
    order = futures_service.get_order_api().add_order(add_order_sync_req)
    logging.info(f"[FUTURES ORDER] Placed {side.value.upper()} order for {amount} {symbol} at {price:.6f}. "
                 f"Order ID: {order.order_id}")

    # Wait for the order to be filled within the timeout period
    deadline = datetime.now() + timedelta(seconds=MAX_PLACE_ORDER_WAIT_TIME_SEC)
    while datetime.now() < deadline:
        # Wait before checking order status again
        logging.info(f"[FUTURES ORDER] Checking order status in 1 seconds...")
        time.sleep(1)

        order_detail = futures_service.get_order_api().get_order_by_order_id(
            FuturesGetOrderByOrderIdReqBuilder().set_order_id(order.order_id).build())
        if order_detail.status == GetOrderByOrderIdResp.StatusEnum.DONE:
            logging.info(f"[FUTURES ORDER] Order filled successfully: {side.value.upper()} {amount} {symbol}. "
                         f"Order ID: {order.order_id}")
            return True

    # Cancel the order if it was not filled within the timeout period
    logging.warning(
        f"[FUTURES ORDER] Order not filled within {MAX_PLACE_ORDER_WAIT_TIME_SEC} seconds. Cancelling order...")
    cancel_resp = futures_service.get_order_api().cancel_order_by_id(
        CancelOrderByIdReqBuilder().set_order_id(order.order_id).build())
    if order.order_id not in cancel_resp.cancelled_order_ids:
        raise RuntimeError(f"[FUTURES ORDER] Failed to cancel order. Order ID: {order.order_id}")

    logging.info(f"[FUTURES ORDER] Order cancelled successfully. Order ID: {order.order_id}")
    return False


def add_margin_order_wait_fill(margin_service: MarginService, symbol: str, amount: float, price: float) -> bool:
    """
    Places a margin order and waits for it to be filled within a specified timeout.

    Args:
        margin_service (MarginService): The API client for the margin market.
        symbol (str): The trading pair symbol (e.g., "BTC-USDT").
        amount (float): The amount of the asset to trade.
        price (float): The price per unit of the asset.

    Returns:
        bool: True if the order is filled successfully, False if the order times out or fails.
    """
    # Build and place the margin order
    add_order_sync_req = (
        MarginAddOrderReqBuilder()
        .set_client_oid(str(uuid.uuid4()))
        .set_side(MarginAddOrderReq.SideEnum.BUY)
        .set_symbol(symbol)
        .set_type(MarginAddOrderReq.TypeEnum.LIMIT)
        .set_is_isolated(False)
        .set_auto_borrow(True)  # Automatically borrow if necessary
        .set_auto_repay(True)  # Automatically repay borrowed amount
        .set_price(f"{price:.4f}")
        .set_size(f"{amount:.4f}")
        .build()
    )
    order = margin_service.get_order_api().add_order(add_order_sync_req)
    logging.info(f"[MARGIN ORDER] Placed SELL order for {amount} {symbol} at {price:.6f}. "
                 f"Order ID: {order.order_id}")

    # Wait for the order to be filled within the timeout period
    deadline = datetime.now() + timedelta(seconds=MAX_PLACE_ORDER_WAIT_TIME_SEC)
    while datetime.now() < deadline:
        order_detail = margin_service.get_order_api().get_order_by_order_id(
            MarginGetOrderByOrderIdReqBuilder().set_symbol(symbol).set_order_id(order.order_id).build())
        if not order_detail.active:
            logging.info(f"[MARGIN ORDER] Order filled successfully: SELL {amount} {symbol}. "
                         f"Order ID: {order.order_id}")
            return True

        # Wait before checking order status again
        logging.info(f"[MARGIN ORDER] Checking order status in 1 seconds...")
        time.sleep(1)

    # Cancel the order if it was not filled within the timeout period
    logging.warning(
        f"[MARGIN ORDER] Order not filled within {MAX_PLACE_ORDER_WAIT_TIME_SEC} seconds. Cancelling order...")
    cancel_resp = margin_service.get_order_api().cancel_order_by_order_id(
        CancelOrderByOrderIdReqBuilder().set_order_id(order.order_id).set_symbol(symbol).build())
    if cancel_resp.order_id != '':
        raise RuntimeError(f"[MARGIN ORDER] Failed to cancel order. Order ID: {order.order_id}")

    logging.info(f"[MARGIN ORDER] Order cancelled successfully. Order ID: {order.order_id}")
    return False


if __name__ == "__main__":
    """
    Main function to run the funding rate arbitrage strategy for a specific duration.
    """
    # Initialize APIs
    logging.basicConfig(
        level=logging.INFO,
        format='%(asctime)s %(levelname)s - %(message)s',
        datefmt='%Y-%m-%d %H:%M:%S'
    )

    key = os.getenv("API_KEY", "")
    secret = os.getenv("API_SECRET", "")
    passphrase = os.getenv("API_PASSPHRASE", "")

    http_transport_option = (
        TransportOptionBuilder()
        .set_keep_alive(True)
        .set_max_pool_size(10)
        .set_max_connection_per_pool(10)
        .build()
    )

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
    kucoin_rest_service = client.rest_service()

    futures_service = kucoin_rest_service.get_futures_service()
    spot_service = kucoin_rest_service.get_spot_service()
    margin_service = kucoin_rest_service.get_margin_service()
    account_service = kucoin_rest_service.get_account_service()

    amount = 100  # Amount to trade
    threshold = 0.005  # Minimum profit threshold

    logging.info(f"Starting funding rate arbitrage strategy...")

    funding_rate_arbitrage_strategy(futures_service, spot_service, margin_service, account_service, amount, threshold)
