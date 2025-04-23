<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

use Evenement\EventEmitterTrait;
use Exception;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Internal\Interfaces\WebSocketClient;
use KuCoin\UniversalSDK\Internal\Interfaces\WsTokenProvider;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\WebSocketClientOption;
use KuCoin\UniversalSDK\Model\WsMessage;
use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use RuntimeException;
use function React\Promise\reject;

class DefaultWebSocketClient implements WebSocketClient
{
    use EventEmitterTrait;

    private $loop;
    private $connector;
    private $conn;
    private $url;
    /**
     * @var WsTokenProvider $tokenProvider
     */
    private $tokenProvider;
    /**@var WebSocketClientOption $options */
    private $options;
    private $connected = false;
    private $reconnectAttempts = 0;
    private $ackMap = [];
    private $serializer;

    private $timer;

    public function __construct($tokenProvider, $options)
    {
        $this->loop = Loop::get();
        $this->connector = new Connector($this->loop);
        $this->tokenProvider = $tokenProvider;
        $this->options = $options;
        $this->serializer = SerializerBuilder::create()->addDefaultHandlers()
            ->configureHandlers(function ($handlerRegistry) {
                $handlerRegistry->registerSubscribingHandler(new JsonSerializedHandler());
            })->build();
    }

    public function start(): PromiseInterface
    {
        return $this->dial();
    }

    private function dial(): PromiseInterface
    {
        $tokenInfo = $this->randomEndpoint($this->tokenProvider->getToken());
        $this->url = $tokenInfo->endpoint . '?token=' . $tokenInfo->token;

        $deferred = new Deferred();
        $dailTimer = $this->loop->addTimer($this->options->dialTimeout, function () use ($deferred) {
            $deferred->reject(new RuntimeException("wait server response timed out"));
        });
        $this->connector->__invoke($this->url)->then(
            function (WebSocket $conn) use ($deferred, $dailTimer) {
                $this->conn = $conn;
                $this->connected = true;
                $this->reconnectAttempts = 0;

                $conn->once('message', function ($message) use ($conn, $deferred, $dailTimer) {
                    $this->loop->cancelTimer($dailTimer);
                    if ($this->onHelloMessage($message)) {
                        $conn->on('message', function ($msg) {
                            try {
                                $this->onMessage($msg);
                            } catch (Exception $exception) {
                                error_log('[WebSocketClient] onMessage error: ' . $exception->getMessage());
                            }
                        });
                        $deferred->resolve($message);
                    } else {
                        $deferred->reject(new RuntimeException("Handshake failed: unexpected hello message: " . $message));
                    }
                });

                $conn->on('close', function ($code = null, $reason = null) {
                    $this->onClose($code, $reason);
                });
            },
            function (Exception $e) use ($deferred, $dailTimer) {
                $this->loop->cancelTimer($dailTimer);
                $deferred->reject($e);
                $this->onError($e);
            }
        );
        return $deferred->promise();
    }

    private function onOpen()
    {
        echo "Connected to WebSocket server.\n";
        $this->startHeartbeat();
    }


    private function onHelloMessage($msg): bool
    {
        $data = WsMessage::jsonDeserialize($msg, $this->serializer);

        if ($data->type === Constants::WS_MESSAGE_TYPE_WELCOME) {
            return true;
        } else {
            return false;
        }
    }

    private function onMessage($msg)
    {
        echo "Received message: {$msg}\n";

        $data = WsMessage::jsonDeserialize($msg, $this->serializer);

        switch ($data->type) {
            case Constants::WS_MESSAGE_TYPE_WELCOME :
                echo "Welcome message received.\n";
                break;
            case Constants::WS_MESSAGE_TYPE_PONG:
            case Constants::WS_MESSAGE_TYPE_ACK:
                $this->handleAck($data->id, null);
                break;
            case Constants::WS_MESSAGE_TYPE_ERROR:
                $this->handleAck($data->id, new Exception($data->rawData ?? 'unknown error'));
                break;
            case Constants::WS_MESSAGE_TYPE_MESSAGE:
                $this->emit('message', [$data]);
                break;
            default:
                echo "Unknown message type: " . $data->type . "\n";
        }
    }

    private function onClose($code, $reason)
    {
        echo "Connection closed ({$code} - {$reason}).\n";
        $this->connected = false;
        $this->attemptReconnect();
    }

    private function onError($e)
    {
        echo "Connection error: {$e->getMessage()}\n";
        $this->connected = false;
        $this->attemptReconnect();
    }

    private function attemptReconnect()
    {
        if ($this->reconnectAttempts < $this->options->reconnectAttempts) {
            $this->reconnectAttempts++;
            echo "Reconnecting in {$this->options->reconnectInterval} seconds...\n";
            $this->loop->addTimer($this->options->reconnectInterval, function () {
                $this->dail();
            });
        } else {
            echo "Max reconnect attempts reached. Giving up.\n";
        }
    }

    private function startHeartbeat()
    {
        $this->timer = $this->loop->addPeriodicTimer($this->options->pingInterval, function () {
            if ($this->connected && $this->conn) {
                $pingMessage = new WsMessage('ping', []);
                $this->write($pingMessage, $this->options->writeTimeout)
                    ->then(function () {
                        echo "Ping acknowledged.\n";
                    })
                    ->otherwise(function ($e) {
                        echo "Ping failed: {$e->getMessage()}\n";
                    });
            }
        });
    }

    public function stop(): PromiseInterface
    {
        if ($this->timer) {
            $this->timer->cancel();
        }
        if ($this->conn) {
            $this->conn->close();
        }
        $this->connected = false;
        echo "WebSocket client stopped.\n";
    }

    public function write(WsMessage $message, int $timeout): PromiseInterface
    {
        if ($this->connected && $this->conn) {
            $deferred = new Deferred();
            $this->ackMap[$message->id] = $deferred;

            $this->loop->addTimer($timeout / 1000, function () use ($message, $deferred) {
                if (isset($this->ackMap[$message->id])) {
                    unset($this->ackMap[$message->id]);
                    $deferred->reject(new Exception("Ack timeout for {$message->id}"));
                }
            });

            try {
                $this->conn->send($message->jsonSerialize($this->serializer));
            } catch (Exception $e) {
                if (isset($this->ackMap[$message->id])) {
                    unset($this->ackMap[$message->id]);
                }
                $deferred->reject($e);
            }

            return $deferred->promise();
        } else {
            return reject(new Exception("Not connected"));
        }
    }

    private function handleAck($id, $err)
    {
        if (isset($this->ackMap[$id])) {
            $deferred = $this->ackMap[$id];
            unset($this->ackMap[$id]);
            if ($err) {
                $deferred->reject($err);
            } else {
                $deferred->resolve();
            }
        } else {
            echo "Unknown ack id: $id\n";
        }
    }

    private function randomEndpoint(array $tokens)
    {
        if (empty($tokens)) {
            throw new RuntimeException('Tokens list is empty');
        }

        return $tokens[array_rand($tokens)];
    }
}