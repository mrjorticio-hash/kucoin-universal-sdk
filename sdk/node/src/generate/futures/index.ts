import * as ORDER from './order';
import * as POSITIONS from './positions';
import * as FUNDINGFEES from './fundingfees';
import * as MARKET from './market';
import * as FUTURESPRIVATE from './futuresprivate';
import * as FUTURESPUBLIC from './futurespublic';
export const Futures = {
    Order: ORDER,
    Positions: POSITIONS,
    FundingFees: FUNDINGFEES,
    Market: MARKET,
    FuturesPrivate: FUTURESPRIVATE,
    FuturesPublic: FUTURESPUBLIC,
};
export namespace Futures {
    export type OrderAPI = ORDER.OrderAPI;
    export type PositionsAPI = POSITIONS.PositionsAPI;
    export type FundingFeesAPI = FUNDINGFEES.FundingFeesAPI;
    export type MarketAPI = MARKET.MarketAPI;
    export type FuturesPrivateWS = FUTURESPRIVATE.FuturesPrivateWS;
    export type FuturesPublicWS = FUTURESPUBLIC.FuturesPublicWS;
    export namespace Order {
        export type AddOrderReq = ORDER.AddOrderReq;
        export type AddOrderResp = ORDER.AddOrderResp;
        export type AddOrderTestReq = ORDER.AddOrderTestReq;
        export type AddOrderTestResp = ORDER.AddOrderTestResp;
        export type AddTPSLOrderReq = ORDER.AddTPSLOrderReq;
        export type AddTPSLOrderResp = ORDER.AddTPSLOrderResp;
        export type BatchAddOrdersData = ORDER.BatchAddOrdersData;
        export type BatchAddOrdersItem = ORDER.BatchAddOrdersItem;
        export type BatchAddOrdersReq = ORDER.BatchAddOrdersReq;
        export type BatchAddOrdersResp = ORDER.BatchAddOrdersResp;
        export type BatchCancelOrdersClientOidsList = ORDER.BatchCancelOrdersClientOidsList;
        export type BatchCancelOrdersData = ORDER.BatchCancelOrdersData;
        export type BatchCancelOrdersReq = ORDER.BatchCancelOrdersReq;
        export type BatchCancelOrdersResp = ORDER.BatchCancelOrdersResp;
        export type CancelAllOrdersV1Req = ORDER.CancelAllOrdersV1Req;
        export type CancelAllOrdersV1Resp = ORDER.CancelAllOrdersV1Resp;
        export type CancelAllOrdersV3Req = ORDER.CancelAllOrdersV3Req;
        export type CancelAllOrdersV3Resp = ORDER.CancelAllOrdersV3Resp;
        export type CancelAllStopOrdersReq = ORDER.CancelAllStopOrdersReq;
        export type CancelAllStopOrdersResp = ORDER.CancelAllStopOrdersResp;
        export type CancelOrderByClientOidReq = ORDER.CancelOrderByClientOidReq;
        export type CancelOrderByClientOidResp = ORDER.CancelOrderByClientOidResp;
        export type CancelOrderByIdReq = ORDER.CancelOrderByIdReq;
        export type CancelOrderByIdResp = ORDER.CancelOrderByIdResp;
        export type GetOpenOrderValueReq = ORDER.GetOpenOrderValueReq;
        export type GetOpenOrderValueResp = ORDER.GetOpenOrderValueResp;
        export type GetOrderByClientOidReq = ORDER.GetOrderByClientOidReq;
        export type GetOrderByClientOidResp = ORDER.GetOrderByClientOidResp;
        export type GetOrderByOrderIdReq = ORDER.GetOrderByOrderIdReq;
        export type GetOrderByOrderIdResp = ORDER.GetOrderByOrderIdResp;
        export type GetOrderListItems = ORDER.GetOrderListItems;
        export type GetOrderListReq = ORDER.GetOrderListReq;
        export type GetOrderListResp = ORDER.GetOrderListResp;
        export type GetRecentClosedOrdersData = ORDER.GetRecentClosedOrdersData;
        export type GetRecentClosedOrdersReq = ORDER.GetRecentClosedOrdersReq;
        export type GetRecentClosedOrdersResp = ORDER.GetRecentClosedOrdersResp;
        export type GetRecentTradeHistoryData = ORDER.GetRecentTradeHistoryData;
        export type GetRecentTradeHistoryReq = ORDER.GetRecentTradeHistoryReq;
        export type GetRecentTradeHistoryResp = ORDER.GetRecentTradeHistoryResp;
        export type GetStopOrderListItems = ORDER.GetStopOrderListItems;
        export type GetStopOrderListReq = ORDER.GetStopOrderListReq;
        export type GetStopOrderListResp = ORDER.GetStopOrderListResp;
        export type GetTradeHistoryItems = ORDER.GetTradeHistoryItems;
        export type GetTradeHistoryReq = ORDER.GetTradeHistoryReq;
        export type GetTradeHistoryResp = ORDER.GetTradeHistoryResp;
    }
    export namespace FuturesPrivate {
        export type AllOrderEvent = FUTURESPRIVATE.AllOrderEvent;
        export type AllPositionEvent = FUTURESPRIVATE.AllPositionEvent;
        export type BalanceEvent = FUTURESPRIVATE.BalanceEvent;
        export type CrossLeverageDataValue = FUTURESPRIVATE.CrossLeverageDataValue;
        export type CrossLeverageEvent = FUTURESPRIVATE.CrossLeverageEvent;
        export type MarginModeEvent = FUTURESPRIVATE.MarginModeEvent;
        export type OrderEvent = FUTURESPRIVATE.OrderEvent;
        export type PositionEvent = FUTURESPRIVATE.PositionEvent;
        export type StopOrdersEvent = FUTURESPRIVATE.StopOrdersEvent;
    }
    export namespace FundingFees {
        export type GetCurrentFundingRateReq = FUNDINGFEES.GetCurrentFundingRateReq;
        export type GetCurrentFundingRateResp = FUNDINGFEES.GetCurrentFundingRateResp;
        export type GetPrivateFundingHistoryDataList = FUNDINGFEES.GetPrivateFundingHistoryDataList;
        export type GetPrivateFundingHistoryReq = FUNDINGFEES.GetPrivateFundingHistoryReq;
        export type GetPrivateFundingHistoryResp = FUNDINGFEES.GetPrivateFundingHistoryResp;
        export type GetPublicFundingHistoryData = FUNDINGFEES.GetPublicFundingHistoryData;
        export type GetPublicFundingHistoryReq = FUNDINGFEES.GetPublicFundingHistoryReq;
        export type GetPublicFundingHistoryResp = FUNDINGFEES.GetPublicFundingHistoryResp;
    }
    export namespace Positions {
        export type AddIsolatedMarginReq = POSITIONS.AddIsolatedMarginReq;
        export type AddIsolatedMarginResp = POSITIONS.AddIsolatedMarginResp;
        export type BatchSwitchMarginModeErrors = POSITIONS.BatchSwitchMarginModeErrors;
        export type BatchSwitchMarginModeReq = POSITIONS.BatchSwitchMarginModeReq;
        export type BatchSwitchMarginModeResp = POSITIONS.BatchSwitchMarginModeResp;
        export type GetCrossMarginLeverageReq = POSITIONS.GetCrossMarginLeverageReq;
        export type GetCrossMarginLeverageResp = POSITIONS.GetCrossMarginLeverageResp;
        export type GetCrossMarginRiskLimitData = POSITIONS.GetCrossMarginRiskLimitData;
        export type GetCrossMarginRiskLimitReq = POSITIONS.GetCrossMarginRiskLimitReq;
        export type GetCrossMarginRiskLimitResp = POSITIONS.GetCrossMarginRiskLimitResp;
        export type GetIsolatedMarginRiskLimitData = POSITIONS.GetIsolatedMarginRiskLimitData;
        export type GetIsolatedMarginRiskLimitReq = POSITIONS.GetIsolatedMarginRiskLimitReq;
        export type GetIsolatedMarginRiskLimitResp = POSITIONS.GetIsolatedMarginRiskLimitResp;
        export type GetMarginModeReq = POSITIONS.GetMarginModeReq;
        export type GetMarginModeResp = POSITIONS.GetMarginModeResp;
        export type GetMaxOpenSizeReq = POSITIONS.GetMaxOpenSizeReq;
        export type GetMaxOpenSizeResp = POSITIONS.GetMaxOpenSizeResp;
        export type GetMaxWithdrawMarginReq = POSITIONS.GetMaxWithdrawMarginReq;
        export type GetMaxWithdrawMarginResp = POSITIONS.GetMaxWithdrawMarginResp;
        export type GetPositionDetailsReq = POSITIONS.GetPositionDetailsReq;
        export type GetPositionDetailsResp = POSITIONS.GetPositionDetailsResp;
        export type GetPositionListData = POSITIONS.GetPositionListData;
        export type GetPositionListReq = POSITIONS.GetPositionListReq;
        export type GetPositionListResp = POSITIONS.GetPositionListResp;
        export type GetPositionsHistoryItems = POSITIONS.GetPositionsHistoryItems;
        export type GetPositionsHistoryReq = POSITIONS.GetPositionsHistoryReq;
        export type GetPositionsHistoryResp = POSITIONS.GetPositionsHistoryResp;
        export type ModifyAutoDepositStatusReq = POSITIONS.ModifyAutoDepositStatusReq;
        export type ModifyAutoDepositStatusResp = POSITIONS.ModifyAutoDepositStatusResp;
        export type ModifyIsolatedMarginRiskLimtReq = POSITIONS.ModifyIsolatedMarginRiskLimtReq;
        export type ModifyIsolatedMarginRiskLimtResp = POSITIONS.ModifyIsolatedMarginRiskLimtResp;
        export type ModifyMarginLeverageReq = POSITIONS.ModifyMarginLeverageReq;
        export type ModifyMarginLeverageResp = POSITIONS.ModifyMarginLeverageResp;
        export type RemoveIsolatedMarginReq = POSITIONS.RemoveIsolatedMarginReq;
        export type RemoveIsolatedMarginResp = POSITIONS.RemoveIsolatedMarginResp;
        export type SwitchMarginModeReq = POSITIONS.SwitchMarginModeReq;
        export type SwitchMarginModeResp = POSITIONS.SwitchMarginModeResp;
    }
    export namespace FuturesPublic {
        export type AnnouncementEvent = FUTURESPUBLIC.AnnouncementEvent;
        export type ExecutionEvent = FUTURESPUBLIC.ExecutionEvent;
        export type InstrumentEvent = FUTURESPUBLIC.InstrumentEvent;
        export type KlinesEvent = FUTURESPUBLIC.KlinesEvent;
        export type OrderbookIncrementEvent = FUTURESPUBLIC.OrderbookIncrementEvent;
        export type OrderbookLevel50Event = FUTURESPUBLIC.OrderbookLevel50Event;
        export type OrderbookLevel5Event = FUTURESPUBLIC.OrderbookLevel5Event;
        export type SymbolSnapshotEvent = FUTURESPUBLIC.SymbolSnapshotEvent;
        export type TickerV1Event = FUTURESPUBLIC.TickerV1Event;
        export type TickerV2Event = FUTURESPUBLIC.TickerV2Event;
    }
    export namespace Market {
        export type Get24hrStatsResp = MARKET.Get24hrStatsResp;
        export type GetAllSymbolsData = MARKET.GetAllSymbolsData;
        export type GetAllSymbolsResp = MARKET.GetAllSymbolsResp;
        export type GetAllTickersData = MARKET.GetAllTickersData;
        export type GetAllTickersResp = MARKET.GetAllTickersResp;
        export type GetFullOrderBookReq = MARKET.GetFullOrderBookReq;
        export type GetFullOrderBookResp = MARKET.GetFullOrderBookResp;
        export type GetInterestRateIndexDataList = MARKET.GetInterestRateIndexDataList;
        export type GetInterestRateIndexReq = MARKET.GetInterestRateIndexReq;
        export type GetInterestRateIndexResp = MARKET.GetInterestRateIndexResp;
        export type GetKlinesReq = MARKET.GetKlinesReq;
        export type GetKlinesResp = MARKET.GetKlinesResp;
        export type GetMarkPriceReq = MARKET.GetMarkPriceReq;
        export type GetMarkPriceResp = MARKET.GetMarkPriceResp;
        export type GetPartOrderBookReq = MARKET.GetPartOrderBookReq;
        export type GetPartOrderBookResp = MARKET.GetPartOrderBookResp;
        export type GetPremiumIndexDataList = MARKET.GetPremiumIndexDataList;
        export type GetPremiumIndexReq = MARKET.GetPremiumIndexReq;
        export type GetPremiumIndexResp = MARKET.GetPremiumIndexResp;
        export type GetPrivateTokenInstanceServers = MARKET.GetPrivateTokenInstanceServers;
        export type GetPrivateTokenResp = MARKET.GetPrivateTokenResp;
        export type GetPublicTokenInstanceServers = MARKET.GetPublicTokenInstanceServers;
        export type GetPublicTokenResp = MARKET.GetPublicTokenResp;
        export type GetServerTimeResp = MARKET.GetServerTimeResp;
        export type GetServiceStatusResp = MARKET.GetServiceStatusResp;
        export type GetSpotIndexPriceDataList = MARKET.GetSpotIndexPriceDataList;
        export type GetSpotIndexPriceDataListDecomposionList =
            MARKET.GetSpotIndexPriceDataListDecomposionList;
        export type GetSpotIndexPriceReq = MARKET.GetSpotIndexPriceReq;
        export type GetSpotIndexPriceResp = MARKET.GetSpotIndexPriceResp;
        export type GetSymbolReq = MARKET.GetSymbolReq;
        export type GetSymbolResp = MARKET.GetSymbolResp;
        export type GetTickerReq = MARKET.GetTickerReq;
        export type GetTickerResp = MARKET.GetTickerResp;
        export type GetTradeHistoryData = MARKET.GetTradeHistoryData;
        export type GetTradeHistoryReq = MARKET.GetTradeHistoryReq;
        export type GetTradeHistoryResp = MARKET.GetTradeHistoryResp;
    }
}
