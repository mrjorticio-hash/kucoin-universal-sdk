package deposit

import (
	"encoding/json"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestDepositAddDepositAddressV3ReqModel(t *testing.T) {
	// AddDepositAddressV3
	// Add Deposit Address (V3)
	// /api/v3/deposit-address/create

	data := "{\"currency\": \"TON\", \"chain\": \"ton\", \"to\": \"trade\"}"
	req := &AddDepositAddressV3Req{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDepositAddDepositAddressV3RespModel(t *testing.T) {
	// AddDepositAddressV3
	// Add Deposit Address (V3)
	// /api/v3/deposit-address/create

	data := "{\"code\":\"200000\",\"data\":{\"address\":\"EQCA1BI4QRZ8qYmskSRDzJmkucGodYRTZCf_b9hckjla6dZl\",\"memo\":\"2090821203\",\"chainId\":\"ton\",\"to\":\"TRADE\",\"expirationDate\":0,\"currency\":\"TON\",\"chainName\":\"TON\"}}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &AddDepositAddressV3Resp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestDepositGetDepositAddressV3ReqModel(t *testing.T) {
	// GetDepositAddressV3
	// Get Deposit Address (V3)
	// /api/v3/deposit-addresses

	data := "{\"currency\": \"BTC\", \"amount\": \"example_string_default_value\", \"chain\": \"eth\"}"
	req := &GetDepositAddressV3Req{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDepositGetDepositAddressV3RespModel(t *testing.T) {
	// GetDepositAddressV3
	// Get Deposit Address (V3)
	// /api/v3/deposit-addresses

	data := "{\"code\":\"200000\",\"data\":[{\"address\":\"TSv3L1fS7yA3SxzKD8c1qdX4nLP6rqNxYz\",\"memo\":\"\",\"chainId\":\"trx\",\"to\":\"TRADE\",\"expirationDate\":0,\"currency\":\"USDT\",\"contractAddress\":\"TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t\",\"chainName\":\"TRC20\"},{\"address\":\"0x551e823a3b36865e8c5dc6e6ac6cc0b00d98533e\",\"memo\":\"\",\"chainId\":\"kcc\",\"to\":\"TRADE\",\"expirationDate\":0,\"currency\":\"USDT\",\"contractAddress\":\"0x0039f574ee5cc39bdd162e9a88e3eb1f111baf48\",\"chainName\":\"KCC\"},{\"address\":\"EQCA1BI4QRZ8qYmskSRDzJmkucGodYRTZCf_b9hckjla6dZl\",\"memo\":\"2085202643\",\"chainId\":\"ton\",\"to\":\"TRADE\",\"expirationDate\":0,\"currency\":\"USDT\",\"contractAddress\":\"EQCxE6mUtQJKFnGfaROTKOt1lZbDiiX1kCixRv7Nw2Id_sDs\",\"chainName\":\"TON\"},{\"address\":\"0x0a2586d5a901c8e7e68f6b0dc83bfd8bd8600ff5\",\"memo\":\"\",\"chainId\":\"eth\",\"to\":\"MAIN\",\"expirationDate\":0,\"currency\":\"USDT\",\"contractAddress\":\"0xdac17f958d2ee523a2206206994597c13d831ec7\",\"chainName\":\"ERC20\"}]}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetDepositAddressV3Resp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestDepositGetDepositHistoryReqModel(t *testing.T) {
	// GetDepositHistory
	// Get Deposit History
	// /api/v1/deposits

	data := "{\"currency\": \"BTC\", \"status\": \"SUCCESS\", \"startAt\": 1728663338000, \"endAt\": 1728692138000, \"currentPage\": 1, \"pageSize\": 50}"
	req := &GetDepositHistoryReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDepositGetDepositHistoryRespModel(t *testing.T) {
	// GetDepositHistory
	// Get Deposit History
	// /api/v1/deposits

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 5,\n        \"totalPage\": 1,\n        \"items\": [\n            {\n                \"currency\": \"USDT\",\n                \"chain\": \"\",\n                \"status\": \"SUCCESS\",\n                \"address\": \"a435*****@gmail.com\",\n                \"memo\": \"\",\n                \"isInner\": true,\n                \"amount\": \"1.00000000\",\n                \"fee\": \"0.00000000\",\n                \"walletTxId\": null,\n                \"createdAt\": 1728555875000,\n                \"updatedAt\": 1728555875000,\n                \"remark\": \"\",\n                \"arrears\": false\n            },\n            {\n                \"currency\": \"USDT\",\n                \"chain\": \"trx\",\n                \"status\": \"SUCCESS\",\n                \"address\": \"TSv3L1fS7******X4nLP6rqNxYz\",\n                \"memo\": \"\",\n                \"isInner\": true,\n                \"amount\": \"6.00000000\",\n                \"fee\": \"0.00000000\",\n                \"walletTxId\": null,\n                \"createdAt\": 1721730920000,\n                \"updatedAt\": 1721730920000,\n                \"remark\": \"\",\n                \"arrears\": false\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetDepositHistoryResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestDepositGetDepositAddressV2ReqModel(t *testing.T) {
	// GetDepositAddressV2
	// Get Deposit Addresses (V2)
	// /api/v2/deposit-addresses

	data := "{\"currency\": \"BTC\", \"chain\": \"eth\"}"
	req := &GetDepositAddressV2Req{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDepositGetDepositAddressV2RespModel(t *testing.T) {
	// GetDepositAddressV2
	// Get Deposit Addresses (V2)
	// /api/v2/deposit-addresses

	data := "{\n    \"code\": \"200000\",\n    \"data\": [\n        {\n            \"address\": \"0x02028456*****87ede7a73d7c\",\n            \"memo\": \"\",\n            \"chain\": \"ERC20\",\n            \"chainId\": \"eth\",\n            \"to\": \"MAIN\",\n            \"currency\": \"ETH\",\n            \"contractAddress\": \"\"\n        }\n    ]\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetDepositAddressV2Resp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestDepositGetDepositAddressV1ReqModel(t *testing.T) {
	// GetDepositAddressV1
	// Get Deposit Addresses - V1
	// /api/v1/deposit-addresses

	data := "{\"currency\": \"BTC\", \"chain\": \"eth\"}"
	req := &GetDepositAddressV1Req{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDepositGetDepositAddressV1RespModel(t *testing.T) {
	// GetDepositAddressV1
	// Get Deposit Addresses - V1
	// /api/v1/deposit-addresses

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"address\": \"0xea220bf61c3c2b0adc2cfa29fec3d2677745a379\",\n        \"memo\": \"\",\n        \"chain\": \"ERC20\",\n        \"chainId\": \"eth\",\n        \"to\": \"MAIN\",\n        \"currency\": \"USDT\"\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetDepositAddressV1Resp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestDepositGetDepositHistoryOldReqModel(t *testing.T) {
	// GetDepositHistoryOld
	// Get Deposit History - Old
	// /api/v1/hist-deposits

	data := "{\"currency\": \"BTC\", \"status\": \"SUCCESS\", \"startAt\": 1728663338000, \"endAt\": 1728692138000}"
	req := &GetDepositHistoryOldReq{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDepositGetDepositHistoryOldRespModel(t *testing.T) {
	// GetDepositHistoryOld
	// Get Deposit History - Old
	// /api/v1/hist-deposits

	data := "{\n    \"code\": \"200000\",\n    \"data\": {\n        \"currentPage\": 1,\n        \"pageSize\": 50,\n        \"totalNum\": 0,\n        \"totalPage\": 0,\n        \"items\": [\n            {\n                \"currency\": \"BTC\",\n                \"createAt\": 1528536998,\n                \"amount\": \"0.03266638\",\n                \"walletTxId\": \"55c643bc2c68d6f17266383ac1be9e454038864b929ae7cee0bc408cc5c869e8@12ffGWmMMD1zA1WbFm7Ho3JZ1w6NYXjpFk@234\",\n                \"isInner\": false,\n                \"status\": \"SUCCESS\"\n            }\n        ]\n    }\n}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &GetDepositHistoryOldResp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}

func TestDepositAddDepositAddressV1ReqModel(t *testing.T) {
	// AddDepositAddressV1
	// Add Deposit Address - V1
	// /api/v1/deposit-addresses

	data := "{\"currency\": \"ETH\", \"chain\": \"eth\", \"to\": \"MAIN\"}"
	req := &AddDepositAddressV1Req{}
	err := json.Unmarshal([]byte(data), req)
	req.ToMap()
	assert.Nil(t, err)
}

func TestDepositAddDepositAddressV1RespModel(t *testing.T) {
	// AddDepositAddressV1
	// Add Deposit Address - V1
	// /api/v1/deposit-addresses

	data := "{\"code\":\"200000\",\"data\":{\"address\":\"0x02028456f38e78609904e8a002c787ede7a73d7c\",\"memo\":null,\"chain\":\"ERC20\",\"chainId\":\"eth\",\"to\":\"MAIN\",\"currency\":\"ETH\"}}"
	commonResp := &types.RestResponse{}
	err := json.Unmarshal([]byte(data), commonResp)
	assert.Nil(t, err)
	assert.NotNil(t, commonResp.Data)
	resp := &AddDepositAddressV1Resp{}
	err = json.Unmarshal([]byte(commonResp.Data), resp)
	resp.ToMap()
	assert.Nil(t, err)
}
