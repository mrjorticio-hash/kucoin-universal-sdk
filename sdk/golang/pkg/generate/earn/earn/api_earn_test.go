package earn

import (
	"encoding/json"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestEarnPurchaseReqModel(t *testing.T) {
	// Purchase
	// Purchase
	// /api/v1/earn/orders

	data := "{\"productId\": \"2611\", \"amount\": \"1\", \"accountType\": \"TRADE\"}"
	req := &PurchaseReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestEarnPurchaseRespModel(t *testing.T) {
	// Purchase
	// Purchase
	// /api/v1/earn/orders

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"2767291\",\n        \"orderTxId\": \"6603694\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &PurchaseResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetRedeemPreviewReqModel(t *testing.T) {
	// GetRedeemPreview
	// Get Redeem Preview
	// /api/v1/earn/redeem-preview

	data := "{\"orderId\": \"2767291\", \"fromAccountType\": \"MAIN\"}"
	req := &GetRedeemPreviewReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetRedeemPreviewRespModel(t *testing.T) {
	// GetRedeemPreview
	// Get Redeem Preview
	// /api/v1/earn/redeem-preview

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currency\": \"KCS\",\n        \"redeemAmount\": \"1\",\n        \"penaltyInterestAmount\": \"0\",\n        \"redeemPeriod\": 3,\n        \"deliverTime\": 1729518951000,\n        \"manualRedeemable\": true,\n        \"redeemAll\": false\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetRedeemPreviewResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestEarnRedeemReqModel(t *testing.T) {
	// Redeem
	// Redeem
	// /api/v1/earn/orders

	data := "{\"orderId\": \"2767291\", \"amount\": \"example_string_default_value\", \"fromAccountType\": \"MAIN\", \"confirmPunishRedeem\": \"1\"}"
	req := &RedeemReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestEarnRedeemRespModel(t *testing.T) {
	// Redeem
	// Redeem
	// /api/v1/earn/orders

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderTxId\": \"6603700\",\n        \"deliverTime\": 1729517805000,\n        \"status\": \"PENDING\",\n        \"amount\": \"1\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &RedeemResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetSavingsProductsReqModel(t *testing.T) {
	// GetSavingsProducts
	// Get Savings Products
	// /api/v1/earn/saving/products

	data := "{\"currency\": \"BTC\"}"
	req := &GetSavingsProductsReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetSavingsProductsRespModel(t *testing.T) {
	// GetSavingsProducts
	// Get Savings Products
	// /api/v1/earn/saving/products

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"2172\",\n            \"currency\": \"BTC\",\n            \"category\": \"DEMAND\",\n            \"type\": \"DEMAND\",\n            \"precision\": 8,\n            \"productUpperLimit\": \"480\",\n            \"productRemainAmount\": \"132.36153083\",\n            \"userUpperLimit\": \"20\",\n            \"userLowerLimit\": \"0.01\",\n            \"redeemPeriod\": 0,\n            \"lockStartTime\": 1644807600000,\n            \"lockEndTime\": null,\n            \"applyStartTime\": 1644807600000,\n            \"applyEndTime\": null,\n            \"returnRate\": \"0.00047208\",\n            \"incomeCurrency\": \"BTC\",\n            \"earlyRedeemSupported\": 0,\n            \"status\": \"ONGOING\",\n            \"redeemType\": \"MANUAL\",\n            \"incomeReleaseType\": \"DAILY\",\n            \"interestDate\": 1729267200000,\n            \"duration\": 0,\n            \"newUserOnly\": 0\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetSavingsProductsResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetPromotionProductsReqModel(t *testing.T) {
	// GetPromotionProducts
	// Get Promotion Products
	// /api/v1/earn/promotion/products

	data := "{\"currency\": \"BTC\"}"
	req := &GetPromotionProductsReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetPromotionProductsRespModel(t *testing.T) {
	// GetPromotionProducts
	// Get Promotion Products
	// /api/v1/earn/promotion/products

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"2685\",\n            \"currency\": \"BTC\",\n            \"category\": \"ACTIVITY\",\n            \"type\": \"TIME\",\n            \"precision\": 8,\n            \"productUpperLimit\": \"50\",\n            \"userUpperLimit\": \"1\",\n            \"userLowerLimit\": \"0.001\",\n            \"redeemPeriod\": 0,\n            \"lockStartTime\": 1702371601000,\n            \"lockEndTime\": 1729858405000,\n            \"applyStartTime\": 1702371600000,\n            \"applyEndTime\": null,\n            \"returnRate\": \"0.03\",\n            \"incomeCurrency\": \"BTC\",\n            \"earlyRedeemSupported\": 0,\n            \"productRemainAmount\": \"49.78203998\",\n            \"status\": \"ONGOING\",\n            \"redeemType\": \"TRANS_DEMAND\",\n            \"incomeReleaseType\": \"DAILY\",\n            \"interestDate\": 1729253605000,\n            \"duration\": 7,\n            \"newUserOnly\": 1\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetPromotionProductsResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetStakingProductsReqModel(t *testing.T) {
	// GetStakingProducts
	// Get Staking Products
	// /api/v1/earn/staking/products

	data := "{\"currency\": \"BTC\"}"
	req := &GetStakingProductsReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetStakingProductsRespModel(t *testing.T) {
	// GetStakingProducts
	// Get Staking Products
	// /api/v1/earn/staking/products

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"2535\",\n            \"currency\": \"STX\",\n            \"category\": \"STAKING\",\n            \"type\": \"DEMAND\",\n            \"precision\": 8,\n            \"productUpperLimit\": \"1000000\",\n            \"userUpperLimit\": \"10000\",\n            \"userLowerLimit\": \"1\",\n            \"redeemPeriod\": 14,\n            \"lockStartTime\": 1688614514000,\n            \"lockEndTime\": null,\n            \"applyStartTime\": 1688614512000,\n            \"applyEndTime\": null,\n            \"returnRate\": \"0.045\",\n            \"incomeCurrency\": \"BTC\",\n            \"earlyRedeemSupported\": 0,\n            \"productRemainAmount\": \"254032.90178701\",\n            \"status\": \"ONGOING\",\n            \"redeemType\": \"MANUAL\",\n            \"incomeReleaseType\": \"DAILY\",\n            \"interestDate\": 1729267200000,\n            \"duration\": 0,\n            \"newUserOnly\": 0\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetStakingProductsResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetKcsStakingProductsReqModel(t *testing.T) {
	// GetKcsStakingProducts
	// Get KCS Staking Products
	// /api/v1/earn/kcs-staking/products

	data := "{\"currency\": \"BTC\"}"
	req := &GetKcsStakingProductsReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetKcsStakingProductsRespModel(t *testing.T) {
	// GetKcsStakingProducts
	// Get KCS Staking Products
	// /api/v1/earn/kcs-staking/products

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"2611\",\n            \"currency\": \"KCS\",\n            \"category\": \"KCS_STAKING\",\n            \"type\": \"DEMAND\",\n            \"precision\": 8,\n            \"productUpperLimit\": \"100000000\",\n            \"userUpperLimit\": \"100000000\",\n            \"userLowerLimit\": \"1\",\n            \"redeemPeriod\": 3,\n            \"lockStartTime\": 1701252000000,\n            \"lockEndTime\": null,\n            \"applyStartTime\": 1701252000000,\n            \"applyEndTime\": null,\n            \"returnRate\": \"0.03471727\",\n            \"incomeCurrency\": \"KCS\",\n            \"earlyRedeemSupported\": 0,\n            \"productRemainAmount\": \"58065850.54998251\",\n            \"status\": \"ONGOING\",\n            \"redeemType\": \"MANUAL\",\n            \"incomeReleaseType\": \"DAILY\",\n            \"interestDate\": 1729267200000,\n            \"duration\": 0,\n            \"newUserOnly\": 0\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetKcsStakingProductsResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetETHStakingProductsReqModel(t *testing.T) {
	// GetETHStakingProducts
	// Get ETH Staking Products
	// /api/v1/earn/eth-staking/products

	data := "{\"currency\": \"BTC\"}"
	req := &GetETHStakingProductsReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetETHStakingProductsRespModel(t *testing.T) {
	// GetETHStakingProducts
	// Get ETH Staking Products
	// /api/v1/earn/eth-staking/products

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"id\": \"ETH2\",\n            \"category\": \"ETH2\",\n            \"type\": \"DEMAND\",\n            \"precision\": 8,\n            \"currency\": \"ETH\",\n            \"incomeCurrency\": \"ETH2\",\n            \"returnRate\": \"0.028\",\n            \"userLowerLimit\": \"0.01\",\n            \"userUpperLimit\": \"8557.3597075\",\n            \"productUpperLimit\": \"8557.3597075\",\n            \"productRemainAmount\": \"8557.3597075\",\n            \"redeemPeriod\": 5,\n            \"redeemType\": \"MANUAL\",\n            \"incomeReleaseType\": \"DAILY\",\n            \"applyStartTime\": 1729255485000,\n            \"applyEndTime\": null,\n            \"lockStartTime\": 1729255485000,\n            \"lockEndTime\": null,\n            \"interestDate\": 1729267200000,\n            \"newUserOnly\": 0,\n            \"earlyRedeemSupported\": 0,\n            \"duration\": 0,\n            \"status\": \"ONGOING\"\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetETHStakingProductsResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetAccountHoldingReqModel(t *testing.T) {
	// GetAccountHolding
	// Get Account Holding
	// /api/v1/earn/hold-assets

	data := "{\"currency\": \"KCS\", \"productId\": \"example_string_default_value\", \"productCategory\": \"DEMAND\", \"currentPage\": 1, \"pageSize\": 10}"
	req := &GetAccountHoldingReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestEarnGetAccountHoldingRespModel(t *testing.T) {
	// GetAccountHolding
	// Get Account Holding
	// /api/v1/earn/hold-assets

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"currentPage\": 1,\n        \"pageSize\": 15,\n        \"items\": [\n            {\n                \"orderId\": \"2767291\",\n                \"productId\": \"2611\",\n                \"productCategory\": \"KCS_STAKING\",\n                \"productType\": \"DEMAND\",\n                \"currency\": \"KCS\",\n                \"incomeCurrency\": \"KCS\",\n                \"returnRate\": \"0.03471727\",\n                \"holdAmount\": \"1\",\n                \"redeemedAmount\": \"0\",\n                \"redeemingAmount\": \"1\",\n                \"lockStartTime\": 1701252000000,\n                \"lockEndTime\": null,\n                \"purchaseTime\": 1729257513000,\n                \"redeemPeriod\": 3,\n                \"status\": \"REDEEMING\",\n                \"earlyRedeemSupported\": 0\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetAccountHoldingResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}
