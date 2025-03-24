package infra

import (
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/internal/interfaces"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/internal/util"
	"github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/common/logger"
	"strings"
	"sync"
)

type Callback struct {
	callback interfaces.WebSocketMessageCallback // callback
	id       string                              // sub id
	topic    string                              // real topic
}

type CallbackManager struct {
	idTopicMapping       map[string]map[string]bool // id : topics
	topicCallbackMapping map[string]*Callback       // topic : callback
	lock                 sync.RWMutex               // lock
	topicPrefix          string                     // topic prefix for pattern matching
}

func NewCallbackManager(topicPrefix string) *CallbackManager {
	return &CallbackManager{
		idTopicMapping:       map[string]map[string]bool{},
		topicCallbackMapping: map[string]*Callback{},
		lock:                 sync.RWMutex{},
		topicPrefix:          topicPrefix,
	}
}

func (s *CallbackManager) Empty() bool {
	s.lock.Lock()
	defer s.lock.Unlock()
	return len(s.idTopicMapping) == 0 && len(s.topicCallbackMapping) == 0
}

func (s *CallbackManager) GetSubInfo() []*util.SubInfo {
	s.lock.Lock()
	defer s.lock.Unlock()

	subInfo := make([]*util.SubInfo, 0)
	for _, topics := range s.idTopicMapping {
		info := &util.SubInfo{
			Prefix: s.topicPrefix,
			Args:   make([]string, 0),
		}
		for topic, _ := range topics {
			parts := strings.Split(topic, ":")
			if len(parts) == 2 && parts[1] != "all" {
				info.Args = append(info.Args, parts[1])
			}
			info.Callback = s.topicCallbackMapping[topic].callback
		}

		subInfo = append(subInfo, info)
	}
	return subInfo
}

func (s *CallbackManager) Add(subInfo *util.SubInfo) (created bool) {
	s.lock.Lock()
	defer s.lock.Unlock()

	id := subInfo.ToId()

	_, exist := s.idTopicMapping[id]
	if exist {
		logger.GetLogger().Tracef("callback already exist, id:[%s]", id)
		return false
	}

	topicMap := make(map[string]bool)

	for _, topic := range subInfo.Topics() {

		_, exist := s.topicCallbackMapping[topic]
		if exist {
			logger.GetLogger().Tracef("topic already exist, topic:[%v]", topic)
			continue
		}

		topicMap[topic] = true

		s.topicCallbackMapping[topic] = &Callback{
			callback: subInfo.Callback,
			id:       id,
			topic:    topic,
		}
	}

	s.idTopicMapping[id] = topicMap
	logger.GetLogger().Tracef("id:[%s], topicMap:[%v]", id, topicMap)
	return true
}

func (s *CallbackManager) Remove(id string) {
	s.lock.Lock()
	defer s.lock.Unlock()

	topicMap, exist := s.idTopicMapping[id]
	if !exist {
		logger.GetLogger().Tracef("no topic found for specify id:[%s]", id)
		return
	}

	delete(s.idTopicMapping, id)
	for topic, _ := range topicMap {
		delete(s.topicCallbackMapping, topic)
	}
	logger.GetLogger().Tracef("remove topic:[%v]", topicMap)
}

func (s *CallbackManager) Get(topic string) interfaces.WebSocketMessageCallback {
	s.lock.RLock()
	defer s.lock.RUnlock()

	cb := s.topicCallbackMapping[topic]
	if cb == nil {
		return nil
	}

	return cb.callback
}

type TopicManager struct {
	topicPrefix sync.Map
}

func NewTopicManager() *TopicManager {
	return &TopicManager{
		topicPrefix: sync.Map{},
	}
}

func (m *TopicManager) GetCallbackManager(topic string) *CallbackManager {
	parts := strings.Split(topic, ":")
	prefix := topic
	if len(parts) == 2 && parts[1] != "all" {
		prefix = parts[0]
	}

	ret, _ := m.topicPrefix.LoadOrStore(prefix, NewCallbackManager(topic))
	return ret.(*CallbackManager)
}

func (m *TopicManager) Range(f func(key, value any) bool) {
	m.topicPrefix.Range(f)
}
