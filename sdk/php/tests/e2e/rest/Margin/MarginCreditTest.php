<?php

namespace Tests\e2e\rest\Margin;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Margin\Credit\CreditApi;
use KuCoin\UniversalSDK\Generate\Margin\Credit\GetLoanMarketInterestRateReq;
use KuCoin\UniversalSDK\Generate\Margin\Credit\GetLoanMarketReq;
use KuCoin\UniversalSDK\Generate\Margin\Credit\GetPurchaseOrdersReq;
use KuCoin\UniversalSDK\Generate\Margin\Credit\GetRedeemOrdersReq;
use KuCoin\UniversalSDK\Generate\Margin\Credit\ModifyPurchaseReq;
use KuCoin\UniversalSDK\Generate\Margin\Credit\PurchaseReq;
use KuCoin\UniversalSDK\Generate\Margin\Credit\RedeemReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class MarginCreditTest extends TestCase
{
    /**
     * @var CreditApi $api
     */
    private $api;

    /**
     * @var Serializer $serializer
     */
    private $serializer;

    protected function setUp(): void
    {

        $this->serializer = SerializerBuilder::create()
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
            ->build();

        $client = new DefaultClient($clientOption);
        $kucoinRestService = $client->restService();
        $this->api = $kucoinRestService->getMarginService()->getCreditApi();
    }


    /**
     * getLoanMarket
     * Get Loan Market
     * /api/v3/project/list
     */
    public function testGetLoanMarket()
    {
        $builder = GetLoanMarketReq::builder();
        $builder->setCurrency("DOGE");
        $req = $builder->build();
        $resp = $this->api->getLoanMarket($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->purchaseEnable);
            self::assertNotNull($item->redeemEnable);
            self::assertNotNull($item->increment);
            self::assertNotNull($item->minPurchaseSize);
            self::assertNotNull($item->minInterestRate);
            self::assertNotNull($item->maxInterestRate);
            self::assertNotNull($item->interestIncrement);
            self::assertNotNull($item->maxPurchaseSize);
            self::assertNotNull($item->marketInterestRate);
            self::assertNotNull($item->autoPurchaseEnable);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getLoanMarketInterestRate
     * Get Loan Market Interest Rate
     * /api/v3/project/marketInterestRate
     */
    public function testGetLoanMarketInterestRate()
    {
        $builder = GetLoanMarketInterestRateReq::builder();
        $builder->setCurrency("DOGE");
        $req = $builder->build();
        $resp = $this->api->getLoanMarketInterestRate($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->time);
            self::assertNotNull($item->marketInterestRate);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * purchase
     * Purchase
     * /api/v3/purchase
     */
    public function testPurchase()
    {
        $builder = PurchaseReq::builder();
        $builder->setCurrency("DOGE")->setSize('10')->setInterestRate('0.01');
        $req = $builder->build();
        $resp = $this->api->purchase($req);
        self::assertNotNull($resp->orderNo);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * modifyPurchase
     * Modify Purchase
     * /api/v3/lend/purchase/update
     */
    public function testModifyPurchase()
    {
        $builder = ModifyPurchaseReq::builder();
        $builder->setCurrency("DOGE")->setInterestRate("0.01")->setPurchaseOrderNo("68255650b553ee0007ce5756");
        $req = $builder->build();
        $resp = $this->api->modifyPurchase($req);
        self::assertNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPurchaseOrders
     * Get Purchase Orders
     * /api/v3/purchase/orders
     */
    public function testGetPurchaseOrders()
    {
        $builder = GetPurchaseOrdersReq::builder();
        $builder->setCurrency("DOGE")->setPurchaseOrderNo("68255650b553ee0007ce5756")->setStatus("PENDING");
        $req = $builder->build();
        $resp = $this->api->getPurchaseOrders($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->purchaseOrderNo);
            self::assertNotNull($item->purchaseSize);
            self::assertNotNull($item->matchSize);
            self::assertNotNull($item->interestRate);
            self::assertNotNull($item->incomeSize);
            self::assertNotNull($item->applyTime);
            self::assertNotNull($item->status);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * redeem
     * Redeem
     * /api/v3/redeem
     */
    public function testRedeem()
    {
        $builder = RedeemReq::builder();
        $builder->setCurrency("DOGE")->setSize("10")->setPurchaseOrderNo("68255650b553ee0007ce5756");
        $req = $builder->build();
        $resp = $this->api->redeem($req);
        self::assertNotNull($resp->orderNo);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getRedeemOrders
     * Get Redeem Orders
     * /api/v3/redeem/orders
     */
    public function testGetRedeemOrders()
    {
        $builder = GetRedeemOrdersReq::builder();
        $builder->setStatus("DONE")->setCurrency("DOGE");
        $req = $builder->build();
        $resp = $this->api->getRedeemOrders($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->purchaseOrderNo);
            self::assertNotNull($item->redeemOrderNo);
            self::assertNotNull($item->redeemSize);
            self::assertNotNull($item->receiptSize);
            self::assertNotNull($item->status);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }


}