package main

import (
	"context"
	"log"
	"os"

	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/api"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"

	futurespublic "github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/futures/futurespublic"
	spotmarket "github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/spot/market"
	spotpublic "github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/spot/spotpublic"
)

func main() {
	log.SetFlags(log.LstdFlags | log.Lmicroseconds)

	key := os.Getenv("API_KEY")
	secret := os.Getenv("API_SECRET")
	passphrase := os.Getenv("API_PASSPHRASE")

	wsOpt := types.NewWebSocketClientOptionBuilder().Build()
	transOpt := types.NewTransportOptionBuilder().Build()

	clientOpt := types.NewClientOptionBuilder().
		WithKey(key).
		WithSecret(secret).
		WithPassphrase(passphrase).
		WithWebSocketClientOption(wsOpt).
		WithTransportOption(transOpt).
		WithSpotEndpoint(types.GlobalApiEndpoint).
		WithFuturesEndpoint(types.GlobalFuturesApiEndpoint).
		WithBrokerEndpoint(types.GlobalBrokerApiEndpoint).
		Build()

	client := api.NewClient(clientOpt)
	rest := client.RestService()
	wsSvc := client.WsService()

	symbols, err := getSpotSymbols(rest)
	if err != nil {
		log.Fatalf("get symbols error: %v", err)
	}

	spotWsExample(wsSvc.NewSpotPublicWS(), symbols)
	futuresWsExample(wsSvc.NewFuturesPublicWS())

	log.Println("Total subscribe: 103")
	select {}
}

func getSpotSymbols(rest api.KucoinRestService) ([]string, error) {
	resp, err := rest.GetSpotService().GetMarketAPI().GetAllSymbols(
		spotmarket.NewGetAllSymbolsReqBuilder().SetMarket("USDS").Build(),
		context.Background(),
	)
	if err != nil {
		return nil, err
	}
	symbols := make([]string, 0, 100)
	for _, d := range resp.Data {
		symbols = append(symbols, d.Symbol)
		if len(symbols) == 100 {
			break
		}
	}
	return symbols, nil
}

func tradeCallbackFunc(topic string, subject string, data *spotpublic.TradeEvent) error {
	return nil
}

func tickerCallbackFunc(topic string, subject string, data *spotpublic.TickerEvent) error {
	return nil
}

func futuresTickerv2CallbackFunc(topic string, subject string, data *futurespublic.TickerV2Event) error {
	return nil
}

func futuresTickerv1CallbackFunc(topic string, subject string, data *futurespublic.TickerV1Event) error {
	return nil
}

func spotWsExample(ws spotpublic.SpotPublicWS, symbols []string) {
	ws.Start()
	for _, s := range symbols {
		_, err := ws.Trade([]string{s}, tradeCallbackFunc)
		if err != nil {
			panic(err)
		}
	}
	_, err := ws.Ticker([]string{"BTC-USDT", "ETH-USDT"}, tickerCallbackFunc)
	if err != nil {
		panic(err)
	}
	log.Println("Spot subscribe [OK]")
}

func futuresWsExample(ws futurespublic.FuturesPublicWS) {
	ws.Start()
	_, err := ws.TickerV2("XBTUSDTM", futuresTickerv2CallbackFunc)
	if err != nil {
		panic(err)
	}
	_, err = ws.TickerV1("XBTUSDTM", futuresTickerv1CallbackFunc)
	if err != nil {
		panic(err)
	}
	log.Println("Futures subscribe [OK]")
}
