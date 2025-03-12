package futures

import (
	"encoding/json"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestFuturesAddOrderReqModel(t *testing.T) {
	// AddOrder
	// Add Order
	// /api/v1/copy-trade/futures/orders

	data := "{\"clientOid\": \"5c52e11203aa677f33e493fb\", \"side\": \"buy\", \"symbol\": \"XBTUSDTM\", \"leverage\": 3, \"type\": \"limit\", \"remark\": \"order remarks\", \"reduceOnly\": false, \"marginMode\": \"ISOLATED\", \"price\": \"0.1\", \"size\": 1, \"timeInForce\": \"GTC\"}"
	req := &AddOrderReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestFuturesAddOrderRespModel(t *testing.T) {
	// AddOrder
	// Add Order
	// /api/v1/copy-trade/futures/orders

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"263485113055133696\",\n        \"clientOid\": \"5c52e11203aa677f331e493fb\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &AddOrderResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestFuturesAddOrderTestReqModel(t *testing.T) {
	// AddOrderTest
	// Add Order Test
	// /api/v1/copy-trade/futures/orders/test

	data := "{\"clientOid\": \"5c52e11203aa677f33e493fb\", \"side\": \"buy\", \"symbol\": \"XBTUSDTM\", \"leverage\": 3, \"type\": \"limit\", \"remark\": \"order remarks\", \"reduceOnly\": false, \"marginMode\": \"ISOLATED\", \"price\": \"0.1\", \"size\": 1, \"timeInForce\": \"GTC\"}"
	req := &AddOrderTestReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestFuturesAddOrderTestRespModel(t *testing.T) {
	// AddOrderTest
	// Add Order Test
	// /api/v1/copy-trade/futures/orders/test

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"234125150956625920\",\n        \"clientOid\": \"5c52e11203aa677f33e493fb\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &AddOrderTestResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestFuturesAddTPSLOrderReqModel(t *testing.T) {
	// AddTPSLOrder
	// Add Take Profit And Stop Loss Order
	// /api/v1/copy-trade/futures/st-orders

	data := "{\"clientOid\": \"5c52e11203aa677f33e493fb\", \"side\": \"buy\", \"symbol\": \"XBTUSDTM\", \"leverage\": 3, \"type\": \"limit\", \"remark\": \"order remarks\", \"reduceOnly\": false, \"marginMode\": \"ISOLATED\", \"price\": \"0.2\", \"size\": 1, \"timeInForce\": \"GTC\", \"triggerStopUpPrice\": \"0.3\", \"triggerStopDownPrice\": \"0.1\", \"stopPriceType\": \"TP\"}"
	req := &AddTPSLOrderReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestFuturesAddTPSLOrderRespModel(t *testing.T) {
	// AddTPSLOrder
	// Add Take Profit And Stop Loss Order
	// /api/v1/copy-trade/futures/st-orders

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"234125150956625920\",\n        \"clientOid\": \"5c52e11203aa677f33e493fb\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &AddTPSLOrderResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestFuturesCancelOrderByIdReqModel(t *testing.T) {
	// CancelOrderById
	// Cancel Order By OrderId
	// /api/v1/copy-trade/futures/orders

	data := "{\"orderId\": \"263485113055133696\"}"
	req := &CancelOrderByIdReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestFuturesCancelOrderByIdRespModel(t *testing.T) {
	// CancelOrderById
	// Cancel Order By OrderId
	// /api/v1/copy-trade/futures/orders

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"cancelledOrderIds\": [\n            \"263485113055133696\"\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &CancelOrderByIdResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestFuturesCancelOrderByClientOidReqModel(t *testing.T) {
	// CancelOrderByClientOid
	// Cancel Order By ClientOid
	// /api/v1/copy-trade/futures/orders/client-order

	data := "{\"symbol\": \"XBTUSDTM\", \"clientOid\": \"5c52e11203aa677f331e493fb\"}"
	req := &CancelOrderByClientOidReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestFuturesCancelOrderByClientOidRespModel(t *testing.T) {
	// CancelOrderByClientOid
	// Cancel Order By ClientOid
	// /api/v1/copy-trade/futures/orders/client-order

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"clientOid\": \"5c52e11203aa677f331e4913fb\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &CancelOrderByClientOidResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestFuturesGetMaxOpenSizeReqModel(t *testing.T) {
	// GetMaxOpenSize
	// Get Max Open Size
	// /api/v1/copy-trade/futures/get-max-open-size

	data := "{\"symbol\": \"XBTUSDTM\", \"price\": 123456.0, \"leverage\": 123456}"
	req := &GetMaxOpenSizeReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestFuturesGetMaxOpenSizeRespModel(t *testing.T) {
	// GetMaxOpenSize
	// Get Max Open Size
	// /api/v1/copy-trade/futures/get-max-open-size

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"symbol\": \"XBTUSDTM\",\n        \"maxBuyOpenSize\": \"1000000\",\n        \"maxSellOpenSize\": \"51\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetMaxOpenSizeResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestFuturesGetMaxWithdrawMarginReqModel(t *testing.T) {
	// GetMaxWithdrawMargin
	// Get Max Withdraw Margin
	// /api/v1/copy-trade/futures/position/margin/max-withdraw-margin

	data := "{\"symbol\": \"example_string_default_value\"}"
	req := &GetMaxWithdrawMarginReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestFuturesGetMaxWithdrawMarginRespModel(t *testing.T) {
	// GetMaxWithdrawMargin
	// Get Max Withdraw Margin
	// /api/v1/copy-trade/futures/position/margin/max-withdraw-margin

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

func TestFuturesAddIsolatedMarginReqModel(t *testing.T) {
	// AddIsolatedMargin
	// Add Isolated Margin
	// /api/v1/copy-trade/futures/position/margin/deposit-margin

	data := "{\"symbol\": \"XBTUSDTM\", \"margin\": 3, \"bizNo\": \"112233\"}"
	req := &AddIsolatedMarginReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestFuturesAddIsolatedMarginRespModel(t *testing.T) {
	// AddIsolatedMargin
	// Add Isolated Margin
	// /api/v1/copy-trade/futures/position/margin/deposit-margin

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"id\": \"400000000000974886\",\n        \"symbol\": \"XBTUSDTM\",\n        \"autoDeposit\": true,\n        \"maintMarginReq\": \"0.004\",\n        \"riskLimit\": 100000,\n        \"realLeverage\": \"1.83\",\n        \"crossMode\": false,\n        \"marginMode\": \"\",\n        \"positionSide\": \"\",\n        \"leverage\": \"1.83\",\n        \"delevPercentage\": 0.2,\n        \"openingTimestamp\": 1736932881164,\n        \"currentTimestamp\": 1736933530230,\n        \"currentQty\": 1,\n        \"currentCost\": \"97.302\",\n        \"currentComm\": \"0.0583812\",\n        \"unrealisedCost\": \"97.302\",\n        \"realisedGrossCost\": \"0.0000000000\",\n        \"realisedCost\": \"0.0583812000\",\n        \"isOpen\": true,\n        \"markPrice\": \"96939.98\",\n        \"markValue\": \"96.9399800000\",\n        \"posCost\": \"97.302\",\n        \"posCross\": \"20.9874\",\n        \"posInit\": \"32.4339999967\",\n        \"posComm\": \"0.0904415999\",\n        \"posLoss\": \"0\",\n        \"posMargin\": \"53.5118415966\",\n        \"posMaint\": \"0.4796495999\",\n        \"maintMargin\": \"53.1498215966\",\n        \"realisedGrossPnl\": \"0.0000000000\",\n        \"realisedPnl\": \"-0.0583812000\",\n        \"unrealisedPnl\": \"-0.3620200000\",\n        \"unrealisedPnlPcnt\": \"-0.0037\",\n        \"unrealisedRoePcnt\": \"-0.0112\",\n        \"avgEntryPrice\": \"97302.00\",\n        \"liquidationPrice\": \"44269.81\",\n        \"bankruptPrice\": \"43880.61\",\n        \"settleCurrency\": \"USDT\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &AddIsolatedMarginResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestFuturesRemoveIsolatedMarginReqModel(t *testing.T) {
	// RemoveIsolatedMargin
	// Remove Isolated Margin
	// /api/v1/copy-trade/futures/position/margin/withdraw-margin

	data := "{\"symbol\": \"XBTUSDTM\", \"withdrawAmount\": 1e-07}"
	req := &RemoveIsolatedMarginReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestFuturesRemoveIsolatedMarginRespModel(t *testing.T) {
	// RemoveIsolatedMargin
	// Remove Isolated Margin
	// /api/v1/copy-trade/futures/position/margin/withdraw-margin

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

func TestFuturesModifyIsolatedMarginRiskLimtReqModel(t *testing.T) {
	// ModifyIsolatedMarginRiskLimt
	// Modify Isolated Margin Risk Limit
	// /api/v1/copy-trade/futures/position/risk-limit-level/change

	data := "{\"symbol\": \"XBTUSDTM\", \"level\": 1}"
	req := &ModifyIsolatedMarginRiskLimtReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestFuturesModifyIsolatedMarginRiskLimtRespModel(t *testing.T) {
	// ModifyIsolatedMarginRiskLimt
	// Modify Isolated Margin Risk Limit
	// /api/v1/copy-trade/futures/position/risk-limit-level/change

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

func TestFuturesModifyAutoDepositStatusReqModel(t *testing.T) {
	// ModifyAutoDepositStatus
	// Modify Isolated Margin Auto-Deposit Status
	// /api/v1/copy-trade/futures/position/margin/auto-deposit-status

	data := "{\"symbol\": \"XBTUSDTM\", \"status\": true}"
	req := &ModifyAutoDepositStatusReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestFuturesModifyAutoDepositStatusRespModel(t *testing.T) {
	// ModifyAutoDepositStatus
	// Modify Isolated Margin Auto-Deposit Status
	// /api/v1/copy-trade/futures/position/margin/auto-deposit-status

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
