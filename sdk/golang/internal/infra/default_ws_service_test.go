package infra

import (
	"context"
	"fmt"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/types"
	"github.com/stretchr/testify/assert"
	"sync"
	"sync/atomic"
	"testing"
	"time"
)

type websocketClientMock struct {
	readMsg  chan *types.WsMessage
	resubMsg chan struct{}
	writeCnt int64
}

func (wc *websocketClientMock) Start() error {
	return nil
}

func (wc *websocketClientMock) Stop() error {
	return nil
}

func (wc *websocketClientMock) Reconnected() <-chan struct{} {
	return wc.resubMsg
}

func (wc *websocketClientMock) Write(ctx context.Context, message *types.WsMessage) <-chan error {
	c := make(chan error, 1)
	atomic.AddInt64(&wc.writeCnt, 1)
	go func() {
		<-time.After(100 * time.Millisecond)
		c <- nil
	}()
	return c
}

func (wc *websocketClientMock) Read() <-chan *types.WsMessage {
	if wc.readMsg == nil {
		return make(chan *types.WsMessage)
	}
	return wc.readMsg
}

type cbObj struct {
	cb func(message *types.WsMessage) error
}

func (o *cbObj) OnMessage(message *types.WsMessage) error {
	return o.cb(message)
}

func newEmptyCallback() *cbObj {
	return &cbObj{cb: func(message *types.WsMessage) error { return nil }}
}

func assertTopicPrefix(t *testing.T, wsService *DefaultWsService, count int) {
	m := wsService.topicManager
	c := 0
	m.topicPrefix.Range(func(k, v interface{}) bool {
		cbm, _ := v.(*CallbackManager)
		if !cbm.Empty() {
			c++
		}
		return true
	})
	assert.Equal(t, count, c)
}

func TestDefaultWsService_Subscribe_Timeout(t *testing.T) {
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Millisecond * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{}
	wsService.Start()
	defer wsService.Stop()

	_, err := wsService.Subscribe("/mock", []string{}, newEmptyCallback())

	assert.EqualError(t, err, "context deadline exceeded")
	assertTopicPrefix(t, wsService, 0)
}

func TestDefaultWsService_Subscribe_Ok(t *testing.T) {
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{}
	wsService.Start()
	defer wsService.Stop()

	_, err := wsService.Subscribe("/mock", []string{}, newEmptyCallback())

	assert.Nil(t, err)
}

func TestDefaultWsService_Subscribe_Multiple(t *testing.T) {
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{}
	wsService.Start()
	defer wsService.Stop()

	wg := sync.WaitGroup{}
	for i := 0; i < 10; i++ {
		wg.Add(1)
		go func(i int) {
			defer wg.Done()
			_, err := wsService.Subscribe(fmt.Sprintf("%s/%d", "/mock", i), []string{}, newEmptyCallback())
			assert.Nil(t, err)
		}(i)
	}

	wg.Wait()

	assertTopicPrefix(t, wsService, 10)
}

func TestDefaultWsService_Subscribe_Multiple2(t *testing.T) {
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{}
	wsService.Start()
	defer wsService.Stop()

	wg := sync.WaitGroup{}
	for i := 0; i < 10; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			_, err := wsService.Subscribe(fmt.Sprintf("%s/%d", "/mock", i), []string{fmt.Sprintf("i:%d", i)}, newEmptyCallback())
			assert.Nil(t, err)
		}()
	}

	wg.Wait()

	assertTopicPrefix(t, wsService, 10)
}

func TestDefaultWsService_Subscribe_SameTopic(t *testing.T) {
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{}
	wsService.Start()
	defer wsService.Stop()

	_, err := wsService.Subscribe("/mock", []string{}, newEmptyCallback())
	assert.Nil(t, err)

	_, err = wsService.Subscribe("/mock", []string{}, newEmptyCallback())
	assert.EqualError(t, err, "already subscribed")

	assertTopicPrefix(t, wsService, 1)
}

func TestDefaultWsService_Subscribe_SameTopic2(t *testing.T) {
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{}
	wsService.Start()
	defer wsService.Stop()

	wg := sync.WaitGroup{}
	for i := 0; i < 10; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			wsService.Subscribe("/mock", []string{"abc"}, newEmptyCallback())
		}()
	}
	wg.Wait()

	assertTopicPrefix(t, wsService, 1)
}

func TestDefaultWsService_Subscribe_SameTopic3(t *testing.T) {
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{}
	wsService.Start()
	defer wsService.Stop()

	_, err := wsService.Subscribe("/mock", []string{"a", "b"}, newEmptyCallback())
	assert.Nil(t, err)

	_, err = wsService.Subscribe("/mock", []string{"c"}, newEmptyCallback())
	assert.Nil(t, err)

	assertTopicPrefix(t, wsService, 1)

	cbm := wsService.topicManager.GetCallbackManager("/mock")
	assert.NotNil(t, cbm)
	assert.True(t, !cbm.Empty())

	assert.Equal(t, 3, len(cbm.topicCallbackMapping))
	assert.Equal(t, 2, len(cbm.idTopicMapping))
}

