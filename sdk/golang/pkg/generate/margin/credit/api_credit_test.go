package credit

import (
	"encoding/json"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestCreditGetLoanMarketReqModel(t *testing.T) {
	// GetLoanMarket
	// Get Loan Market
	// /api/v3/project/list

	data := "{\"currency\": \"BTC\"}"
	req := &GetLoanMarketReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestCreditGetLoanMarketRespModel(t *testing.T) {
	// GetLoanMarket
	// Get Loan Market
	// /api/v3/project/list

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"currency\": \"BTC\",\n            \"purchaseEnable\": true,\n            \"redeemEnable\": true,\n            \"increment\": \"0.00000001\",\n            \"minPurchaseSize\": \"0.001\",\n            \"maxPurchaseSize\": \"40\",\n            \"interestIncrement\": \"0.0001\",\n            \"minInterestRate\": \"0.005\",\n            \"marketInterestRate\": \"0.005\",\n            \"maxInterestRate\": \"0.32\",\n            \"autoPurchaseEnable\": false\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetLoanMarketResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestCreditGetLoanMarketInterestRateReqModel(t *testing.T) {
	// GetLoanMarketInterestRate
	// Get Loan Market Interest Rate
	// /api/v3/project/marketInterestRate

	data := "{\"currency\": \"BTC\"}"
	req := &GetLoanMarketInterestRateReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestCreditGetLoanMarketInterestRateRespModel(t *testing.T) {
	// GetLoanMarketInterestRate
	// Get Loan Market Interest Rate
	// /api/v3/project/marketInterestRate

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"time\": \"202410170000\",\n            \"marketInterestRate\": \"0.005\"\n        },\n        {\n            \"time\": \"202410170100\",\n            \"marketInterestRate\": \"0.005\"\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetLoanMarketInterestRateResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestCreditPurchaseReqModel(t *testing.T) {
	// Purchase
	// Purchase
	// /api/v3/purchase

	data := "{\"currency\": \"BTC\", \"size\": \"0.001\", \"interestRate\": \"0.1\"}"
	req := &PurchaseReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestCreditPurchaseRespModel(t *testing.T) {
	// Purchase
	// Purchase
	// /api/v3/purchase

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderNo\": \"671bafa804c26d000773c533\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &PurchaseResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestCreditModifyPurchaseReqModel(t *testing.T) {
	// ModifyPurchase
	// Modify Purchase
	// /api/v3/lend/purchase/update

	data := "{\"currency\": \"BTC\", \"purchaseOrderNo\": \"671bafa804c26d000773c533\", \"interestRate\": \"0.09\"}"
	req := &ModifyPurchaseReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestCreditModifyPurchaseRespModel(t *testing.T) {
	// ModifyPurchase
	// Modify Purchase
	// /api/v3/lend/purchase/update

	data := "{\n    \"code\": \"200000\",\n    \"data\": null\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &ModifyPurchaseResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestCreditGetPurchaseOrdersReqModel(t *testing.T) {
	// GetPurchaseOrders
	// Get Purchase Orders
	// /api/v3/purchase/orders

	data := "{\"status\": \"DONE\", \"currency\": \"BTC\", \"purchaseOrderNo\": \"example_string_default_value\", \"currentPage\": 1, \"pageSize\": 50}"
	req := &GetPurchaseOrdersReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestCreditGetPurchaseOrdersRespModel(t *testing.T) {
	// GetPurchaseOrders
	// Get Purchase Orders
	// /api/v3/purchase/orders

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 10,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"currency\": \"BTC\",\n                \"purchaseOrderNo\": \"671bb15a3b3f930007880bae\",\n                \"purchaseSize\": \"0.001\",\n                \"matchSize\": \"0\",\n                \"interestRate\": \"0.1\",\n                \"incomeSize\": \"0\",\n                \"applyTime\": 1729868122172,\n                \"status\": \"PENDING\"\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetPurchaseOrdersResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestCreditRedeemReqModel(t *testing.T) {
	// Redeem
	// Redeem
	// /api/v3/redeem

	data := "{\"currency\": \"BTC\", \"size\": \"0.001\", \"purchaseOrderNo\": \"671bafa804c26d000773c533\"}"
	req := &RedeemReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestCreditRedeemRespModel(t *testing.T) {
	// Redeem
	// Redeem
	// /api/v3/redeem

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderNo\": \"671bafa804c26d000773c533\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &RedeemResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestCreditGetRedeemOrdersReqModel(t *testing.T) {
	// GetRedeemOrders
	// Get Redeem Orders
	// /api/v3/redeem/orders

	data := "{\"status\": \"DONE\", \"currency\": \"BTC\", \"redeemOrderNo\": \"example_string_default_value\", \"currentPage\": 1, \"pageSize\": 50}"
	req := &GetRedeemOrdersReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestCreditGetRedeemOrdersRespModel(t *testing.T) {
	// GetRedeemOrders
	// Get Redeem Orders
	// /api/v3/redeem/orders

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 10,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"currency\": \"BTC\",\n                \"purchaseOrderNo\": \"671bafa804c26d000773c533\",\n                \"redeemOrderNo\": \"671bb01004c26d000773c55c\",\n                \"redeemSize\": \"0.001\",\n                \"receiptSize\": \"0.001\",\n                \"applyTime\": null,\n                \"status\": \"DONE\"\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetRedeemOrdersResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}
