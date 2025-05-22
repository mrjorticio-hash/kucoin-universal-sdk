<?php


use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\RestError;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;
use Swoole\Event;
use Swoole\Runtime;

class ReliabilityTest extends TestCase
{
    public function testExtraConfig()
    {

        $key = getenv('API_KEY') ?: '';
        $secret = getenv('API_SECRET') ?: '';
        $passphrase = getenv('API_PASSPHRASE') ?: '';

        $httpTransportOption = (new TransportOptionBuilder())
            ->setKeepAlive(true)
            ->setTotalTimeout(1)
            ->setMaxConnections(10)
            ->setRetryDelay(0)
            ->setMaxRetries(1)
            ->setExtraOptions(["proxy" => ['http' => '192.168.1.1', 'https' => '192.168.1.1']])
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
        try {
            $kucoinRestService->getSpotService()->getMarketApi()->getAllCurrencies();
        } catch (RestError $exception) {
            self::assertTrue($exception->getError() instanceof GuzzleHttp\Exception\ConnectException);
        }
    }


    public function testCoroutineExtraConfig()
    {

        Runtime::enableCoroutine();

        $key = getenv('API_KEY') ?: '';
        $secret = getenv('API_SECRET') ?: '';
        $passphrase = getenv('API_PASSPHRASE') ?: '';

        $httpTransportOption = (new TransportOptionBuilder())
            ->setKeepAlive(true)
            ->setMaxConnections(10)
            ->setRetryDelay(0)
            ->setUseCoroutineHttp(true)
            ->setMaxRetries(1)
            ->setExtraOptions(['proxy' => 'http://127.0.0.1:1111'])
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
        go(function () use ($kucoinRestService) {
            try {
                $kucoinRestService->getSpotService()->getMarketApi()->getAllCurrencies();
            } catch (RestError $exception) {
                Logger::error($exception->getMessage());
                self::assertTrue($exception->getError() instanceof Swlib\Http\Exception\ConnectException);
            }
        });

        Event::wait();
    }


    public function testTimeoutRetry()
    {
        $key = getenv('API_KEY') ?: '';
        $secret = getenv('API_SECRET') ?: '';
        $passphrase = getenv('API_PASSPHRASE') ?: '';

        $httpTransportOption = (new TransportOptionBuilder())
            ->setKeepAlive(true)
            ->setMaxConnections(10)
            ->setRetryDelay(3)
            ->setMaxRetries(3)
            ->setTotalTimeout(1)
            ->setExtraOptions(["proxy" => ['http' => '192.168.1.1', 'https' => '192.168.1.1']])
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
        $start = time();
        try {
            $kucoinRestService->getSpotService()->getMarketApi()->getAllCurrencies();
        } catch (RestError $exception) {
            $end = time();
            self::assertTrue($exception->getError() instanceof GuzzleHttp\Exception\ConnectException);
            self::assertTrue($end - $start > (3 + 1) * 3);
        }
    }

    public function testConnection()
    {

        $key = getenv('API_KEY') ?: '';
        $secret = getenv('API_SECRET') ?: '';
        $passphrase = getenv('API_PASSPHRASE') ?: '';

        $httpTransportOption = (new TransportOptionBuilder())
            ->setKeepAlive(true)
            ->setMaxConnections(2)
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
        for ($i = 0; $i < 10; $i++) {
            $kucoinRestService->getSpotService()->getMarketApi()->getAllCurrencies();
            sleep(1);
        }
    }
}
