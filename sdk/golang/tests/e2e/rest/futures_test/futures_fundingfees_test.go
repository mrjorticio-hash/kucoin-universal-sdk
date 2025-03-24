package futures_test

import (
	"context"
	"encoding/json"
	"fmt"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/extension/interceptor"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/api"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/common/logger"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/futures/fundingfees"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"os"
	"testing"
)

var fundingfeesApi fundingfees.FundingFeesAPI

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

	fundingfeesApi = client.RestService().GetFuturesService().GetFundingFeesAPI()
}

func TestFundingFeesGetPublicFundingHistoryReq(t *testing.T) {
	// GetPublicFundingHistory
	// Get Public Funding History
	// /api/v1/contract/funding-rates

	builder := fundingfees.NewGetPublicFundingHistoryReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetFrom(1732464000000).SetTo(1732500000000)
	req := builder.Build()

	resp, err := fundingfeesApi.GetPublicFundingHistory(req, context.TODO())
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

func TestFundingFeesGetPrivateFundingHistoryReq(t *testing.T) {
	// GetPrivateFundingHistory
	// Get Private Funding History
	// /api/v1/funding-history

	builder := fundingfees.NewGetPrivateFundingHistoryReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetStartAt(1700310700000).SetEndAt(1702310700000).
		SetReverse(true).SetMaxCount(100)
	req := builder.Build()

	resp, err := fundingfeesApi.GetPrivateFundingHistory(req, context.TODO())
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

func TestFundingFeesGetCurrentFundingRateReq(t *testing.T) {
	// GetCurrentFundingRate
	// Get Current Funding Rate
	// /api/v1/funding-rate/{symbol}/current

	builder := fundingfees.NewGetCurrentFundingRateReqBuilder()
	builder.SetSymbol("XBTUSDTM")
	req := builder.Build()

	resp, err := fundingfeesApi.GetCurrentFundingRate(req, context.TODO())
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
