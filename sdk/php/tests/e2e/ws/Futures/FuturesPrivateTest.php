<?php


use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPrivate\AllOrderEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPrivate\AllPositionEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPrivate\BalanceEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPrivate\CrossLeverageEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPrivate\FuturesPrivateWs;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPrivate\MarginModeEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPrivate\OrderEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPrivate\PositionEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPrivate\StopOrdersEvent;
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


class FuturesPrivateTest extends TestCase
{
    /**
     * @var FuturesPrivateWs $futuresPrivate
     */
    private static $futuresPrivate;

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
        self::$futuresPrivate = $kucoinWsService->newFuturesPrivateWS();

        register_shutdown_function(function () {
            self::$loop->run();
        });


        await(self::$futuresPrivate->start()->then(
            function () {
                Logger::info("FuturesPrivate started");
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
        await(self::$futuresPrivate->stop()->then(
            function () {
                Logger::info("FuturesPrivate stopped");
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
    public function testAllOrder()
    {
        $counter = 0;
        await(
            self::$futuresPrivate->allOrder(
                function (string $topic, string $subject, AllOrderEvent $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->side);
                    self::assertNotNull($data->canceledSize);
                    self::assertNotNull($data->orderId);
                    self::assertNotNull($data->marginMode);
                    self::assertNotNull($data->type);
                    self::assertNotNull($data->orderTime);
                    self::assertNotNull($data->size);
                    self::assertNotNull($data->filledSize);
                    self::assertNotNull($data->price);
                    self::assertNotNull($data->remainSize);
                    self::assertNotNull($data->status);
                    self::assertNotNull($data->ts);
                    self::assertNotNull($data->tradeType);
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
                return self::$futuresPrivate->unSubscribe($id)->catch(function ($e) {
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
    public function testAllPosition()
    {
        $counter = 0;
        await(
            self::$futuresPrivate->allPosition(
                function (string $topic, string $subject, AllPositionEvent $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->crossMode);
                    self::assertNotNull($data->delevPercentage);
                    self::assertNotNull($data->openingTimestamp);
                    self::assertNotNull($data->currentTimestamp);
                    self::assertNotNull($data->currentQty);
                    self::assertNotNull($data->currentCost);
                    self::assertNotNull($data->currentComm);
                    self::assertNotNull($data->unrealisedCost);
                    self::assertNotNull($data->realisedGrossCost);
                    self::assertNotNull($data->realisedCost);
                    self::assertNotNull($data->isOpen);
                    self::assertNotNull($data->markPrice);
                    self::assertNotNull($data->markValue);
                    self::assertNotNull($data->posCost);
                    self::assertNotNull($data->posInit);
                    self::assertNotNull($data->realisedGrossPnl);
                    self::assertNotNull($data->realisedPnl);
                    self::assertNotNull($data->unrealisedPnl);
                    self::assertNotNull($data->unrealisedPnlPcnt);
                    self::assertNotNull($data->unrealisedRoePcnt);
                    self::assertNotNull($data->avgEntryPrice);
                    self::assertNotNull($data->liquidationPrice);
                    self::assertNotNull($data->bankruptPrice);
                    self::assertNotNull($data->settleCurrency);
                    self::assertNotNull($data->marginMode);
                    self::assertNotNull($data->positionSide);
                    self::assertNotNull($data->leverage);
                    self::assertNotNull($data->maintMarginReq);
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
                return waitFor(120.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$futuresPrivate->unSubscribe($id)->catch(function ($e) {
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
    public function testBalance()
    {
        $counter = 0;
        await(
            self::$futuresPrivate->balance(
                function (string $topic, string $subject, BalanceEvent $data) use (&$counter) {
                    self::assertNotNull($data->crossPosMargin);
                    self::assertNotNull($data->isolatedOrderMargin);
                    self::assertNotNull($data->holdBalance);
                    self::assertNotNull($data->equity);
                    self::assertNotNull($data->version);
                    self::assertNotNull($data->availableBalance);
                    self::assertNotNull($data->isolatedPosMargin);
                    self::assertNotNull($data->walletBalance);
                    self::assertNotNull($data->isolatedFundingFeeMargin);
                    self::assertNotNull($data->crossUnPnl);
                    self::assertNotNull($data->totalCrossMargin);
                    self::assertNotNull($data->currency);
                    self::assertNotNull($data->isolatedUnPnl);
                    self::assertNotNull($data->crossOrderMargin);
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
                return self::$futuresPrivate->unSubscribe($id)->catch(function ($e) {
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
    public function testCrossLeverage()
    {
        $counter = 0;
        await(
            self::$futuresPrivate->crossLeverage(
                function (string $topic, string $subject, CrossLeverageEvent $data) use (&$counter) {
                    self::assertNotNull($data->data);
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
                return self::$futuresPrivate->unSubscribe($id)->catch(function ($e) {
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
    public function testMarginMode()
    {
        $counter = 0;
        await(
            self::$futuresPrivate->marginMode(
                function (string $topic, string $subject, MarginModeEvent $data) use (&$counter) {
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
                return self::$futuresPrivate->unSubscribe($id)->catch(function ($e) {
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
    public function testOrder()
    {
        $counter = 0;
        await(
            self::$futuresPrivate->order("XBTUSDTM",
                function (string $topic, string $subject, OrderEvent $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->side);
                    self::assertNotNull($data->canceledSize);
                    self::assertNotNull($data->orderId);
                    self::assertNotNull($data->marginMode);
                    self::assertNotNull($data->type);
                    self::assertNotNull($data->orderTime);
                    self::assertNotNull($data->size);
                    self::assertNotNull($data->filledSize);
                    self::assertNotNull($data->price);
                    self::assertNotNull($data->remainSize);
                    self::assertNotNull($data->status);
                    self::assertNotNull($data->ts);
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
                return self::$futuresPrivate->unSubscribe($id)->catch(function ($e) {
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
    public function testPosition()
    {
        $counter = 0;
        await(
            self::$futuresPrivate->position("DOGEUSDTM",
                function (string $topic, string $subject, PositionEvent $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->crossMode);
                    self::assertNotNull($data->delevPercentage);
                    self::assertNotNull($data->openingTimestamp);
                    self::assertNotNull($data->currentTimestamp);
                    self::assertNotNull($data->currentQty);
                    self::assertNotNull($data->currentCost);
                    self::assertNotNull($data->currentComm);
                    self::assertNotNull($data->unrealisedCost);
                    self::assertNotNull($data->realisedGrossCost);
                    self::assertNotNull($data->realisedCost);
                    self::assertNotNull($data->isOpen);
                    self::assertNotNull($data->markPrice);
                    self::assertNotNull($data->markValue);
                    self::assertNotNull($data->posCost);
                    self::assertNotNull($data->posInit);
                    self::assertNotNull($data->realisedGrossPnl);
                    self::assertNotNull($data->realisedPnl);
                    self::assertNotNull($data->unrealisedPnl);
                    self::assertNotNull($data->unrealisedPnlPcnt);
                    self::assertNotNull($data->unrealisedRoePcnt);
                    self::assertNotNull($data->avgEntryPrice);
                    self::assertNotNull($data->liquidationPrice);
                    self::assertNotNull($data->bankruptPrice);
                    self::assertNotNull($data->settleCurrency);
                    self::assertNotNull($data->marginMode);
                    self::assertNotNull($data->positionSide);
                    self::assertNotNull($data->leverage);
                    self::assertNotNull($data->maintMarginReq);
                    self::assertNotNull($data->posMaint);
                    self::assertNotNull($data->fundingFee);
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
                return self::$futuresPrivate->unSubscribe($id)->catch(function ($e) {
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
    public function testStopOrders()
    {
        $counter = 0;
        await(
            self::$futuresPrivate->stopOrders(
                function (string $topic, string $subject, StopOrdersEvent $data) use (&$counter) {
                    self::assertNotNull($data->createdAt);
                    self::assertNotNull($data->marginMode);
                    self::assertNotNull($data->orderId);
                    self::assertNotNull($data->orderPrice);
                    self::assertNotNull($data->orderType);
                    self::assertNotNull($data->side);
                    self::assertNotNull($data->size);
                    self::assertNotNull($data->stop);
                    self::assertNotNull($data->stopPrice);
                    self::assertNotNull($data->stopPriceType);
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
                return waitFor(30.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$futuresPrivate->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }
}