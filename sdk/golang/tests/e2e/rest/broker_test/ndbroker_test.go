package broker_test

import (
	"context"
	"encoding/json"
	"fmt"
	"os"
	"testing"

	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/extension/interceptor"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/api"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/common/logger"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/broker/ndbroker"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/google/uuid"
)

var ndbrokerApi ndbroker.NDBrokerAPI

func init() {
	key := os.Getenv("API_KEY")
	secret := os.Getenv("API_SECRET")
	passphrase := os.Getenv("API_PASSPHRASE")

	brokerName := os.Getenv("BROKER_NAME")
	brokerKey := os.Getenv("BROKER_KEY")
	brokerPartner := os.Getenv("BROKER_PARTNER")

	defaultLogger := logger.NewDefaultLogger()
	logger.SetLogger(defaultLogger)

	httpOptionBuilder := types.NewTransportOptionBuilder().
		SetKeepAlive(true).
		AddInterceptors(interceptor.NewLoggingInterceptor(false, defaultLogger))

	option := types.NewClientOptionBuilder().
		WithKey(key).
		WithSecret(secret).
		WithPassphrase(passphrase).
		WithBrokerName(brokerName).
		WithBrokerPartner(brokerPartner).
		WithBrokerKey(brokerKey).
		WithSpotEndpoint(types.GlobalApiEndpoint).
		WithFuturesEndpoint(types.GlobalFuturesApiEndpoint).
		WithBrokerEndpoint(types.GlobalBrokerApiEndpoint).
		WithTransportOption(httpOptionBuilder.Build()).
		Build()

	client := api.NewClient(option)

	ndbrokerApi = client.RestService().GetBrokerService().GetNDBrokerAPI()
}

