<?php

use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\AllTickersEvent;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use KuCoin\UniversalSDK\Model\WebSocketClientOptionBuilder;
use React\EventLoop\Loop;

include '../vendor/autoload.php';

function wsExample()
{
    // Credentials & setup
    $key = getenv('API_KEY') ?: '';
    $secret = getenv('API_SECRET') ?: '';
    $passphrase = getenv('API_PASSPHRASE') ?: '';

    $brokerName = getenv('BROKER_NAME');
    $brokerKey = getenv('BROKER_KEY');
    $brokerPartner = getenv('BROKER_PARTNER');

    $httpTransportOption = (new TransportOptionBuilder())
        ->setKeepAlive(true)
        ->setMaxConnections(10)
        ->build();

    $websocketTransportOption = (new WebSocketClientOptionBuilder())->build();

    $clientOption = (new ClientOptionBuilder())
        ->setKey($key)
        ->setSecret($secret)
        ->setPassphrase($passphrase)
        ->setBrokerName($brokerName)
        ->setBrokerKey($brokerKey)
        ->setBrokerPartner($brokerPartner)
        ->setSpotEndpoint(Constants::GLOBAL_API_ENDPOINT)
        ->setFuturesEndpoint(Constants::GLOBAL_FUTURES_API_ENDPOINT)
        ->setBrokerEndpoint(Constants::GLOBAL_BROKER_API_ENDPOINT)
        ->setTransportOption($httpTransportOption)
        ->setWebSocketClientOption($websocketTransportOption)
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
                Logger::info("Ticker update", [
                    'topic' => $topic,
                    'subject' => $subject,
                    'bestBid' => $data->bestBid,
                    'bestAsk' => $data->bestAsk,
                ]);
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
        Logger::error("Failed to start", ['error' => $e->getMessage()]);
        $spotWs->stop();
    });

    // Run the event loop to process async tasks
    $loop->run();
}

if (php_sapi_name() === 'cli') {
    wsExample();
}
