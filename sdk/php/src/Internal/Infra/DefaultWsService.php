<?php


namespace KuCoin\UniversalSDK\Internal\Infra;


use KuCoin\UniversalSDK\Internal\Interfaces\WebSocketClient;
use KuCoin\UniversalSDK\Internal\Interfaces\WebSocketService;
use KuCoin\UniversalSDK\Model\ClientOption;
use KuCoin\UniversalSDK\Model\WebSocketClientOption;
use KuCoin\UniversalSDK\Model\WebSocketEvent;
use KuCoin\UniversalSDK\Model\WsMessage;
use React\Promise\PromiseInterface;

class DefaultWsService implements WebSocketService
{

    /** @var WebSocketClientOption */
    private $wsOption;

    /** @var bool */
    private $privateChannel;

    /** @var DefaultTransport */
    private $tokenTransport;

    /** @var TopicManager */
    private $topicManager;

    /** @var WebSocketClient */
    private $client;

    public function __construct(ClientOption $option,
                                string       $domainType,
                                bool         $privateChannel,
                                string       $versionString)
    {

        $this->tokenTransport = new DefaultTransport($option, $versionString);
        $this->client = new DefaultWebSocketClient(
            new DefaultWsTokenProvider($this->tokenTransport, $domainType, $privateChannel), $option);
        $this->client->on("message", function (WsMessage $message) {
            //
        });
        $this->client->on("event", function (WebsocketEvent $event) {
            $this->emitEvent($event, "", "");
        });
        $this->client->on("reconnected", function (WsMessage $message) {
            //
        });
    }

    private function emitEvent(WebSocketEvent $event, string $data, string $msg)
    {
        if ($this->wsOption->eventCallback) {
            call_user_func($this->wsOption->eventCallback, $event, $data, $msg);
        }
    }

    public function start(): PromiseInterface
    {
        $this->client->start();
    }

    public function stop(): PromiseInterface
    {
        $this->client->stop();
    }

    public function subscribe($topicPrefix, array $args, $callback): PromiseInterface
    {
        // TODO: Implement subscribe() method.
    }

    public function unsubscribe($id): PromiseInterface
    {
        // TODO: Implement unsubscribe() method.
    }
}
