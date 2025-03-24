import {
    ClientOptionBuilder,
    GlobalApiEndpoint,
    GlobalBrokerApiEndpoint,
    GlobalFuturesApiEndpoint,
    TransportOptionBuilder,
} from '@model/index';
import {
    FundingFeesAPI,
    GetCurrentFundingRateReq,
    GetPrivateFundingHistoryReq,
    GetPublicFundingHistoryReq,
} from '@src/generate/futures/fundingfees';
import { DefaultClient } from '@api/index';

describe('Auto Test', () => {
    let api: FundingFeesAPI;

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
        api = kucoinRestService.getFuturesService().getFundingFeesApi();
    });

    test('getCurrentFundingRate request test', () => {
        /**
         * getCurrentFundingRate
         * Get Current Funding Rate
         * /api/v1/funding-rate/{symbol}/current
         */
        let builder = GetCurrentFundingRateReq.builder();
        builder.setSymbol('XBTUSDTM');
        let req = builder.build();
        let resp = api.getCurrentFundingRate(req);
        return resp.then((result) => {
            expect(result.symbol).toEqual(expect.anything());
            expect(result.granularity).toEqual(expect.anything());
            expect(result.timePoint).toEqual(expect.anything());
            expect(result.value).toEqual(expect.anything());
            expect(result.predictedValue).toEqual(expect.anything());
            expect(result.fundingRateCap).toEqual(expect.anything());
            expect(result.fundingRateFloor).toEqual(expect.anything());
            console.log(resp);
        });
    });

    test('getPublicFundingHistory request test', () => {
        /**
         * getPublicFundingHistory
         * Get Public Funding History
         * /api/v1/contract/funding-rates
         */
        let builder = GetPublicFundingHistoryReq.builder();
        builder.setSymbol('XBTUSDTM').setFrom(1700310700000).setTo(1702310700000);
        let req = builder.build();
        let resp = api.getPublicFundingHistory(req);
        return resp.then((result) => {
            result.data.forEach((item) => {
                expect(item.symbol).toEqual(expect.any(String));
                expect(item.fundingRate).toEqual(expect.any(Number));
                expect(item.timepoint).toEqual(expect.any(Number));
            })
        });
    });

    test('getPrivateFundingHistory request test', () => {
        /**
         * getPrivateFundingHistory
         * Get Private Funding History
         * /api/v1/funding-history
         */
        let builder = GetPrivateFundingHistoryReq.builder();
        builder
            .setSymbol('XBTUSDTM')
            .setStartAt(1700310700000)
            .setEndAt(1702310700000)
            .setReverse(true)
            .setMaxCount(100);
        let req = builder.build();
        let resp = api.getPrivateFundingHistory(req);
        return resp.then((result) => {
            result.dataList.forEach((item) => {
                expect(item.id).toEqual(expect.any(Number));
                expect(item.symbol).toEqual(expect.any(String));
                expect(item.timePoint).toEqual(expect.any(Number));
                expect(item.fundingRate).toEqual(expect.any(Number));
                expect(item.markPrice).toEqual(expect.any(Number));
                expect(item.positionQty).toEqual(expect.any(Number));
                expect(item.positionCost).toEqual(expect.any(Number));
                expect(item.funding).toEqual(expect.any(Number));
                expect(item.settleCurrency).toEqual(expect.any(String));
                expect(item.context).toEqual(expect.any(String));
                expect(item.marginMode).toEqual(expect.any(String));
            })
            expect(result.hasMore).toEqual(expect.anything());
            console.log(resp);
        });
    });
});
