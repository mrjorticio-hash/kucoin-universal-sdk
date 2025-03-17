import {
    ClientOptionBuilder,
    GlobalApiEndpoint,
    GlobalBrokerApiEndpoint,
    GlobalFuturesApiEndpoint,
    TransportOptionBuilder,
} from '@model/index';
import {
    AddDepositAddressV1Req,
    AddDepositAddressV3Req,
    DepositAPI,
    GetDepositAddressV1Req,
    GetDepositAddressV2Req,
    GetDepositAddressV3Req,
    GetDepositHistoryOldReq,
    GetDepositHistoryReq,
} from '@src/generate/account/deposit';
import { DefaultClient } from '@api/index';
import ToEnum = AddDepositAddressV1Req.ToEnum;

describe('Auto Test', () => {
    let api: DepositAPI;

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
        api = kucoinRestService.getAccountService().getDepositApi();
    });
    test('addDepositAddressV3 request test', () => {
        /**
         * addDepositAddressV3
         * Add Deposit Address(V3)
         * /api/v3/deposit-address/create
         */
        let builder = AddDepositAddressV3Req.builder();
        builder
            .setCurrency('USDT')
            .setChain('near')
            .setTo(AddDepositAddressV3Req.ToEnum.MAIN)
            .setAmount('1');
        let req = builder.build();
        let resp = api.addDepositAddressV3(req);
        return resp.then((result) => {
            expect(result.address).toEqual(expect.anything());
            expect(result.chainId).toEqual(expect.anything());
            expect(result.to).toEqual(expect.anything());
            expect(result.expirationDate).toEqual(expect.anything());
            expect(result.currency).toEqual(expect.anything());
            expect(result.chainName).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('getDepositAddressV3 request test', () => {
        /**
         * getDepositAddressV3
         * Get Deposit Address(V3)
         * /api/v3/deposit-addresses
         */
        let builder = GetDepositAddressV3Req.builder();
        builder.setCurrency('ETH').setAmount('10').setChain('eth');
        let req = builder.build();
        let resp = api.getDepositAddressV3(req);
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('getDepositHistory request test', () => {
        /**
         * getDepositHistory
         * Get Deposit History
         * /api/v1/deposits
         */
        let builder = GetDepositHistoryReq.builder();
        builder.setCurrency('USDT').setStartAt(1673496371000).setEndAt(1705032371000);
        let req = builder.build();
        let resp = api.getDepositHistory(req);
        return resp.then((result) => {
            expect(result.currentPage).toEqual(expect.anything());
            expect(result.pageSize).toEqual(expect.anything());
            expect(result.totalNum).toEqual(expect.anything());
            expect(result.totalPage).toEqual(expect.anything());
            result.items.forEach((item) => {
                expect(item.currency).toEqual(expect.any(String));
                expect(item.chain).toEqual(expect.any(String));
                expect(item.status).toEqual(expect.any(String));
                expect(item.address).toEqual(expect.any(String));
                expect(item.memo).toEqual(expect.any(String));
                expect(item.isInner).toEqual(expect.any(Boolean));
                expect(item.amount).toEqual(expect.any(String));
                expect(item.fee).toEqual(expect.any(String));
                expect(item.walletTxId).toEqual(expect.any(String));
                expect(item.createdAt).toEqual(expect.any(Number));
                expect(item.updatedAt).toEqual(expect.any(Number));
                expect(item.remark).toEqual(expect.any(String));
                expect(item.arrears).toEqual(expect.any(Boolean));
            });
            console.log(result);
        });
    });

    test('getDepositAddressV2 request test', () => {
        /**
         * getDepositAddressV2
         * Get Deposit Addresses(V2)
         * /api/v2/deposit-addresses
         */
        let builder = GetDepositAddressV2Req.builder();
        builder.setCurrency('USDT').setChain("SOL");
        let req = builder.build();
        let resp = api.getDepositAddressV2(req);
        return resp.then((result) => {
            result.data.forEach((item) => {
                expect(item.address).toEqual(expect.any(String));
                expect(item.memo).toEqual(expect.any(String));
                expect(item.chain).toEqual(expect.any(String));
                expect(item.chainId).toEqual(expect.any(String));
                expect(item.to).toEqual(expect.any(String));
                expect(item.currency).toEqual(expect.any(String));
                expect(item.contractAddress).toEqual(expect.any(String));
            });
            console.log(result);
        });
    });

    test('getDepositAddressV1 request test', () => {
        /**
         * getDepositAddressV1
         * Get Deposit Addresses - V1
         * /api/v1/deposit-addresses
         */
        let builder = GetDepositAddressV1Req.builder();
        builder.setCurrency('USDT').setChain('eth');
        let req = builder.build();
        let resp = api.getDepositAddressV1(req);
        return resp.then((result) => {
            expect(result.address).toEqual(expect.anything());
            expect(result.memo).toEqual(expect.anything());
            expect(result.chain).toEqual(expect.anything());
            expect(result.chainId).toEqual(expect.anything());
            expect(result.to).toEqual(expect.anything());
            expect(result.currency).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('getDepositHistoryOld request test', () => {
        /**
         * getDepositHistoryOld
         * Get Deposit History - Old
         * /api/v1/hist-deposits
         */
        let builder = GetDepositHistoryOldReq.builder();
        builder.setStartAt(1714492800000).setEndAt(1732982400000);
        let req = builder.build();
        let resp = api.getDepositHistoryOld(req);
        return resp.then((result) => {
            expect(result.currentPage).toEqual(expect.anything());
            expect(result.pageSize).toEqual(expect.anything());
            expect(result.totalNum).toEqual(expect.anything());
            expect(result.totalPage).toEqual(expect.anything());
            expect(result.items).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('addDepositAddressV1 request test', () => {
        /**
         * addDepositAddressV1
         * Add Deposit Address - V1
         * /api/v1/deposit-addresses
         */
        let builder = AddDepositAddressV1Req.builder();
        builder.setCurrency('ETH').setChain('kcc').setTo(ToEnum.TRADE);
        let req = builder.build();
        let resp = api.addDepositAddressV1(req);
        return resp.then((result) => {
            expect(result.address).toEqual(expect.anything());
            expect(result.chain).toEqual(expect.anything());
            expect(result.chainId).toEqual(expect.anything());
            expect(result.to).toEqual(expect.anything());
            expect(result.currency).toEqual(expect.anything());
            console.log(result);
        });
    });
});
