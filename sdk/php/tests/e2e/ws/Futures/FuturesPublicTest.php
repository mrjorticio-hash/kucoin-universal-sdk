<?php


use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\AnnouncementEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\ExecutionEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\FuturesPublicWs;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\InstrumentEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\KlinesEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\OrderbookIncrementEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\OrderbookLevel50Event;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\OrderbookLevel5Event;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\SymbolSnapshotEvent;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\TickerV1Event;
use KuCoin\UniversalSDK\Generate\Futures\FuturesPublic\TickerV2Event;
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

class FuturesPublicTest extends TestCase
{
    /**
     * @var FuturesPublicWs $futuresPublic
     */
    private static $futuresPublic;

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
        self::$futuresPublic = $kucoinWsService->newFuturesPublicWS();

        register_shutdown_function(function () {
            self::$loop->run();
        });


        await(self::$futuresPublic->start()->then(
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
        await(self::$futuresPublic->stop()->then(
            function () {
                Logger::info("FuturesPublic stopped");
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
     * TODO
     */
    public function testAnnouncement()
    {
        $counter = 0;
        await(
            self::$futuresPublic->announcement("XBTUSDTM",
                function (string $topic, string $subject, AnnouncementEvent $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->fundingTime);
                    self::assertNotNull($data->fundingRate);
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
                return self::$futuresPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

    public function testExecution()
    {
        $counter = 0;
        await(
            self::$futuresPublic->execution("XBTUSDTM",
                function (string $topic, string $subject, ExecutionEvent $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->sequence);
                    self::assertNotNull($data->side);
                    self::assertNotNull($data->size);
                    self::assertNotNull($data->price);
                    self::assertNotNull($data->takerOrderId);
                    self::assertNotNull($data->makerOrderId);
                    self::assertNotNull($data->tradeId);
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
                return self::$futuresPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

    public function testInstrument()
    {
        $counter = 0;
        await(
            self::$futuresPublic->instrument("XBTUSDTM",
                function (string $topic, string $subject, InstrumentEvent $data) use (&$counter) {
                    self::assertNotNull($data->granularity);
                    self::assertNotNull($data->timestamp);
                    self::assertNotNull($data->markPrice);
                    self::assertNotNull($data->indexPrice);
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
                return self::$futuresPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

    public function testKlines()
    {
        $counter = 0;
        await(
            self::$futuresPublic->klines("XBTUSDTM", "1min",
                function (string $topic, string $subject, KlinesEvent $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    foreach ($data->candles as $item) {
                        self::assertNotNull($item);
                    }

                    self::assertNotNull($data->time);
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
                return self::$futuresPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }


    public function testOrderbookIncrement()
    {
        $counter = 0;
        await(
            self::$futuresPublic->orderbookIncrement("XBTUSDTM",
                function (string $topic, string $subject, OrderbookIncrementEvent $data) use (&$counter) {
                    self::assertNotNull($data->sequence);
                    self::assertNotNull($data->change);
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
                return waitFor(5.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$futuresPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

    public function testOrderbookLevel50()
    {
        $counter = 0;
        await(
            self::$futuresPublic->orderbookLevel50("XBTUSDTM",
                function (string $topic, string $subject, OrderbookLevel50Event $data) use (&$counter) {
                    foreach ($data->bids as $item) {
                        self::assertNotNull($item);
                    }

                    self::assertNotNull($data->sequence);
                    self::assertNotNull($data->timestamp);
                    self::assertNotNull($data->ts);
                    foreach ($data->asks as $item) {
                        self::assertNotNull($item);
                    }

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
                return self::$futuresPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

    public function testOrderbookLevel5()
    {
        $counter = 0;
        await(
            self::$futuresPublic->orderbookLevel5("XBTUSDTM",
                function (string $topic, string $subject, OrderbookLevel5Event $data) use (&$counter) {
                    foreach ($data->bids as $item) {
                        self::assertNotNull($item);
                    }

                    self::assertNotNull($data->sequence);
                    self::assertNotNull($data->timestamp);
                    self::assertNotNull($data->ts);
                    foreach ($data->asks as $item) {
                        self::assertNotNull($item);
                    }

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
                return self::$futuresPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

    public function testSymbolSnapshot()
    {
        $counter = 0;
        await(
            self::$futuresPublic->symbolSnapshot("XBTUSDTM",
                function (string $topic, string $subject, SymbolSnapshotEvent $data) use (&$counter) {
                    self::assertNotNull($data->highPrice);
                    self::assertNotNull($data->lastPrice);
                    self::assertNotNull($data->lowPrice);
                    self::assertNotNull($data->price24HoursBefore);
                    self::assertNotNull($data->priceChg);
                    self::assertNotNull($data->priceChgPct);
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->ts);
                    self::assertNotNull($data->turnover);
                    self::assertNotNull($data->volume);
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
                return self::$futuresPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

    public function testTickerV1()
    {
        $counter = 0;
        await(
            self::$futuresPublic->tickerV1("XBTUSDTM",
                function (string $topic, string $subject, TickerV1Event $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->sequence);
                    self::assertNotNull($data->side);
                    self::assertNotNull($data->size);
                    self::assertNotNull($data->price);
                    self::assertNotNull($data->bestBidSize);
                    self::assertNotNull($data->bestBidPrice);
                    self::assertNotNull($data->bestAskPrice);
                    self::assertNotNull($data->tradeId);
                    self::assertNotNull($data->bestAskSize);
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
                return self::$futuresPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

    public function testTickerV2()
    {
        $counter = 0;
        await(
            self::$futuresPublic->tickerV2("XBTUSDTM",
                function (string $topic, string $subject, TickerV2Event $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->sequence);
                    self::assertNotNull($data->bestBidSize);
                    self::assertNotNull($data->bestBidPrice);
                    self::assertNotNull($data->bestAskPrice);
                    self::assertNotNull($data->bestAskSize);
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
                return waitFor(5.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$futuresPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

}