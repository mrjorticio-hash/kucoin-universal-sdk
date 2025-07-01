package main

import (
	"context"
	"log"
	"os"
	"sync/atomic"
	"time"

	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/api"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"

	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/spot/market"
	spotmarket "github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/spot/market"
	spotpublic "github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/spot/spotpublic"
)

const (
	sleepSeconds = 5 * time.Second
	sleepForever = 12 * 30 * 24 * time.Hour
)

type Runner struct {
	wsMsgCnt  uint64
	wsErrCnt  uint64
	marketErr uint64
	wsService api.KucoinWSService
	marketAPI market.MarketAPI
}

func newRunner() *Runner {
	log.SetFlags(log.LstdFlags | log.Lmicroseconds)

	key := os.Getenv("API_KEY")
	secret := os.Getenv("API_SECRET")
	passphrase := os.Getenv("API_PASSPHRASE")

	httpOpt := types.NewTransportOptionBuilder().
		SetKeepAlive(true).
		SetMaxConnsPerHost(10).
		Build()

	wsOpt := types.NewWebSocketClientOptionBuilder().Build()

	clientOpt := types.NewClientOptionBuilder().
		WithKey(key).
		WithSecret(secret).
		WithPassphrase(passphrase).
		WithSpotEndpoint(types.GlobalApiEndpoint).
		WithFuturesEndpoint(types.GlobalFuturesApiEndpoint).
		WithBrokerEndpoint(types.GlobalBrokerApiEndpoint).
		WithTransportOption(httpOpt).
		WithWebSocketClientOption(wsOpt).
		Build()

	client := api.NewClient(clientOpt)
	return &Runner{
		wsService: client.WsService(),
		marketAPI: client.RestService().GetSpotService().GetMarketAPI(),
	}
}

func (r *Runner) wsStartStopLoop() {
	cb := func(topic, subject string, data *spotpublic.TickerEvent) error {
		_ = len(data.BestAsk)
		_ = len(data.BestBid)
		return nil
	}

	for {
		time.Sleep(sleepSeconds)
		spot := r.wsService.NewSpotPublicWS()
		if err := spot.Start(); err != nil {
			log.Println("WS STAR/STOP: [ERROR]", err)
			atomic.AddUint64(&r.wsErrCnt, 1)
			continue
		}
		subID, _ := spot.Ticker([]string{"ETH-USDT", "BTC-USDT"}, cb)
		time.Sleep(sleepSeconds)
		_ = spot.UnSubscribe(subID)
		if err := spot.Stop(); err != nil {
			log.Println("WS STAR/STOP: [ERROR]", err)
			atomic.AddUint64(&r.wsErrCnt, 1)
			continue
		}
		log.Println("WS STAR/STOP: [OK]")
	}
}

func (r *Runner) wsForever() {
	cb := func(topic, subject string, data *spotpublic.OrderbookLevel50Event) error {
		_ = len(data.Asks)
		atomic.AddUint64(&r.wsMsgCnt, 1)
		return nil
	}
	spot := r.wsService.NewSpotPublicWS()
	_ = spot.Start()
	_, err := spot.OrderbookLevel50([]string{"ETH-USDT", "BTC-USDT"}, cb)
	if err != nil {
		log.Println("WS: [Error]", err)
		panic(err)
	}
	log.Println("WS: [OK]")
	time.Sleep(sleepForever)
}

func (r *Runner) marketLoop() {
	for {
		time.Sleep(sleepSeconds)
		req := spotmarket.NewGetAllSymbolsReqBuilder().SetMarket("USDS").Build()
		resp, err := r.marketAPI.GetAllSymbols(req, context.Background())
		if err != nil {
			log.Println("MARKET API: [ERROR]", err)
			atomic.AddUint64(&r.marketErr, 1)
			continue
		}
		log.Printf("MARKET API: [OK] %d", len(resp.Data))
	}
}

func (r *Runner) statLoop() {
	for {
		time.Sleep(sleepSeconds)
		log.Printf("Stat, Market_ERROR:[%d], WS_SS_ERROR:[%d], WS_MESSAGE:[%d]",
			atomic.LoadUint64(&r.marketErr),
			atomic.LoadUint64(&r.wsErrCnt),
			atomic.LoadUint64(&r.wsMsgCnt))
	}
}

func (r *Runner) run() {
	go r.marketLoop()
	go r.wsForever()
	go r.wsStartStopLoop()
	go r.statLoop()
}

func main() {
	r := newRunner()
	r.run()
	time.Sleep(sleepForever)
}
