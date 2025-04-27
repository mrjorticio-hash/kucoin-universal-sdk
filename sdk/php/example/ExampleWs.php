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
    $key = getenv('API_KEY') ?: '';
    $secret = getenv('API_SECRET') ?: '';
    $passphrase = getenv('API_PASSPHRASE') ?: '';

    $brokerName = getenv('BROKER_NAME');
    $brokerKey = getenv('BROKER_KEY');
    $brokerPartner = getenv('BROKER_PARTNER');

    $httpTransportOption = (new TransportOptionBuilder())
        ->setKeepAlive(true)
        ->setMaxPoolSize(10)
        ->setMaxConnectionPerPool(10)
        ->build();

    $websocketTransportOption = (new WebSocketClientOptionBuilder())
        ->build();

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

    $loop = Loop::get();

    $client = new DefaultClient($clientOption, $loop);
    $wsService = $client->wsService();
    $spotWs = $wsService->newSpotPublicWS();

    $spotWs->start()->then(function () use ($spotWs, $loop, $wsService) {
        Logger::info("Spot WebSocket started");

        $spotWs->allTickers(function (string $topic, string $subject, AllTickersEvent $data) {
            Logger::info("Ticker update", [
                "topic" => $topic,
                "subject" => $subject,
                "bestBid" => $data->bestBid,
                "bestAsk" => $data->bestAsk
            ]);
        })->then(function (string $id) use ($spotWs, $loop, $wsService) {
            Logger::info("Subscribed to allTickers with ID: $id");

            $loop->addTimer(1, function () use ($id, $spotWs, $loop, $wsService) {
                Logger::info("Unsubscribing from allTickers...");

                $spotWs->unSubscribe($id)->then(function () use ($spotWs, $wsService, $loop) {
                    Logger::info("Unsubscribed, stopping WebSocket");

                    $spotWs->stop()->then(function () use ($wsService, $loop) {
                        $wsService->stopEventLoop();
                        $loop->stop();
                    })->catch(function ($e) {
                        Logger::error("Failed to stop WebSocket", ['error' => $e->getMessage()]);
                    });

                })->catch(function ($e) {
                    Logger::error("Unsubscribe failed", ['error' => $e->getMessage()]);
                });
            });

        })->catch(function (Exception $e) {
            Logger::error("Subscription error", ['error' => $e->getMessage()]);
        });

    }, function (Exception $e) use ($wsService, $loop) {
        Logger::error("WebSocket start failed", ['error' => $e->getMessage()]);
        $wsService->stopEventLoop();
        $loop->stop();
    });

    $loop->run();
}

if (php_sapi_name() === 'cli') {
    wsExample();
}
