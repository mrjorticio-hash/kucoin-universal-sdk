package spot_test

import (
	"encoding/json"
	"fmt"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/api"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/common/logger"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/spot/spotprivate"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"log"
	"os"
	"sync"
	"testing"
)

var spotPrivateApi spotprivate.SpotPrivateWS

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

	spotPrivateApi = client.WsService().NewSpotPrivateWS()
	err := spotPrivateApi.Start()
	if err != nil {
		panic(err)
	}
}
func TestPrivateAccount(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPrivateApi.Account(func(topic string, subject string, data *spotprivate.AccountEvent) error {
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

	err = spotPrivateApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPrivateOrderV1(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPrivateApi.OrderV1(func(topic string, subject string, data *spotprivate.OrderV1Event) error {
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

	err = spotPrivateApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPrivateOrderV2(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPrivateApi.OrderV2(func(topic string, subject string, data *spotprivate.OrderV2Event) error {
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

	err = spotPrivateApi.UnSubscribe(id)
	assert.Nil(t, err)
}

func TestPrivateStopOrder(t *testing.T) {
	wg := sync.WaitGroup{}
	wg.Add(1)

	i := 0
	id, err := spotPrivateApi.StopOrder(func(topic string, subject string, data *spotprivate.StopOrderEvent) error {
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

	err = spotPrivateApi.UnSubscribe(id)
	assert.Nil(t, err)
}
