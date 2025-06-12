package account_test

import (
	"context"
	"encoding/json"
	"fmt"
	"os"
	"testing"

	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/extension/interceptor"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/api"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/common/logger"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/account/withdrawal"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
)

var withdrawalApi withdrawal.WithdrawalAPI

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

	withdrawalApi = client.RestService().GetAccountService().GetWithdrawalAPI()
}

// TODO empty data
func TestWithdrawalGetWithdrawalHistoryOldReq(t *testing.T) {
	// GetWithdrawalHistoryOld
	// Get Withdrawal History(OLD)
	// /api/v1/hist-withdrawals

	builder := withdrawal.NewGetWithdrawalHistoryOldReqBuilder()
	builder.SetCurrency("USDT").SetStartAt(1669824000000).SetEndAt(1732982400000)
	req := builder.Build()

	resp, err := withdrawalApi.GetWithdrawalHistoryOld(req, context.TODO())
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

func TestWithdrawalGetWithdrawalHistoryReq(t *testing.T) {
	// GetWithdrawalHistory
	// Get Withdrawal History
	// /api/v1/withdrawals

	builder := withdrawal.NewGetWithdrawalHistoryReqBuilder()

	builder.SetCurrency("USDT").SetStartAt(1727712000000).SetEndAt(1732982400000)
	req := builder.Build()

	resp, err := withdrawalApi.GetWithdrawalHistory(req, context.TODO())
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

func TestWithdrawalGetWithdrawalHistoryByIdReq(t *testing.T) {
	// GetWithdrawalHistoryById
	// Get Withdrawal History By ID
	// /api/v1/withdrawals/{withdrawalId}

	builder := withdrawal.NewGetWithdrawalHistoryByIdReqBuilder()
	builder.SetWithdrawalId("674576dc74b2bb000778452c")
	req := builder.Build()

	resp, err := withdrawalApi.GetWithdrawalHistoryById(req, context.TODO())
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

func TestWithdrawalWithdrawalV1Req(t *testing.T) {
	// WithdrawalV1
	// Withdraw(V1)
	// /api/v1/withdrawals

	builder := withdrawal.NewWithdrawalV1ReqBuilder()
	builder.SetCurrency("USDT").SetChain("bsc").
		SetAddress("**********").SetAmount(20).
		SetIsInner(false)
	req := builder.Build()

	resp, err := withdrawalApi.WithdrawalV1(req, context.TODO())
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

func TestWithdrawalGetWithdrawalQuotasReq(t *testing.T) {
	// GetWithdrawalQuotas
	// Get Withdrawal Quotas
	// /api/v1/withdrawals/quotas

	builder := withdrawal.NewGetWithdrawalQuotasReqBuilder()
	builder.SetCurrency("USDT").SetChain("bsc")
	req := builder.Build()

	resp, err := withdrawalApi.GetWithdrawalQuotas(req, context.TODO())
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

func TestWithdrawalCancelWithdrawalReq(t *testing.T) {
	// CancelWithdrawal
	// Cancel Withdrawal
	// /api/v1/withdrawals/{withdrawalId}

	builder := withdrawal.NewCancelWithdrawalReqBuilder()
	builder.SetWithdrawalId("674576dc74b2bb000778452c")
	req := builder.Build()

	resp, err := withdrawalApi.CancelWithdrawal(req, context.TODO())
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

func TestWithdrawalWithdrawalV3Req(t *testing.T) {
	// WithdrawalV3
	// Withdraw(V3)
	// /api/v3/withdrawals

	builder := withdrawal.NewWithdrawalV3ReqBuilder()
	builder.SetCurrency("USDT").SetChain("bsc").SetWithdrawType("ADDRESS").
		SetToAddress("********").SetAmount("20").
		SetIsInner(false)
	req := builder.Build()

	resp, err := withdrawalApi.WithdrawalV3(req, context.TODO())
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
