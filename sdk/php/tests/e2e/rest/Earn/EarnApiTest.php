<?php

namespace Tests\e2e\rest\Earn;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Earn\Earn\EarnApi;
use KuCoin\UniversalSDK\Generate\Earn\Earn\GetAccountHoldingReq;
use KuCoin\UniversalSDK\Generate\Earn\Earn\GetETHStakingProductsReq;
use KuCoin\UniversalSDK\Generate\Earn\Earn\GetKcsStakingProductsReq;
use KuCoin\UniversalSDK\Generate\Earn\Earn\GetPromotionProductsReq;
use KuCoin\UniversalSDK\Generate\Earn\Earn\GetRedeemPreviewReq;
use KuCoin\UniversalSDK\Generate\Earn\Earn\GetSavingsProductsReq;
use KuCoin\UniversalSDK\Generate\Earn\Earn\GetStakingProductsReq;
use KuCoin\UniversalSDK\Generate\Earn\Earn\PurchaseReq;
use KuCoin\UniversalSDK\Generate\Earn\Earn\RedeemReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class EarnApiTest extends TestCase
{
    /**
     * @var EarnApi $api
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
        $this->api = $kucoinRestService->getEarnService()->getEarnApi();
    }

    /**
     * purchase
     * Purchase
     * /api/v1/earn/orders
     */
    public function testPurchase()
    {
        $builder = PurchaseReq::builder();
        $builder->setProductId("2152")->setAmount("10")->setAccountType("MAIN");
        $req = $builder->build();
        $resp = $this->api->purchase($req);
        self::assertNotNull($resp->orderId);
        self::assertNotNull($resp->orderTxId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getRedeemPreview
     * Get Redeem Preview
     * /api/v1/earn/redeem-preview
     */
    public function testGetRedeemPreview()
    {
        $builder = GetRedeemPreviewReq::builder();
        $builder->setOrderId("2155441")->setFromAccountType("MAIN");
        $req = $builder->build();
        $resp = $this->api->getRedeemPreview($req);
        self::assertNotNull($resp->currency);
        self::assertNotNull($resp->redeemAmount);
        self::assertNotNull($resp->penaltyInterestAmount);
        self::assertNotNull($resp->redeemPeriod);
        self::assertNotNull($resp->deliverTime);
        self::assertNotNull($resp->manualRedeemable);
        self::assertNotNull($resp->redeemAll);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * redeem
     * Redeem
     * /api/v1/earn/orders
     */
    public function testRedeem()
    {
        $builder = RedeemReq::builder();
        $builder->setOrderId("2155441")->setAmount("10")->setFromAccountType("MAIN")->setConfirmPunishRedeem("1");
        $req = $builder->build();
        $resp = $this->api->redeem($req);
        self::assertNotNull($resp->orderTxId);
        self::assertNotNull($resp->deliverTime);
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->amount);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getSavingsProducts
     * Get Savings Products
     * /api/v1/earn/saving/products
     */
    public function testGetSavingsProducts() {
        $builder = GetSavingsProductsReq::builder();
        $builder->setCurrency("USDT");
        $req = $builder->build();
        $resp = $this->api->getSavingsProducts($req);
        foreach($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->category);
            self::assertNotNull($item->type);
            self::assertNotNull($item->precision);
            self::assertNotNull($item->productUpperLimit);
            self::assertNotNull($item->userUpperLimit);
            self::assertNotNull($item->userLowerLimit);
            self::assertNotNull($item->redeemPeriod);
            self::assertNotNull($item->lockStartTime);
            self::assertNotNull($item->applyStartTime);
            self::assertNotNull($item->returnRate);
            self::assertNotNull($item->incomeCurrency);
            self::assertNotNull($item->earlyRedeemSupported);
            self::assertNotNull($item->productRemainAmount);
            self::assertNotNull($item->status);
            self::assertNotNull($item->redeemType);
            self::assertNotNull($item->incomeReleaseType);
            self::assertNotNull($item->interestDate);
            self::assertNotNull($item->duration);
            self::assertNotNull($item->newUserOnly);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPromotionProducts
     * Get Promotion Products
     * /api/v1/earn/promotion/products
     */
    public function testGetPromotionProducts() {
        $builder = GetPromotionProductsReq::builder();
        $builder->setCurrency("USDT");
        $req = $builder->build();
        $resp = $this->api->getPromotionProducts($req);
        foreach($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->category);
            self::assertNotNull($item->type);
            self::assertNotNull($item->precision);
            self::assertNotNull($item->productUpperLimit);
            self::assertNotNull($item->userUpperLimit);
            self::assertNotNull($item->userLowerLimit);
            self::assertNotNull($item->redeemPeriod);
            self::assertNotNull($item->lockStartTime);
            self::assertNotNull($item->applyStartTime);
            self::assertNotNull($item->returnRate);
            self::assertNotNull($item->incomeCurrency);
            self::assertNotNull($item->earlyRedeemSupported);
            self::assertNotNull($item->productRemainAmount);
            self::assertNotNull($item->status);
            self::assertNotNull($item->redeemType);
            self::assertNotNull($item->incomeReleaseType);
            self::assertNotNull($item->interestDate);
            self::assertNotNull($item->duration);
            self::assertNotNull($item->newUserOnly);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getStakingProducts
     * Get Staking Products
     * /api/v1/earn/staking/products
     */
    public function testGetStakingProducts() {
        $builder = GetStakingProductsReq::builder();
        $builder->setCurrency("ATOM");
        $req = $builder->build();
        $resp = $this->api->getStakingProducts($req);
        foreach($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->category);
            self::assertNotNull($item->type);
            self::assertNotNull($item->precision);
            self::assertNotNull($item->productUpperLimit);
            self::assertNotNull($item->userUpperLimit);
            self::assertNotNull($item->userLowerLimit);
            self::assertNotNull($item->redeemPeriod);
            self::assertNotNull($item->lockStartTime);
            self::assertNotNull($item->applyStartTime);
            self::assertNotNull($item->returnRate);
            self::assertNotNull($item->incomeCurrency);
            self::assertNotNull($item->earlyRedeemSupported);
            self::assertNotNull($item->productRemainAmount);
            self::assertNotNull($item->status);
            self::assertNotNull($item->redeemType);
            self::assertNotNull($item->incomeReleaseType);
            self::assertNotNull($item->interestDate);
            self::assertNotNull($item->duration);
            self::assertNotNull($item->newUserOnly);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getKcsStakingProducts
     * Get KCS Staking Products
     * /api/v1/earn/kcs-staking/products
     */
    public function testGetKcsStakingProducts() {
        $builder = GetKcsStakingProductsReq::builder();
        $builder->setCurrency("KCS");
        $req = $builder->build();
        $resp = $this->api->getKcsStakingProducts($req);
        foreach($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->category);
            self::assertNotNull($item->type);
            self::assertNotNull($item->precision);
            self::assertNotNull($item->productUpperLimit);
            self::assertNotNull($item->userUpperLimit);
            self::assertNotNull($item->userLowerLimit);
            self::assertNotNull($item->redeemPeriod);
            self::assertNotNull($item->lockStartTime);
            self::assertNotNull($item->applyStartTime);
            self::assertNotNull($item->returnRate);
            self::assertNotNull($item->incomeCurrency);
            self::assertNotNull($item->earlyRedeemSupported);
            self::assertNotNull($item->productRemainAmount);
            self::assertNotNull($item->status);
            self::assertNotNull($item->redeemType);
            self::assertNotNull($item->incomeReleaseType);
            self::assertNotNull($item->interestDate);
            self::assertNotNull($item->duration);
            self::assertNotNull($item->newUserOnly);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getETHStakingProducts
     * Get ETH Staking Products
     * /api/v1/earn/eth-staking/products
     */
    public function testGetETHStakingProducts() {
        $builder = GetETHStakingProductsReq::builder();
        $builder->setCurrency("eth");
        $req = $builder->build();
        $resp = $this->api->getETHStakingProducts($req);
        foreach($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->category);
            self::assertNotNull($item->type);
            self::assertNotNull($item->precision);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->incomeCurrency);
            self::assertNotNull($item->returnRate);
            self::assertNotNull($item->userLowerLimit);
            self::assertNotNull($item->userUpperLimit);
            self::assertNotNull($item->productUpperLimit);
            self::assertNotNull($item->productRemainAmount);
            self::assertNotNull($item->redeemPeriod);
            self::assertNotNull($item->redeemType);
            self::assertNotNull($item->incomeReleaseType);
            self::assertNotNull($item->applyStartTime);
            self::assertNotNull($item->lockStartTime);
            self::assertNotNull($item->interestDate);
            self::assertNotNull($item->newUserOnly);
            self::assertNotNull($item->earlyRedeemSupported);
            self::assertNotNull($item->duration);
            self::assertNotNull($item->status);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getAccountHolding
     * Get Account Holding
     * /api/v1/earn/hold-assets
     */
    public function testGetAccountHolding() {
        $builder = GetAccountHoldingReq::builder();
        $builder->setCurrency("USDT")->setProductId("2152")->setProductCategory("DEMAND");
        $req = $builder->build();
        $resp = $this->api->getAccountHolding($req);
        self::assertNotNull($resp->totalNum);
        foreach($resp->items as $item) {
            self::assertNotNull($item->orderId);
            self::assertNotNull($item->productId);
            self::assertNotNull($item->productCategory);
            self::assertNotNull($item->productType);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->incomeCurrency);
            self::assertNotNull($item->returnRate);
            self::assertNotNull($item->holdAmount);
            self::assertNotNull($item->redeemedAmount);
            self::assertNotNull($item->redeemingAmount);
            self::assertNotNull($item->lockStartTime);
            self::assertNotNull($item->purchaseTime);
            self::assertNotNull($item->redeemPeriod);
            self::assertNotNull($item->status);
            self::assertNotNull($item->earlyRedeemSupported);
        }

        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalPage);
        Logger::info($resp->jsonSerialize($this->serializer));
    }



}