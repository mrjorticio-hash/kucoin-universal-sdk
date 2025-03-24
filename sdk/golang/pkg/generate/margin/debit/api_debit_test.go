package debit

import (
	"encoding/json"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestDebitBorrowReqModel(t *testing.T) {
	// Borrow
	// Borrow
	// /api/v3/margin/borrow

	data := "{\"currency\": \"USDT\", \"size\": 10, \"timeInForce\": \"FOK\", \"isIsolated\": false, \"isHf\": false}"
	req := &BorrowReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDebitBorrowRespModel(t *testing.T) {
	// Borrow
	// Borrow
	// /api/v3/margin/borrow

	data := "{\"code\":\"200000\",\"data\":{\"orderNo\":\"67187162c0d6990007717b15\",\"actualSize\":\"10\"}}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &BorrowResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestDebitGetBorrowHistoryReqModel(t *testing.T) {
	// GetBorrowHistory
	// Get Borrow History
	// /api/v3/margin/borrow

	data := "{\"currency\": \"BTC\", \"isIsolated\": true, \"symbol\": \"BTC-USDT\", \"orderNo\": \"example_string_default_value\", \"startTime\": 123456, \"endTime\": 123456, \"currentPage\": 1, \"pageSize\": 50}"
	req := &GetBorrowHistoryReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDebitGetBorrowHistoryRespModel(t *testing.T) {
	// GetBorrowHistory
	// Get Borrow History
	// /api/v3/margin/borrow

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"timestamp\": 1729657580449,\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 2,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"orderNo\": \"67187162c0d6990007717b15\",\n                \"symbol\": null,\n                \"currency\": \"USDT\",\n                \"size\": \"10\",\n                \"actualSize\": \"10\",\n                \"status\": \"SUCCESS\",\n                \"createdTime\": 1729655138000\n            },\n            {\n                \"orderNo\": \"67187155b088e70007149585\",\n                \"symbol\": null,\n                \"currency\": \"USDT\",\n                \"size\": \"0.1\",\n                \"actualSize\": \"0\",\n                \"status\": \"FAILED\",\n                \"createdTime\": 1729655125000\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetBorrowHistoryResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestDebitRepayReqModel(t *testing.T) {
	// Repay
	// Repay
	// /api/v3/margin/repay

	data := "{\"currency\": \"USDT\", \"size\": 10}"
	req := &RepayReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDebitRepayRespModel(t *testing.T) {
	// Repay
	// Repay
	// /api/v3/margin/repay

	data := "{\"code\":\"200000\",\"data\":{\"timestamp\":1729655606816,\"orderNo\":\"671873361d5bd400075096ad\",\"actualSize\":\"10\"}}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &RepayResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestDebitGetRepayHistoryReqModel(t *testing.T) {
	// GetRepayHistory
	// Get Repay History
	// /api/v3/margin/repay

	data := "{\"currency\": \"BTC\", \"isIsolated\": true, \"symbol\": \"BTC-USDT\", \"orderNo\": \"example_string_default_value\", \"startTime\": 123456, \"endTime\": 123456, \"currentPage\": 1, \"pageSize\": 50}"
	req := &GetRepayHistoryReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDebitGetRepayHistoryRespModel(t *testing.T) {
	// GetRepayHistory
	// Get Repay History
	// /api/v3/margin/repay

	data := "{\"code\":\"200000\",\"data\":{\"timestamp\":1729663471891,\"currentPage\":1,\"pageSize\":50,\"totalNum\":1,\"totalPage\":1,\"items\":[{\"orderNo\":\"671873361d5bd400075096ad\",\"symbol\":null,\"currency\":\"USDT\",\"size\":\"10\",\"principal\":\"9.99986518\",\"interest\":\"0.00013482\",\"status\":\"SUCCESS\",\"createdTime\":1729655606000}]}}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetRepayHistoryResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestDebitGetInterestHistoryReqModel(t *testing.T) {
	// GetInterestHistory
	// Get Interest History.
	// /api/v3/margin/interest

	data := "{\"currency\": \"BTC\", \"isIsolated\": true, \"symbol\": \"BTC-USDT\", \"startTime\": 123456, \"endTime\": 123456, \"currentPage\": 1, \"pageSize\": 50}"
	req := &GetInterestHistoryReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDebitGetInterestHistoryRespModel(t *testing.T) {
	// GetInterestHistory
	// Get Interest History.
	// /api/v3/margin/interest

	data := "{\"code\":\"200000\",\"data\":{\"timestamp\":1729665170701,\"currentPage\":1,\"pageSize\":50,\"totalNum\":3,\"totalPage\":1,\"items\":[{\"currency\":\"USDT\",\"dayRatio\":\"0.000296\",\"interestAmount\":\"0.00000001\",\"createdTime\":1729663213375},{\"currency\":\"USDT\",\"dayRatio\":\"0.000296\",\"interestAmount\":\"0.00000001\",\"createdTime\":1729659618802},{\"currency\":\"USDT\",\"dayRatio\":\"0.000296\",\"interestAmount\":\"0.00000001\",\"createdTime\":1729656028077}]}}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetInterestHistoryResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestDebitModifyLeverageReqModel(t *testing.T) {
	// ModifyLeverage
	// Modify Leverage
	// /api/v3/position/update-user-leverage

	data := "{\"leverage\": \"5\"}"
	req := &ModifyLeverageReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDebitModifyLeverageRespModel(t *testing.T) {
	// ModifyLeverage
	// Modify Leverage
	// /api/v3/position/update-user-leverage

	data := "{\"code\":\"200000\",\"data\":null}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &ModifyLeverageResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}
