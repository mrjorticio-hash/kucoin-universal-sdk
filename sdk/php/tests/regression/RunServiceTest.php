<?php

include 'vendor/autoload.php';

use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Generate\Account\Fee\GetBasicFeeReq;
use KuCoin\UniversalSDK\Generate\Earn\Earn\GetSavingsProductsReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\{AddOrderReq, CancelOrderByIdReq};
use KuCoin\UniversalSDK\Generate\Margin\Order\{AddOrderReq as MAdd, CancelOrderByOrderIdReq as MCancel};
use KuCoin\UniversalSDK\Generate\Spot\Market\Get24hrStatsReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\{AddOrderSyncReq, CancelOrderByOrderIdReq as SCancel};
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RunServiceTest extends TestCase
{
    private $rest;

    protected function setUp(): void
    {
        $this->rest = (new DefaultClient(
            (new ClientOptionBuilder())
                ->setKey(getenv('API_KEY') ?: '')
                ->setSecret(getenv('API_SECRET') ?: '')
                ->setPassphrase(getenv('API_PASSPHRASE') ?: '')
                ->setTransportOption((new TransportOptionBuilder())->build())
                ->setSpotEndpoint(Constants::GLOBAL_API_ENDPOINT)
                ->setFuturesEndpoint(Constants::GLOBAL_FUTURES_API_ENDPOINT)
                ->setBrokerEndpoint(Constants::GLOBAL_BROKER_API_ENDPOINT)
                ->build()
        ))->restService();
    }

    private function ok($cr): void
    {
        $this->assertEquals('200000', $cr->code);
        $this->assertNotNull($cr->rateLimit);
    }

    public function testAccount(): void
    {
        $r = $this->rest->getAccountService()->getFeeApi()
            ->getBasicFee(GetBasicFeeReq::builder()->setCurrencyType(0)->build());
        $this->ok($r->commonResponse);
        $this->assertNotEmpty($r->makerFeeRate);
    }

    public function testEarn(): void
    {
        $r = $this->rest->getEarnService()->getEarnApi()
            ->getSavingsProducts(GetSavingsProductsReq::builder()->setCurrency('USDT')->build());
        $this->ok($r->commonResponse);
        $this->assertNotEmpty($r->data);
    }

    public function testMargin(): void
    {
        $api = $this->rest->getMarginService()->getOrderApi();
        $add = $api->addOrder(
            MAdd::builder()
                ->setClientOid(Uuid::uuid4()->toString())
                ->setSide('buy')->setSymbol('BTC-USDT')->setType('limit')
                ->setPrice('10000')->setSize('0.001')
                ->setAutoRepay(true)->setAutoBorrow(true)->setIsIsolated(true)
                ->build()
        );
        $this->ok($add->commonResponse);

        $api->cancelOrderByOrderId(
            MCancel::builder()->setOrderId($add->orderId)->setSymbol('BTC-USDT')->build()
        );
    }

    public function testSpot(): void
    {
        $spot = $this->rest->getSpotService();
        $m = $spot->getMarketApi()
            ->get24hrStats(Get24hrStatsReq::builder()->setSymbol('BTC-USDT')->build());
        $this->ok($m->commonResponse);

        $oapi = $spot->getOrderApi();
        $add = $oapi->addOrderSync(
            AddOrderSyncReq::builder()
                ->setClientOid(Uuid::uuid4()->toString())
                ->setSide('buy')->setSymbol('BTC-USDT')->setType('limit')
                ->setRemark('sdk_test')->setPrice('10000')->setSize('0.001')
                ->build()
        );
        $this->ok($add->commonResponse);

        $oapi->cancelOrderByOrderId(
            SCancel::builder()->setOrderId($add->orderId)->setSymbol('BTC-USDT')->build()
        );
    }

    public function testFutures(): void
    {
        $oapi = $this->rest->getFuturesService()->getOrderApi();
        $add = $oapi->addOrder(
            AddOrderReq::builder()
                ->setClientOid(Uuid::uuid4()->toString())
                ->setSide('buy')->setSymbol('XBTUSDTM')->setLeverage(1)
                ->setType('limit')->setRemark('sdk_test')->setMarginMode('CROSS')
                ->setPrice('1')->setSize(1)->build()
        );
        $this->ok($add->commonResponse);

        $oapi->cancelOrderById(
            CancelOrderByIdReq::builder()->setOrderId($add->orderId)->build()
        );
    }
}
