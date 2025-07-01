<?php
require 'vendor/autoload.php';

use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\FuturesPublicWs;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetAllSymbolsReq;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\SpotPublicWs;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use KuCoin\UniversalSDK\Model\WebSocketClientOptionBuilder;
use React\EventLoop\Loop;

function run_reconnect_test(): void
{
    $key = getenv('API_KEY') ?: '';
    $sec = getenv('API_SECRET') ?: '';
    $psw = getenv('API_PASSPHRASE') ?: '';

    $loop = Loop::get();
    $client = new DefaultClient(
        (new ClientOptionBuilder())
            ->setKey($key)
            ->setSecret($sec)
            ->setPassphrase($psw)
            ->setTransportOption((new TransportOptionBuilder())->build())
            ->setWebSocketClientOption((new WebSocketClientOptionBuilder())->build())
            ->setSpotEndpoint(Constants::GLOBAL_API_ENDPOINT)
            ->setFuturesEndpoint(Constants::GLOBAL_FUTURES_API_ENDPOINT)
            ->setBrokerEndpoint(Constants::GLOBAL_BROKER_API_ENDPOINT)
            ->build(), $loop
    );

    $rest = $client->restService();
    $wsSvc = $client->wsService();
    $symbols = array_slice(
        array_map(fn($d) => $d->symbol,
            $rest->getSpotService()->getMarketApi()
                ->getAllSymbols(GetAllSymbolsReq::builder()->setMarket('USDS')->build())->data),
        0, 50
    );

    spot_ws_example($wsSvc->newSpotPublicWs(), $symbols);
    futures_ws_example($wsSvc->newFuturesPublicWs());

    echo "Total subscribe: 53\n";
    $loop->run();
}

function spot_ws_example(SpotPublicWs $ws, array $symbols): void
{

    $promise = $ws->start();
    foreach ($symbols as $s) {
        $promise = $promise->then(function () use ($ws, $s) {
            return $ws->trade([$s], static function () {
            });
        });
    }

    $promise->then(function () use ($ws) {
        return $ws->ticker(['BTC-USDT', 'ETH-USDT'], static function () {
        });
    })->then(function () use ($ws) {
        echo "Spot subscribe [OK]\n";
    })->catch(function ($error) use ($ws) {
        echo "Spot subscribe [FAIL]" . $error->getMessage() . "\n";
        exit - 1;
    });
}

function futures_ws_example(FuturesPublicWs $ws): void
{
    $ws->start()->then(function () use ($ws) {
        return $ws->tickerV2('XBTUSDTM', static function () {
        });
    })->then(function () use ($ws) {
        return $ws->tickerV1('XBTUSDTM', static function () {
        });
    })->then(function () use ($ws) {
        echo "Futures subscribe [OK]\n";
    }, function ($error) use ($ws) {
        echo "Futures subscribe [Error]" . $error->getMessage() . "\n";
    });
}

run_reconnect_test();
