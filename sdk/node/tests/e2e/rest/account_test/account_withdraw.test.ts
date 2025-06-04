import {
    ClientOptionBuilder,
    GlobalApiEndpoint,
    GlobalBrokerApiEndpoint,
    GlobalFuturesApiEndpoint,
    TransportOptionBuilder,
} from '@model/index';
import { DefaultClient } from '@api/index';
import {
    CancelWithdrawalReq,
    GetWithdrawalHistoryByIdReq,
    GetWithdrawalHistoryOldReq,
    GetWithdrawalHistoryReq,
    GetWithdrawalQuotasReq,
    WithdrawalAPI,
    WithdrawalV1Req,
    WithdrawalV3Req,
} from '@src/generate/account/withdrawal';
import StatusEnum = GetWithdrawalHistoryReq.StatusEnum;

describe('Auto Test', () => {
    let api: WithdrawalAPI;

    beforeAll(() => {
        const key = process.env.API_KEY || '';
        const secret = process.env.API_SECRET || '';
        const passphrase = process.env.API_PASSPHRASE || '';

        // Set specific options, others will fall back to default values
        const httpTransportOption = new TransportOptionBuilder()
            .setKeepAlive(true)
            .setMaxConnsPerHost(10)
            .setMaxIdleConns(10)
            .build();

        // Create a client using the specified options
        const clientOption = new ClientOptionBuilder()
            .setKey(key)
            .setSecret(secret)
            .setPassphrase(passphrase)
            .setSpotEndpoint(GlobalApiEndpoint)
            .setFuturesEndpoint(GlobalFuturesApiEndpoint)
            .setBrokerEndpoint(GlobalBrokerApiEndpoint)
            .setTransportOption(httpTransportOption)
            .build();

        const client = new DefaultClient(clientOption);

        // Get the Restful Service
        const kucoinRestService = client.restService();
        api = kucoinRestService.getAccountService().getWithdrawalApi();
    });

    test('getWithdrawalQuotas request test', () => {
        /**
         * getWithdrawalQuotas
         * Get Withdrawal Quotas
         * /api/v1/withdrawals/quotas
         */
        let builder = GetWithdrawalQuotasReq.builder();
        builder.setCurrency('USDT').setChain('bsc');
        let req = builder.build();
        let resp = api.getWithdrawalQuotas(req);
        return resp.then((result) => {
            expect(result.currency).toEqual(expect.anything());
            expect(result.limitBTCAmount).toEqual(expect.anything());
            expect(result.usedBTCAmount).toEqual(expect.anything());
            expect(result.quotaCurrency).toEqual(expect.anything());
            expect(result.limitQuotaCurrencyAmount).toEqual(expect.anything());
            expect(result.usedQuotaCurrencyAmount).toEqual(expect.anything());
            expect(result.remainAmount).toEqual(expect.anything());
            expect(result.availableAmount).toEqual(expect.anything());
            expect(result.withdrawMinFee).toEqual(expect.anything());
            expect(result.innerWithdrawMinFee).toEqual(expect.anything());
            expect(result.withdrawMinSize).toEqual(expect.anything());
            expect(result.isWithdrawEnabled).toEqual(expect.anything());
            expect(result.precision).toEqual(expect.anything());
            expect(result.chain).toEqual(expect.anything());
            expect(result.lockedAmount).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('withdrawalV3 request test', () => {
        /**
         * withdrawalV3
         * Withdraw(V3)
         * /api/v3/withdrawals
         */
        let builder = WithdrawalV3Req.builder();
        builder
            .setCurrency('USDT')
            .setChain('bsc')
            .setAmount('20')
            .setIsInner(false)
            .setRemark('******')
            .setWithdrawType(WithdrawalV3Req.WithdrawTypeEnum.ADDRESS)
            .setToAddress('****');
        let req = builder.build();
        let resp = api.withdrawalV3(req);
        return resp.then((result) => {
            expect(result.withdrawalId).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('cancelWithdrawal request test', () => {
        /**
         * cancelWithdrawal
         * Cancel Withdrawal
         * /api/v1/withdrawals/{withdrawalId}
         */
        let builder = CancelWithdrawalReq.builder();
        builder.setWithdrawalId('674fba71b6afc90007e29bd2');
        let req = builder.build();
        let resp = api.cancelWithdrawal(req);
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('getWithdrawalHistory request test', () => {
        /**
         * getWithdrawalHistory
         * Get Withdrawal History
         * /api/v1/withdrawals
         */
        let builder = GetWithdrawalHistoryReq.builder();
        builder
            .setCurrency('USDT')
            .setStartAt(1703001600000)
            .setEndAt(1703260800000)
            .setStatus(StatusEnum.FAILURE);
        let req = builder.build();
        let resp = api.getWithdrawalHistory(req);
        return resp.then((result) => {
            expect(result.currentPage).toEqual(expect.anything());
            expect(result.pageSize).toEqual(expect.anything());
            expect(result.totalNum).toEqual(expect.anything());
            expect(result.totalPage).toEqual(expect.anything());
            expect(result.items).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('getWithdrawalHistoryById request test', () => {
        /**
         * getWithdrawalHistoryById
         * Get Withdrawal History By ID
         * /api/v1/withdrawals/{withdrawalId}
         */
        let builder = GetWithdrawalHistoryByIdReq.builder();
        builder.setWithdrawalId('674576dc74b2bb000778452c');
        let req = builder.build();
        let resp = api.getWithdrawalHistoryById(req);
        return resp.then((result) => {
            expect(result.id).toEqual(expect.anything());
            expect(result.uid).toEqual(expect.anything());
            expect(result.currency).toEqual(expect.anything());
            expect(result.chainId).toEqual(expect.anything());
            expect(result.chainName).toEqual(expect.anything());
            expect(result.currencyName).toEqual(expect.anything());
            expect(result.status).toEqual(expect.anything());
            expect(result.failureReason).toEqual(expect.anything());
            expect(result.failureReasonMsg).toEqual(expect.anything());
            expect(result.address).toEqual(expect.anything());
            expect(result.memo).toEqual(expect.anything());
            expect(result.isInner).toEqual(expect.anything());
            expect(result.amount).toEqual(expect.anything());
            expect(result.fee).toEqual(expect.anything());
            expect(result.walletTxId).toEqual(expect.anything());
            expect(result.addressRemark).toEqual(expect.anything());
            expect(result.remark).toEqual(expect.anything());
            expect(result.createdAt).toEqual(expect.anything());
            expect(result.cancelType).toEqual(expect.anything());
            expect(result.returnStatus).toEqual(expect.anything());
            expect(result.returnCurrency).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getWithdrawalHistoryOld request test', () => {
        /**
         * getWithdrawalHistoryOld
         * Get Withdrawal History - Old
         * /api/v1/hist-withdrawals
         */
        let builder = GetWithdrawalHistoryOldReq.builder();
        builder.setCurrency('USDT').setStartAt(1703001600000).setEndAt(1703260800000);
        let req = builder.build();
        let resp = api.getWithdrawalHistoryOld(req);
        return resp.then((result) => {
            expect(result.currentPage).toEqual(expect.anything());
            expect(result.pageSize).toEqual(expect.anything());
            expect(result.totalNum).toEqual(expect.anything());
            expect(result.totalPage).toEqual(expect.anything());
            expect(result.items).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('withdrawalV1 request test', () => {
        /**
         * withdrawalV1
         * Withdraw - V1
         * /api/v1/withdrawals
         */
        let builder = WithdrawalV1Req.builder();
        builder
            .setCurrency('USDT')
            .setChain('bsc')
            .setAddress('***********')
            .setAmount(20)
            .setIsInner(false);
        let req = builder.build();
        let resp = api.withdrawalV1(req);
        return resp.then((result) => {
            expect(result.withdrawalId).toEqual(expect.anything());
            console.log(result);
        });
    });
});
