package main

import (
	"context"
	"log"
	"os"
	"testing"
	"time"

	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/api"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"

	accountfee "github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/account/fee"
	earnapi "github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/earn/earn"
	futuresorder "github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/futures/order"
	marginorder "github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/margin/order"
	spotmarket "github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/spot/market"
	spotorder "github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/spot/order"

	"github.com/google/uuid"
)

func newClient() api.KucoinRestService {
	log.SetFlags(log.LstdFlags | log.Lmicroseconds)

	key := os.Getenv("API_KEY")
	secret := os.Getenv("API_SECRET")
	passphrase := os.Getenv("API_PASSPHRASE")

	httpOpt := types.NewTransportOptionBuilder().
		SetKeepAlive(true).
		SetMaxConnsPerHost(10).
		Build()

	clientOpt := types.NewClientOptionBuilder().
		WithKey(key).WithSecret(secret).WithPassphrase(passphrase).
		WithSpotEndpoint(types.GlobalApiEndpoint).
		WithFuturesEndpoint(types.GlobalFuturesApiEndpoint).
		WithBrokerEndpoint(types.GlobalBrokerApiEndpoint).
		WithTransportOption(httpOpt).
		Build()

	return api.NewClient(clientOpt).RestService()
}

func checkCommon(t *testing.T, cr *types.RestResponse) {
	t.Helper()
	if cr == nil || cr.Code != "200000" {
		t.Fatalf("bad code: %+v", cr)
	}
	if cr.RateLimit == nil {
		t.Fatalf("no rate limit: %+v", cr)
	}
}

func TestAccountService(t *testing.T) {
	rest := newClient()
	resp, err := rest.GetAccountService().
		GetFeeAPI().
		GetBasicFee(
			accountfee.NewGetBasicFeeReqBuilder().
				SetCurrencyType(0).
				Build(),
			context.Background(),
		)
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, resp.CommonResponse)
	if len(resp.MakerFeeRate) == 0 || len(resp.TakerFeeRate) == 0 {
		t.Fatalf("empty fee rate")
	}
}

func TestEarnService(t *testing.T) {
	rest := newClient()
	resp, err := rest.GetEarnService().
		GetEarnAPI().
		GetSavingsProducts(
			earnapi.NewGetSavingsProductsReqBuilder().SetCurrency("USDT").Build(),
			context.Background(),
		)
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, resp.CommonResponse)
	if len(resp.Data) == 0 {
		t.Fatalf("no earn products")
	}
}

func TestMarginService(t *testing.T) {
	rest := newClient()
	orderAPI := rest.GetMarginService().GetOrderAPI()

	addReq := marginorder.NewAddOrderReqBuilder().
		SetClientOid(uuid.NewString()).
		SetSide("buy").
		SetSymbol("BTC-USDT").
		SetType("limit").
		SetPrice("10000").
		SetSize("0.001").
		SetAutoRepay(true).
		SetAutoBorrow(true).
		SetIsIsolated(true).
		Build()
	addResp, err := orderAPI.AddOrder(addReq, context.Background())
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, addResp.CommonResponse)
	if addResp.OrderId == "" {
		t.Fatalf("no order id")
	}

	queryResp, err := orderAPI.GetOrderByOrderId(
		marginorder.NewGetOrderByOrderIdReqBuilder().
			SetSymbol("BTC-USDT").
			SetOrderId(addResp.OrderId).
			Build(),
		context.Background(),
	)
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, queryResp.CommonResponse)
	if queryResp.Symbol == "" {
		t.Fatalf("query empty")
	}

	cancelResp, err := orderAPI.CancelOrderByOrderId(
		marginorder.NewCancelOrderByOrderIdReqBuilder().
			SetOrderId(addResp.OrderId).
			SetSymbol("BTC-USDT").
			Build(),
		context.Background(),
	)
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, cancelResp.CommonResponse)
	if cancelResp.OrderId == "" {
		t.Fatalf("cancel failed")
	}
}

func TestSpotService(t *testing.T) {
	rest := newClient()
	marketAPI := rest.GetSpotService().GetMarketAPI()
	statResp, err := marketAPI.Get24hrStats(
		spotmarket.NewGet24hrStatsReqBuilder().SetSymbol("BTC-USDT").Build(),
		context.Background(),
	)
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, statResp.CommonResponse)
	if statResp.Last == "" {
		t.Fatalf("stat empty")
	}

	orderAPI := rest.GetSpotService().GetOrderAPI()
	addReq := spotorder.NewAddOrderSyncReqBuilder().
		SetClientOid(uuid.NewString()).
		SetSide("buy").
		SetSymbol("BTC-USDT").
		SetType("limit").
		SetRemark("sdk_test").
		SetPrice("10000").
		SetSize("0.001").
		Build()
	addResp, err := orderAPI.AddOrderSync(addReq, context.Background())
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, addResp.CommonResponse)
	if addResp.OrderId == "" || addResp.OrderTime <= 0 {
		t.Fatalf("add order failed")
	}

	queryResp, err := orderAPI.GetOrderByOrderId(
		spotorder.NewGetOrderByOrderIdReqBuilder().
			SetSymbol("BTC-USDT").
			SetOrderId(addResp.OrderId).
			Build(),
		context.Background(),
	)
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, queryResp.CommonResponse)
	if queryResp.Symbol == "" {
		t.Fatalf("query empty")
	}

	cancelResp, err := orderAPI.CancelOrderByOrderId(
		spotorder.NewCancelOrderByOrderIdReqBuilder().
			SetOrderId(addResp.OrderId).
			SetSymbol("BTC-USDT").
			Build(),
		context.Background(),
	)
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, cancelResp.CommonResponse)
	if cancelResp.OrderId == "" {
		t.Fatalf("cancel failed")
	}
}

func TestFuturesService(t *testing.T) {
	rest := newClient()
	marketAPI := rest.GetFuturesService().GetMarketAPI()
	statResp, err := marketAPI.Get24hrStats(context.Background())
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, statResp.CommonResponse)
	orderAPI := rest.GetFuturesService().GetOrderAPI()
	addReq := futuresorder.NewAddOrderReqBuilder().
		SetClientOid(uuid.NewString()).
		SetSide("buy").
		SetSymbol("XBTUSDTM").
		SetLeverage(1).
		SetType("limit").
		SetRemark("sdk_test").
		SetMarginMode("CROSS").
		SetPrice("1").
		SetSize(1).
		Build()
	addResp, err := orderAPI.AddOrder(addReq, context.Background())
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, addResp.CommonResponse)
	if addResp.OrderId == "" {
		t.Fatalf("add order failed")
	}

	queryResp, err := orderAPI.GetOrderByOrderId(
		futuresorder.NewGetOrderByOrderIdReqBuilder().
			SetOrderId(addResp.OrderId).
			Build(),
		context.Background(),
	)
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, queryResp.CommonResponse)
	if queryResp.Symbol == "" {
		t.Fatalf("query empty")
	}

	cancelResp, err := orderAPI.CancelOrderById(
		futuresorder.NewCancelOrderByIdReqBuilder().
			SetOrderId(addResp.OrderId).
			Build(),
		context.Background(),
	)
	if err != nil {
		t.Fatal(err)
	}
	checkCommon(t, cancelResp.CommonResponse)
	if len(cancelResp.CancelledOrderIds) == 0 {
		t.Fatalf("cancel failed")
	}

	time.Sleep(1 * time.Second)
}
