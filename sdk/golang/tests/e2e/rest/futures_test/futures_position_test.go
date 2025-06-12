package futures_test

import (
	"context"
	"encoding/json"
	"fmt"
	"os"
	"testing"

	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/extension/interceptor"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/api"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/common/logger"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/futures/positions"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
)

var positionsApi positions.PositionsAPI

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

	positionsApi = client.RestService().GetFuturesService().GetPositionsAPI()
}

func TestPositionsGetIsolatedMarginRiskLimitReq(t *testing.T) {
	// GetIsolatedMarginRiskLimit
	// Get Isolated Margin Risk Limit
	// /api/v1/contracts/risk-limit/{symbol}

	builder := positions.NewGetIsolatedMarginRiskLimitReqBuilder()
	builder.SetSymbol("XBTUSDTM")
	req := builder.Build()

	resp, err := positionsApi.GetIsolatedMarginRiskLimit(req, context.TODO())
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

func TestPositionsGetPositionsHistoryReq(t *testing.T) {
	// GetPositionsHistory
	// Get Positions History
	// /api/v1/history-positions

	builder := positions.NewGetPositionsHistoryReqBuilder()
	builder.SetSymbol("XBTUSDTM")
	req := builder.Build()

	resp, err := positionsApi.GetPositionsHistory(req, context.TODO())
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

func TestPositionsGetMaxWithdrawMarginReq(t *testing.T) {
	// GetMaxWithdrawMargin
	// Get Max Withdraw Margin
	// /api/v1/margin/maxWithdrawMargin

	builder := positions.NewGetMaxWithdrawMarginReqBuilder()
	builder.SetSymbol("XBTUSDTM")
	req := builder.Build()

	resp, err := positionsApi.GetMaxWithdrawMargin(req, context.TODO())
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

func TestPositionsRemoveIsolatedMarginReq(t *testing.T) {
	// RemoveIsolatedMargin
	// Remove Isolated Margin
	// /api/v1/margin/withdrawMargin

	builder := positions.NewRemoveIsolatedMarginReqBuilder()
	builder.SetSymbol("DOGEUSDTM").SetWithdrawAmount("1")
	req := builder.Build()

	resp, err := positionsApi.RemoveIsolatedMargin(req, context.TODO())
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

func TestPositionsGetCrossMarginRiskLimitReq(t *testing.T) {
	// GetCrossMarginRiskLimit
	// Get Cross Margin Risk Limit
	// /api/v2/batchGetCrossOrderLimit

	builder := positions.NewGetCrossMarginRiskLimitReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetTotalMargin("10000").SetLeverage(1)
	req := builder.Build()

	resp, err := positionsApi.GetCrossMarginRiskLimit(req, context.TODO())
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

func TestPositionsGetPositionDetailsReq(t *testing.T) {
	// GetPositionDetails
	// Get Position Details
	// /api/v1/position

	builder := positions.NewGetPositionDetailsReqBuilder()
	builder.SetSymbol("XBTUSDTM")
	req := builder.Build()

	resp, err := positionsApi.GetPositionDetails(req, context.TODO())
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

func TestPositionsModifyAutoDepositStatusReq(t *testing.T) {
	// ModifyAutoDepositStatus
	// Modify Isolated Margin Auto-Deposit Status
	// /api/v1/position/margin/auto-deposit-status

	builder := positions.NewModifyAutoDepositStatusReqBuilder()
	builder.SetSymbol("DOGEUSDTM").SetStatus(true)
	req := builder.Build()

	resp, err := positionsApi.ModifyAutoDepositStatus(req, context.TODO())
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

func TestPositionsAddIsolatedMarginReq(t *testing.T) {
	// AddIsolatedMargin
	// Add Isolated Margin
	// /api/v1/position/margin/deposit-margin

	builder := positions.NewAddIsolatedMarginReqBuilder()
	builder.SetSymbol("DOGEUSDTM").SetMargin(2).SetBizNo("251160679598325760")
	req := builder.Build()

	resp, err := positionsApi.AddIsolatedMargin(req, context.TODO())
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
func TestPositionsModifyIsolatedMarginRiskLimtReq(t *testing.T) {
	// ModifyIsolatedMarginRiskLimt
	// Modify Isolated Margin Risk Limit
	// /api/v1/position/risk-limit-level/change

	builder := positions.NewModifyIsolatedMarginRiskLimtReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetLevel(10)
	req := builder.Build()

	resp, err := positionsApi.ModifyIsolatedMarginRiskLimt(req, context.TODO())
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

func TestPositionsGetPositionListReq(t *testing.T) {
	// GetPositionList
	// Get Position List
	// /api/v1/positions

	builder := positions.NewGetPositionListReqBuilder()
	builder.SetCurrency("USDT")
	req := builder.Build()

	resp, err := positionsApi.GetPositionList(req, context.TODO())
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

func TestPositionsModifyMarginLeverageReq(t *testing.T) {
	// ModifyMarginLeverage
	// Modify Cross Margin Leverage
	// /api/v2/changeCrossUserLeverage

	builder := positions.NewModifyMarginLeverageReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetLeverage("20")
	req := builder.Build()

	resp, err := positionsApi.ModifyMarginLeverage(req, context.TODO())
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
func TestPositionsGetCrossMarginLeverageReq(t *testing.T) {
	// GetCrossMarginLeverage
	// Get Cross Margin Leverage
	// /api/v2/getCrossUserLeverage

	builder := positions.NewGetCrossMarginLeverageReqBuilder()
	builder.SetSymbol("XBTUSDTM")
	req := builder.Build()

	resp, err := positionsApi.GetCrossMarginLeverage(req, context.TODO())
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
func TestPositionsGetMaxOpenSizeReq(t *testing.T) {
	// GetMaxOpenSize
	// Get Max Open Size
	// /api/v2/getMaxOpenSize

	builder := positions.NewGetMaxOpenSizeReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetPrice("10000").SetLeverage(10)
	req := builder.Build()

	resp, err := positionsApi.GetMaxOpenSize(req, context.TODO())
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

func TestPositionsSwitchMarginModeReq(t *testing.T) {
	// SwitchMarginMode
	// Switch Margin Mode
	// /api/v2/position/changeMarginMode

	builder := positions.NewSwitchMarginModeReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetMarginMode("CROSS")
	req := builder.Build()

	resp, err := positionsApi.SwitchMarginMode(req, context.TODO())
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

func TestPositionsBatchSwitchMarginModeReq(t *testing.T) {
	// BatchSwitchMarginMode
	// Batch Switch Margin Mode
	// /api/v2/position/batchChangeMarginMode

	builder := positions.NewBatchSwitchMarginModeReqBuilder()
	builder.SetMarginMode("ISOLATED").SetSymbols([]string{"XBTUSDTM", "DOGEUSDTM"})
	req := builder.Build()

	resp, err := positionsApi.BatchSwitchMarginMode(req, context.TODO())
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

func TestPositionsGetMarginModeReq(t *testing.T) {
	// GetMarginMode
	// Get Margin Mode
	// /api/v2/position/getMarginMode

	builder := positions.NewGetMarginModeReqBuilder()
	builder.SetSymbol("XBTUSDTM")
	req := builder.Build()

	resp, err := positionsApi.GetMarginMode(req, context.TODO())
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
