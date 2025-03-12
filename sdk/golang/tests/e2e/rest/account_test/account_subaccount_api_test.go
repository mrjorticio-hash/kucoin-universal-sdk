package account_test

import (
	"context"
	"encoding/json"
	"fmt"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/extension/interceptor"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/api"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/common/logger"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/account/subaccount"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"os"
	"testing"
)

var subaccountApi subaccount.SubAccountAPI

func init() {
	key := os.Getenv("API_KEY")
	secret := os.Getenv("API_SECRET")
	passphrase := os.Getenv("API_PASSPHRASE")

	defaultLogger := logger.NewDefaultLogger()
	logger.SetLogger(defaultLogger)

	httpOptionBuilder := types.NewTransportOptionBuilder().
		SetKeepAlive(true).
		AddInterceptors(interceptor.NewLoggingInterceptor(false, defaultLogger))

	option := types.NewClientOptionBuilder().
		WithKey(key).
		WithSecret(secret).
		WithPassphrase(passphrase).
		WithSpotEndpoint(types.GlobalApiEndpoint).
		WithFuturesEndpoint(types.GlobalFuturesApiEndpoint).
		WithBrokerEndpoint(types.GlobalBrokerApiEndpoint).
		WithTransportOption(httpOptionBuilder.Build()).
		Build()

	client := api.NewClient(option)

	subaccountApi = client.RestService().GetAccountService().GetSubAccountAPI()
}

func TestSubAccountGetFuturesSubAccountListV2Req(t *testing.T) {
	// GetFuturesSubAccountListV2
	// Get SubAccount List - Futures Balance(V2)
	// /api/v1/account-overview-all

	builder := subaccount.NewGetFuturesSubAccountListV2ReqBuilder()
	builder.SetCurrency("XBT")
	req := builder.Build()

	resp, err := subaccountApi.GetFuturesSubAccountListV2(req, context.TODO())
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

func TestSubAccountGetSpotSubAccountListV1Req(t *testing.T) {
	// GetSpotSubAccountListV1
	// Get SubAccount List - Spot Balance(V1)
	// /api/v1/sub-accounts

	resp, err := subaccountApi.GetSpotSubAccountListV1(context.TODO())
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

func TestSubAccountGetSpotSubAccountDetailReq(t *testing.T) {
	// GetSpotSubAccountDetail
	// Get SubAccount Detail - Balance
	// /api/v1/sub-accounts/{subUserId}

	builder := subaccount.NewGetSpotSubAccountDetailReqBuilder()
	builder.SetSubUserId("6745b7bc890ba20001911363").
		SetIncludeBaseAmount(false).SetBaseCurrency("USDT").SetBaseAmount("0.1")
	req := builder.Build()

	resp, err := subaccountApi.GetSpotSubAccountDetail(req, context.TODO())
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

func TestSubAccountDeleteSubAccountApiReq(t *testing.T) {
	// DeleteSubAccountApi
	// Delete SubAccount API
	// /api/v1/sub/api-key

	builder := subaccount.NewDeleteSubAccountApiReqBuilder()
	builder.SetApiKey("673eab2a955ebf000195d7e4").
		SetSubName("*********").
		SetPassphrase("*********")
	req := builder.Build()

	resp, err := subaccountApi.DeleteSubAccountApi(req, context.TODO())
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

func TestSubAccountGetSubAccountApiListReq(t *testing.T) {
	// GetSubAccountApiList
	// Get SubAccount API List
	// /api/v1/sub/api-key

	builder := subaccount.NewGetSubAccountApiListReqBuilder()
	builder.SetApiKey("673eab2a955ebf000195d7e4").SetSubName("*********")
	req := builder.Build()

	resp, err := subaccountApi.GetSubAccountApiList(req, context.TODO())
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

func TestSubAccountAddSubAccountApiReq(t *testing.T) {
	// AddSubAccountApi
	// Add SubAccount API
	// /api/v1/sub/api-key

	builder := subaccount.NewAddSubAccountApiReqBuilder()
	builder.SetPassphrase("*********").
		SetRemark("*********").
		SetPermission("General,Spot").
		SetExpire("30").
		SetSubName("*********")
	req := builder.Build()

	resp, err := subaccountApi.AddSubAccountApi(req, context.TODO())
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

func TestSubAccountModifySubAccountApiReq(t *testing.T) {
	// ModifySubAccountApi
	// Modify SubAccount API
	// /api/v1/sub/api-key/update

	builder := subaccount.NewModifySubAccountApiReqBuilder()
	builder.SetPassphrase("*********").
		SetPermission("General").
		SetIpWhitelist("10.0.0.1").
		SetExpire("-1").
		SetSubName("*********").
		SetApiKey("xxxx")
	req := builder.Build()

	resp, err := subaccountApi.ModifySubAccountApi(req, context.TODO())
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

func TestSubAccountGetSpotSubAccountsSummaryV1Req(t *testing.T) {
	// GetSpotSubAccountsSummaryV1
	// Get SubAccount List - Summary Info(V1)
	// /api/v1/sub/user

	resp, err := subaccountApi.GetSpotSubAccountsSummaryV1(context.TODO())
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

func TestSubAccountGetSpotSubAccountListV2Req(t *testing.T) {
	// GetSpotSubAccountListV2
	// Get SubAccount List - Spot Balance(V2)
	// /api/v2/sub-accounts

	builder := subaccount.NewGetSpotSubAccountListV2ReqBuilder()
	builder.SetCurrentPage(1).SetPageSize(10)
	req := builder.Build()

	resp, err := subaccountApi.GetSpotSubAccountListV2(req, context.TODO())
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

func TestSubAccountAddSubAccountReq(t *testing.T) {
	// AddSubAccount
	// Add SubAccount
	// /api/v2/sub/user/created

	builder := subaccount.NewAddSubAccountReqBuilder()
	builder.SetPassword("*********@123").SetRemarks("*********").
		SetSubName("sdkTestIntegration1").SetAccess("Spot")
	req := builder.Build()

	resp, err := subaccountApi.AddSubAccount(req, context.TODO())
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

func TestSubAccountGetSpotSubAccountsSummaryV2Req(t *testing.T) {
	// GetSpotSubAccountsSummaryV2
	// Get SubAccount List - Summary Info
	// /api/v2/sub/user

	builder := subaccount.NewGetSpotSubAccountsSummaryV2ReqBuilder()
	builder.SetCurrentPage(1).SetPageSize(10)
	req := builder.Build()

	resp, err := subaccountApi.GetSpotSubAccountsSummaryV2(req, context.TODO())
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

func TestSubAccountAddSubAccountFuturesPermissionReq(t *testing.T) {
	// AddSubAccountFuturesPermission
	// Add SubAccount Futures Permission
	// /api/v3/sub/user/futures/enable

	builder := subaccount.NewAddSubAccountFuturesPermissionReqBuilder()
	builder.SetUid("229290616")
	req := builder.Build()

	resp, err := subaccountApi.AddSubAccountFuturesPermission(req, context.TODO())
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

func TestSubAccountAddSubAccountMarginPermissionReq(t *testing.T) {
	// AddSubAccountMarginPermission
	// Add SubAccount Margin Permission
	// /api/v3/sub/user/margin/enable

	builder := subaccount.NewAddSubAccountMarginPermissionReqBuilder()
	builder.SetUid("229409095")
	req := builder.Build()

	resp, err := subaccountApi.AddSubAccountMarginPermission(req, context.TODO())
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
