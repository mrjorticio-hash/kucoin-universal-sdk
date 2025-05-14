<?php

namespace Tests\e2e\rest\Account;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Extension\Interceptor\Logging;
use KuCoin\UniversalSDK\Generate\Account\Withdrawal\CancelWithdrawalReq;
use KuCoin\UniversalSDK\Generate\Account\Withdrawal\GetWithdrawalHistoryOldReq;
use KuCoin\UniversalSDK\Generate\Account\Withdrawal\GetWithdrawalHistoryReq;
use KuCoin\UniversalSDK\Generate\Account\Withdrawal\GetWithdrawalQuotasReq;
use KuCoin\UniversalSDK\Generate\Account\Withdrawal\WithdrawalApi;
use KuCoin\UniversalSDK\Generate\Account\Withdrawal\WithdrawalV1Req;
use KuCoin\UniversalSDK\Generate\Account\Withdrawal\WithdrawalV3Req;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class AccountWithdrawTest extends TestCase
{
    /**
     * @var WithdrawalApi $api
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
            ->setInterceptors([new Logging()])
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
        $this->api = $kucoinRestService->getAccountService()->getWithdrawalApi();
    }


    /**
     * getWithdrawalQuotas
     * Get Withdrawal Quotas
     * /api/v1/withdrawals/quotas
     */
    public function testGetWithdrawalQuotas()
    {
        $builder = GetWithdrawalQuotasReq::builder();
        $builder->setCurrency("USDT")->setChain("bsc");
        $req = $builder->build();
        $resp = $this->api->getWithdrawalQuotas($req);
        self::assertNotNull($resp->currency);
        self::assertNotNull($resp->limitBTCAmount);
        self::assertNotNull($resp->usedBTCAmount);
        self::assertNotNull($resp->quotaCurrency);
        self::assertNotNull($resp->limitQuotaCurrencyAmount);
        self::assertNotNull($resp->usedQuotaCurrencyAmount);
        self::assertNotNull($resp->remainAmount);
        self::assertNotNull($resp->availableAmount);
        self::assertNotNull($resp->withdrawMinFee);
        self::assertNotNull($resp->innerWithdrawMinFee);
        self::assertNotNull($resp->withdrawMinSize);
        self::assertNotNull($resp->isWithdrawEnabled);
        self::assertNotNull($resp->precision);
        self::assertNotNull($resp->chain);
        self::assertNotNull($resp->lockedAmount);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * withdrawalV3
     * Withdraw (V3)
     * /api/v3/withdrawals
     */
    public function testWithdrawalV3()
    {
        $builder = WithdrawalV3Req::builder();
        $builder->setCurrency("USDT")->setChain("bsc")->setAmount("20")->setIsInner(false)->setRemark("***")
            ->setFeeDeductType("INTERNAL")->setToAddress("******")->setWithdrawType("ADDRESS");
        $req = $builder->build();
        $resp = $this->api->withdrawalV3($req);
        self::assertNotNull($resp->withdrawalId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * cancelWithdrawal
     * Cancel Withdrawal
     * /api/v1/withdrawals/{withdrawalId}
     */
    public function testCancelWithdrawal()
    {
        $builder = CancelWithdrawalReq::builder();
        $builder->setWithdrawalId("682411bd537806000718515a");
        $req = $builder->build();
        $resp = $this->api->cancelWithdrawal($req);
        self::assertNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getWithdrawalHistory
     * Get Withdrawal History
     * /api/v1/withdrawals
     */
    public function testGetWithdrawalHistory()
    {
        $builder = GetWithdrawalHistoryReq::builder();
        $builder->setCurrency("USDT");
        $req = $builder->build();
        $resp = $this->api->getWithdrawalHistory($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->chain);
            self::assertNotNull($item->status);
            self::assertNotNull($item->address);
            self::assertNotNull($item->memo);
            self::assertNotNull($item->isInner);
            self::assertNotNull($item->amount);
            self::assertNotNull($item->fee);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->updatedAt);
            self::assertNotNull($item->remark);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getWithdrawalHistoryOld
     * Get Withdrawal History - Old
     * /api/v1/hist-withdrawals
     */
    public function testGetWithdrawalHistoryOld()
    {
        $builder = GetWithdrawalHistoryOldReq::builder();
        $builder->setCurrency("USDT");
        $req = $builder->build();
        $resp = $this->api->getWithdrawalHistoryOld($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->currency);
            self::assertNotNull($item->createAt);
            self::assertNotNull($item->amount);
            self::assertNotNull($item->address);
            self::assertNotNull($item->walletTxId);
            self::assertNotNull($item->isInner);
            self::assertNotNull($item->status);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * withdrawalV1
     * Withdraw - V1
     * /api/v1/withdrawals
     */
    public function testWithdrawalV1()
    {
        $builder = WithdrawalV1Req::builder();
        $builder->setCurrency("USDT")->setChain("bsc")->setAmount("20")->setIsInner(false)->setRemark("***")
            ->setFeeDeductType("INTERNAL")->setAddress("****");

        $req = $builder->build();
        $resp = $this->api->withdrawalV1($req);
        self::assertNotNull($resp->withdrawalId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }


}