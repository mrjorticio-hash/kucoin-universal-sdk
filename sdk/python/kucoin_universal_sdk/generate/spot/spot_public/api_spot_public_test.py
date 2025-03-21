import unittest
from .model_all_tickers_event import AllTickersEvent
from .model_call_auction_info_event import CallAuctionInfoEvent
from .model_call_auction_orderbook_level50_event import CallAuctionOrderbookLevel50Event
from .model_klines_event import KlinesEvent
from .model_market_snapshot_event import MarketSnapshotEvent
from .model_orderbook_increment_event import OrderbookIncrementEvent
from .model_orderbook_level1_event import OrderbookLevel1Event
from .model_orderbook_level50_event import OrderbookLevel50Event
from .model_orderbook_level5_event import OrderbookLevel5Event
from .model_symbol_snapshot_event import SymbolSnapshotEvent
from .model_ticker_event import TickerEvent
from .model_trade_event import TradeEvent
from typing import List
from kucoin_universal_sdk.model.common import WsMessage


class SpotPublicAPITest(unittest.TestCase):

    def test_all_tickers_resp_model(self):
        """
        all_tickers
        Get All Tickers
        /allTickers/market/ticker:all
        """
        data = "{\"topic\":\"/market/ticker:all\",\"type\":\"message\",\"subject\":\"BTC-USDT\",\"data\":{\"bestAsk\":\"67218.7\",\"bestAskSize\":\"1.92318539\",\"bestBid\":\"67218.6\",\"bestBidSize\":\"0.01045638\",\"price\":\"67220\",\"sequence\":\"14691455768\",\"size\":\"0.00004316\",\"time\":1729757723612}}"
        common_response = WsMessage.from_json(data)
        resp = AllTickersEvent.from_dict(common_response.raw_data)

    def test_call_auction_info_resp_model(self):
        """
        call_auction_info
        Get Call Auction Info
        /callAuctionInfo/callauction/callauctionData:_symbol_
        """
        data = "{\"type\":\"message\",\"topic\":\"/callauction/callauctionData:BTC-USDT\",\"subject\":\"callauction.callauctionData\",\"data\":{\"symbol\":\"BTC-USDT\",\"estimatedPrice\":\"0.17\",\"estimatedSize\":\"0.03715004\",\"sellOrderRangeLowPrice\":\"1.788\",\"sellOrderRangeHighPrice\":\"2.788\",\"buyOrderRangeLowPrice\":\"1.788\",\"buyOrderRangeHighPrice\":\"2.788\",\"time\":1550653727731}}"
        common_response = WsMessage.from_json(data)
        resp = CallAuctionInfoEvent.from_dict(common_response.raw_data)

    def test_call_auction_orderbook_level50_resp_model(self):
        """
        call_auction_orderbook_level50
        CallAuctionOrderbook - Level50
        /callAuctionOrderbookLevel50/callauction/level2Depth50:_symbol_
        """
        data = "{\"topic\":\"/spotMarket/level2Depth50:BTC-USDT\",\"type\":\"message\",\"subject\":\"level2\",\"data\":{\"asks\":[[\"95964.3\",\"0.08168874\"],[\"95967.9\",\"0.00985094\"],[\"95969.9\",\"0.00078081\"],[\"95971.2\",\"0.10016039\"],[\"95971.3\",\"0.12531139\"],[\"95971.7\",\"0.00291\"],[\"95971.9\",\"0.10271829\"],[\"95973.3\",\"0.00021\"],[\"95974.7\",\"0.10271829\"],[\"95976.9\",\"0.03095177\"],[\"95977\",\"0.10271829\"],[\"95978.7\",\"0.00022411\"],[\"95979.1\",\"0.00023017\"],[\"95981\",\"0.00022008\"],[\"95981.2\",\"0.14330324\"],[\"95982.3\",\"0.27922082\"],[\"95982.5\",\"0.02302674\"],[\"95983.8\",\"0.00011035\"],[\"95985\",\"0.00104222\"],[\"95985.1\",\"0.00021808\"],[\"95985.5\",\"0.211127\"],[\"95986.2\",\"0.09690904\"],[\"95986.3\",\"0.31261\"],[\"95986.9\",\"0.09225037\"],[\"95987\",\"0.01042013\"],[\"95990.5\",\"0.12712438\"],[\"95990.6\",\"0.0916115\"],[\"95992.2\",\"0.279\"],[\"95992.7\",\"0.00521084\"],[\"95995.2\",\"0.00033\"],[\"95999.1\",\"0.02973561\"],[\"96001.1\",\"0.083825\"],[\"96002.6\",\"0.01900906\"],[\"96002.7\",\"0.00041665\"],[\"96002.8\",\"0.12531139\"],[\"96002.9\",\"0.279\"],[\"96004.8\",\"0.02081884\"],[\"96006.3\",\"0.00065542\"],[\"96008.5\",\"0.00033166\"],[\"96011\",\"0.08776246\"],[\"96012.5\",\"0.279\"],[\"96013.3\",\"0.00066666\"],[\"96013.9\",\"0.26097183\"],[\"96014\",\"0.01087009\"],[\"96017\",\"0.06248892\"],[\"96017.1\",\"0.20829641\"],[\"96022\",\"0.00107066\"],[\"96022.1\",\"0.279\"],[\"96022.9\",\"0.0006499\"],[\"96024.6\",\"0.00104131\"]],\"bids\":[[\"95964.2\",\"1.35483359\"],[\"95964.1\",\"0.01117492\"],[\"95962.1\",\"0.0062\"],[\"95961.8\",\"0.03081549\"],[\"95961.7\",\"0.10271829\"],[\"95958.5\",\"0.04681571\"],[\"95958.4\",\"0.05177498\"],[\"95958.2\",\"0.00155911\"],[\"95957.8\",\"0.10271829\"],[\"95954.7\",\"0.16312181\"],[\"95954.6\",\"0.44102109\"],[\"95952.6\",\"0.10271829\"],[\"95951.3\",\"0.0062\"],[\"95951\",\"0.17075141\"],[\"95950.9\",\"0.279\"],[\"95949.5\",\"0.13567811\"],[\"95949.2\",\"0.05177498\"],[\"95948.3\",\"0.10271829\"],[\"95947.2\",\"0.04634798\"],[\"95944.7\",\"0.10271829\"],[\"95944.2\",\"0.05177498\"],[\"95942.3\",\"0.26028569\"],[\"95942.2\",\"0.10271829\"],[\"95940.6\",\"0.12531139\"],[\"95940.2\",\"0.43349327\"],[\"95938.3\",\"0.01041604\"],[\"95937.4\",\"0.04957577\"],[\"95937.2\",\"0.00305\"],[\"95936.3\",\"0.10271829\"],[\"95934\",\"0.05177498\"],[\"95931.9\",\"0.03394093\"],[\"95931.8\",\"0.10271829\"],[\"95930\",\"0.01041814\"],[\"95927.9\",\"0.10271829\"],[\"95927\",\"0.13312774\"],[\"95926.9\",\"0.33077498\"],[\"95924.9\",\"0.10271829\"],[\"95924\",\"0.00180915\"],[\"95923.8\",\"0.00022434\"],[\"95919.6\",\"0.00021854\"],[\"95919.1\",\"0.01471872\"],[\"95919\",\"0.05177498\"],[\"95918.1\",\"0.00001889\"],[\"95917.8\",\"0.1521089\"],[\"95917.5\",\"0.00010962\"],[\"95916.2\",\"0.00021958\"],[\"95915.5\",\"0.12531139\"],[\"95915.3\",\"0.279\"],[\"95913.6\",\"0.01739249\"],[\"95913.5\",\"0.05177498\"]],\"timestamp\":1733124805073}}"
        common_response = WsMessage.from_json(data)
        resp = CallAuctionOrderbookLevel50Event.from_dict(
            common_response.raw_data)

    def test_klines_resp_model(self):
        """
        klines
        Klines
        /klines/market/candles:_symbol___type_
        """
        data = "{\"topic\":\"/market/candles:BTC-USDT_1hour\",\"type\":\"message\",\"subject\":\"trade.candles.update\",\"data\":{\"symbol\":\"BTC-USDT\",\"candles\":[\"1729839600\",\"67644.9\",\"67437.6\",\"67724.8\",\"67243.8\",\"44.88321441\",\"3027558.991928447\"],\"time\":1729842192785164840}}"
        common_response = WsMessage.from_json(data)
        resp = KlinesEvent.from_dict(common_response.raw_data)

    def test_market_snapshot_resp_model(self):
        """
        market_snapshot
        Market Snapshot
        /marketSnapshot/market/snapshot:_market_
        """
        data = "{\"topic\":\"/market/snapshot:BTC\",\"type\":\"message\",\"subject\":\"trade.snapshot\",\"data\":{\"sequence\":\"1729785948015\",\"data\":{\"askSize\":1375.1096,\"averagePrice\":0.00000262,\"baseCurrency\":\"CHR\",\"bidSize\":152.0912,\"board\":0,\"buy\":0.00000263,\"changePrice\":0.00000005300000000000,\"changeRate\":0.0200,\"close\":0.000002698,\"datetime\":1729785948008,\"high\":0.00000274600000000000,\"lastTradedPrice\":0.000002698,\"low\":0.00000255800000000000,\"makerCoefficient\":1.000000,\"makerFeeRate\":0.001,\"marginTrade\":false,\"mark\":0,\"market\":\"BTC\",\"marketChange1h\":{\"changePrice\":-0.00000000900000000000,\"changeRate\":-0.0033,\"high\":0.00000270700000000000,\"low\":0.00000264200000000000,\"open\":0.00000270700000000000,\"vol\":27.10350000000000000000,\"volValue\":0.00007185015660000000},\"marketChange24h\":{\"changePrice\":0.00000005300000000000,\"changeRate\":0.0200,\"high\":0.00000274600000000000,\"low\":0.00000255800000000000,\"open\":0.00000264500000000000,\"vol\":6824.94800000000000000000,\"volValue\":0.01789509649520000000},\"marketChange4h\":{\"changePrice\":0.00000000600000000000,\"changeRate\":0.0022,\"high\":0.00000270700000000000,\"low\":0.00000264200000000000,\"open\":0.00000269200000000000,\"vol\":92.69020000000000000000,\"volValue\":0.00024903875740000000},\"markets\":[\"BTC\",\"DePIN\",\"Layer 1\"],\"open\":0.00000264500000000000,\"quoteCurrency\":\"BTC\",\"sell\":0.000002695,\"siteTypes\":[\"global\"],\"sort\":100,\"symbol\":\"CHR-BTC\",\"symbolCode\":\"CHR-BTC\",\"takerCoefficient\":1.000000,\"takerFeeRate\":0.001,\"trading\":true,\"vol\":6824.94800000000000000000,\"volValue\":0.01789509649520000000}}}"
        common_response = WsMessage.from_json(data)
        resp = MarketSnapshotEvent.from_dict(common_response.raw_data)

    def test_orderbook_increment_resp_model(self):
        """
        orderbook_increment
        Orderbook - Increment
        /orderbookIncrement/market/level2:_symbol_,_symbol_
        """
        data = "{\"topic\":\"/market/level2:BTC-USDT\",\"type\":\"message\",\"subject\":\"trade.l2update\",\"data\":{\"changes\":{\"asks\":[[\"67993.3\",\"1.21427407\",\"14701689783\"]],\"bids\":[]},\"sequenceEnd\":14701689783,\"sequenceStart\":14701689783,\"symbol\":\"BTC-USDT\",\"time\":1729816425625}}"
        common_response = WsMessage.from_json(data)
        resp = OrderbookIncrementEvent.from_dict(common_response.raw_data)

    def test_orderbook_level1_resp_model(self):
        """
        orderbook_level1
        Orderbook - Level1
        /orderbookLevel1/spotMarket/level1:_symbol_,_symbol_
        """
        data = "{\"topic\":\"/spotMarket/level1:BTC-USDT\",\"type\":\"message\",\"subject\":\"level1\",\"data\":{\"asks\":[\"68145.8\",\"0.51987471\"],\"bids\":[\"68145.7\",\"1.29267802\"],\"timestamp\":1729816058766}}"
        common_response = WsMessage.from_json(data)
        resp = OrderbookLevel1Event.from_dict(common_response.raw_data)

    def test_orderbook_level50_resp_model(self):
        """
        orderbook_level50
        Orderbook - Level50
        /orderbookLevel50/spotMarket/level2Depth50:_symbol_,_symbol_
        """
        data = "{\"topic\":\"/spotMarket/level2Depth50:BTC-USDT\",\"type\":\"message\",\"subject\":\"level2\",\"data\":{\"asks\":[[\"95964.3\",\"0.08168874\"],[\"95967.9\",\"0.00985094\"],[\"95969.9\",\"0.00078081\"],[\"95971.2\",\"0.10016039\"],[\"95971.3\",\"0.12531139\"],[\"95971.7\",\"0.00291\"],[\"95971.9\",\"0.10271829\"],[\"95973.3\",\"0.00021\"],[\"95974.7\",\"0.10271829\"],[\"95976.9\",\"0.03095177\"],[\"95977\",\"0.10271829\"],[\"95978.7\",\"0.00022411\"],[\"95979.1\",\"0.00023017\"],[\"95981\",\"0.00022008\"],[\"95981.2\",\"0.14330324\"],[\"95982.3\",\"0.27922082\"],[\"95982.5\",\"0.02302674\"],[\"95983.8\",\"0.00011035\"],[\"95985\",\"0.00104222\"],[\"95985.1\",\"0.00021808\"],[\"95985.5\",\"0.211127\"],[\"95986.2\",\"0.09690904\"],[\"95986.3\",\"0.31261\"],[\"95986.9\",\"0.09225037\"],[\"95987\",\"0.01042013\"],[\"95990.5\",\"0.12712438\"],[\"95990.6\",\"0.0916115\"],[\"95992.2\",\"0.279\"],[\"95992.7\",\"0.00521084\"],[\"95995.2\",\"0.00033\"],[\"95999.1\",\"0.02973561\"],[\"96001.1\",\"0.083825\"],[\"96002.6\",\"0.01900906\"],[\"96002.7\",\"0.00041665\"],[\"96002.8\",\"0.12531139\"],[\"96002.9\",\"0.279\"],[\"96004.8\",\"0.02081884\"],[\"96006.3\",\"0.00065542\"],[\"96008.5\",\"0.00033166\"],[\"96011\",\"0.08776246\"],[\"96012.5\",\"0.279\"],[\"96013.3\",\"0.00066666\"],[\"96013.9\",\"0.26097183\"],[\"96014\",\"0.01087009\"],[\"96017\",\"0.06248892\"],[\"96017.1\",\"0.20829641\"],[\"96022\",\"0.00107066\"],[\"96022.1\",\"0.279\"],[\"96022.9\",\"0.0006499\"],[\"96024.6\",\"0.00104131\"]],\"bids\":[[\"95964.2\",\"1.35483359\"],[\"95964.1\",\"0.01117492\"],[\"95962.1\",\"0.0062\"],[\"95961.8\",\"0.03081549\"],[\"95961.7\",\"0.10271829\"],[\"95958.5\",\"0.04681571\"],[\"95958.4\",\"0.05177498\"],[\"95958.2\",\"0.00155911\"],[\"95957.8\",\"0.10271829\"],[\"95954.7\",\"0.16312181\"],[\"95954.6\",\"0.44102109\"],[\"95952.6\",\"0.10271829\"],[\"95951.3\",\"0.0062\"],[\"95951\",\"0.17075141\"],[\"95950.9\",\"0.279\"],[\"95949.5\",\"0.13567811\"],[\"95949.2\",\"0.05177498\"],[\"95948.3\",\"0.10271829\"],[\"95947.2\",\"0.04634798\"],[\"95944.7\",\"0.10271829\"],[\"95944.2\",\"0.05177498\"],[\"95942.3\",\"0.26028569\"],[\"95942.2\",\"0.10271829\"],[\"95940.6\",\"0.12531139\"],[\"95940.2\",\"0.43349327\"],[\"95938.3\",\"0.01041604\"],[\"95937.4\",\"0.04957577\"],[\"95937.2\",\"0.00305\"],[\"95936.3\",\"0.10271829\"],[\"95934\",\"0.05177498\"],[\"95931.9\",\"0.03394093\"],[\"95931.8\",\"0.10271829\"],[\"95930\",\"0.01041814\"],[\"95927.9\",\"0.10271829\"],[\"95927\",\"0.13312774\"],[\"95926.9\",\"0.33077498\"],[\"95924.9\",\"0.10271829\"],[\"95924\",\"0.00180915\"],[\"95923.8\",\"0.00022434\"],[\"95919.6\",\"0.00021854\"],[\"95919.1\",\"0.01471872\"],[\"95919\",\"0.05177498\"],[\"95918.1\",\"0.00001889\"],[\"95917.8\",\"0.1521089\"],[\"95917.5\",\"0.00010962\"],[\"95916.2\",\"0.00021958\"],[\"95915.5\",\"0.12531139\"],[\"95915.3\",\"0.279\"],[\"95913.6\",\"0.01739249\"],[\"95913.5\",\"0.05177498\"]],\"timestamp\":1733124805073}}"
        common_response = WsMessage.from_json(data)
        resp = OrderbookLevel50Event.from_dict(common_response.raw_data)

    def test_orderbook_level5_resp_model(self):
        """
        orderbook_level5
        Orderbook - Level5
        /orderbookLevel5/spotMarket/level2Depth5:_symbol_,_symbol_
        """
        data = "{\"topic\":\"/spotMarket/level2Depth5:BTC-USDT\",\"type\":\"message\",\"subject\":\"level2\",\"data\":{\"asks\":[[\"67996.7\",\"1.14213262\"],[\"67996.8\",\"0.21748212\"],[\"67996.9\",\"0.1503747\"],[\"67997\",\"0.11446863\"],[\"67997.1\",\"0.14842782\"]],\"bids\":[[\"67996.6\",\"0.37969491\"],[\"67995.3\",\"0.20779746\"],[\"67994.5\",\"0.00047785\"],[\"67993.4\",\"0.405\"],[\"67993.3\",\"0.13528566\"]],\"timestamp\":1729822226746}}"
        common_response = WsMessage.from_json(data)
        resp = OrderbookLevel5Event.from_dict(common_response.raw_data)

    def test_symbol_snapshot_resp_model(self):
        """
        symbol_snapshot
        Symbol Snapshot
        /symbolSnapshot/market/snapshot:_symbol_
        """
        data = "{\"topic\":\"/market/snapshot:BTC-USDT\",\"type\":\"message\",\"subject\":\"trade.snapshot\",\"data\":{\"sequence\":\"14691517895\",\"data\":{\"askSize\":1.15955795,\"averagePrice\":66867.89967612,\"baseCurrency\":\"BTC\",\"bidSize\":0.81772627,\"board\":1,\"buy\":67158.1,\"changePrice\":315.20000000000000000000,\"changeRate\":0.0047,\"close\":67158.1,\"datetime\":1729758286011,\"high\":67611.80000000000000000000,\"lastTradedPrice\":67158.1,\"low\":65257.10000000000000000000,\"makerCoefficient\":1.000000,\"makerFeeRate\":0.001,\"marginTrade\":true,\"mark\":0,\"market\":\"USDS\",\"marketChange1h\":{\"changePrice\":-102.10000000000000000000,\"changeRate\":-0.0015,\"high\":67310.60000000000000000000,\"low\":67051.80000000000000000000,\"open\":67260.20000000000000000000,\"vol\":53.73698081000000000000,\"volValue\":3609965.13819127700000000000},\"marketChange24h\":{\"changePrice\":315.20000000000000000000,\"changeRate\":0.0047,\"high\":67611.80000000000000000000,\"low\":65257.10000000000000000000,\"open\":66842.90000000000000000000,\"vol\":2227.69895852000000000000,\"volValue\":147972941.07857507300000000000},\"marketChange4h\":{\"changePrice\":-166.30000000000000000000,\"changeRate\":-0.0024,\"high\":67476.60000000000000000000,\"low\":67051.80000000000000000000,\"open\":67324.40000000000000000000,\"vol\":173.76971188000000000000,\"volValue\":11695949.43841656500000000000},\"markets\":[\"USDS\",\"PoW\"],\"open\":66842.90000000000000000000,\"quoteCurrency\":\"USDT\",\"sell\":67158.2,\"siteTypes\":[\"turkey\",\"thailand\",\"global\"],\"sort\":100,\"symbol\":\"BTC-USDT\",\"symbolCode\":\"BTC-USDT\",\"takerCoefficient\":1.000000,\"takerFeeRate\":0.001,\"trading\":true,\"vol\":2227.69895852000000000000,\"volValue\":147972941.07857507300000000000}}}"
        common_response = WsMessage.from_json(data)
        resp = SymbolSnapshotEvent.from_dict(common_response.raw_data)

    def test_ticker_resp_model(self):
        """
        ticker
        Get Ticker
        /ticker/market/ticker:_symbol_,_symbol_
        """
        data = "{\"type\":\"message\",\"topic\":\"/market/ticker:BTC-USDT\",\"subject\":\"trade.ticker\",\"data\":{\"sequence\":\"1545896668986\",\"price\":\"0.08\",\"size\":\"0.011\",\"bestAsk\":\"0.08\",\"bestAskSize\":\"0.18\",\"bestBid\":\"0.049\",\"bestBidSize\":\"0.036\",\"Time\":1704873323416}}"
        common_response = WsMessage.from_json(data)
        resp = TickerEvent.from_dict(common_response.raw_data)

    def test_trade_resp_model(self):
        """
        trade
        Trade
        /trade/market/match:_symbol_,_symbol_
        """
        data = "{\"topic\":\"/market/match:BTC-USDT\",\"type\":\"message\",\"subject\":\"trade.l3match\",\"data\":{\"makerOrderId\":\"671b5007389355000701b1d3\",\"price\":\"67523\",\"sequence\":\"11067996711960577\",\"side\":\"buy\",\"size\":\"0.003\",\"symbol\":\"BTC-USDT\",\"takerOrderId\":\"671b50161777ff00074c168d\",\"time\":\"1729843222921000000\",\"tradeId\":\"11067996711960577\",\"type\":\"match\"}}"
        common_response = WsMessage.from_json(data)
        resp = TradeEvent.from_dict(common_response.raw_data)
