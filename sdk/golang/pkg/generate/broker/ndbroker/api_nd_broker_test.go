package ndbroker

import (
	"encoding/json"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestNDBrokerGetBrokerInfoReqModel(t *testing.T) {
	// GetBrokerInfo
	// Get Broker Info
	// /api/v1/broker/nd/info

	data := "{\"begin\": \"20240510\", \"end\": \"20241010\", \"tradeType\": \"1\"}"
	req := &GetBrokerInfoReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetBrokerInfoRespModel(t *testing.T) {
	// GetBrokerInfo
	// Get Broker Info
	// /api/v1/broker/nd/info

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"accountSize\": 0,\n        \"maxAccountSize\": null,\n        \"level\": 0\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetBrokerInfoResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerAddSubAccountReqModel(t *testing.T) {
	// AddSubAccount
	// Add sub-account
	// /api/v1/broker/nd/account

	data := "{\"accountName\": \"Account1\"}"
	req := &AddSubAccountReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerAddSubAccountRespModel(t *testing.T) {
	// AddSubAccount
	// Add sub-account
	// /api/v1/broker/nd/account

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"accountName\": \"Account15\",\n        \"uid\": \"226383154\",\n        \"createdAt\": 1729819381908,\n        \"level\": 0\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &AddSubAccountResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetSubAccountReqModel(t *testing.T) {
	// GetSubAccount
	// Get sub-account
	// /api/v1/broker/nd/account

	data := "{\"uid\": \"226383154\", \"currentPage\": 1, \"pageSize\": 20}"
	req := &GetSubAccountReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetSubAccountRespModel(t *testing.T) {
	// GetSubAccount
	// Get sub-account
	// /api/v1/broker/nd/account

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 20,\n        \"totalNum\": 1,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"accountName\": \"Account15\",\n                \"uid\": \"226383154\",\n                \"createdAt\": 1729819382000,\n                \"level\": 0\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetSubAccountResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerAddSubAccountApiReqModel(t *testing.T) {
	// AddSubAccountApi
	// Add sub-account API
	// /api/v1/broker/nd/account/apikey

	data := "{\"uid\": \"226383154\", \"passphrase\": \"11223344\", \"ipWhitelist\": [\"127.0.0.1\", \"123.123.123.123\"], \"permissions\": [\"general\", \"spot\"], \"label\": \"This is remarks\"}"
	req := &AddSubAccountApiReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerAddSubAccountApiRespModel(t *testing.T) {
	// AddSubAccountApi
	// Add sub-account API
	// /api/v1/broker/nd/account/apikey

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"uid\": \"226383154\",\n        \"label\": \"This is remarks\",\n        \"apiKey\": \"671afb36cee20f00015cfaf1\",\n        \"secretKey\": \"d694df2******5bae05b96\",\n        \"apiVersion\": 3,\n        \"permissions\": [\n            \"General\",\n            \"Spot\"\n        ],\n        \"ipWhitelist\": [\n            \"127.0.0.1\",\n            \"123.123.123.123\"\n        ],\n        \"createdAt\": 1729821494000\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &AddSubAccountApiResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetSubAccountAPIReqModel(t *testing.T) {
	// GetSubAccountAPI
	// Get sub-account API
	// /api/v1/broker/nd/account/apikey

	data := "{\"uid\": \"226383154\", \"apiKey\": \"671afb36cee20f00015cfaf1\"}"
	req := &GetSubAccountAPIReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetSubAccountAPIRespModel(t *testing.T) {
	// GetSubAccountAPI
	// Get sub-account API
	// /api/v1/broker/nd/account/apikey

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"uid\": \"226383154\",\n            \"label\": \"This is remarks\",\n            \"apiKey\": \"671afb36cee20f00015cfaf1\",\n            \"apiVersion\": 3,\n            \"permissions\": [\n                \"General\",\n                \"Spot\"\n            ],\n            \"ipWhitelist\": [\n                \"127.**.1\",\n                \"203.**.154\"\n            ],\n            \"createdAt\": 1729821494000\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetSubAccountAPIResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerModifySubAccountApiReqModel(t *testing.T) {
	// ModifySubAccountApi
	// Modify sub-account API
	// /api/v1/broker/nd/account/update-apikey

	data := "{\"uid\": \"226383154\", \"apiKey\": \"671afb36cee20f00015cfaf1\", \"ipWhitelist\": [\"127.0.0.1\", \"123.123.123.123\"], \"permissions\": [\"general\", \"spot\"], \"label\": \"This is remarks\"}"
	req := &ModifySubAccountApiReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerModifySubAccountApiRespModel(t *testing.T) {
	// ModifySubAccountApi
	// Modify sub-account API
	// /api/v1/broker/nd/account/update-apikey

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"uid\": \"226383154\",\n        \"label\": \"This is remarks\",\n        \"apiKey\": \"671afb36cee20f00015cfaf1\",\n        \"apiVersion\": 3,\n        \"permissions\": [\n            \"General\",\n            \"Spot\"\n        ],\n        \"ipWhitelist\": [\n            \"127.**.1\",\n            \"123.**.123\"\n        ],\n        \"createdAt\": 1729821494000\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &ModifySubAccountApiResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerDeleteSubAccountAPIReqModel(t *testing.T) {
	// DeleteSubAccountAPI
	// Delete sub-account API
	// /api/v1/broker/nd/account/apikey

	data := "{\"uid\": \"226383154\", \"apiKey\": \"671afb36cee20f00015cfaf1\"}"
	req := &DeleteSubAccountAPIReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerDeleteSubAccountAPIRespModel(t *testing.T) {
	// DeleteSubAccountAPI
	// Delete sub-account API
	// /api/v1/broker/nd/account/apikey

	data := "{\n    \"code\": \"200000\",\n    \"data\": true\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &DeleteSubAccountAPIResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerTransferReqModel(t *testing.T) {
	// Transfer
	// Transfer
	// /api/v1/broker/nd/transfer

	data := "{\"currency\": \"USDT\", \"amount\": \"1\", \"clientOid\": \"e6c24d23-6bc2-401b-bf9e-55e2daddfbc1\", \"direction\": \"OUT\", \"accountType\": \"MAIN\", \"specialUid\": \"226383154\", \"specialAccountType\": \"MAIN\"}"
	req := &TransferReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerTransferRespModel(t *testing.T) {
	// Transfer
	// Transfer
	// /api/v1/broker/nd/transfer

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"671b4600c1e3dd000726866d\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &TransferResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetTransferHistoryReqModel(t *testing.T) {
	// GetTransferHistory
	// Get Transfer History
	// /api/v3/broker/nd/transfer/detail

	data := "{\"orderId\": \"671b4600c1e3dd000726866d\"}"
	req := &GetTransferHistoryReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetTransferHistoryRespModel(t *testing.T) {
	// GetTransferHistory
	// Get Transfer History
	// /api/v3/broker/nd/transfer/detail

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"orderId\": \"671b4600c1e3dd000726866d\",\n        \"currency\": \"USDT\",\n        \"amount\": \"1\",\n        \"fromUid\": 165111215,\n        \"fromAccountType\": \"MAIN\",\n        \"fromAccountTag\": \"DEFAULT\",\n        \"toUid\": 226383154,\n        \"toAccountType\": \"MAIN\",\n        \"toAccountTag\": \"DEFAULT\",\n        \"status\": \"SUCCESS\",\n        \"reason\": null,\n        \"createdAt\": 1729840640000\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetTransferHistoryResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetDepositListReqModel(t *testing.T) {
	// GetDepositList
	// Get Deposit List
	// /api/v1/asset/ndbroker/deposit/list

	data := "{\"currency\": \"USDT\", \"status\": \"SUCCESS\", \"hash\": \"example_string_default_value\", \"startTimestamp\": 123456, \"endTimestamp\": 123456, \"limit\": 100}"
	req := &GetDepositListReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetDepositListRespModel(t *testing.T) {
	// GetDepositList
	// Get Deposit List
	// /api/v1/asset/ndbroker/deposit/list

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"uid\": 165111215,\n            \"hash\": \"6724e363a492800007ec602b\",\n            \"address\": \"xxxxxxx@gmail.com\",\n            \"memo\": \"\",\n            \"amount\": \"3.0\",\n            \"fee\": \"0.0\",\n            \"currency\": \"USDT\",\n            \"isInner\": true,\n            \"walletTxId\": \"bbbbbbbbb\",\n            \"status\": \"SUCCESS\",\n            \"chain\": \"\",\n            \"remark\": \"\",\n            \"createdAt\": 1730470760000,\n            \"updatedAt\": 1730470760000\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetDepositListResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetDepositDetailReqModel(t *testing.T) {
	// GetDepositDetail
	// Get Deposit Detail
	// /api/v3/broker/nd/deposit/detail

	data := "{\"currency\": \"USDT\", \"hash\": \"30bb0e0b***4156c5188\"}"
	req := &GetDepositDetailReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetDepositDetailRespModel(t *testing.T) {
	// GetDepositDetail
	// Get Deposit Detail
	// /api/v3/broker/nd/deposit/detail

	data := "{\n  \"data\" : {\n    \"chain\" : \"trx\",\n    \"hash\" : \"30bb0e0b***4156c5188\",\n    \"walletTxId\" : \"30bb0***610d1030f\",\n    \"uid\" : 201496341,\n    \"updatedAt\" : 1713429174000,\n    \"amount\" : \"8.5\",\n    \"memo\" : \"\",\n    \"fee\" : \"0.0\",\n    \"address\" : \"THLPzUrbd1o***vP7d\",\n    \"remark\" : \"Deposit\",\n    \"isInner\" : false,\n    \"currency\" : \"USDT\",\n    \"status\" : \"SUCCESS\",\n    \"createdAt\" : 1713429173000\n  },\n  \"code\" : \"200000\"\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetDepositDetailResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetWithdrawDetailReqModel(t *testing.T) {
	// GetWithdrawDetail
	// Get Withdraw Detail
	// /api/v3/broker/nd/withdraw/detail

	data := "{\"withdrawalId\": \"66617a2***3c9a\"}"
	req := &GetWithdrawDetailReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetWithdrawDetailRespModel(t *testing.T) {
	// GetWithdrawDetail
	// Get Withdraw Detail
	// /api/v3/broker/nd/withdraw/detail

	data := "{\n    \"data\": {\n        \"id\": \"66617a2***3c9a\",\n        \"chain\": \"ton\",\n        \"walletTxId\": \"AJ***eRI=\",\n        \"uid\": 157267400,\n        \"amount\": \"1.00000000\",\n        \"memo\": \"7025734\",\n        \"fee\": \"0.00000000\",\n        \"address\": \"EQDn***dKbGzr\",\n        \"remark\": \"\",\n        \"isInner\": false,\n        \"currency\": \"USDT\",\n        \"status\": \"SUCCESS\",\n        \"createdAt\": 1717664288000,\n        \"updatedAt\": 1717664375000\n    },\n    \"code\": \"200000\"\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetWithdrawDetailResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetRebaseReqModel(t *testing.T) {
	// GetRebase
	// Get Broker Rebate
	// /api/v1/broker/nd/rebase/download

	data := "{\"begin\": \"20240610\", \"end\": \"20241010\", \"tradeType\": \"1\"}"
	req := &GetRebaseReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestNDBrokerGetRebaseRespModel(t *testing.T) {
	// GetRebase
	// Get Broker Rebate
	// /api/v1/broker/nd/rebase/download

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"url\": \"https://kc-v2-promotion.s3.ap-northeast-1.amazonaws.com/broker/671aec522593f600019766d0_file.csv?X-Amz-Security-Token=IQo*********2cd90f14efb\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetRebaseResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}
