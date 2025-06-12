import * as ACCOUNT from './account';
import * as DEPOSIT from './deposit';
import * as WITHDRAWAL from './withdrawal';
import * as FEE from './fee';
import * as SUBACCOUNT from './subaccount';
import * as TRANSFER from './transfer';
export const Account = {
    Account: ACCOUNT,
    Deposit: DEPOSIT,
    Withdrawal: WITHDRAWAL,
    Fee: FEE,
    SubAccount: SUBACCOUNT,
    Transfer: TRANSFER,
};
export namespace Account {
    export type AccountAPI = ACCOUNT.AccountAPI;
    export type DepositAPI = DEPOSIT.DepositAPI;
    export type WithdrawalAPI = WITHDRAWAL.WithdrawalAPI;
    export type FeeAPI = FEE.FeeAPI;
    export type SubAccountAPI = SUBACCOUNT.SubAccountAPI;
    export type TransferAPI = TRANSFER.TransferAPI;
    export namespace Deposit {
        export type AddDepositAddressV1Req = DEPOSIT.AddDepositAddressV1Req;
        export type AddDepositAddressV1Resp = DEPOSIT.AddDepositAddressV1Resp;
        export type AddDepositAddressV3Req = DEPOSIT.AddDepositAddressV3Req;
        export type AddDepositAddressV3Resp = DEPOSIT.AddDepositAddressV3Resp;
        export type GetDepositAddressV1Req = DEPOSIT.GetDepositAddressV1Req;
        export type GetDepositAddressV1Resp = DEPOSIT.GetDepositAddressV1Resp;
        export type GetDepositAddressV2Data = DEPOSIT.GetDepositAddressV2Data;
        export type GetDepositAddressV2Req = DEPOSIT.GetDepositAddressV2Req;
        export type GetDepositAddressV2Resp = DEPOSIT.GetDepositAddressV2Resp;
        export type GetDepositAddressV3Data = DEPOSIT.GetDepositAddressV3Data;
        export type GetDepositAddressV3Req = DEPOSIT.GetDepositAddressV3Req;
        export type GetDepositAddressV3Resp = DEPOSIT.GetDepositAddressV3Resp;
        export type GetDepositHistoryItems = DEPOSIT.GetDepositHistoryItems;
        export type GetDepositHistoryOldItems = DEPOSIT.GetDepositHistoryOldItems;
        export type GetDepositHistoryOldReq = DEPOSIT.GetDepositHistoryOldReq;
        export type GetDepositHistoryOldResp = DEPOSIT.GetDepositHistoryOldResp;
        export type GetDepositHistoryReq = DEPOSIT.GetDepositHistoryReq;
        export type GetDepositHistoryResp = DEPOSIT.GetDepositHistoryResp;
    }
    export namespace Fee {
        export type GetBasicFeeReq = FEE.GetBasicFeeReq;
        export type GetBasicFeeResp = FEE.GetBasicFeeResp;
        export type GetFuturesActualFeeReq = FEE.GetFuturesActualFeeReq;
        export type GetFuturesActualFeeResp = FEE.GetFuturesActualFeeResp;
        export type GetSpotActualFeeData = FEE.GetSpotActualFeeData;
        export type GetSpotActualFeeReq = FEE.GetSpotActualFeeReq;
        export type GetSpotActualFeeResp = FEE.GetSpotActualFeeResp;
    }
    export namespace Withdrawal {
        export type CancelWithdrawalReq = WITHDRAWAL.CancelWithdrawalReq;
        export type CancelWithdrawalResp = WITHDRAWAL.CancelWithdrawalResp;
        export type GetWithdrawalHistoryByIdReq = WITHDRAWAL.GetWithdrawalHistoryByIdReq;
        export type GetWithdrawalHistoryByIdResp = WITHDRAWAL.GetWithdrawalHistoryByIdResp;
        export type GetWithdrawalHistoryItems = WITHDRAWAL.GetWithdrawalHistoryItems;
        export type GetWithdrawalHistoryOldItems = WITHDRAWAL.GetWithdrawalHistoryOldItems;
        export type GetWithdrawalHistoryOldReq = WITHDRAWAL.GetWithdrawalHistoryOldReq;
        export type GetWithdrawalHistoryOldResp = WITHDRAWAL.GetWithdrawalHistoryOldResp;
        export type GetWithdrawalHistoryReq = WITHDRAWAL.GetWithdrawalHistoryReq;
        export type GetWithdrawalHistoryResp = WITHDRAWAL.GetWithdrawalHistoryResp;
        export type GetWithdrawalQuotasReq = WITHDRAWAL.GetWithdrawalQuotasReq;
        export type GetWithdrawalQuotasResp = WITHDRAWAL.GetWithdrawalQuotasResp;
        export type WithdrawalV1Req = WITHDRAWAL.WithdrawalV1Req;
        export type WithdrawalV1Resp = WITHDRAWAL.WithdrawalV1Resp;
        export type WithdrawalV3Req = WITHDRAWAL.WithdrawalV3Req;
        export type WithdrawalV3Resp = WITHDRAWAL.WithdrawalV3Resp;
    }
    export namespace Transfer {
        export type FlexTransferReq = TRANSFER.FlexTransferReq;
        export type FlexTransferResp = TRANSFER.FlexTransferResp;
        export type FuturesAccountTransferInReq = TRANSFER.FuturesAccountTransferInReq;
        export type FuturesAccountTransferInResp = TRANSFER.FuturesAccountTransferInResp;
        export type FuturesAccountTransferOutReq = TRANSFER.FuturesAccountTransferOutReq;
        export type FuturesAccountTransferOutResp = TRANSFER.FuturesAccountTransferOutResp;
        export type GetFuturesAccountTransferOutLedgerItems =
            TRANSFER.GetFuturesAccountTransferOutLedgerItems;
        export type GetFuturesAccountTransferOutLedgerReq =
            TRANSFER.GetFuturesAccountTransferOutLedgerReq;
        export type GetFuturesAccountTransferOutLedgerResp =
            TRANSFER.GetFuturesAccountTransferOutLedgerResp;
        export type GetTransferQuotasReq = TRANSFER.GetTransferQuotasReq;
        export type GetTransferQuotasResp = TRANSFER.GetTransferQuotasResp;
        export type InnerTransferReq = TRANSFER.InnerTransferReq;
        export type InnerTransferResp = TRANSFER.InnerTransferResp;
        export type SubAccountTransferReq = TRANSFER.SubAccountTransferReq;
        export type SubAccountTransferResp = TRANSFER.SubAccountTransferResp;
    }
    export namespace SubAccount {
        export type AddSubAccountApiReq = SUBACCOUNT.AddSubAccountApiReq;
        export type AddSubAccountApiResp = SUBACCOUNT.AddSubAccountApiResp;
        export type AddSubAccountFuturesPermissionReq =
            SUBACCOUNT.AddSubAccountFuturesPermissionReq;
        export type AddSubAccountFuturesPermissionResp =
            SUBACCOUNT.AddSubAccountFuturesPermissionResp;
        export type AddSubAccountMarginPermissionReq = SUBACCOUNT.AddSubAccountMarginPermissionReq;
        export type AddSubAccountMarginPermissionResp =
            SUBACCOUNT.AddSubAccountMarginPermissionResp;
        export type AddSubAccountReq = SUBACCOUNT.AddSubAccountReq;
        export type AddSubAccountResp = SUBACCOUNT.AddSubAccountResp;
        export type DeleteSubAccountApiReq = SUBACCOUNT.DeleteSubAccountApiReq;
        export type DeleteSubAccountApiResp = SUBACCOUNT.DeleteSubAccountApiResp;
        export type GetFuturesSubAccountListV2Accounts =
            SUBACCOUNT.GetFuturesSubAccountListV2Accounts;
        export type GetFuturesSubAccountListV2Req = SUBACCOUNT.GetFuturesSubAccountListV2Req;
        export type GetFuturesSubAccountListV2Resp = SUBACCOUNT.GetFuturesSubAccountListV2Resp;
        export type GetFuturesSubAccountListV2Summary =
            SUBACCOUNT.GetFuturesSubAccountListV2Summary;
        export type GetSpotSubAccountDetailMainAccounts =
            SUBACCOUNT.GetSpotSubAccountDetailMainAccounts;
        export type GetSpotSubAccountDetailMarginAccounts =
            SUBACCOUNT.GetSpotSubAccountDetailMarginAccounts;
        export type GetSpotSubAccountDetailReq = SUBACCOUNT.GetSpotSubAccountDetailReq;
        export type GetSpotSubAccountDetailResp = SUBACCOUNT.GetSpotSubAccountDetailResp;
        export type GetSpotSubAccountDetailTradeAccounts =
            SUBACCOUNT.GetSpotSubAccountDetailTradeAccounts;
        export type GetSpotSubAccountListV1Data = SUBACCOUNT.GetSpotSubAccountListV1Data;
        export type GetSpotSubAccountListV1DataMainAccounts =
            SUBACCOUNT.GetSpotSubAccountListV1DataMainAccounts;
        export type GetSpotSubAccountListV1DataMarginAccounts =
            SUBACCOUNT.GetSpotSubAccountListV1DataMarginAccounts;
        export type GetSpotSubAccountListV1DataTradeAccounts =
            SUBACCOUNT.GetSpotSubAccountListV1DataTradeAccounts;
        export type GetSpotSubAccountListV1Resp = SUBACCOUNT.GetSpotSubAccountListV1Resp;
        export type GetSpotSubAccountListV2Items = SUBACCOUNT.GetSpotSubAccountListV2Items;
        export type GetSpotSubAccountListV2ItemsMainAccounts =
            SUBACCOUNT.GetSpotSubAccountListV2ItemsMainAccounts;
        export type GetSpotSubAccountListV2ItemsMarginAccounts =
            SUBACCOUNT.GetSpotSubAccountListV2ItemsMarginAccounts;
        export type GetSpotSubAccountListV2ItemsTradeAccounts =
            SUBACCOUNT.GetSpotSubAccountListV2ItemsTradeAccounts;
        export type GetSpotSubAccountListV2Req = SUBACCOUNT.GetSpotSubAccountListV2Req;
        export type GetSpotSubAccountListV2Resp = SUBACCOUNT.GetSpotSubAccountListV2Resp;
        export type GetSpotSubAccountsSummaryV1Data = SUBACCOUNT.GetSpotSubAccountsSummaryV1Data;
        export type GetSpotSubAccountsSummaryV1Resp = SUBACCOUNT.GetSpotSubAccountsSummaryV1Resp;
        export type GetSpotSubAccountsSummaryV2Items = SUBACCOUNT.GetSpotSubAccountsSummaryV2Items;
        export type GetSpotSubAccountsSummaryV2Req = SUBACCOUNT.GetSpotSubAccountsSummaryV2Req;
        export type GetSpotSubAccountsSummaryV2Resp = SUBACCOUNT.GetSpotSubAccountsSummaryV2Resp;
        export type GetSubAccountApiListData = SUBACCOUNT.GetSubAccountApiListData;
        export type GetSubAccountApiListReq = SUBACCOUNT.GetSubAccountApiListReq;
        export type GetSubAccountApiListResp = SUBACCOUNT.GetSubAccountApiListResp;
        export type ModifySubAccountApiReq = SUBACCOUNT.ModifySubAccountApiReq;
        export type ModifySubAccountApiResp = SUBACCOUNT.ModifySubAccountApiResp;
    }
    export namespace Account {
        export type GetAccountInfoResp = ACCOUNT.GetAccountInfoResp;
        export type GetApikeyInfoResp = ACCOUNT.GetApikeyInfoResp;
        export type GetCrossMarginAccountAccounts = ACCOUNT.GetCrossMarginAccountAccounts;
        export type GetCrossMarginAccountReq = ACCOUNT.GetCrossMarginAccountReq;
        export type GetCrossMarginAccountResp = ACCOUNT.GetCrossMarginAccountResp;
        export type GetFuturesAccountReq = ACCOUNT.GetFuturesAccountReq;
        export type GetFuturesAccountResp = ACCOUNT.GetFuturesAccountResp;
        export type GetFuturesLedgerDataList = ACCOUNT.GetFuturesLedgerDataList;
        export type GetFuturesLedgerReq = ACCOUNT.GetFuturesLedgerReq;
        export type GetFuturesLedgerResp = ACCOUNT.GetFuturesLedgerResp;
        export type GetIsolatedMarginAccountAssets = ACCOUNT.GetIsolatedMarginAccountAssets;
        export type GetIsolatedMarginAccountAssetsBaseAsset =
            ACCOUNT.GetIsolatedMarginAccountAssetsBaseAsset;
        export type GetIsolatedMarginAccountAssetsQuoteAsset =
            ACCOUNT.GetIsolatedMarginAccountAssetsQuoteAsset;
        export type GetIsolatedMarginAccountDetailV1BaseAsset =
            ACCOUNT.GetIsolatedMarginAccountDetailV1BaseAsset;
        export type GetIsolatedMarginAccountDetailV1QuoteAsset =
            ACCOUNT.GetIsolatedMarginAccountDetailV1QuoteAsset;
        export type GetIsolatedMarginAccountDetailV1Req =
            ACCOUNT.GetIsolatedMarginAccountDetailV1Req;
        export type GetIsolatedMarginAccountDetailV1Resp =
            ACCOUNT.GetIsolatedMarginAccountDetailV1Resp;
        export type GetIsolatedMarginAccountListV1Assets =
            ACCOUNT.GetIsolatedMarginAccountListV1Assets;
        export type GetIsolatedMarginAccountListV1AssetsBaseAsset =
            ACCOUNT.GetIsolatedMarginAccountListV1AssetsBaseAsset;
        export type GetIsolatedMarginAccountListV1AssetsQuoteAsset =
            ACCOUNT.GetIsolatedMarginAccountListV1AssetsQuoteAsset;
        export type GetIsolatedMarginAccountListV1Req = ACCOUNT.GetIsolatedMarginAccountListV1Req;
        export type GetIsolatedMarginAccountListV1Resp = ACCOUNT.GetIsolatedMarginAccountListV1Resp;
        export type GetIsolatedMarginAccountReq = ACCOUNT.GetIsolatedMarginAccountReq;
        export type GetIsolatedMarginAccountResp = ACCOUNT.GetIsolatedMarginAccountResp;
        export type GetMarginAccountDetailAccounts = ACCOUNT.GetMarginAccountDetailAccounts;
        export type GetMarginAccountDetailResp = ACCOUNT.GetMarginAccountDetailResp;
        export type GetMarginHFLedgerData = ACCOUNT.GetMarginHFLedgerData;
        export type GetMarginHFLedgerReq = ACCOUNT.GetMarginHFLedgerReq;
        export type GetMarginHFLedgerResp = ACCOUNT.GetMarginHFLedgerResp;
        export type GetSpotAccountDetailReq = ACCOUNT.GetSpotAccountDetailReq;
        export type GetSpotAccountDetailResp = ACCOUNT.GetSpotAccountDetailResp;
        export type GetSpotAccountListData = ACCOUNT.GetSpotAccountListData;
        export type GetSpotAccountListReq = ACCOUNT.GetSpotAccountListReq;
        export type GetSpotAccountListResp = ACCOUNT.GetSpotAccountListResp;
        export type GetSpotAccountTypeResp = ACCOUNT.GetSpotAccountTypeResp;
        export type GetSpotHFLedgerData = ACCOUNT.GetSpotHFLedgerData;
        export type GetSpotHFLedgerReq = ACCOUNT.GetSpotHFLedgerReq;
        export type GetSpotHFLedgerResp = ACCOUNT.GetSpotHFLedgerResp;
        export type GetSpotLedgerItems = ACCOUNT.GetSpotLedgerItems;
        export type GetSpotLedgerReq = ACCOUNT.GetSpotLedgerReq;
        export type GetSpotLedgerResp = ACCOUNT.GetSpotLedgerResp;
    }
}
