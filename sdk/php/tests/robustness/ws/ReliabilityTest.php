<?php


use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\AllTickersEvent;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use KuCoin\UniversalSDK\Model\WebSocketClientOptionBuilder;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;


function waitFor(float $seconds, $result): PromiseInterface
{
    $deferred = new Deferred();

    Loop::get()->addTimer($seconds, function () use ($result, $deferred) {
        $deferred->resolve($result);
    });

    return $deferred->promise();
}

class ReliabilityTest extends TestCase
{
    public function testEventCallback()
    {
        $key = getenv('API_KEY') ?: '';
        $secret = getenv('API_SECRET') ?: '';
        $passphrase = getenv('API_PASSPHRASE') ?: '';

        $httpTransportOption = (new TransportOptionBuilder())
            ->setKeepAlive(true)
            ->setTotalTimeout(1)
            ->setRetryDelay(0)
            ->setMaxRetries(1)
            ->build();


        $eventCounter = 0;
        $websocketOption = (new WebSocketClientOptionBuilder())->setEventCallback(function (string $eventType, string $eventMessage) use (&$eventCounter) {
            Logger::info("event called", ["eventType" => $eventType, "eventMessage" => $eventMessage]);
            $eventCounter++;
        })->build();

        // Create a client using the specified options
        $clientOption = (new ClientOptionBuilder())
            ->setKey($key)
            ->setSecret($secret)
            ->setPassphrase($passphrase)
            ->setSpotEndpoint(Constants::GLOBAL_API_ENDPOINT)
            ->setFuturesEndpoint(Constants::GLOBAL_FUTURES_API_ENDPOINT)
            ->setBrokerEndpoint(Constants::GLOBAL_BROKER_API_ENDPOINT)
            ->setTransportOption($httpTransportOption)
            ->setWebSocketClientOption($websocketOption)
            ->build();

        // Create or get the global event loop
        $loop = Loop::get();

        $client = new DefaultClient($clientOption, $loop);
        $spotWs = $client->wsService()->newSpotPublicWS();

        // Start connection
        $spotWs->start()->then(function () use ($spotWs, $loop) {
            Logger::info("WebSocket started");

            // Subscribe to allTickers
            return $spotWs->allTickers(
            // Called when data is received
                function (string $topic, string $subject, AllTickersEvent $data) {

                },
                // Called when subscription is successful
                function (string $id) use ($spotWs, $loop) {
                    Logger::info("Subscribed with ID: $id");

                    // Schedule unsubscribe and shutdown after 5 seconds
                    $loop->addTimer(5, function () use ($id, $spotWs) {
                        Logger::info("Unsubscribing...");
                        $spotWs->unSubscribe($id)->finally(function () use ($spotWs) {
                            $spotWs->stop();
                        });
                    });
                },
                // Called when subscription fails
                function (Exception $e) use ($spotWs) {
                    Logger::error("Subscription failed", ['error' => $e->getMessage()]);
                    $spotWs->stop();
                }
            );
        })->catch(function (Exception $e) use ($spotWs) {
            self::fail($e->getMessage());
            $spotWs->stop();
        });

        // Run the event loop to process async tasks
        $loop->run();

        self::assertTrue($eventCounter > 0);
    }


    public function getClient()
    {
        $key = getenv('API_KEY') ?: '';
        $secret = getenv('API_SECRET') ?: '';
        $passphrase = getenv('API_PASSPHRASE') ?: '';

        $httpTransportOption = (new TransportOptionBuilder())
            ->setKeepAlive(true)
            ->setTotalTimeout(1)
            ->setRetryDelay(0)
            ->setMaxRetries(1)
            ->build();


        $websocketOption = (new WebSocketClientOptionBuilder())->setEventCallback(function (string $eventType, string $eventMessage) use (&$eventCounter) {
            Logger::info("event called", ["eventType" => $eventType, "eventMessage" => $eventMessage]);
        })->build();

        // Create a client using the specified options
        $clientOption = (new ClientOptionBuilder())
            ->setKey($key)
            ->setSecret($secret)
            ->setPassphrase($passphrase)
            ->setSpotEndpoint(Constants::GLOBAL_API_ENDPOINT)
            ->setFuturesEndpoint(Constants::GLOBAL_FUTURES_API_ENDPOINT)
            ->setBrokerEndpoint(Constants::GLOBAL_BROKER_API_ENDPOINT)
            ->setTransportOption($httpTransportOption)
            ->setWebSocketClientOption($websocketOption)
            ->build();

        $loop = Loop::get();

        $client = new DefaultClient($clientOption, $loop);
        $spotWs = $client->wsService()->newSpotPublicWS();

        return $spotWs;
    }

    public function testReconnect()
    {
        $loop = Loop::get();

        $spotWs = self::getClient();

        // Start connection
        $spotWs->start()->then(function () use ($spotWs, $loop) {
            Logger::info("WebSocket started");

            // Subscribe to allTickers
            return $spotWs->allTickers(
            // Called when data is received
                function (string $topic, string $subject, AllTickersEvent $data) {
                    // pass
                },
                null,
                // Called when subscription fails
                null,
            );
        })->then(function () {
            return waitFor(3600, []);
        })->catch(function (Exception $e) {
            self::fail($e->getMessage());
        })->finally(function () use ($spotWs, $loop) {
            $spotWs->stop();
        });

        // Run the event loop to process async tasks
        $loop->run();
    }
}