func TestNDBrokerSubmitKYCReq(t *testing.T) {
	// SubmitKYC
	// Submit KYC
	// /api/kyc/ndBroker/proxyClient/submit

	builder := ndbroker.NewSubmitKYCReqBuilder()
	builder.SetClientUid("226383154").SetFirstName("Kaylah").SetLastName("Padberg").
		SetIssueCountry("JP").SetBirthDate("2000-01-01").SetIdentityType("passport").
		SetIdentityNumber("55").SetExpireDate("2030-01-01").
		SetFrontPhoto("*****").
		SetBackendPhoto("*****").
		SetFacePhoto("******")
	req := builder.Build()

	resp, err := ndbrokerApi.SubmitKYC(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerGetKYCStatusReq(t *testing.T) {
	// GetKYCStatus
	// Get KYC Status
	// /api/kyc/ndBroker/proxyClient/status/list

	builder := ndbroker.NewGetKYCStatusReqBuilder()
	builder.SetClientUids("226383154")
	req := builder.Build()

	resp, err := ndbrokerApi.GetKYCStatus(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerGetKYCStatusListReq(t *testing.T) {
	// GetKYCStatusList
	// Get KYC Status List
	// /api/kyc/ndBroker/proxyClient/status/page

	builder := ndbroker.NewGetKYCStatusListReqBuilder()
	builder.SetPageNumber(1).SetPageSize(100)
	req := builder.Build()

	resp, err := ndbrokerApi.GetKYCStatusList(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerGetDepositListReq(t *testing.T) {
	// GetDepositList
	// Get Deposit List
	// /api/v1/asset/ndbroker/deposit/list

	builder := ndbroker.NewGetDepositListReqBuilder()
	builder.SetCurrency("USDT")
	req := builder.Build()

	resp, err := ndbrokerApi.GetDepositList(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerDeleteSubAccountAPIReq(t *testing.T) {
	// DeleteSubAccountAPI
	// Delete SubAccount API
	// /api/v1/broker/nd/account/apikey

	builder := ndbroker.NewDeleteSubAccountAPIReqBuilder()
	builder.SetUid("229317507").SetApiKey("67447006890ba200018d2722")
	req := builder.Build()

	resp, err := ndbrokerApi.DeleteSubAccountAPI(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}
func TestNDBrokerGetSubAccountAPIReq(t *testing.T) {
	// GetSubAccountAPI
	// Get SubAccount API
	// /api/v1/broker/nd/account/apikey

	builder := ndbroker.NewGetSubAccountAPIReqBuilder()
	builder.SetUid("226383154").SetApiKey("67446f679d8f66000107eb6d")
	req := builder.Build()

	resp, err := ndbrokerApi.GetSubAccountAPI(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}
func TestNDBrokerAddSubAccountApiReq(t *testing.T) {
	// AddSubAccountApi
	// Add SubAccount API
	// /api/v1/broker/nd/account/apikey

	builder := ndbroker.NewAddSubAccountApiReqBuilder()
	builder.SetUid("229317507").SetPassphrase("11223344").
		SetIpWhitelist([]string{"127.0.0.1", "192.168.1.1"}).SetPermissions([]string{"general", "spot"}).SetLabel("This is remarks")
	req := builder.Build()

	resp, err := ndbrokerApi.AddSubAccountApi(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerGetSubAccountReq(t *testing.T) {
	// GetSubAccount
	// Get SubAccount
	// /api/v1/broker/nd/account

	builder := ndbroker.NewGetSubAccountReqBuilder()
	builder.SetUid("229317507")
	req := builder.Build()

	resp, err := ndbrokerApi.GetSubAccount(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}
func TestNDBrokerAddSubAccountReq(t *testing.T) {
	// AddSubAccount
	// Add SubAccount
	// /api/v1/broker/nd/account

	builder := ndbroker.NewAddSubAccountReqBuilder()
	builder.SetAccountName("sdk_test")
	req := builder.Build()

	resp, err := ndbrokerApi.AddSubAccount(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerModifySubAccountApiReq(t *testing.T) {
	// ModifySubAccountApi
	// Modify SubAccount API
	// /api/v1/broker/nd/account/update-apikey

	builder := ndbroker.NewModifySubAccountApiReqBuilder()
	builder.SetUid("229317507").SetApiKey("67447006890ba200018d2722").SetIpWhitelist([]string{"127.0.0.1"}).
		SetPermissions([]string{"general"}).SetLabel("This is remarks")
	req := builder.Build()

	resp, err := ndbrokerApi.ModifySubAccountApi(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerGetBrokerInfoReq(t *testing.T) {
	// GetBrokerInfo
	// Get Broker Info
	// /api/v1/broker/nd/info

	builder := ndbroker.NewGetBrokerInfoReqBuilder()
	builder.SetTradeType("1")
	req := builder.Build()

	resp, err := ndbrokerApi.GetBrokerInfo(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerGetRebaseReq(t *testing.T) {
	// GetRebase
	// Get Broker Rebate
	// /api/v1/broker/nd/rebase/download

	builder := ndbroker.NewGetRebaseReqBuilder()
	builder.SetBegin("20240610").SetEnd("20241010").SetTradeType("1")
	req := builder.Build()

	resp, err := ndbrokerApi.GetRebase(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerTransferReq(t *testing.T) {
	// Transfer
	// Transfer
	// /api/v1/broker/nd/transfer

	builder := ndbroker.NewTransferReqBuilder()
	builder.SetCurrency("USDT").SetAmount("0.01").SetDirection("OUT").SetAccountType("TRADE").
		SetSpecialUid("229317507").SetSpecialAccountType("MAIN").SetClientOid(uuid.NewString())
	req := builder.Build()

	resp, err := ndbrokerApi.Transfer(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerGetDepositDetailReq(t *testing.T) {
	// GetDepositDetail
	// Get Deposit Detail
	// /api/v3/broker/nd/deposit/detail

	builder := ndbroker.NewGetDepositDetailReqBuilder()
	builder.SetCurrency("USDT").SetHash("6724e363a492800007ec602b")
	req := builder.Build()

	resp, err := ndbrokerApi.GetDepositDetail(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerGetTransferHistoryReq(t *testing.T) {
	// GetTransferHistory
	// Get Transfer History
	// /api/v3/broker/nd/transfer/detail

	builder := ndbroker.NewGetTransferHistoryReqBuilder()
	builder.SetOrderId("671b4600c1e3dd000726866d")
	req := builder.Build()

	resp, err := ndbrokerApi.GetTransferHistory(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}

func TestNDBrokerGetWithdrawDetailReq(t *testing.T) {
	// GetWithdrawDetail
	// Get Withdraw Detail
	// /api/v3/broker/nd/withdraw/detail

	builder := ndbroker.NewGetWithdrawDetailReqBuilder()
	builder.SetWithdrawalId("674686fa1ac01f0007b25768")
	req := builder.Build()

	resp, err := ndbrokerApi.GetWithdrawDetail(req, context.TODO())
	if err != nil {
		panic(err)
	}
	data, err := json.Marshal(resp.ToMap())
	if err != nil {
		panic(err)
	}
	fmt.Println("code:", resp.CommonResponse.Code)
	fmt.Println("message:", resp.CommonResponse.Message)
	fmt.Println("data:", string(data))
}
