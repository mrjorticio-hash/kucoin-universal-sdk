<?php


use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use KuCoin\UniversalSDK\Model\WebSocketClientOptionBuilder;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

function getProcessTCPConnectionsWithLsof(): int
{
    $pid = getmypid();
    try {
        $cmd = "lsof -iTCP -n -P | grep $pid | wc -l";
        $output = shell_exec($cmd);
        return $output !== null ? (int)trim($output) : 0;
    } catch (Throwable $e) {
        error_log("Error executing lsof: " . $e->getMessage());
        return 0;
    }
}

function waitFor(float $seconds, $result): PromiseInterface
{
    $deferred = new Deferred();

    Loop::get()->addTimer($seconds, function () use ($result, $deferred) {
        $deferred->resolve($result);
    });

    return $deferred->promise();
}


class ResourceTest extends TestCase
{
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

        return new DefaultClient($clientOption, $loop);
    }

    public function testConnection()
    {
        $loop = Loop::get();

        $client = self::getClient();
        $spotWs = $client->wsService()->newSpotPublicWS();
        $futuresWs = $client->wsService()->newFuturesPublicWS();

        $before = getProcessTCPConnectionsWithLsof();


        $spotWs->start()->then(function () use ($client) {
            Logger::info("current: " . getProcessTCPConnectionsWithLsof());
            return waitFor(5, []);
        })->then(function () use ($spotWs) {
            return $spotWs->stop();
        })->catch(function (Exception $e) {
            self::fail($e->getMessage());
        });

        $futuresWs->start()->then(function () use ($client) {
            Logger::info("current: " . getProcessTCPConnectionsWithLsof());
            return waitFor(5, []);
        })->then(function () use ($futuresWs) {
            return $futuresWs->stop();
        })->catch(function (Exception $e) {
            self::fail($e->getMessage());
        });
        // Run the event loop to process async tasks
        $loop->run();
        $after = getProcessTCPConnectionsWithLsof();

        Logger::info("connection", ["before" => $before, "after" => $after]);
        self::assertEquals(0, $before);
        self::assertEquals(0, $after);
    }
}
