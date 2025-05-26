<?php


use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;
use Swoole\Event;
use Swoole\Runtime;

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

class ResourceTest extends TestCase
{
    public function testConnectionCoroutine()
    {
        Runtime::enableCoroutine();

        $key = getenv('API_KEY') ?: '';
        $secret = getenv('API_SECRET') ?: '';
        $passphrase = getenv('API_PASSPHRASE') ?: '';

        $httpTransportOption = (new TransportOptionBuilder())
            ->setKeepAlive(true)
            ->setMaxConnections(2)
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
        $kucoinRestService = $client->restService();
        $before = getProcessTCPConnectionsWithLsof();
        for ($i = 0; $i < 10; $i++) {
            go(function () use ($kucoinRestService) {
                try {
                    $kucoinRestService->getSpotService()->getMarketApi()->getAllCurrencies();
                } catch (Exception $exception) {
                    self::fail($exception->getMessage());
                }
                sleep(1);
            });
        }
        $after = getProcessTCPConnectionsWithLsof();

        Event::wait();

        self::assertEquals(2, $after);
        Logger::info("connection", ["before" => $before, "after" => $after]);
    }
}