func TestDefaultWsService_Unsubscribe(t *testing.T) {
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{}
	wsService.Start()
	defer wsService.Stop()

	id1, err := wsService.Subscribe("/mock", []string{"a", "b"}, newEmptyCallback())
	assert.Nil(t, err)

	id2, err := wsService.Subscribe("/mock", []string{"c"}, newEmptyCallback())
	assert.Nil(t, err)

	err = wsService.Unsubscribe(id1)
	assert.Nil(t, err)
	err = wsService.Unsubscribe(id2)
	assert.Nil(t, err)

	cbm := wsService.topicManager.GetCallbackManager("/mock")
	assert.NotNil(t, cbm)
	assert.True(t, cbm.Empty())
}

func TestDefaultWsService_Unsubscribe2(t *testing.T) {
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{}
	wsService.Start()
	defer wsService.Stop()

	id1, err := wsService.Subscribe("/mock:all", []string{}, newEmptyCallback())
	assert.Nil(t, err)

	err = wsService.Unsubscribe(id1)
	assert.Nil(t, err)

	cbm := wsService.topicManager.GetCallbackManager("/mock:all")
	assert.NotNil(t, cbm)
	assert.True(t, cbm.Empty())
}

func TestDefaultWsService_Unsubscribe3(t *testing.T) {
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{}
	wsService.Start()
	defer wsService.Stop()

	id1, err := wsService.Subscribe("/mock:all", []string{}, newEmptyCallback())
	assert.Nil(t, err)

	id2, err := wsService.Subscribe("/mock", []string{"c"}, newEmptyCallback())
	assert.Nil(t, err)

	err = wsService.Unsubscribe(id1)
	assert.Nil(t, err)
	err = wsService.Unsubscribe(id2)
	assert.Nil(t, err)

	cbm := wsService.topicManager.GetCallbackManager("/mock")
	assert.NotNil(t, cbm)
	assert.True(t, cbm.Empty())

	cbm2 := wsService.topicManager.GetCallbackManager("/mock:all")
	assert.NotNil(t, cbm2)
	assert.True(t, cbm2.Empty())
}

func TestDefaultWsService_CB(t *testing.T) {
	readMsg := make(chan *types.WsMessage, 10)
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{readMsg: readMsg}
	wsService.Start()
	defer wsService.Stop()

	cnt := 0

	id1, err := wsService.Subscribe("/mock", []string{"a", "b"}, &cbObj{cb: func(message *types.WsMessage) error {
		cnt++
		return nil
	}})
	assert.Nil(t, err)

	for i := 0; i < 10; i++ {
		readMsg <- &types.WsMessage{
			Type:  types.Message,
			Topic: "/mock:a",
		}
		readMsg <- &types.WsMessage{
			Type:  types.Message,
			Topic: "/mock:b",
		}
	}
	<-time.After(time.Millisecond * 100)

	err = wsService.Unsubscribe(id1)
	assert.Nil(t, err)

	assert.Equal(t, 20, cnt)
}

func TestDefaultWsService_CB_2(t *testing.T) {
	readMsg := make(chan *types.WsMessage, 10)
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	wsService.client = &websocketClientMock{readMsg: readMsg}
	wsService.Start()
	defer wsService.Stop()

	cnt := 0
	cnt2 := 0

	id1, err := wsService.Subscribe("/mock", []string{"a", "b"}, &cbObj{cb: func(message *types.WsMessage) error {
		cnt++
		return nil
	}})
	assert.Nil(t, err)
	id2, err := wsService.Subscribe("/mock2", []string{"aa"}, &cbObj{cb: func(message *types.WsMessage) error {
		cnt2++
		return nil
	}})
	assert.Nil(t, err)

	for i := 0; i < 10; i++ {
		readMsg <- &types.WsMessage{
			Type:  types.Message,
			Topic: "/mock:a",
		}
		readMsg <- &types.WsMessage{
			Type:  types.Message,
			Topic: "/mock:b",
		}

		readMsg <- &types.WsMessage{
			Type:  types.Message,
			Topic: "/mock2:aa",
		}
	}
	<-time.After(time.Millisecond * 100)

	err = wsService.Unsubscribe(id1)
	assert.Nil(t, err)

	err = wsService.Unsubscribe(id2)
	assert.Nil(t, err)

	assert.Equal(t, 20, cnt)
	assert.Equal(t, 10, cnt2)
}

func TestDefaultWsService_ReSubscribe(t *testing.T) {
	websocketOption := types.NewWebSocketClientOptionBuilder().WithWriteTimeout(time.Second * 10).Build()
	clientOption := types.NewClientOptionBuilder().WithWebSocketClientOption(websocketOption).Build()
	wsService, _ := NewDefaultWsService(clientOption, "", true, "").(*DefaultWsService)
	mock := &websocketClientMock{}
	mock.resubMsg = make(chan struct{}, 1)
	wsService.client = mock
	wsService.Start()
	defer wsService.Stop()

	_, err := wsService.Subscribe("/mock", []string{"a"}, newEmptyCallback())
	assert.Nil(t, err)
	_, err = wsService.Subscribe("/mock", []string{"b", "c"}, newEmptyCallback())
	assert.Nil(t, err)
	_, err = wsService.Subscribe("/mock2", []string{"b", "c"}, newEmptyCallback())
	assert.Nil(t, err)
	_, err = wsService.Subscribe("/mock3:all", []string{}, newEmptyCallback())
	assert.Nil(t, err)

	mock.resubMsg <- struct{}{}
	<-time.After(time.Millisecond * 2000)
}
