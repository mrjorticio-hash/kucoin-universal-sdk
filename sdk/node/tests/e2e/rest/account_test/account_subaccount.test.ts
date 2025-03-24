import {
    ClientOptionBuilder,
    GlobalApiEndpoint,
    GlobalBrokerApiEndpoint,
    GlobalFuturesApiEndpoint,
    TransportOptionBuilder,
} from '@model/index';
import { DefaultClient } from '@api/index';
import {
    AddSubAccountApiReq,
    AddSubAccountFuturesPermissionReq,
    AddSubAccountMarginPermissionReq,
    AddSubAccountReq,
    DeleteSubAccountApiReq,
    GetFuturesSubAccountListV2Req,
    GetSpotSubAccountDetailReq,
    GetSpotSubAccountListV2Req,
    GetSpotSubAccountsSummaryV2Req,
    GetSubAccountApiListReq,
    ModifySubAccountApiReq,
    SubAccountAPI,
} from '@src/generate/account/subaccount';

describe('Auto Test', () => {
    let api: SubAccountAPI;

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
        api = kucoinRestService.getAccountService().getSubAccountApi();
    });

    test('addSubAccount request test', () => {
        /**
         * addSubAccount
         * Add SubAccount
         * /api/v2/sub/user/created
         */
        let builder = AddSubAccountReq.builder();
        builder
            .setPassword('**********@123')
            .setRemarks('**********@123')
            .setSubName('sdkTestIntegration2')
            .setAccess(AddSubAccountReq.AccessEnum.SPOT);
        let req = builder.build();
        let resp = api.addSubAccount(req);
        return resp.then((result) => {
            expect(result.uid).toEqual(expect.anything());
            expect(result.subName).toEqual(expect.anything());
            expect(result.remarks).toEqual(expect.anything());
            expect(result.access).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('addSubAccountMarginPermission request test', () => {
        /**
         * addSubAccountMarginPermission
         * Add SubAccount Margin Permission
         * /api/v3/sub/user/margin/enable
         */
        let builder = AddSubAccountMarginPermissionReq.builder();
        builder.setUid('111');
        let req = builder.build();
        let resp = api.addSubAccountMarginPermission(req);
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('addSubAccountFuturesPermission request test', () => {
        /**
         * addSubAccountFuturesPermission
         * Add SubAccount Futures Permission
         * /api/v3/sub/user/futures/enable
         */
        let builder = AddSubAccountFuturesPermissionReq.builder();
        builder.setUid('1111');
        let req = builder.build();
        let resp = api.addSubAccountFuturesPermission(req);
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('getSpotSubAccountsSummaryV2 request test', () => {
        /**
         * getSpotSubAccountsSummaryV2
         * Get SubAccount List - Summary Info
         * /api/v2/sub/user
         */
        let builder = GetSpotSubAccountsSummaryV2Req.builder();
        builder.setCurrentPage(1).setPageSize(10);
        let req = builder.build();
        let resp = api.getSpotSubAccountsSummaryV2(req);
        return resp.then((result) => {
            expect(result.currentPage).toEqual(expect.anything());
            expect(result.pageSize).toEqual(expect.anything());
            expect(result.totalNum).toEqual(expect.anything());
            expect(result.totalPage).toEqual(expect.anything());
            result.items.forEach((item) => {
                expect(item.userId).toEqual(expect.any(String));
                expect(item.uid).toEqual(expect.any(Number));
                expect(item.subName).toEqual(expect.any(String));
                expect(item.status).toEqual(expect.anything());
                expect(item.type).toEqual(expect.anything());
                expect(item.access).toEqual(expect.any(String));
                expect(item.createdAt).toEqual(expect.any(Number));
                expect(item.remarks).toEqual(expect.any(String));
                expect(item.tradeTypes).toEqual(expect.any(Array));
                expect(item.openedTradeTypes).toEqual(expect.any(Array));
            });
            console.log(result);
        });
    });

    test('getSpotSubAccountDetail request test', () => {
        /**
         * getSpotSubAccountDetail
         * Get SubAccount Detail - Balance
         * /api/v1/sub-accounts/{subUserId}
         */
        let builder = GetSpotSubAccountDetailReq.builder();
        builder.setSubUserId('6745b7bc890ba20001911363').setIncludeBaseAmount(false).setBaseCurrency("USDT").setBaseAmount("0.1");
        let req = builder.build();
        let resp = api.getSpotSubAccountDetail(req);
        return resp.then((result) => {
            expect(result.subUserId).toEqual(expect.anything());
            expect(result.subName).toEqual(expect.anything());
            expect(result.mainAccounts).toEqual(expect.anything());
            expect(result.tradeAccounts).toEqual(expect.anything());
            expect(result.marginAccounts).toEqual(expect.anything());
            expect(result.tradeHFAccounts).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('getSpotSubAccountListV2 request test', () => {
        /**
         * getSpotSubAccountListV2
         * Get SubAccount List - Spot Balance(V2)
         * /api/v2/sub-accounts
         */
        let builder = GetSpotSubAccountListV2Req.builder();
        builder.setCurrentPage(1).setPageSize(10);
        let req = builder.build();
        let resp = api.getSpotSubAccountListV2(req);
        return resp.then((result) => {
            expect(result.currentPage).toEqual(expect.anything());
            expect(result.pageSize).toEqual(expect.anything());
            expect(result.totalNum).toEqual(expect.anything());
            expect(result.totalPage).toEqual(expect.anything());
            expect(result.items).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('getFuturesSubAccountListV2 request test', () => {
        /**
         * getFuturesSubAccountListV2
         * Get SubAccount List - Futures Balance(V2)
         * /api/v1/account-overview-all
         */
        let builder = GetFuturesSubAccountListV2Req.builder();
        builder.setCurrency('XBT');
        let req = builder.build();
        let resp = api.getFuturesSubAccountListV2(req);
        return resp.then((result) => {
            expect(result.summary).toEqual(expect.anything());
            result.accounts.forEach((item) => {
                expect(item.accountName).toEqual(expect.any(String));
                expect(item.accountEquity).toEqual(expect.any(Number));
                expect(item.unrealisedPNL).toEqual(expect.any(Number));
                expect(item.marginBalance).toEqual(expect.any(Number));
                expect(item.positionMargin).toEqual(expect.any(Number));
                expect(item.orderMargin).toEqual(expect.any(Number));
                expect(item.frozenFunds).toEqual(expect.any(Number));
                expect(item.availableBalance).toEqual(expect.any(Number));
                expect(item.currency).toEqual(expect.any(String));
            });
            console.log(result);
        });
    });

    test('addSubAccountApi request test', () => {
        /**
         * addSubAccountApi
         * Add SubAccount API
         * /api/v1/sub/api-key
         */
        let builder = AddSubAccountApiReq.builder();
        builder
            .setPassphrase('*****')
            .setRemark('*****')
            .setPermission('General,Spot')
            .setExpire(AddSubAccountApiReq.ExpireEnum._30)
            .setSubName('*****');
        let req = builder.build();
        let resp = api.addSubAccountApi(req);
        return resp.then((result) => {
            expect(result.apiKey).toEqual(expect.anything());
            expect(result.apiSecret).toEqual(expect.anything());
            expect(result.apiVersion).toEqual(expect.anything());
            expect(result.passphrase).toEqual(expect.anything());
            expect(result.permission).toEqual(expect.anything());
            expect(result.createdAt).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('modifySubAccountApi request test', () => {
        /**
         * modifySubAccountApi
         * Modify SubAccount API
         * /api/v1/sub/api-key/update
         */
        let builder = ModifySubAccountApiReq.builder();
        builder
            .setPassphrase('******')
            .setPermission('General,Spot')
            .setIpWhitelist('General')
            .setExpire(ModifySubAccountApiReq.ExpireEnum._30)
            .setSubName('******')
            .setApiKey('******');
        let req = builder.build();
        let resp = api.modifySubAccountApi(req);
        return resp.then((result) => {
            expect(result.subName).toEqual(expect.anything());
            expect(result.apiKey).toEqual(expect.anything());
            expect(result.permission).toEqual(expect.anything());
            expect(result.ipWhitelist).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('getSubAccountApiList request test', () => {
        /**
         * getSubAccountApiList
         * Get SubAccount API List
         * /api/v1/sub/api-key
         */
        let builder = GetSubAccountApiListReq.builder();
        builder.setApiKey('********').setSubName('********');
        let req = builder.build();
        let resp = api.getSubAccountApiList(req);
        return resp.then((result) => {
            result.data.forEach((item) => {
                expect(item.subName).toEqual(expect.any(String));
                expect(item.remark).toEqual(expect.any(String));
                expect(item.apiKey).toEqual(expect.any(String));
                expect(item.apiVersion).toEqual(expect.any(Number));
                expect(item.permission).toEqual(expect.any(String));
                expect(item.createdAt).toEqual(expect.any(Number));
                expect(item.uid).toEqual(expect.any(Number));
                expect(item.isMaster).toEqual(expect.any(Boolean));
            });
            console.log(result);
        });
    });

    test('deleteSubAccountApi request test', () => {
        /**
         * deleteSubAccountApi
         * Delete SubAccount API
         * /api/v1/sub/api-key
         */
        let builder = DeleteSubAccountApiReq.builder();
        builder.setApiKey('*****').setSubName('*****').setPassphrase('********');
        let req = builder.build();
        let resp = api.deleteSubAccountApi(req);
        return resp.then((result) => {
            expect(result.subName).toEqual(expect.anything());
            expect(result.apiKey).toEqual(expect.anything());
            console.log(result);
        });
    });

    test('getSpotSubAccountsSummaryV1 request test', () => {
        /**
         * getSpotSubAccountsSummaryV1
         * Get SubAccount List - Summary Info(V1)
         * /api/v1/sub/user
         */
        let resp = api.getSpotSubAccountsSummaryV1();
        return resp.then((result) => {
            result.data.forEach((item) => {
                expect(item.userId).toEqual(expect.any(String));
                expect(item.uid).toEqual(expect.any(Number));
                expect(item.subName).toEqual(expect.any(String));
                expect(item.type).toEqual(expect.any(Number));
                expect(item.remarks).toEqual(expect.any(String));
                expect(item.access).toEqual(expect.any(String));
            });
            console.log(result);
        });
    });

    test('getSpotSubAccountListV1 request test', () => {
        /**
         * getSpotSubAccountListV1
         * Get SubAccount List - Spot Balance(V1)
         * /api/v1/sub-accounts
         */
        let resp = api.getSpotSubAccountListV1();
        return resp.then((result) => {
            expect(result.data).toEqual(expect.anything());
            console.log(result);
        });
    });
});
