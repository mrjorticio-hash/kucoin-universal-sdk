package account

import (
	"encoding/json"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestAccountGetAccountInfoReqModel(t *testing.T) {
	// GetAccountInfo
	// Get Account Summary Info
	// /api/v2/user-info

}

func TestAccountGetAccountInfoRespModel(t *testing.T) {
	// GetAccountInfo
	// Get Account Summary Info
	// /api/v2/user-info

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"level\": 0,\n        \"subQuantity\": 3,\n        \"spotSubQuantity\": 3,\n        \"marginSubQuantity\": 2,\n        \"futuresSubQuantity\": 2,\n        \"optionSubQuantity\": 0,\n        \"maxSubQuantity\": 5,\n        \"maxDefaultSubQuantity\": 5,\n        \"maxSpotSubQuantity\": 0,\n        \"maxMarginSubQuantity\": 0,\n        \"maxFuturesSubQuantity\": 0,\n        \"maxOptionSubQuantity\": 0\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetAccountInfoResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetApikeyInfoReqModel(t *testing.T) {
	// GetApikeyInfo
	// Get Apikey Info
	// /api/v1/user/api-key

}

func TestAccountGetApikeyInfoRespModel(t *testing.T) {
	// GetApikeyInfo
	// Get Apikey Info
	// /api/v1/user/api-key

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"remark\": \"account1\",\n        \"apiKey\": \"6705f5c311545b000157d3eb\",\n        \"apiVersion\": 3,\n        \"permission\": \"General,Futures,Spot,Earn,InnerTransfer,Transfer,Margin\",\n        \"ipWhitelist\": \"203.**.154,103.**.34\",\n        \"createdAt\": 1728443843000,\n        \"uid\": 165111215,\n        \"isMaster\": true\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetApikeyInfoResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetSpotAccountTypeReqModel(t *testing.T) {
	// GetSpotAccountType
	// Get Account Type - Spot
	// /api/v1/hf/accounts/opened

}

func TestAccountGetSpotAccountTypeRespModel(t *testing.T) {
	// GetSpotAccountType
	// Get Account Type - Spot
	// /api/v1/hf/accounts/opened

	data := "{\"code\":\"200000\",\"data\":false}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetSpotAccountTypeResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetSpotAccountListReqModel(t *testing.T) {
	// GetSpotAccountList
	// Get Account List - Spot
	// /api/v1/accounts

	data := "{\"currency\": \"USDT\", \"type\": \"main\"}"
	req := &GetSpotAccountListReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetSpotAccountListRespModel(t *testing.T) {
	// GetSpotAccountList
	// Get Account List - Spot
	// /api/v1/accounts

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"548674591753\",\n            \"currency\": \"USDT\",\n            \"type\": \"trade\",\n            \"balance\": \"26.66759503\",\n            \"available\": \"26.66759503\",\n            \"holds\": \"0\"\n        },\n        {\n            \"id\": \"63355cd156298d0001b66e61\",\n            \"currency\": \"USDT\",\n            \"type\": \"main\",\n            \"balance\": \"0.01\",\n            \"available\": \"0.01\",\n            \"holds\": \"0\"\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetSpotAccountListResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetSpotAccountDetailReqModel(t *testing.T) {
	// GetSpotAccountDetail
	// Get Account Detail - Spot
	// /api/v1/accounts/{accountId}

	data := "{\"accountId\": \"548674591753\"}"
	req := &GetSpotAccountDetailReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetSpotAccountDetailRespModel(t *testing.T) {
	// GetSpotAccountDetail
	// Get Account Detail - Spot
	// /api/v1/accounts/{accountId}

	data := "{\"code\":\"200000\",\"data\":{\"currency\":\"USDT\",\"balance\":\"26.66759503\",\"available\":\"26.66759503\",\"holds\":\"0\"}}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetSpotAccountDetailResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetCrossMarginAccountReqModel(t *testing.T) {
	// GetCrossMarginAccount
	// Get Account - Cross Margin
	// /api/v3/margin/accounts

	data := "{\"quoteCurrency\": \"USDT\", \"queryType\": \"MARGIN\"}"
	req := &GetCrossMarginAccountReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetCrossMarginAccountRespModel(t *testing.T) {
	// GetCrossMarginAccount
	// Get Account - Cross Margin
	// /api/v3/margin/accounts

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"totalAssetOfQuoteCurrency\": \"0.02\",\n        \"totalLiabilityOfQuoteCurrency\": \"0\",\n        \"debtRatio\": \"0\",\n        \"status\": \"EFFECTIVE\",\n        \"accounts\": [\n            {\n                \"currency\": \"USDT\",\n                \"total\": \"0.02\",\n                \"available\": \"0.02\",\n                \"hold\": \"0\",\n                \"liability\": \"0\",\n                \"maxBorrowSize\": \"0\",\n                \"borrowEnabled\": true,\n                \"transferInEnabled\": true\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetCrossMarginAccountResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetIsolatedMarginAccountReqModel(t *testing.T) {
	// GetIsolatedMarginAccount
	// Get Account - Isolated Margin
	// /api/v3/isolated/accounts

	data := "{\"symbol\": \"example_string_default_value\", \"quoteCurrency\": \"USDT\", \"queryType\": \"ISOLATED\"}"
	req := &GetIsolatedMarginAccountReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetIsolatedMarginAccountRespModel(t *testing.T) {
	// GetIsolatedMarginAccount
	// Get Account - Isolated Margin
	// /api/v3/isolated/accounts

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"totalAssetOfQuoteCurrency\": \"0.01\",\n        \"totalLiabilityOfQuoteCurrency\": \"0\",\n        \"timestamp\": 1728725465994,\n        \"assets\": [\n            {\n                \"symbol\": \"BTC-USDT\",\n                \"status\": \"EFFECTIVE\",\n                \"debtRatio\": \"0\",\n                \"baseAsset\": {\n                    \"currency\": \"BTC\",\n                    \"borrowEnabled\": true,\n                    \"transferInEnabled\": true,\n                    \"liability\": \"0\",\n                    \"total\": \"0\",\n                    \"available\": \"0\",\n                    \"hold\": \"0\",\n                    \"maxBorrowSize\": \"0\"\n                },\n                \"quoteAsset\": {\n                    \"currency\": \"USDT\",\n                    \"borrowEnabled\": true,\n                    \"transferInEnabled\": true,\n                    \"liability\": \"0\",\n                    \"total\": \"0.01\",\n                    \"available\": \"0.01\",\n                    \"hold\": \"0\",\n                    \"maxBorrowSize\": \"0\"\n                }\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetIsolatedMarginAccountResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetFuturesAccountReqModel(t *testing.T) {
	// GetFuturesAccount
	// Get Account - Futures
	// /api/v1/account-overview

	data := "{\"currency\": \"USDT\"}"
	req := &GetFuturesAccountReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetFuturesAccountRespModel(t *testing.T) {
	// GetFuturesAccount
	// Get Account - Futures
	// /api/v1/account-overview

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"accountEquity\": 394.439280806,\n        \"unrealisedPNL\": 20.15278,\n        \"marginBalance\": 371.394298816,\n        \"positionMargin\": 102.20664159,\n        \"orderMargin\": 10.06002012,\n        \"frozenFunds\": 0.0,\n        \"availableBalance\": 290.326799096,\n        \"currency\": \"USDT\",\n        \"riskRatio\": 0.0065289525,\n        \"maxWithdrawAmount\": 290.326419096\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetFuturesAccountResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetSpotLedgerReqModel(t *testing.T) {
	// GetSpotLedger
	// Get Account Ledgers - Spot/Margin
	// /api/v1/accounts/ledgers

	data := "{\"currency\": \"BTC\", \"direction\": \"in\", \"bizType\": \"TRANSFER\", \"startAt\": 1728663338000, \"endAt\": 1728692138000, \"currentPage\": 1, \"pageSize\": 50}"
	req := &GetSpotLedgerReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetSpotLedgerRespModel(t *testing.T) {
	// GetSpotLedger
	// Get Account Ledgers - Spot/Margin
	// /api/v1/accounts/ledgers

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"id\": \"265329987780896\",\n                \"currency\": \"USDT\",\n                \"amount\": \"0.01\",\n                \"fee\": \"0\",\n                \"balance\": \"0\",\n                \"accountType\": \"TRADE\",\n                \"bizType\": \"SUB_TRANSFER\",\n                \"direction\": \"out\",\n                \"createdAt\": 1728658481484,\n                \"context\": \"\"\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetSpotLedgerResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetSpotHFLedgerReqModel(t *testing.T) {
	// GetSpotHFLedger
	// Get Account Ledgers - Trade_hf
	// /api/v1/hf/accounts/ledgers

	data := "{\"currency\": \"BTC\", \"direction\": \"in\", \"bizType\": \"TRANSFER\", \"lastId\": 254062248624417, \"limit\": 100, \"startAt\": 1728663338000, \"endAt\": 1728692138000}"
	req := &GetSpotHFLedgerReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetSpotHFLedgerRespModel(t *testing.T) {
	// GetSpotHFLedger
	// Get Account Ledgers - Trade_hf
	// /api/v1/hf/accounts/ledgers

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"254062248624417\",\n            \"currency\": \"USDT\",\n            \"amount\": \"1.59760080\",\n            \"fee\": \"0.00159920\",\n            \"tax\": \"0\",\n            \"balance\": \"26.73759503\",\n            \"accountType\": \"TRADE_HF\",\n            \"bizType\": \"TRADE_EXCHANGE\",\n            \"direction\": \"in\",\n            \"createdAt\": \"1728443957539\",\n            \"context\": \"{\\\"symbol\\\":\\\"KCS-USDT\\\",\\\"orderId\\\":\\\"6705f6350dc7210007d6a36d\\\",\\\"tradeId\\\":\\\"10046097631627265\\\"}\"\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetSpotHFLedgerResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetMarginHFLedgerReqModel(t *testing.T) {
	// GetMarginHFLedger
	// Get Account Ledgers - Margin_hf
	// /api/v3/hf/margin/account/ledgers

	data := "{\"currency\": \"BTC\", \"direction\": \"in\", \"bizType\": \"TRANSFER\", \"lastId\": 254062248624417, \"limit\": 100, \"startAt\": 1728663338000, \"endAt\": 1728692138000}"
	req := &GetMarginHFLedgerReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetMarginHFLedgerRespModel(t *testing.T) {
	// GetMarginHFLedger
	// Get Account Ledgers - Margin_hf
	// /api/v3/hf/margin/account/ledgers

	data := "{\"code\":\"200000\",\"data\":[{\"id\":1949641706720,\"currency\":\"USDT\",\"amount\":\"0.01000000\",\"fee\":\"0.00000000\",\"balance\":\"0.01000000\",\"accountType\":\"MARGIN_V2\",\"bizType\":\"TRANSFER\",\"direction\":\"in\",\"createdAt\":1728664091208,\"context\":\"{}\",\"tax\":\"0.00000000\"}]}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetMarginHFLedgerResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetFuturesLedgerReqModel(t *testing.T) {
	// GetFuturesLedger
	// Get Account Ledgers - Futures
	// /api/v1/transaction-history

	data := "{\"currency\": \"XBT\", \"type\": \"Transferin\", \"offset\": 254062248624417, \"forward\": true, \"maxCount\": 50, \"startAt\": 1728663338000, \"endAt\": 1728692138000}"
	req := &GetFuturesLedgerReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetFuturesLedgerRespModel(t *testing.T) {
	// GetFuturesLedger
	// Get Account Ledgers - Futures
	// /api/v1/transaction-history

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"dataList\": [\n            {\n                \"time\": 1728665747000,\n                \"type\": \"TransferIn\",\n                \"amount\": 0.01,\n                \"fee\": 0.0,\n                \"accountEquity\": 14.02924938,\n                \"status\": \"Completed\",\n                \"remark\": \"Transferred from High-Frequency Trading Account\",\n                \"offset\": 51360793,\n                \"currency\": \"USDT\"\n            },\n            {\n                \"time\": 1728648000000,\n                \"type\": \"RealisedPNL\",\n                \"amount\": 0.00630042,\n                \"fee\": 0.0,\n                \"accountEquity\": 20.0,\n                \"status\": \"Completed\",\n                \"remark\": \"XBTUSDTM\",\n                \"offset\": 51352430,\n                \"currency\": \"USDT\"\n            }\n        ],\n        \"hasMore\": false\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetFuturesLedgerResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetMarginAccountDetailReqModel(t *testing.T) {
	// GetMarginAccountDetail
	// Get Account Detail - Margin
	// /api/v1/margin/account

}

func TestAccountGetMarginAccountDetailRespModel(t *testing.T) {
	// GetMarginAccountDetail
	// Get Account Detail - Margin
	// /api/v1/margin/account

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"debtRatio\": \"0\",\n        \"accounts\": [\n            {\n                \"currency\": \"USDT\",\n                \"totalBalance\": \"0.03\",\n                \"availableBalance\": \"0.02\",\n                \"holdBalance\": \"0.01\",\n                \"liability\": \"0\",\n                \"maxBorrowSize\": \"0\"\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetMarginAccountDetailResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetIsolatedMarginAccountListV1ReqModel(t *testing.T) {
	// GetIsolatedMarginAccountListV1
	// Get Account List - Isolated Margin - V1
	// /api/v1/isolated/accounts

	data := "{\"balanceCurrency\": \"USDT\"}"
	req := &GetIsolatedMarginAccountListV1Req{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetIsolatedMarginAccountListV1RespModel(t *testing.T) {
	// GetIsolatedMarginAccountListV1
	// Get Account List - Isolated Margin - V1
	// /api/v1/isolated/accounts

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"totalConversionBalance\": \"0.01\",\n        \"liabilityConversionBalance\": \"0\",\n        \"assets\": [\n            {\n                \"symbol\": \"BTC-USDT\",\n                \"status\": \"CLEAR\",\n                \"debtRatio\": \"0\",\n                \"baseAsset\": {\n                    \"currency\": \"BTC\",\n                    \"totalBalance\": \"0\",\n                    \"holdBalance\": \"0\",\n                    \"availableBalance\": \"0\",\n                    \"liability\": \"0\",\n                    \"interest\": \"0\",\n                    \"borrowableAmount\": \"0\"\n                },\n                \"quoteAsset\": {\n                    \"currency\": \"USDT\",\n                    \"totalBalance\": \"0.01\",\n                    \"holdBalance\": \"0\",\n                    \"availableBalance\": \"0.01\",\n                    \"liability\": \"0\",\n                    \"interest\": \"0\",\n                    \"borrowableAmount\": \"0\"\n                }\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetIsolatedMarginAccountListV1Resp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetIsolatedMarginAccountDetailV1ReqModel(t *testing.T) {
	// GetIsolatedMarginAccountDetailV1
	// Get Account Detail - Isolated Margin - V1
	// /api/v1/isolated/account/{symbol}

	data := "{\"symbol\": \"example_string_default_value\"}"
	req := &GetIsolatedMarginAccountDetailV1Req{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestAccountGetIsolatedMarginAccountDetailV1RespModel(t *testing.T) {
	// GetIsolatedMarginAccountDetailV1
	// Get Account Detail - Isolated Margin - V1
	// /api/v1/isolated/account/{symbol}

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"symbol\": \"BTC-USDT\",\n        \"status\": \"CLEAR\",\n        \"debtRatio\": \"0\",\n        \"baseAsset\": {\n            \"currency\": \"BTC\",\n            \"totalBalance\": \"0\",\n            \"holdBalance\": \"0\",\n            \"availableBalance\": \"0\",\n            \"liability\": \"0\",\n            \"interest\": \"0\",\n            \"borrowableAmount\": \"0\"\n        },\n        \"quoteAsset\": {\n            \"currency\": \"USDT\",\n            \"totalBalance\": \"0.01\",\n            \"holdBalance\": \"0\",\n            \"availableBalance\": \"0.01\",\n            \"liability\": \"0\",\n            \"interest\": \"0\",\n            \"borrowableAmount\": \"0\"\n        }\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetIsolatedMarginAccountDetailV1Resp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}
