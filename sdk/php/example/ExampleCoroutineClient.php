<?php


use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetFullOrderBookReq;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetPartOrderBookReq;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use Swoole\Event;
use Swoole\Runtime;

include '../vendor/autoload.php';


function stringifyDepth($depth): string
{
    return implode(', ', array_map(function ($row) {
        return '[' . implode(', ', $row) . ']';
    }, $depth));
}

function example()
{
    // Enable coroutine globally
    Runtime::enableCoroutine();

    // Retrieve API secret information from environment variables
    $key = getenv('API_KEY') ?: '';
    $secret = getenv('API_SECRET') ?: '';
    $passphrase = getenv('API_PASSPHRASE') ?: '';

    // Set specific options, others will fall back to default values
    $httpTransportOption = (new TransportOptionBuilder())
        ->setKeepAlive(true)
        ->setMaxConnections(10)
        ->setUseCoroutineHttp(true)
        ->build();

    // Create a client using the specified options
    $clientOption = (new ClientOptionBuilder())
        ->setKey($key)
        ->setSecret($secret)
        ->setPassphrase($passphrase)
        ->setSpotEndpoint(Constants::GLOBAL_API_ENDPOINT)
        ->setFuturesEndpoint(Constants::GLOBAL_FUTURES_API_ENDPOINT)
        ->setBrokerEndpoint(Constants::GLOBAL_BROKER_API_ENDPOINT)
        ->setTransportOption($httpTransportOption)
        ->build();

    $client = new DefaultClient($clientOption);

    // Get the Restful Service
    $kucoinRestService = $client->restService();

    $spotMarketApi = $kucoinRestService->getSpotService()->getMarketApi();

    go(function () use ($spotMarketApi) {

        $request = GetPartOrderBookReq::builder()
            ->setSymbol("BTC-USDT")
            ->setSize("20")
            ->build();

        $response = $spotMarketApi->getPartOrderBook($request);

        Logger::info(sprintf(
            "time=%d, sequence=%d, bids=%s, asks=%s",
            $response->time,
            $response->sequence,
            stringifyDepth($response->bids),
            stringifyDepth($response->asks)
        ));
    });

    go(function () use ($spotMarketApi) {

        $request = GetFullOrderBookReq::builder()
            ->setSymbol("BTC-USDT")
            ->build();

        $response = $spotMarketApi->getFullOrderBook($request);

        Logger::info(sprintf(
            "time=%d, sequence=%d, bids=%s, asks=%s",
            $response->time,
            $response->sequence,
            stringifyDepth($response->bids),
            stringifyDepth($response->asks)
        ));
    });

    Event::wait();
}


if (php_sapi_name() === 'cli') {
    example();
}
