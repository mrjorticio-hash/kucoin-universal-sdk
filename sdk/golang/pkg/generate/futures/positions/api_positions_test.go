package positions

import (
	"encoding/json"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestPositionsGetMarginModeReqModel(t *testing.T) {
	// GetMarginMode
	// Get Margin Mode
	// /api/v2/position/getMarginMode

	data := "{\"symbol\": \"XBTUSDTM\"}"
	req := &GetMarginModeReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetMarginModeRespModel(t *testing.T) {
	// GetMarginMode
	// Get Margin Mode
	// /api/v2/position/getMarginMode

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"symbol\": \"XBTUSDTM\",\n        \"marginMode\": \"ISOLATED\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetMarginModeResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsSwitchMarginModeReqModel(t *testing.T) {
	// SwitchMarginMode
	// Switch Margin Mode
	// /api/v2/position/changeMarginMode

	data := "{\"symbol\": \"XBTUSDTM\", \"marginMode\": \"ISOLATED\"}"
	req := &SwitchMarginModeReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsSwitchMarginModeRespModel(t *testing.T) {
	// SwitchMarginMode
	// Switch Margin Mode
	// /api/v2/position/changeMarginMode

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"symbol\": \"XBTUSDTM\",\n        \"marginMode\": \"ISOLATED\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &SwitchMarginModeResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsBatchSwitchMarginModeReqModel(t *testing.T) {
	// BatchSwitchMarginMode
	// Batch Switch Margin Mode
	// /api/v2/position/batchChangeMarginMode

	data := "{\"marginMode\": \"ISOLATED\", \"symbols\": [\"XBTUSDTM\", \"ETHUSDTM\"]}"
	req := &BatchSwitchMarginModeReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsBatchSwitchMarginModeRespModel(t *testing.T) {
	// BatchSwitchMarginMode
	// Batch Switch Margin Mode
	// /api/v2/position/batchChangeMarginMode

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"marginMode\": {\n            \"ETHUSDTM\": \"ISOLATED\",\n            \"XBTUSDTM\": \"CROSS\"\n        },\n        \"errors\": [\n            {\n                \"code\": \"50002\",\n                \"msg\": \"exist.order.or.position\",\n                \"symbol\": \"XBTUSDTM\"\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &BatchSwitchMarginModeResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetMaxOpenSizeReqModel(t *testing.T) {
	// GetMaxOpenSize
	// Get Max Open Size
	// /api/v2/getMaxOpenSize

	data := "{\"symbol\": \"XBTUSDTM\", \"price\": \"example_string_default_value\", \"leverage\": 123456}"
	req := &GetMaxOpenSizeReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetMaxOpenSizeRespModel(t *testing.T) {
	// GetMaxOpenSize
	// Get Max Open Size
	// /api/v2/getMaxOpenSize

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"symbol\": \"XBTUSDTM\",\n        \"maxBuyOpenSize\": 0,\n        \"maxSellOpenSize\": 0\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetMaxOpenSizeResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetPositionDetailsReqModel(t *testing.T) {
	// GetPositionDetails
	// Get Position Details
	// /api/v1/position

	data := "{\"symbol\": \"example_string_default_value\"}"
	req := &GetPositionDetailsReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetPositionDetailsRespModel(t *testing.T) {
	// GetPositionDetails
	// Get Position Details
	// /api/v1/position

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"id\": \"500000000000988255\",\n        \"symbol\": \"XBTUSDTM\",\n        \"autoDeposit\": false,\n        \"crossMode\": false,\n        \"maintMarginReq\": 0.005,\n        \"riskLimit\": 500000,\n        \"realLeverage\": 2.88,\n        \"delevPercentage\": 0.18,\n        \"openingTimestamp\": 1729155616322,\n        \"currentTimestamp\": 1729482542135,\n        \"currentQty\": 1,\n        \"currentCost\": 67.4309,\n        \"currentComm\": 0.01925174,\n        \"unrealisedCost\": 67.4309,\n        \"realisedGrossCost\": 0.0,\n        \"realisedCost\": 0.01925174,\n        \"isOpen\": true,\n        \"markPrice\": 68900.7,\n        \"markValue\": 68.9007,\n        \"posCost\": 67.4309,\n        \"posCross\": 0.01645214,\n        \"posCrossMargin\": 0,\n        \"posInit\": 22.4769666644,\n        \"posComm\": 0.0539546299,\n        \"posCommCommon\": 0.0539447199,\n        \"posLoss\": 0.03766885,\n        \"posMargin\": 22.5097045843,\n        \"posFunding\": -0.0212068,\n        \"posMaint\": 0.3931320569,\n        \"maintMargin\": 23.9795045843,\n        \"realisedGrossPnl\": 0.0,\n        \"realisedPnl\": -0.06166534,\n        \"unrealisedPnl\": 1.4698,\n        \"unrealisedPnlPcnt\": 0.0218,\n        \"unrealisedRoePcnt\": 0.0654,\n        \"avgEntryPrice\": 67430.9,\n        \"liquidationPrice\": 45314.33,\n        \"bankruptPrice\": 44975.16,\n        \"settleCurrency\": \"USDT\",\n        \"maintainMargin\": 0.005,\n        \"riskLimitLevel\": 2,\n        \"marginMode\": \"ISOLATED\",\n        \"positionSide\": \"BOTH\",\n        \"leverage\": 2.88\n    }\n}\n"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetPositionDetailsResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetPositionListReqModel(t *testing.T) {
	// GetPositionList
	// Get Position List
	// /api/v1/positions

	data := "{\"currency\": \"USDT\"}"
	req := &GetPositionListReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetPositionListRespModel(t *testing.T) {
	// GetPositionList
	// Get Position List
	// /api/v1/positions

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"500000000001046430\",\n            \"symbol\": \"ETHUSDM\",\n            \"crossMode\": true,\n            \"delevPercentage\": 0.71,\n            \"openingTimestamp\": 1730635780702,\n            \"currentTimestamp\": 1730636040926,\n            \"currentQty\": 1,\n            \"currentCost\": -4.069805E-4,\n            \"currentComm\": 2.441E-7,\n            \"unrealisedCost\": -4.069805E-4,\n            \"realisedGrossCost\": 0.0,\n            \"realisedCost\": 2.441E-7,\n            \"isOpen\": true,\n            \"markPrice\": 2454.12,\n            \"markValue\": -4.07478E-4,\n            \"posCost\": -4.069805E-4,\n            \"posInit\": 4.06981E-5,\n            \"posMargin\": 4.07478E-5,\n            \"realisedGrossPnl\": 0.0,\n            \"realisedPnl\": -2.441E-7,\n            \"unrealisedPnl\": -4.975E-7,\n            \"unrealisedPnlPcnt\": -0.0012,\n            \"unrealisedRoePcnt\": -0.0122,\n            \"avgEntryPrice\": 2457.12,\n            \"liquidationPrice\": 1429.96,\n            \"bankruptPrice\": 1414.96,\n            \"settleCurrency\": \"ETH\",\n            \"isInverse\": true,\n            \"marginMode\": \"CROSS\",\n            \"positionSide\": \"BOTH\",\n            \"leverage\": 10\n        },\n        {\n            \"id\": \"500000000000988255\",\n            \"symbol\": \"XBTUSDTM\",\n            \"autoDeposit\": true,\n            \"crossMode\": false,\n            \"maintMarginReq\": 0.005,\n            \"riskLimit\": 500000,\n            \"realLeverage\": 2.97,\n            \"delevPercentage\": 0.5,\n            \"openingTimestamp\": 1729155616322,\n            \"currentTimestamp\": 1730636040926,\n            \"currentQty\": 1,\n            \"currentCost\": 67.4309,\n            \"currentComm\": -0.15936162,\n            \"unrealisedCost\": 67.4309,\n            \"realisedGrossCost\": 0.0,\n            \"realisedCost\": -0.15936162,\n            \"isOpen\": true,\n            \"markPrice\": 68323.06,\n            \"markValue\": 68.32306,\n            \"posCost\": 67.4309,\n            \"posCross\": 0.06225152,\n            \"posCrossMargin\": 0,\n            \"posInit\": 22.2769666644,\n            \"posComm\": 0.0539821899,\n            \"posCommCommon\": 0.0539447199,\n            \"posLoss\": 0.26210915,\n            \"posMargin\": 22.1310912243,\n            \"posFunding\": -0.19982016,\n            \"posMaint\": 0.4046228699,\n            \"maintMargin\": 23.0232512243,\n            \"realisedGrossPnl\": 0.0,\n            \"realisedPnl\": -0.2402787,\n            \"unrealisedPnl\": 0.89216,\n            \"unrealisedPnlPcnt\": 0.0132,\n            \"unrealisedRoePcnt\": 0.04,\n            \"avgEntryPrice\": 67430.9,\n            \"liquidationPrice\": 45704.44,\n            \"bankruptPrice\": 45353.8,\n            \"settleCurrency\": \"USDT\",\n            \"isInverse\": false,\n            \"maintainMargin\": 0.005,\n            \"marginMode\": \"ISOLATED\",\n            \"positionSide\": \"BOTH\",\n            \"leverage\": 2.97\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetPositionListResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetPositionsHistoryReqModel(t *testing.T) {
	// GetPositionsHistory
	// Get Positions History
	// /api/v1/history-positions

	data := "{\"symbol\": \"example_string_default_value\", \"from\": 123456, \"to\": 123456, \"limit\": 10, \"pageId\": 1}"
	req := &GetPositionsHistoryReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetPositionsHistoryRespModel(t *testing.T) {
	// GetPositionsHistory
	// Get Positions History
	// /api/v1/history-positions

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 10,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"closeId\": \"500000000036305465\",\n                \"userId\": \"633559791e1cbc0001f319bc\",\n                \"symbol\": \"XBTUSDTM\",\n                \"settleCurrency\": \"USDT\",\n                \"leverage\": \"1.0\",\n                \"type\": \"CLOSE_LONG\",\n                \"pnl\": \"0.51214413\",\n                \"realisedGrossCost\": \"-0.5837\",\n                \"realisedGrossCostNew\": \"-0.5837\",\n                \"withdrawPnl\": \"0.0\",\n                \"tradeFee\": \"0.03766066\",\n                \"fundingFee\": \"-0.03389521\",\n                \"openTime\": 1735549162120,\n                \"closeTime\": 1735589352069,\n                \"openPrice\": \"93859.8\",\n                \"closePrice\": \"94443.5\",\n                \"marginMode\": \"CROSS\",\n                \"tax\": \"0.0\",\n                \"roe\": null,\n                \"liquidAmount\": null,\n                \"liquidPrice\": null,\n                \"side\": \"LONG\"\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetPositionsHistoryResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetMaxWithdrawMarginReqModel(t *testing.T) {
	// GetMaxWithdrawMargin
	// Get Max Withdraw Margin
	// /api/v1/margin/maxWithdrawMargin

	data := "{\"symbol\": \"example_string_default_value\"}"
	req := &GetMaxWithdrawMarginReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetMaxWithdrawMarginRespModel(t *testing.T) {
	// GetMaxWithdrawMargin
	// Get Max Withdraw Margin
	// /api/v1/margin/maxWithdrawMargin

	data := "{\n    \"code\": \"200000\",\n    \"data\": \"21.1135719252\"\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetMaxWithdrawMarginResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetCrossMarginLeverageReqModel(t *testing.T) {
	// GetCrossMarginLeverage
	// Get Cross Margin Leverage
	// /api/v2/getCrossUserLeverage

	data := "{\"symbol\": \"XBTUSDTM\"}"
	req := &GetCrossMarginLeverageReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetCrossMarginLeverageRespModel(t *testing.T) {
	// GetCrossMarginLeverage
	// Get Cross Margin Leverage
	// /api/v2/getCrossUserLeverage

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"symbol\": \"XBTUSDTM\",\n        \"leverage\": \"3\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetCrossMarginLeverageResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsModifyMarginLeverageReqModel(t *testing.T) {
	// ModifyMarginLeverage
	// Modify Cross Margin Leverage
	// /api/v2/changeCrossUserLeverage

	data := "{\"symbol\": \"XBTUSDTM\", \"leverage\": \"10\"}"
	req := &ModifyMarginLeverageReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsModifyMarginLeverageRespModel(t *testing.T) {
	// ModifyMarginLeverage
	// Modify Cross Margin Leverage
	// /api/v2/changeCrossUserLeverage

	data := "{\n    \"code\": \"200000\",\n    \"data\": true\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &ModifyMarginLeverageResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsAddIsolatedMarginReqModel(t *testing.T) {
	// AddIsolatedMargin
	// Add Isolated Margin
	// /api/v1/position/margin/deposit-margin

	data := "{\"symbol\": \"string\", \"margin\": 0, \"bizNo\": \"string\"}"
	req := &AddIsolatedMarginReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsAddIsolatedMarginRespModel(t *testing.T) {
	// AddIsolatedMargin
	// Add Isolated Margin
	// /api/v1/position/margin/deposit-margin

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"id\": \"6200c9b83aecfb000152ddcd\",\n        \"symbol\": \"XBTUSDTM\",\n        \"autoDeposit\": false,\n        \"maintMarginReq\": 0.005,\n        \"riskLimit\": 500000,\n        \"realLeverage\": 18.72,\n        \"crossMode\": false,\n        \"delevPercentage\": 0.66,\n        \"openingTimestamp\": 1646287090131,\n        \"currentTimestamp\": 1646295055021,\n        \"currentQty\": 1,\n        \"currentCost\": 43.388,\n        \"currentComm\": 0.0260328,\n        \"unrealisedCost\": 43.388,\n        \"realisedGrossCost\": 0,\n        \"realisedCost\": 0.0260328,\n        \"isOpen\": true,\n        \"markPrice\": 43536.65,\n        \"markValue\": 43.53665,\n        \"posCost\": 43.388,\n        \"posCross\": 0.000024985,\n        \"posInit\": 2.1694,\n        \"posComm\": 0.02733446,\n        \"posLoss\": 0,\n        \"posMargin\": 2.19675944,\n        \"posMaint\": 0.24861326,\n        \"maintMargin\": 2.34540944,\n        \"realisedGrossPnl\": 0,\n        \"realisedPnl\": -0.0260328,\n        \"unrealisedPnl\": 0.14865,\n        \"unrealisedPnlPcnt\": 0.0034,\n        \"unrealisedRoePcnt\": 0.0685,\n        \"avgEntryPrice\": 43388,\n        \"liquidationPrice\": 41440,\n        \"bankruptPrice\": 41218,\n        \"userId\": 1234321123,\n        \"settleCurrency\": \"USDT\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &AddIsolatedMarginResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsRemoveIsolatedMarginReqModel(t *testing.T) {
	// RemoveIsolatedMargin
	// Remove Isolated Margin
	// /api/v1/margin/withdrawMargin

	data := "{\"symbol\": \"XBTUSDTM\", \"withdrawAmount\": \"0.0000001\"}"
	req := &RemoveIsolatedMarginReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsRemoveIsolatedMarginRespModel(t *testing.T) {
	// RemoveIsolatedMargin
	// Remove Isolated Margin
	// /api/v1/margin/withdrawMargin

	data := "{\n    \"code\": \"200000\",\n    \"data\": \"0.1\"\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &RemoveIsolatedMarginResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetCrossMarginRiskLimitReqModel(t *testing.T) {
	// GetCrossMarginRiskLimit
	// Get Cross Margin Risk Limit
	// /api/v2/batchGetCrossOrderLimit

	data := "{\"symbol\": \"XBTUSDTM\", \"totalMargin\": \"example_string_default_value\", \"leverage\": 123456}"
	req := &GetCrossMarginRiskLimitReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetCrossMarginRiskLimitRespModel(t *testing.T) {
	// GetCrossMarginRiskLimit
	// Get Cross Margin Risk Limit
	// /api/v2/batchGetCrossOrderLimit

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"maxOpenSize\": 12102,\n            \"maxOpenValue\": \"1234549.2240000000\",\n            \"totalMargin\": \"10000\",\n            \"price\": \"102012\",\n            \"leverage\": \"125.00\",\n            \"mmr\": \"0.00416136\",\n            \"imr\": \"0.008\",\n            \"currency\": \"USDT\"\n        },\n        {\n            \"symbol\": \"ETHUSDTM\",\n            \"maxOpenSize\": 38003,\n            \"maxOpenValue\": \"971508.6920000000\",\n            \"totalMargin\": \"10000\",\n            \"price\": \"2556.4\",\n            \"leverage\": \"100.00\",\n            \"mmr\": \"0.0054623236\",\n            \"imr\": \"0.01\",\n            \"currency\": \"USDT\"\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetCrossMarginRiskLimitResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetIsolatedMarginRiskLimitReqModel(t *testing.T) {
	// GetIsolatedMarginRiskLimit
	// Get Isolated Margin Risk Limit
	// /api/v1/contracts/risk-limit/{symbol}

	data := "{\"symbol\": \"XBTUSDTM\"}"
	req := &GetIsolatedMarginRiskLimitReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsGetIsolatedMarginRiskLimitRespModel(t *testing.T) {
	// GetIsolatedMarginRiskLimit
	// Get Isolated Margin Risk Limit
	// /api/v1/contracts/risk-limit/{symbol}

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 1,\n            \"maxRiskLimit\": 100000,\n            \"minRiskLimit\": 0,\n            \"maxLeverage\": 125,\n            \"initialMargin\": 0.008,\n            \"maintainMargin\": 0.004\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 2,\n            \"maxRiskLimit\": 500000,\n            \"minRiskLimit\": 100000,\n            \"maxLeverage\": 100,\n            \"initialMargin\": 0.01,\n            \"maintainMargin\": 0.005\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 3,\n            \"maxRiskLimit\": 1000000,\n            \"minRiskLimit\": 500000,\n            \"maxLeverage\": 75,\n            \"initialMargin\": 0.014,\n            \"maintainMargin\": 0.007\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 4,\n            \"maxRiskLimit\": 2000000,\n            \"minRiskLimit\": 1000000,\n            \"maxLeverage\": 50,\n            \"initialMargin\": 0.02,\n            \"maintainMargin\": 0.01\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 5,\n            \"maxRiskLimit\": 3000000,\n            \"minRiskLimit\": 2000000,\n            \"maxLeverage\": 30,\n            \"initialMargin\": 0.034,\n            \"maintainMargin\": 0.017\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 6,\n            \"maxRiskLimit\": 5000000,\n            \"minRiskLimit\": 3000000,\n            \"maxLeverage\": 20,\n            \"initialMargin\": 0.05,\n            \"maintainMargin\": 0.025\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 7,\n            \"maxRiskLimit\": 8000000,\n            \"minRiskLimit\": 5000000,\n            \"maxLeverage\": 10,\n            \"initialMargin\": 0.1,\n            \"maintainMargin\": 0.05\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 8,\n            \"maxRiskLimit\": 12000000,\n            \"minRiskLimit\": 8000000,\n            \"maxLeverage\": 5,\n            \"initialMargin\": 0.2,\n            \"maintainMargin\": 0.1\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 9,\n            \"maxRiskLimit\": 20000000,\n            \"minRiskLimit\": 12000000,\n            \"maxLeverage\": 4,\n            \"initialMargin\": 0.25,\n            \"maintainMargin\": 0.125\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 10,\n            \"maxRiskLimit\": 30000000,\n            \"minRiskLimit\": 20000000,\n            \"maxLeverage\": 3,\n            \"initialMargin\": 0.334,\n            \"maintainMargin\": 0.167\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 11,\n            \"maxRiskLimit\": 40000000,\n            \"minRiskLimit\": 30000000,\n            \"maxLeverage\": 2,\n            \"initialMargin\": 0.5,\n            \"maintainMargin\": 0.25\n        },\n        {\n            \"symbol\": \"XBTUSDTM\",\n            \"level\": 12,\n            \"maxRiskLimit\": 50000000,\n            \"minRiskLimit\": 40000000,\n            \"maxLeverage\": 1,\n            \"initialMargin\": 1.0,\n            \"maintainMargin\": 0.5\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetIsolatedMarginRiskLimitResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsModifyIsolatedMarginRiskLimtReqModel(t *testing.T) {
	// ModifyIsolatedMarginRiskLimt
	// Modify Isolated Margin Risk Limit
	// /api/v1/position/risk-limit-level/change

	data := "{\"symbol\": \"XBTUSDTM\", \"level\": 2}"
	req := &ModifyIsolatedMarginRiskLimtReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsModifyIsolatedMarginRiskLimtRespModel(t *testing.T) {
	// ModifyIsolatedMarginRiskLimt
	// Modify Isolated Margin Risk Limit
	// /api/v1/position/risk-limit-level/change

	data := "{\n    \"code\": \"200000\",\n    \"data\": true\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &ModifyIsolatedMarginRiskLimtResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestPositionsModifyAutoDepositStatusReqModel(t *testing.T) {
	// ModifyAutoDepositStatus
	// Modify Isolated Margin Auto-Deposit Status
	// /api/v1/position/margin/auto-deposit-status

	data := "{\"symbol\": \"XBTUSDTM\", \"status\": true}"
	req := &ModifyAutoDepositStatusReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestPositionsModifyAutoDepositStatusRespModel(t *testing.T) {
	// ModifyAutoDepositStatus
	// Modify Isolated Margin Auto-Deposit Status
	// /api/v1/position/margin/auto-deposit-status

	data := "{\n    \"code\": \"200000\",\n    \"data\": true\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &ModifyAutoDepositStatusResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}
