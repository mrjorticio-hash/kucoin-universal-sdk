<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Internal\Interfaces\Transport;
use KuCoin\UniversalSDK\Internal\Interfaces\WebSocketClient;
use KuCoin\UniversalSDK\Internal\Interfaces\WebSocketMessageCallback;
use KuCoin\UniversalSDK\Internal\Interfaces\WebSocketService;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Internal\Utils\SubInfo;
use KuCoin\UniversalSDK\Model\ClientOption;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\WebSocketClientOption;
use KuCoin\UniversalSDK\Model\WebSocketEvent;
use KuCoin\UniversalSDK\Model\WsMessage;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use RuntimeException;

class DefaultWsService implements WebSocketService
{
    /**
     * @var WebSocketClientOption $wsOption
     */
    private $wsOption;
    /**
     * @var bool $privateChannel
     */
    private $privateChannel;
    /**
     * @var Transport $tokenTransport
     */
    private $tokenTransport;
    /**
     * @var TopicManager $topicManager
     */
    private $topicManager;
    /**
     * @var WebSocketClient $client
     */
    private $client;
    /**
     * @var Serializer $serializer
     */
    private $serializer;

    public function __construct(ClientOption $option, $loop, $domainType, $privateChannel, $versionString)
    {
        if (!$option->websocketClientOption) {
            throw new RuntimeException("websocketClientOption is undefined");
        }

        $this->wsOption = $option->websocketClientOption;
        $this->privateChannel = $privateChannel;
        $this->tokenTransport = new DefaultTransport($option, $versionString);
        $this->topicManager = new TopicManager();
        $this->serializer = SerializerBuilder::create()
            ->addDefaultHandlers()
            ->configureHandlers(function ($handlerRegistry) {
                $handlerRegistry->registerSubscribingHandler(
                    new JsonSerializedHandler()
                );
            })
            ->build();

        $this->client = new DefaultWebSocketClient(
            new DefaultWsTokenProvider($this->tokenTransport, $domainType, $privateChannel),
            $this->wsOption, $loop
        );

        $this->client->on("event", function (WebSocketEvent $event, $msg) {
            $this->emitEvent($event, $msg);
        });

        $this->client->on("message", function (WsMessage $message) {
            $this->processMessages($message);
        });

        $this->client->on("reconnected", function () {
            $this->recovery();
        });
    }

    private function emitEvent($event, $msg)
    {
        if (isset($this->wsOption->eventCallback)) {
            try {
                call_user_func($this->wsOption->eventCallback, $event, $msg);
            } catch (\Throwable $e) {
                error_log('event callback error: ' . $e->getMessage());
            }
        }
    }

    public function start(): PromiseInterface
    {
        return $this->client->start();
    }

    public function stop(): PromiseInterface
    {
        return $this->client->stop()->then(function () {
            $this->tokenTransport->close();
            $deferred = new Deferred();
            $deferred->resolve('');
            return $deferred->promise();
        });
    }

    public function subscribe(string $prefix, array $args, WebSocketMessageCallback $callback): PromiseInterface
    {
        $deferred = new Deferred();

        $subInfo = new SubInfo($prefix, $args, $callback);
        $subId = $subInfo->toId();
        $callbackManager = $this->topicManager->getCallbackManager($prefix);

        if (!$callbackManager->add($subInfo)) {
            $deferred->reject(new RuntimeException("Already subscribed: $subId"));
            return $deferred->promise();
        }

        $subEvent = new WsMessage();
        $subEvent->id = $subId;
        $subEvent->type = Constants::WS_MESSAGE_TYPE_SUBSCRIBE;
        $subEvent->topic = $subInfo->subTopic();
        $subEvent->privateChannel = $this->privateChannel;
        $subEvent->response = true;

        $this->client->write($subEvent, $this->wsOption->writeTimeout)->then(function () use ($deferred, $subId) {
            $deferred->resolve($subId);
        }, function ($err) use ($deferred, $callbackManager, $subId, $prefix) {
            $callbackManager->remove($subId);
            $deferred->reject($err);
        });

        return $deferred->promise();
    }

    public function unsubscribe($id): PromiseInterface
    {
        $deferred = new Deferred();

        $subInfo = SubInfo::fromId($id);
        $callbackManager = $this->topicManager->getCallbackManager($subInfo->prefix);

        $unsubEvent = new WsMessage();
        $unsubEvent->id = uniqid('', true);
        $unsubEvent->type = Constants::WS_MESSAGE_TYPE_UNSUBSCRIBE;
        $unsubEvent->topic = $subInfo->subTopic();
        $unsubEvent->privateChannel = $this->privateChannel;
        $unsubEvent->response = true;

        $this->client->write($unsubEvent, $this->wsOption->writeTimeout)->then(function () use ($callbackManager, $id, $deferred) {
            $callbackManager->remove($id);
            $deferred->resolve();
        }, function ($e) use ($deferred) {
            $deferred->reject($e);
        });

        return $deferred->promise();
    }

    private function processMessages(WsMessage $message)
    {
        $callbackManager = $this->topicManager->getCallbackManager($message->topic);

        $callback = $callbackManager->get($message->topic);
        if (!$callback) {
            return;
        }

        try {
            $callback->onMessage($message, $this->serializer);
        } catch (\Throwable $e) {
            $this->emitEvent(WebSocketEvent::EVENT_CALLBACK_ERROR, (string)$e);
        }
    }

    private function recovery()
    {
        $oldTopicManager = $this->topicManager;
        $this->topicManager = new TopicManager();

        $oldTopicManager->range(function ($key, $callbackManager) {
            foreach ($callbackManager->getSubInfo() as $sub) {
                if ($sub->callback) {
                    $this->subscribe($sub->prefix, $sub->args, $sub->callback)->then(function ($id) {
                        $this->emitEvent(WebSocketEvent::EVENT_RE_SUBSCRIBE_OK, $id);
                    }, function ($err) use ($sub) {
                        $this->emitEvent(WebSocketEvent::EVENT_RE_SUBSCRIBE_ERROR, (string)$err);
                    });
                }
            }
            return true;
        });
    }
}
