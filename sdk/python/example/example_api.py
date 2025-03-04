import logging
import os
import uuid
from datetime import datetime, timedelta

from kucoin_universal_sdk.api import DefaultClient
from kucoin_universal_sdk.generate.account.fee import GetSpotActualFeeReqBuilder
from kucoin_universal_sdk.generate.futures.market import GetKlinesReqBuilder, GetKlinesReq
from kucoin_universal_sdk.generate.service import AccountService
from kucoin_universal_sdk.generate.service import FuturesService
from kucoin_universal_sdk.generate.service import SpotService
from kucoin_universal_sdk.generate.spot.order import AddOrderSyncReqBuilder, AddOrderSyncReq
from kucoin_universal_sdk.generate.spot.order import CancelOrderByOrderIdSyncReqBuilder
from kucoin_universal_sdk.generate.spot.order import GetOrderByOrderIdReqBuilder
from kucoin_universal_sdk.model import ClientOptionBuilder
from kucoin_universal_sdk.model import GLOBAL_API_ENDPOINT, GLOBAL_FUTURES_API_ENDPOINT, \
    GLOBAL_BROKER_API_ENDPOINT
from kucoin_universal_sdk.model import TransportOptionBuilder


def rest_example():
    logging.basicConfig(
        level=logging.INFO,
        format='%(asctime)s %(levelname)s - %(message)s',
        datefmt='%Y-%m-%d %H:%M:%S'
    )

    #  Retrieve API secret information from environment variables
    key = os.getenv("API_KEY", "")
    secret = os.getenv("API_SECRET", "")
    passphrase = os.getenv("API_PASSPHRASE", "")

    # Optional: Retrieve broker secret information from environment variables; applicable for broker API only
    broker_name = os.getenv("BROKER_NAME")
    broker_key = os.getenv("BROKER_KEY")
    broker_partner = os.getenv("BROKER_PARTNER")

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
        .set_broker_name(broker_name)
        .set_broker_key(broker_key)
        .set_broker_partner(broker_partner)
        .set_spot_endpoint(GLOBAL_API_ENDPOINT)
        .set_futures_endpoint(GLOBAL_FUTURES_API_ENDPOINT)
        .set_broker_endpoint(GLOBAL_BROKER_API_ENDPOINT)
        .set_transport_option(http_transport_option)
        .build()
    )
    client = DefaultClient(client_option)

    # Get the Restful Service
    kucoin_rest_service = client.rest_service()

    account_service_example(kucoin_rest_service.get_account_service())
    spot_service_example(kucoin_rest_service.get_spot_service())
    futures_service_example(kucoin_rest_service.get_futures_service())


def account_service_example(account_service: AccountService):
    # Get account api
    account_api = account_service.get_account_api()
    # Get summary information
    account_info_resp = account_api.get_account_info()
    logging.info(f"account info: level:{account_info_resp.level}, SubAccountSize:{account_info_resp.sub_quantity}")
    # Other Account APIs...

    # Get fee api
    fee_api = account_service.get_fee_api()
    get_actual_fee_req = GetSpotActualFeeReqBuilder().set_symbols("BTC-USDT,ETH-USDT").build()
    get_actual_fee_resp = fee_api.get_spot_actual_fee(get_actual_fee_req)
    for fee_data in get_actual_fee_resp.data:
        logging.info(
            f"fee info: symbol:{fee_data.symbol}, TakerFee:{fee_data.taker_fee_rate}, MakerFee:{fee_data.maker_fee_rate}")
    # Other Fee APIs...

    #  Other APIs related to account...


def spot_service_example(spot_service: SpotService):
    order_api = spot_service.get_order_api()

    # Add limit order
    add_order_req = (AddOrderSyncReqBuilder()
                     .set_client_oid(str(uuid.uuid4()))
                     .set_side(AddOrderSyncReq.SideEnum.BUY)
                     .set_symbol("BTC-USDT")
                     .set_type(AddOrderSyncReq.TypeEnum.LIMIT)
                     .set_remark("sdk_example")
                     .set_price("10000")
                     .set_size("0.001")
                     .build())

    resp = order_api.add_order_sync(add_order_req)
    logging.info(f"Add order success, id: {resp.order_id}, oid: {resp.order_id}")

    # Query order detail
    query_order_detail_req = (GetOrderByOrderIdReqBuilder()
                              .set_order_id(resp.order_id)
                              .set_symbol("BTC-USDT")
                              .build())
    order_detail_resp = order_api.get_order_by_order_id(query_order_detail_req)
    logging.info(f"Order detail: {order_detail_resp.to_json()}")

    # Cancel order
    cancel_order_req = (CancelOrderByOrderIdSyncReqBuilder()
                        .set_order_id(resp.order_id)
                        .set_symbol("BTC-USDT")
                        .build())
    cancel_order_resp = order_api.cancel_order_by_order_id_sync(cancel_order_req)
    logging.info(f"Cancel order success, id: {cancel_order_resp.order_id}")


def futures_service_example(futures_service: FuturesService):
    market_api = futures_service.get_market_api()

    # Query all symbols
    all_symbol_resp = market_api.get_all_symbols()
    max_query = min(len(all_symbol_resp.data), 10)

    for i in range(max_query):
        symbol = all_symbol_resp.data[i]
        # Query the Kline of the symbol
        start = int((datetime.now() - timedelta(minutes=10)).timestamp() * 1000)
        end = int(datetime.now().timestamp() * 1000)
        get_kline_req = (GetKlinesReqBuilder()
                         .set_symbol(symbol.symbol)
                         .set_granularity(GetKlinesReq.GranularityEnum.T_1)
                         .set_from_(start)
                         .set_to(end)
                         .build())
        get_kline_resp = market_api.get_klines(get_kline_req)
        rows = []
        for row in get_kline_resp.data:
            timestamp = datetime.fromtimestamp(row[0] / 1000).strftime("%Y-%m-%d %H:%M:%S")
            formatted_row = (
                f"[Time: {timestamp}, Open: {row[1]:.2f}, High: {row[2]:.2f}, "
                f"Low: {row[3]:.2f}, Close: {row[4]:.2f}, Volume: {row[5]:.2f}]"
            )
            rows.append(formatted_row)
        logging.info(f"Symbol: {symbol.symbol}, Kline: {', '.join(rows)}")


if __name__ == "__main__":
    rest_example()
