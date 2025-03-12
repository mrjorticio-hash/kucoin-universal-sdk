package spot_test

import (
	"encoding/json"
	"fmt"
	"log"
	"os"
	"sync"
	"testing"

	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/api"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/common/logger"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/spot/spotpublic"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
)

var spotPublicApi spotpublic.SpotPublicWS

func init() {
	logger.SetLogger(
		&logger.DefaultLogger{
			Logger:     log.New(os.Stdout, "", 0),
			Level:      logger.TRACE,
			Formatter:  "%s [%s] [%s+%d] [%s] %s",
			TimeFormat: "2006-01-02 15:04:05.000",
		})

	// get api key from env
	key := os.Getenv("API_KEY")
	secret := os.Getenv("API_SECRET")
	passphrase := os.Getenv("API_PASSPHRASE")

	// set callback worker number
	wsOption := types.NewWebSocketClientOptionBuilder().
		Build()

	// create a client with transport options
	option := types.NewClientOptionBuilder().
		WithKey(key).
		WithSecret(secret).
		WithPassphrase(passphrase).
		WithSpotEndpoint(types.GlobalApiEndpoint).
		WithFuturesEndpoint(types.GlobalFuturesApiEndpoint).
		WithBrokerEndpoint(types.GlobalBrokerApiEndpoint).
		WithWebSocketClientOption(wsOption).
		Build()

	client := api.NewClient(option)

	spotPublicApi = client.WsService().NewSpotPublicWS()
	err := spotPublicApi.Start()
	if err != nil {
		panic(err)
	}
}

func TestPublicAllTickers(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.AllTickers(func(topic string, subject string, data *spotpublic.AllTickersEvent) error {
		assert.NotNil(t, data)
		i++
		if i == 1 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPublicCallAuctionInfo(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.CallAuctionInfo("PAWS-USDT", func(topic string, subject string, data *spotpublic.CallAuctionInfoEvent) error {
		assert.NotNil(t, data)
		i++
		if i == 1 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPublicCallAuctionOrderbookLevel50(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.CallAuctionOrderbookLevel50("PAWS-USDT", func(topic string, subject string, data *spotpublic.CallAuctionOrderbookLevel50Event) error {
		assert.NotNil(t, data)
		i++
		if i == 1 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPublicKlines(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.Klines("BTC-USDT", "1min", func(topic string, subject string, data *spotpublic.KlinesEvent) error {
		assert.NotNil(t, data)
		i++
		if i == 10 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPublicMarketSnapshot(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.MarketSnapshot("BTC-USDT", func(topic string, subject string, data *spotpublic.MarketSnapshotEvent) error {
		assert.NotNil(t, data)
		i++
		if i == 10 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPublicOrderbookIncrement(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.OrderbookIncrement([]string{"BTC-USDT", "DOGE-USDT"}, func(topic string, subject string, data *spotpublic.OrderbookIncrementEvent) error {
		assert.NotNil(t, data)
		i++
		if i == 10 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPublicOrderbookLevel1(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.OrderbookLevel1([]string{"BTC-USDT", "DOGE-USDT"}, func(topic string, subject string, data *spotpublic.OrderbookLevel1Event) error {
		assert.NotNil(t, data)
		i++
		if i == 10 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPublicOrderbookLevel50(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.OrderbookLevel50([]string{"BTC-USDT", "DOGE-USDT"}, func(topic string, subject string, data *spotpublic.OrderbookLevel50Event) error {
		assert.NotNil(t, data)
		i++
		if i == 10 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPublicOrderbookLevel5(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.OrderbookLevel5([]string{"BTC-USDT", "DOGE-USDT"}, func(topic string, subject string, data *spotpublic.OrderbookLevel5Event) error {
		assert.NotNil(t, data)
		i++
		if i == 10 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPublicSymbolSnapshot(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.SymbolSnapshot("BTC-USDT", func(topic string, subject string, data *spotpublic.SymbolSnapshotEvent) error {
		assert.NotNil(t, data)
		i++
		if i == 10 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPublicTicker(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.Ticker([]string{"BTC-USDT", "ETH-USDT"}, func(topic string, subject string, data *spotpublic.TickerEvent) error {
		assert.NotNil(t, data)
		i++
		if i == 10 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPublicTrade(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPublicApi.Trade([]string{"BTC-USDT", "ETH-USDT"}, func(topic string, subject string, data *spotpublic.TradeEvent) error {
		assert.NotNil(t, data)
		i++
		if i == 10 {
			wg.Done()
		}
		str, _ := json.Marshal(data)
		fmt.Println(topic, subject, string(str))
		return nil
	})
	assert.Nil(t, err)
	wg.Wait()

	err = spotPublicApi.UnSubscribe(id)
	assert.Nil(t, err)
}
