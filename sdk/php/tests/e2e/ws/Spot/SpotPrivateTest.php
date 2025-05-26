<?php


use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Spot\SpotPrivate\AccountEvent;
use KuCoin\UniversalSDK\Generate\Spot\SpotPrivate\OrderV1Event;
use KuCoin\UniversalSDK\Generate\Spot\SpotPrivate\OrderV2Event;
use KuCoin\UniversalSDK\Generate\Spot\SpotPrivate\SpotPrivateWs;
use KuCoin\UniversalSDK\Generate\Spot\SpotPrivate\StopOrderEvent;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use KuCoin\UniversalSDK\Model\WebSocketClientOptionBuilder;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
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

class SpotPrivateTest extends TestCase
{
    /**
     * @var SpotPrivateWs $spotPrivate
     */
    private static $spotPrivate;

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
        self::$spotPrivate = $kucoinWsService->newSpotPrivateWS();

        register_shutdown_function(function () {
            self::$loop->run();
        });


        await(self::$spotPrivate->start()->then(
            function () {
                Logger::info("SpotPrivate started");
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
        await(self::$spotPrivate->stop()->then(
            function () {
                Logger::info("SpotPublic stopped");
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
    public function testAccount()
    {
        $counter = 0;
        await(
            self::$spotPrivate->account(
                function (string $topic, string $subject, AccountEvent $data) use (&$counter) {
                    self::assertNotNull($data->accountId);
                    self::assertNotNull($data->available);
                    self::assertNotNull($data->availableChange);
                    self::assertNotNull($data->currency);
                    self::assertNotNull($data->hold);
                    self::assertNotNull($data->holdChange);
                    self::assertNotNull($data->relationContext);
                    self::assertNotNull($data->relationEvent);
                    self::assertNotNull($data->relationEventId);
                    self::assertNotNull($data->time);
                    self::assertNotNull($data->total);
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
                return waitFor(60.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$spotPrivate->unSubscribe($id)->catch(function ($e) {
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
    public function testOrderV1()
    {
        $counter = 0;
        await(
            self::$spotPrivate->orderV1(
                function (string $topic, string $subject, OrderV1Event $data) use (&$counter) {
                    self::assertNotNull($data->filledSize);
                    self::assertNotNull($data->orderId);
                    self::assertNotNull($data->orderTime);
                    self::assertNotNull($data->orderType);
                    self::assertNotNull($data->price);
                    self::assertNotNull($data->remainSize);
                    self::assertNotNull($data->side);
                    self::assertNotNull($data->size);
                    self::assertNotNull($data->status);
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->ts);
                    self::assertNotNull($data->type);
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
                return waitFor(60, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$spotPrivate->unSubscribe($id)->catch(function ($e) {
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
    public function testOrderV2()
    {
        $counter = 0;
        await(
            self::$spotPrivate->orderV2(
                function (string $topic, string $subject, OrderV2Event $data) use (&$counter) {
                    self::assertNotNull($data->orderId);
                    self::assertNotNull($data->orderTime);
                    self::assertNotNull($data->orderType);
                    self::assertNotNull($data->price);
                    self::assertNotNull($data->remainSize);
                    self::assertNotNull($data->side);
                    self::assertNotNull($data->size);
                    self::assertNotNull($data->status);
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->ts);
                    self::assertNotNull($data->type);
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
                return waitFor(60.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$spotPrivate->unSubscribe($id)->catch(function ($e) {
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
    public function testStopOrder()
    {
        $counter = 0;
        await(
            self::$spotPrivate->stopOrder(
                function (string $topic, string $subject, StopOrderEvent $data) use (&$counter) {
                    self::assertNotNull($data->createdAt);
                    self::assertNotNull($data->orderId);
                    self::assertNotNull($data->orderPrice);
                    self::assertNotNull($data->orderType);
                    self::assertNotNull($data->side);
                    self::assertNotNull($data->size);
                    self::assertNotNull($data->stop);
                    self::assertNotNull($data->stopPrice);
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->tradeType);
                    self::assertNotNull($data->ts);
                    self::assertNotNull($data->type);
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
                return self::$spotPrivate->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }


}