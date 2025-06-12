package withdrawal

import (
	"encoding/json"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestWithdrawalGetWithdrawalQuotasReqModel(t *testing.T) {
	// GetWithdrawalQuotas
	// Get Withdrawal Quotas
	// /api/v1/withdrawals/quotas

	data := "{\"currency\": \"BTC\", \"chain\": \"eth\"}"
	req := &GetWithdrawalQuotasReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalGetWithdrawalQuotasRespModel(t *testing.T) {
	// GetWithdrawalQuotas
	// Get Withdrawal Quotas
	// /api/v1/withdrawals/quotas

	data := "{\"code\":\"200000\",\"data\":{\"currency\":\"BTC\",\"limitBTCAmount\":\"15.79590095\",\"usedBTCAmount\":\"0.00000000\",\"quotaCurrency\":\"USDT\",\"limitQuotaCurrencyAmount\":\"999999.00000000\",\"usedQuotaCurrencyAmount\":\"0\",\"remainAmount\":\"15.79590095\",\"availableAmount\":\"0\",\"withdrawMinFee\":\"0.0005\",\"innerWithdrawMinFee\":\"0\",\"withdrawMinSize\":\"0.001\",\"isWithdrawEnabled\":true,\"precision\":8,\"chain\":\"BTC\",\"reason\":null,\"lockedAmount\":\"0\"}}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetWithdrawalQuotasResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalWithdrawalV3ReqModel(t *testing.T) {
	// WithdrawalV3
	// Withdraw (V3)
	// /api/v3/withdrawals

	data := "{\"currency\": \"USDT\", \"toAddress\": \"TKFRQXSDcY****GmLrjJggwX8\", \"amount\": \"3\", \"withdrawType\": \"ADDRESS\", \"chain\": \"trx\", \"isInner\": true, \"remark\": \"this is Remark\"}"
	req := &WithdrawalV3Req{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalWithdrawalV3RespModel(t *testing.T) {
	// WithdrawalV3
	// Withdraw (V3)
	// /api/v3/withdrawals

	data := "{\"code\":\"200000\",\"data\":{\"withdrawalId\":\"670deec84d64da0007d7c946\"}}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &WithdrawalV3Resp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalCancelWithdrawalReqModel(t *testing.T) {
	// CancelWithdrawal
	// Cancel Withdrawal
	// /api/v1/withdrawals/{withdrawalId}

	data := "{\"withdrawalId\": \"670b891f7e0f440007730692\"}"
	req := &CancelWithdrawalReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalCancelWithdrawalRespModel(t *testing.T) {
	// CancelWithdrawal
	// Cancel Withdrawal
	// /api/v1/withdrawals/{withdrawalId}

	data := "{\"code\":\"200000\",\"data\":null}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &CancelWithdrawalResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalGetWithdrawalHistoryReqModel(t *testing.T) {
	// GetWithdrawalHistory
	// Get Withdrawal History
	// /api/v1/withdrawals

	data := "{\"currency\": \"BTC\", \"status\": \"SUCCESS\", \"startAt\": 1728663338000, \"endAt\": 1728692138000, \"currentPage\": 1, \"pageSize\": 50}"
	req := &GetWithdrawalHistoryReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalGetWithdrawalHistoryRespModel(t *testing.T) {
	// GetWithdrawalHistory
	// Get Withdrawal History
	// /api/v1/withdrawals

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 5,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"currency\": \"USDT\",\n                \"chain\": \"\",\n                \"status\": \"SUCCESS\",\n                \"address\": \"a435*****@gmail.com\",\n                \"memo\": \"\",\n                \"isInner\": true,\n                \"amount\": \"1.00000000\",\n                \"fee\": \"0.00000000\",\n                \"walletTxId\": null,\n                \"createdAt\": 1728555875000,\n                \"updatedAt\": 1728555875000,\n                \"remark\": \"\",\n                \"arrears\": false\n            },\n            {\n                \"currency\": \"USDT\",\n                \"chain\": \"trx\",\n                \"status\": \"SUCCESS\",\n                \"address\": \"TSv3L1fS7******X4nLP6rqNxYz\",\n                \"memo\": \"\",\n                \"isInner\": true,\n                \"amount\": \"6.00000000\",\n                \"fee\": \"0.00000000\",\n                \"walletTxId\": null,\n                \"createdAt\": 1721730920000,\n                \"updatedAt\": 1721730920000,\n                \"remark\": \"\",\n                \"arrears\": false\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetWithdrawalHistoryResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalGetWithdrawalHistoryByIdReqModel(t *testing.T) {
	// GetWithdrawalHistoryById
	// Get Withdrawal History By ID
	// /api/v1/withdrawals/{withdrawalId}

	data := "{\"withdrawalId\": \"67e6515f7960ba0007b42025\"}"
	req := &GetWithdrawalHistoryByIdReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalGetWithdrawalHistoryByIdRespModel(t *testing.T) {
	// GetWithdrawalHistoryById
	// Get Withdrawal History By ID
	// /api/v1/withdrawals/{withdrawalId}

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"id\": \"67e6515f7960ba0007b42025\",\n        \"uid\": 165111215,\n        \"currency\": \"USDT\",\n        \"chainId\": \"trx\",\n        \"chainName\": \"TRC20\",\n        \"currencyName\": \"USDT\",\n        \"status\": \"SUCCESS\",\n        \"failureReason\": \"\",\n        \"failureReasonMsg\": null,\n        \"address\": \"TKFRQXSDcY4kd3QLzw7uK16GmLrjJggwX8\",\n        \"memo\": \"\",\n        \"isInner\": true,\n        \"amount\": \"3.00000000\",\n        \"fee\": \"0.00000000\",\n        \"walletTxId\": null,\n        \"addressRemark\": null,\n        \"remark\": \"this is Remark\",\n        \"createdAt\": 1743147359000,\n        \"cancelType\": \"NON_CANCELABLE\",\n        \"taxes\": null,\n        \"taxDescription\": null,\n        \"returnStatus\": \"NOT_RETURN\",\n        \"returnAmount\": null,\n        \"returnCurrency\": \"KCS\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetWithdrawalHistoryByIdResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalGetWithdrawalHistoryOldReqModel(t *testing.T) {
	// GetWithdrawalHistoryOld
	// Get Withdrawal History - Old
	// /api/v1/hist-withdrawals

	data := "{\"currency\": \"BTC\", \"status\": \"SUCCESS\", \"startAt\": 1728663338000, \"endAt\": 1728692138000, \"currentPage\": 1, \"pageSize\": 50}"
	req := &GetWithdrawalHistoryOldReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalGetWithdrawalHistoryOldRespModel(t *testing.T) {
	// GetWithdrawalHistoryOld
	// Get Withdrawal History - Old
	// /api/v1/hist-withdrawals

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"currency\": \"BTC\",\n                \"createAt\": 1526723468,\n                \"amount\": \"0.534\",\n                \"address\": \"33xW37ZSW4tQvg443Pc7NLCAs167Yc2XUV\",\n                \"walletTxId\": \"aeacea864c020acf58e51606169240e96774838dcd4f7ce48acf38e3651323f4\",\n                \"isInner\": false,\n                \"status\": \"SUCCESS\"\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetWithdrawalHistoryOldResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalWithdrawalV1ReqModel(t *testing.T) {
	// WithdrawalV1
	// Withdraw - V1
	// /api/v1/withdrawals

	data := "{\"currency\": \"USDT\", \"address\": \"TKFRQXSDc****16GmLrjJggwX8\", \"amount\": 3, \"chain\": \"trx\", \"isInner\": true}"
	req := &WithdrawalV1Req{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestWithdrawalWithdrawalV1RespModel(t *testing.T) {
	// WithdrawalV1
	// Withdraw - V1
	// /api/v1/withdrawals

	data := "{\"code\":\"200000\",\"data\":{\"withdrawalId\":\"670a973cf07b3800070e216c\"}}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &WithdrawalV1Resp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}
