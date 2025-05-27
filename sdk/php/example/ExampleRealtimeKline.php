<?php
/**
 * DISCLAIMER:
 * This strategy is provided for educational and illustrative purposes only. It is not intended to be used as financial
 * or investment advice. Trading cryptocurrencies involves significant risk, and you should carefully consider your
 * investment objectives, level of experience, and risk appetite. Past performance of any trading strategy is not
 * indicative of future results.
 *
 * The authors and contributors of this example are not responsible for any financial losses or damages that may occur
 * from using this code. Use it at your own discretion and consult with a professional financial advisor if necessary.
 */

use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\TradeEvent;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use KuCoin\UniversalSDK\Model\WebSocketClientOptionBuilder;
use React\EventLoop\Loop;

include '../vendor/autoload.php';

// === KLine definition ===
class KLine
{
    public float $open;
    public float $high;
    public float $low;
    public float $close;
    public float $volume;
    public int $startTime;
    public int $endTime;

    public function __construct(float $price, float $size, int $startTime, int $endTime)
    {
        $this->open = $price;
        $this->high = $price;
        $this->low = $price;
        $this->close = $price;
        $this->volume = $size;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public function update(float $price, float $size): void
    {
        $this->high = max($this->high, $price);
        $this->low = min($this->low, $price);
        $this->close = $price;
        $this->volume += $size;
    }
}

// === Global data ===
const TIME_INTERVAL = 60; // seconds
$klineData = []; // [period => [symbol => KLine]]

function processTradeToKline(string $topic, string $subject, TradeEvent $tradeEvent)
{
    global $klineData;

    $symbol = $tradeEvent->symbol;
    $price = floatval($tradeEvent->price);
    $size = floatval($tradeEvent->size);
    $timestamp = intval($tradeEvent->time / 1_000_000_000); // convert nano → sec

    $periodStart = intdiv($timestamp, TIME_INTERVAL) * TIME_INTERVAL;
    $periodEnd = $periodStart + TIME_INTERVAL;

    if (!isset($klineData[$periodStart][$symbol])) {
        $klineData[$periodStart][$symbol] = new KLine($price, $size, $periodStart, $periodEnd);
    } else {
        $klineData[$periodStart][$symbol]->update($price, $size);
    }

    Logger::info(sprintf(
        "KLine @%s [%s]: O=%.4f H=%.4f L=%.4f C=%.4f V=%.4f",
        date('c', $periodStart),
        $symbol,
        $klineData[$periodStart][$symbol]->open,
        $klineData[$periodStart][$symbol]->high,
        $klineData[$periodStart][$symbol]->low,
        $klineData[$periodStart][$symbol]->close,
        $klineData[$periodStart][$symbol]->volume
    ));
}

function printKlineData()
{
    global $klineData;

    ksort($klineData);

    foreach ($klineData as $periodStart => $symbols) {
        echo "\nTime Period: " . date('c', $periodStart) . PHP_EOL;
        foreach ($symbols as $symbol => $kline) {
            echo "  Symbol: $symbol\n";
            echo "    Open: {$kline->open}\n";
            echo "    High: {$kline->high}\n";
            echo "    Low: {$kline->low}\n";
            echo "    Close: {$kline->close}\n";
            echo "    Volume: {$kline->volume}\n";
            echo "    Start Time: " . date('c', $kline->startTime) . "\n";
            echo "    End Time:   " . date('c', $kline->endTime) . "\n";
        }
    }
}

function runKlineCollector()
{
    global $klineData;

    $key = getenv('API_KEY') ?: '';
    $secret = getenv('API_SECRET') ?: '';
    $passphrase = getenv('API_PASSPHRASE') ?: '';

    $httpOption = (new TransportOptionBuilder())->setKeepAlive(true)->build();
    $wsOption = (new WebSocketClientOptionBuilder())->build();

    $clientOption = (new ClientOptionBuilder())
        ->setKey($key)
        ->setSecret($secret)
        ->setPassphrase($passphrase)
        ->setSpotEndpoint(Constants::GLOBAL_API_ENDPOINT)
        ->setFuturesEndpoint(Constants::GLOBAL_FUTURES_API_ENDPOINT)
        ->setBrokerEndpoint(Constants::GLOBAL_BROKER_API_ENDPOINT)
        ->setTransportOption($httpOption)
        ->setWebSocketClientOption($wsOption)
        ->build();

    $loop = Loop::get();
    $client = new DefaultClient($clientOption, $loop);
    $spotWs = $client->wsService()->newSpotPublicWS();

    $symbols = ['BTC-USDT', 'ETH-USDT'];
    $duration = 180;

    $spotWs->start()->then(function () use ($spotWs, $symbols, $loop, $duration) {
        Logger::info("WebSocket started");

        return $spotWs->trade(
            $symbols,
            'processTradeToKline',
            function (string $subId) use ($spotWs, $loop, $duration) {
                Logger::info("Subscribed: $subId");

                $loop->addTimer($duration, function () use ($spotWs, $subId) {
                    Logger::info("Time expired. Unsubscribing...");
                    $spotWs->unSubscribe($subId)->finally(function () use ($spotWs) {
                        Logger::info("Shutting down WebSocket...");
                        $spotWs->stop();
                        printKlineData();
                    });
                });
            },
            function (Exception $e) use ($spotWs) {
                Logger::error("Subscription failed", ['error' => $e->getMessage()]);
                $spotWs->stop();
            }
        );
    })->catch(function (Exception $e) use ($spotWs) {
        Logger::error("WebSocket start failed", ['error' => $e->getMessage()]);
        $spotWs->stop();
    });

    $loop->run();
}

if (php_sapi_name() === 'cli') {
    runKlineCollector();
}
