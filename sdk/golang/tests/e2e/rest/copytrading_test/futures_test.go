package copytrading_test

import (
	"context"
	"encoding/json"
	"fmt"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/extension/interceptor"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/api"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/common/logger"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/copytrading/futures"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/google/uuid"
	"os"
	"testing"
)

var futuresApi futures.FuturesAPI

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

	futuresApi = client.RestService().GetCopytradingService().GetFuturesAPI()
}

func TestFuturesAddOrderReq(t *testing.T) {
	// AddOrder
	// Add Order
	// /api/v1/copy-trade/futures/orders

	builder := futures.NewAddOrderReqBuilder()
	builder.SetClientOid(uuid.NewString()).SetSide("buy").SetSymbol("XBTUSDTM").
		SetLeverage(3).SetType("limit").SetReduceOnly(false).
		SetMarginMode("ISOLATED").SetPrice("0.1").SetSize(1).SetTimeInForce("GTC")
	req := builder.Build()

	resp, err := futuresApi.AddOrder(req, context.TODO())
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

func TestFuturesAddOrderTestReq(t *testing.T) {
	// AddOrderTest
	// Add Order Test
	// /api/v1/copy-trade/futures/orders/test

	builder := futures.NewAddOrderTestReqBuilder()
	builder.SetClientOid(uuid.NewString()).SetSide("buy").SetSymbol("XBTUSDTM").
		SetLeverage(3).SetType("limit").SetReduceOnly(false).
		SetMarginMode("ISOLATED").SetPrice("0.1").SetSize(1).SetTimeInForce("GTC")
	req := builder.Build()

	resp, err := futuresApi.AddOrderTest(req, context.TODO())
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

func TestFuturesAddTPSLOrderReq(t *testing.T) {
	// AddTPSLOrder
	// Add Take Profit And Stop Loss Order
	// /api/v1/copy-trade/futures/st-orders

	builder := futures.NewAddTPSLOrderReqBuilder()
	builder.SetClientOid(uuid.NewString()).SetSide("buy").SetSymbol("XBTUSDTM").
		SetLeverage(3).SetType("limit").SetReduceOnly(false).
		SetMarginMode("ISOLATED").SetPrice("0.1").SetSize(1).SetTimeInForce("GTC").
		SetTriggerStopUpPrice("0.3").SetTriggerStopDownPrice("0.1").SetStopPriceType("TP")
	req := builder.Build()

	resp, err := futuresApi.AddTPSLOrder(req, context.TODO())
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

func TestFuturesCancelOrderByIdReq(t *testing.T) {
	// CancelOrderById
	// Cancel Order By OrderId
	// /api/v1/copy-trade/futures/orders

	builder := futures.NewCancelOrderByIdReqBuilder()
	builder.SetOrderId("268931241925947392")
	req := builder.Build()

	resp, err := futuresApi.CancelOrderById(req, context.TODO())
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

func TestFuturesCancelOrderByClientOidReq(t *testing.T) {
	// CancelOrderByClientOid
	// Cancel Order By ClientOid
	// /api/v1/copy-trade/futures/orders/client-order

	builder := futures.NewCancelOrderByClientOidReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetClientOid("d4edf98a-c896-41d4-b80c-dde6812912fd")
	req := builder.Build()

	resp, err := futuresApi.CancelOrderByClientOid(req, context.TODO())
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

func TestFuturesGetMaxOpenSizeReq(t *testing.T) {
	// GetMaxOpenSize
	// Get Max Open Size
	// /api/v1/copy-trade/futures/get-max-open-size

	builder := futures.NewGetMaxOpenSizeReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetPrice(0.1).SetLeverage(10)
	req := builder.Build()

	resp, err := futuresApi.GetMaxOpenSize(req, context.TODO())
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

func TestFuturesGetMaxWithdrawMarginReq(t *testing.T) {
	// GetMaxWithdrawMargin
	// Get Max Withdraw Margin
	// /api/v1/copy-trade/futures/position/margin/max-withdraw-margin

	builder := futures.NewGetMaxWithdrawMarginReqBuilder()
	builder.SetSymbol("XBTUSDTM")
	req := builder.Build()

	resp, err := futuresApi.GetMaxWithdrawMargin(req, context.TODO())
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

func TestFuturesAddIsolatedMarginReq(t *testing.T) {
	// AddIsolatedMargin
	// Add Isolated Margin
	// /api/v1/copy-trade/futures/position/margin/deposit-margin

	builder := futures.NewAddIsolatedMarginReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetMargin(3).SetBizNo(uuid.NewString())
	req := builder.Build()

	resp, err := futuresApi.AddIsolatedMargin(req, context.TODO())
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

func TestFuturesRemoveIsolatedMarginReq(t *testing.T) {
	// RemoveIsolatedMargin
	// Remove Isolated Margin
	// /api/v1/copy-trade/futures/position/margin/withdraw-margin

	builder := futures.NewRemoveIsolatedMarginReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetWithdrawAmount(0.0000001)
	req := builder.Build()

	resp, err := futuresApi.RemoveIsolatedMargin(req, context.TODO())
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

func TestFuturesModifyIsolatedMarginRiskLimtReq(t *testing.T) {
	// ModifyIsolatedMarginRiskLimt
	// Modify Isolated Margin Risk Limit
	// /api/v1/copy-trade/futures/position/risk-limit-level/change

	builder := futures.NewModifyIsolatedMarginRiskLimtReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetLevel(1)
	req := builder.Build()

	resp, err := futuresApi.ModifyIsolatedMarginRiskLimt(req, context.TODO())
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

func TestFuturesModifyAutoDepositStatusReq(t *testing.T) {
	// ModifyAutoDepositStatus
	// Modify Isolated Margin Auto-Deposit Status
	// /api/v1/copy-trade/futures/position/margin/auto-deposit-status

	builder := futures.NewModifyAutoDepositStatusReqBuilder()
	builder.SetSymbol("XBTUSDTM").SetStatus(true)
	req := builder.Build()

	resp, err := futuresApi.ModifyAutoDepositStatus(req, context.TODO())
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
