<?php


namespace Margin;

use Exception;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Margin\MarginPrivate\CrossMarginPositionEvent;
use KuCoin\UniversalSDK\Generate\Margin\MarginPrivate\IsolatedMarginPositionEvent;
use KuCoin\UniversalSDK\Generate\Margin\MarginPrivate\MarginPrivateWs;
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

class MarginPrivateTest extends TestCase
{
    /**
     * @var MarginPrivateWs $marginPrivate
     */
    private static $marginPrivate;

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
        self::$marginPrivate = $kucoinWsService->newMarginPrivateWS();

        register_shutdown_function(function () {
            self::$loop->run();
        });


        await(self::$marginPrivate->start()->then(
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
        await(self::$marginPrivate->stop()->then(
            function () {
                Logger::info("MarginPrivate stopped");
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
    public function testCrossMarginPosition()
    {
        $counter = 0;
        await(
            self::$marginPrivate->crossMarginPosition(
                function (string $topic, string $subject, CrossMarginPositionEvent $data) use (&$counter) {
                    self::assertNotNull($data->debtRatio);
                    self::assertNotNull($data->totalAsset);
                    self::assertNotNull($data->marginCoefficientTotalAsset);
                    self::assertNotNull($data->totalDebt);
                    self::assertNotNull($data->assetList);
                    self::assertNotNull($data->debtList);
                    self::assertNotNull($data->timestamp);
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
                return waitFor(120, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$marginPrivate->unSubscribe($id)->catch(function ($e) {
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
    public function testIsolatedMarginPosition()
    {
        $counter = 0;
        await(
            self::$marginPrivate->isolatedMarginPosition("BTC-USDT",
                function (string $topic, string $subject, IsolatedMarginPositionEvent $data) use (&$counter) {
                    self::assertNotNull($data->tag);
                    self::assertNotNull($data->status);
                    self::assertNotNull($data->statusBizType);
                    self::assertNotNull($data->accumulatedPrincipal);
                    self::assertNotNull($data->changeAssets);
                    self::assertNotNull($data->timestamp);
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
                return waitFor(30.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$marginPrivate->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }


}