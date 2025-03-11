package viplending

import (
	"encoding/json"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestVIPLendingGetDiscountRateConfigsReqModel(t *testing.T) {
	// GetDiscountRateConfigs
	// Get Discount Rate Configs
	// /api/v1/otc-loan/discount-rate-configs

}

func TestVIPLendingGetDiscountRateConfigsRespModel(t *testing.T) {
	// GetDiscountRateConfigs
	// Get Discount Rate Configs
	// /api/v1/otc-loan/discount-rate-configs

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"currency\": \"BTC\",\n            \"usdtLevels\": [\n                {\n                    \"left\": 0,\n                    \"right\": 20000000,\n                    \"discountRate\": \"1.00000000\"\n                },\n                {\n                    \"left\": 20000000,\n                    \"right\": 50000000,\n                    \"discountRate\": \"0.95000000\"\n                },\n                {\n                    \"left\": 50000000,\n                    \"right\": 100000000,\n                    \"discountRate\": \"0.90000000\"\n                },\n                {\n                    \"left\": 100000000,\n                    \"right\": 300000000,\n                    \"discountRate\": \"0.50000000\"\n                },\n                {\n                    \"left\": 300000000,\n                    \"right\": 99999999999,\n                    \"discountRate\": \"0.00000000\"\n                }\n            ]\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetDiscountRateConfigsResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestVIPLendingGetLoanInfoReqModel(t *testing.T) {
	// GetLoanInfo
	// Get Loan Info
	// /api/v1/otc-loan/loan

}

func TestVIPLendingGetLoanInfoRespModel(t *testing.T) {
	// GetLoanInfo
	// Get Loan Info
	// /api/v1/otc-loan/loan

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"parentUid\": \"1260004199\",\n        \"orders\": [{\n            \"orderId\": \"671a2be815f4140007a588e1\",\n            \"principal\": \"100\",\n            \"interest\": \"0\",\n            \"currency\": \"USDT\"\n        }],\n        \"ltv\": {\n            \"transferLtv\": \"0.6000\",\n            \"onlyClosePosLtv\": \"0.7500\",\n            \"delayedLiquidationLtv\": \"0.7500\",\n            \"instantLiquidationLtv\": \"0.8000\",\n            \"currentLtv\": \"0.1111\"\n        },\n        \"totalMarginAmount\": \"900.00000000\",\n        \"transferMarginAmount\": \"166.66666666\",\n        \"margins\": [{\n            \"marginCcy\": \"USDT\",\n            \"marginQty\": \"1000.00000000\",\n            \"marginFactor\": \"0.9000000000\"\n        }]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetLoanInfoResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestVIPLendingGetAccountsReqModel(t *testing.T) {
	// GetAccounts
	// Get Accounts
	// /api/v1/otc-loan/accounts

}

func TestVIPLendingGetAccountsRespModel(t *testing.T) {
	// GetAccounts
	// Get Accounts
	// /api/v1/otc-loan/accounts

	data := "\n{\n    \"code\": \"200000\",\n    \"data\": [{\n        \"uid\": \"1260004199\",\n        \"marginCcy\": \"USDT\",\n        \"marginQty\": \"900\",\n        \"marginFactor\": \"0.9000000000\",\n        \"accountType\": \"TRADE\",\n        \"isParent\": true\n    }]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetAccountsResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}
