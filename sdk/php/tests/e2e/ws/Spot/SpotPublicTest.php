<?php


use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\AllTickersEvent;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\CallAuctionInfoEvent;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\CallAuctionOrderbookLevel50Event;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\KlinesEvent;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\MarketSnapshotEvent;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\OrderbookIncrementEvent;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\OrderbookLevel1Event;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\OrderbookLevel50Event;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\OrderbookLevel5Event;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\SpotPublicWs;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\SymbolSnapshotEvent;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\TickerEvent;
use KuCoin\UniversalSDK\Generate\Spot\SpotPublic\TradeEvent;
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

class SpotPublicTest extends TestCase
{
    /**
     * @var SpotPublicWs $spotPublic
     */
    private static $spotPublic;

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
        self::$spotPublic = $kucoinWsService->newSpotPublicWS();

        register_shutdown_function(function () {
            self::$loop->run();
        });


        await(self::$spotPublic->start()->then(
            function () {
                Logger::info("SpotPublic started");
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
        await(self::$spotPublic->stop()->then(
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
    public function testAllTicker()
    {
        $counter = 0;
        await(
            self::$spotPublic->allTickers(
                function (string $topic, string $subject, AllTickersEvent $data) use (&$counter) {
                    self::assertNotNull($data->bestAsk);
                    self::assertNotNull($data->bestAskSize);
                    self::assertNotNull($data->bestBid);
                    self::assertNotNull($data->bestBidSize);
                    self::assertNotNull($data->price);
                    self::assertNotNull($data->sequence);
                    self::assertNotNull($data->size);
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
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

    /**
     * @throws Throwable
     * TODO
     */
    public function testCallAuctionInfo()
    {
        $counter = 0;
        await(
            self::$spotPublic->callAuctionInfo("",
                function (string $topic, string $subject, CallAuctionInfoEvent $data) use (&$counter) {
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->estimatedPrice);
                    self::assertNotNull($data->estimatedSize);
                    self::assertNotNull($data->sellOrderRangeLowPrice);
                    self::assertNotNull($data->sellOrderRangeHighPrice);
                    self::assertNotNull($data->buyOrderRangeLowPrice);
                    self::assertNotNull($data->buyOrderRangeHighPrice);
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
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }

    /**
     * @throws Throwable
     * TODO
     */
    public function testCallAuctionOrderbookLevel50()
    {
        $counter = 0;
        await(
            self::$spotPublic->callAuctionOrderbookLevel50("",
                function (string $topic, string $subject, CallAuctionOrderbookLevel50Event $data) use (&$counter) {
                    foreach ($data->asks as $item) {
                        self::assertNotNull($item);
                    }

                    foreach ($data->bids as $item) {
                        self::assertNotNull($item);
                    }

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
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
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
    public function testKlines()
    {
        $counter = 0;
        await(
            self::$spotPublic->klines("BTC-USDT", "1min",
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
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
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
    public function testMarketSnapshot()
    {
        $counter = 0;
        await(
            self::$spotPublic->marketSnapshot("BTC-USDT",
                function (string $topic, string $subject, MarketSnapshotEvent $data) use (&$counter) {
                    self::assertNotNull($data->sequence);
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
                return waitFor(5.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
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
    public function testOrderbookIncrement()
    {
        $counter = 0;
        await(
            self::$spotPublic->orderbookIncrement(["BTC-USDT", "ETH-USDT"],
                function (string $topic, string $subject, OrderbookIncrementEvent $data) use (&$counter) {
                    self::assertNotNull($data->changes);
                    self::assertNotNull($data->sequenceEnd);
                    self::assertNotNull($data->sequenceStart);
                    self::assertNotNull($data->symbol);
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
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
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
    public function testOrderbookLevel1()
    {
        $counter = 0;
        await(
            self::$spotPublic->orderbookLevel1(["BTC-USDT", "ETH-USDT"],
                function (string $topic, string $subject, OrderbookLevel1Event $data) use (&$counter) {
                    foreach ($data->asks as $item) {
                        self::assertNotNull($item);
                    }

                    foreach ($data->bids as $item) {
                        self::assertNotNull($item);
                    }

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
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
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
    public function testOrderbookLevel5()
    {
        $counter = 0;
        await(
            self::$spotPublic->orderbookLevel5(["BTC-USDT", "ETH-USDT"],
                function (string $topic, string $subject, OrderbookLevel5Event $data) use (&$counter) {
                    foreach ($data->asks as $item) {
                        self::assertNotNull($item);
                    }

                    foreach ($data->bids as $item) {
                        self::assertNotNull($item);
                    }

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
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
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
    public function testOrderbookLevel50()
    {
        $counter = 0;
        await(
            self::$spotPublic->orderbookLevel50(["BTC-USDT", "ETH-USDT"],
                function (string $topic, string $subject, OrderbookLevel50Event $data) use (&$counter) {
                    foreach ($data->asks as $item) {
                        self::assertNotNull($item);
                    }

                    foreach ($data->bids as $item) {
                        self::assertNotNull($item);
                    }

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
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
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
    public function testSymbolSnapshot()
    {
        $counter = 0;
        await(
            self::$spotPublic->symbolSnapshot("BTC-USDT",
                function (string $topic, string $subject, SymbolSnapshotEvent $data) use (&$counter) {
                    self::assertNotNull($data->sequence);
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
                return waitFor(5.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
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
    public function testTicker()
    {
        $counter = 0;
        await(
            self::$spotPublic->ticker(["BTC-USDT", "ETH-USDT"],
                function (string $topic, string $subject, TickerEvent $data) use (&$counter) {
                    self::assertNotNull($data->sequence);
                    self::assertNotNull($data->price);
                    self::assertNotNull($data->size);
                    self::assertNotNull($data->bestAsk);
                    self::assertNotNull($data->bestAskSize);
                    self::assertNotNull($data->bestBid);
                    self::assertNotNull($data->bestBidSize);
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
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
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
    public function testTrade()
    {
        $counter = 0;
        await(
            self::$spotPublic->trade(["BTC-USDT", "ETH-USDT"],
                function (string $topic, string $subject, TradeEvent $data) use (&$counter) {
                    self::assertNotNull($data->makerOrderId);
                    self::assertNotNull($data->price);
                    self::assertNotNull($data->sequence);
                    self::assertNotNull($data->side);
                    self::assertNotNull($data->size);
                    self::assertNotNull($data->symbol);
                    self::assertNotNull($data->takerOrderId);
                    self::assertNotNull($data->time);
                    self::assertNotNull($data->tradeId);
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
                return waitFor(5.0, $id);
            })->then(function (string $id) {
                Logger::info("Unsubscribing...");
                return self::$spotPublic->unSubscribe($id)->catch(function ($e) {
                    self::fail($e->getMessage());
                });
            })->then(function () use (&$counter) {
                self::assertTrue($counter > 0);
            })
        );
    }
}