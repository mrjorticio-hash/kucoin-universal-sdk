<?php


namespace Margin;

use Exception;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Margin\MarginPublic\IndexPriceEvent;
use KuCoin\UniversalSDK\Generate\Margin\MarginPublic\MarginPublicWs;
use KuCoin\UniversalSDK\Generate\Margin\MarginPublic\MarkPriceEvent;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use KuCoin\UniversalSDK\Model\WebSocketClientOptionBuilder;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Throwable;
use function React\Async\await;

/**
 * Create a Promise that resolves after N seconds.
 */
function waitFor(float $seconds, $result): PromiseInterface
{
    $deferred = new Deferred();

    Loop::get()->addTimer($seconds, function () use ($result, $deferred) {
        $deferred->resolve($result);
    });

    return $deferred->promise();
}


class MarginPublicTest extends TestCase
{
    /**
     * @var MarginPublicWs $marginPublic
     */
    private static $marginPublic;

    /**
     * @var Serializer $serializer
     */
    private static $serializer;

    /**
     * @var Loop $loop
     */
    private static $loop;

    /**
     * @throws Throwable
     */
    public static function setUpBeforeClass(): void
    {

        self::$serializer = SerializerBuilder::create()
            ->addDefaultHandlers()
            ->configureHandlers(function ($handlerRegistry) {
                $handlerRegistry->registerSubscribingHandler(
                    new JsonSerializedHandler()
                );
            })
            ->build();

        // Retrieve API secret information from environment variables
        $key = getenv('API_KEY') ?: '';
        $secret = getenv('API_SECRET') ?: '';
        $passphrase = getenv('API_PASSPHRASE') ?: '';

        // Optional: Retrieve broker secret information from environment variables; applicable for broker API only
        $brokerName = getenv('BROKER_NAME');
        $brokerKey = getenv('BROKER_KEY');
        $brokerPartner = getenv('BROKER_PARTNER');

        // Set specific options, others will fall back to default values
        $httpTransportOption = (new TransportOptionBuilder())
            ->setKeepAlive(true)
            ->setMaxConnections(10)
            ->build();

        $websocketTransportOption = (new WebSocketClientOptionBuilder())->build();

        // Create a client using the specified options
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

        self::$loop = Loop::get();

        $client = new DefaultClient($clientOption, self::$loop);
        $kucoinWsService = $client->wsService();
        self::$marginPublic = $kucoinWsService->newMarginPublicWS();

        register_shutdown_function(function () {
            self::$loop->run();
        });


        await(self::$marginPublic->start()->then(
            function () {
                Logger::info("FuturesPublic started");
            },
            function (Exception $e) {
                Logger::error("Failed to start", ['error' => $e->getMessage()]);
                self::fail($e->getMessage());
            }
        ));
    }

    /**
     * @throws Throwable
     */
    public static function tearDownAfterClass(): void
    {
        await(self::$marginPublic->stop()->then(
            function () {
                Logger::info("MarginPublic stopped");
                self::$loop->stop();
            },
            function (Exception $e) {
                Logger::error("Failed to stop", ['error' => $e->getMessage()]);
                self::fail($e->getMessage());
            }
        ));
    }

    /**
     * @throws Throwable
     */
    public function testIndexPrice()
    {
        $counter = 0;
        await(
            self::$marginPublic->indexPrice(["USDT-ETH", "USDT-BTC"],
                function (string $topic, string $subject, IndexPriceEvent $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->granularity);
                    self::assertNotNull($data->timestamp);
                    self::assertNotNull($data->value);
                    Logger::info($data->jsonSerialize(self::$serializer));
                    $counter++;
                    self::assertTrue(true);
                },
                null,
                // Called when subscription fails
                function (Exception $e) {
                    Logger::error("Subscription failed", ['error' => $e->getMessage()]);
                    self::fail($e->getMessage());
                }
            )->then(function (string $id) {
                Logger::info("Subscribed with ID: $id");
                return waitFor(5.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$marginPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

    /**
     * @throws Throwable
     */
    public function testMarkPrice()
    {
        $counter = 0;
        await(
            self::$marginPublic->markPrice(["USDT-ETH", "USDT-BTC"],
                function (string $topic, string $subject, MarkPriceEvent $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->granularity);
                    self::assertNotNull($data->timestamp);
                    self::assertNotNull($data->value);
                    Logger::info($data->jsonSerialize(self::$serializer));
                    $counter++;
                    self::assertTrue(true);
                },
                null,
                // Called when subscription fails
                function (Exception $e) {
                    Logger::error("Subscription failed", ['error' => $e->getMessage()]);
                    self::fail($e->getMessage());
                }
            )->then(function (string $id) {
                Logger::info("Subscribed with ID: $id");
                return waitFor(5.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$marginPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

}