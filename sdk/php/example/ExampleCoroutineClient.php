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

/**
 * Entry point of the coroutine example
 *
 * This demonstrates how to run KuCoin REST API calls concurrently using Swoole coroutines.
 *
 * Prerequisites for running this example:
 * 1. PHP with Swoole extension installed (`pecl install swoole`)
 * 2. Saber HTTP client installed via Composer: `composer require swlib/saber`
 * 3. Set environment variables: API_KEY, API_SECRET, API_PASSPHRASE
 *
 * How it works:
 * - Use Swoole's `Runtime::enableCoroutine()` to enable coroutine hooks.
 * - Launch multiple `go()` blocks for concurrent API calls.
 * - Use `Event::wait()` to hold the process until all tasks complete.
 */
function example()
{
    // 1. Enable Swoole coroutine globally (must be called before any I/O)
    Runtime::enableCoroutine();

    // 2. Retrieve API secret information from environment
    $key = getenv('API_KEY') ?: '';
    $secret = getenv('API_SECRET') ?: '';
    $passphrase = getenv('API_PASSPHRASE') ?: '';

    // 3. Configure HTTP transport with coroutine-based HTTP client (Saber)
    $httpTransportOption = (new TransportOptionBuilder())
        ->setKeepAlive(true)
        ->setMaxConnections(10)
        ->setUseCoroutineHttp(true)  // This enables Saber as HTTP client
        ->build();

    // 4. Build client options
    $clientOption = (new ClientOptionBuilder())
        ->setKey($key)
        ->setSecret($secret)
        ->setPassphrase($passphrase)
        ->setSpotEndpoint(Constants::GLOBAL_API_ENDPOINT)
        ->setFuturesEndpoint(Constants::GLOBAL_FUTURES_API_ENDPOINT)
        ->setBrokerEndpoint(Constants::GLOBAL_BROKER_API_ENDPOINT)
        ->setTransportOption($httpTransportOption)
        ->build();

    // 5. Create the KuCoin API client
    $client = new DefaultClient($clientOption);
    $kucoinRestService = $client->restService();
    $spotMarketApi = $kucoinRestService->getSpotService()->getMarketApi();

    // 6. Run GetPartOrderBook concurrently in a coroutine
    go(function () use ($spotMarketApi) {
        try {
            $request = GetPartOrderBookReq::builder()
                ->setSymbol("BTC-USDT")
                ->setSize("20")
                ->build();

            $response = $spotMarketApi->getPartOrderBook($request);

            Logger::info(sprintf(
                "[Partial] time=%d, sequence=%d, bids=%s, asks=%s",
                $response->time,
                $response->sequence,
                stringifyDepth($response->bids),
                stringifyDepth($response->asks)
            ));
        } catch (\Throwable $e) {
            Logger::error("Failed to get partial order book: " . $e->getMessage());
        }
    });

    // 7. Run GetFullOrderBook concurrently in another coroutine
    go(function () use ($spotMarketApi) {
        try {
            $request = GetFullOrderBookReq::builder()
                ->setSymbol("BTC-USDT")
                ->build();

            $response = $spotMarketApi->getFullOrderBook($request);

            Logger::info(sprintf(
                "[Full] time=%d, sequence=%d, bids=%s, asks=%s",
                $response->time,
                $response->sequence,
                stringifyDepth($response->bids),
                stringifyDepth($response->asks)
            ));
        } catch (\Throwable $e) {
            Logger::error("Failed to get full order book: " . $e->getMessage());
        }
    });

    // 8. Start Swoole event loop to allow coroutines to run
    Event::wait();
}

// Run the example only if executed via CLI
if (php_sapi_name() === 'cli') {
    example();
}
