<?php

require 'vendor/autoload.php';

use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetAllSymbolsReq;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use KuCoin\UniversalSDK\Model\WebSocketClientOptionBuilder;
use React\EventLoop\Factory as Loop;

final class Runner
{
    private const STEP = 5.0;

    private int $wsMsgCnt = 0;
    private int $wsErrCnt = 0;
    private int $mkErrCnt = 0;

    private $wsSvc;
    private $marketApi;
    private $loop;

    public function __construct()
    {
        $this->loop = Loop::create();
        $cli = new DefaultClient(
            (new ClientOptionBuilder())
                ->setKey(getenv('API_KEY') ?: '')
                ->setSecret(getenv('API_SECRET') ?: '')
                ->setPassphrase(getenv('API_PASSPHRASE') ?: '')
                ->setTransportOption(
                    (new TransportOptionBuilder())
                        ->setKeepAlive(true)
                        ->build()
                )
                ->setWebSocketClientOption((new WebSocketClientOptionBuilder())->build())
                ->setSpotEndpoint(Constants::GLOBAL_API_ENDPOINT)
                ->setFuturesEndpoint(Constants::GLOBAL_FUTURES_API_ENDPOINT)
                ->setBrokerEndpoint(Constants::GLOBAL_BROKER_API_ENDPOINT)
                ->build()
            , $this->loop
        );

        $this->wsSvc = $cli->wsService();
        $this->marketApi = $cli->restService()->getSpotService()->getMarketApi();

    }

    public function run(): void
    {
        $this->marketLoop();
        $this->wsForever();
        $this->wsStartStopLoop();
        $this->statLoop();
        $this->loop->run();
    }

    private function marketLoop(): void
    {
        $this->loop->addPeriodicTimer(self::STEP, function () {
            try {
                $resp = $this->marketApi
                    ->getAllSymbols(GetAllSymbolsReq::builder()->setMarket('USDS')->build());
                printf("MARKET API [OK] %d \n", count($resp->data));
            } catch (\Throwable $e) {
                $this->mkErrCnt++;
                echo "MARKET API [ERROR] {$e->getMessage()}\n";
            }
        });
    }

    private function wsForever(): void
    {
        $ws = $this->wsSvc->newSpotPublicWs();
        $ws->start()->then(function () use ($ws) {
            return $ws->orderbookLevel50(['ETH-USDT', 'BTC-USDT'], function ($data) {
                $this->wsMsgCnt++;
            });
        })->then(function () {
            echo "WS [OK]\n";
        })->catch(function ($err) {
            echo "WS [Error] " . $err->getMessage() . "\n";
        });

    }

    private function wsStartStopLoop(): void
    {
        $this->loop->addPeriodicTimer(self::STEP, function () {
            $ws = $this->wsSvc->newSpotPublicWs();
            $ws->start()->then(function () use ($ws) {
                $ws->ticker(['ETH-USDT', 'BTC-USDT'], fn() => null)->then(function ($id) {
                    sleep(self::STEP);
                    return $id;
                })->then(function ($id) use ($ws) {
                    return $ws->unsubscribe($id);
                })->then(function () use ($ws) {
                    return $ws->stop();
                })->then(function () {
                    echo "WS STAR/STOP [OK]\n";
                }, function ($err) {
                    $this->wsErrCnt++;
                    echo "WS STAR/STOP [ERROR] {$err->getMessage()}\n";
                });
            });
        });
    }

    private function statLoop(): void
    {
        $this->loop->addPeriodicTimer(self::STEP, function () {
            printf("Stat Market_ERR:%d WS_SS_ERR:%d WS_MSG:%d\n",
                $this->mkErrCnt, $this->wsErrCnt, $this->wsMsgCnt);
        });
    }
}

(new Runner())->run();
