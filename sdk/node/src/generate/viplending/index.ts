import * as VIPLENDING from './viplending';
export const Viplending = {
    VIPLending: VIPLENDING,
};
export namespace Viplending {
    export type VIPLendingAPI = VIPLENDING.VIPLendingAPI;
    export namespace VIPLending {
        export type GetAccountsData = VIPLENDING.GetAccountsData;
        export type GetAccountsResp = VIPLENDING.GetAccountsResp;
        export type GetDiscountRateConfigsData = VIPLENDING.GetDiscountRateConfigsData;
        export type GetDiscountRateConfigsDataUsdtLevels =
            VIPLENDING.GetDiscountRateConfigsDataUsdtLevels;
        export type GetDiscountRateConfigsResp = VIPLENDING.GetDiscountRateConfigsResp;
        export type GetLoanInfoLtv = VIPLENDING.GetLoanInfoLtv;
        export type GetLoanInfoMargins = VIPLENDING.GetLoanInfoMargins;
        export type GetLoanInfoOrders = VIPLENDING.GetLoanInfoOrders;
        export type GetLoanInfoResp = VIPLENDING.GetLoanInfoResp;
    }
}
