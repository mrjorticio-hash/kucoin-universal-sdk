<?php

namespace Tests\e2e\rest\Account;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Account\Transfer\FlexTransferReq;
use KuCoin\UniversalSDK\Generate\Account\Transfer\FuturesAccountTransferInReq;
use KuCoin\UniversalSDK\Generate\Account\Transfer\FuturesAccountTransferOutReq;
use KuCoin\UniversalSDK\Generate\Account\Transfer\GetFuturesAccountTransferOutLedgerReq;
use KuCoin\UniversalSDK\Generate\Account\Transfer\GetTransferQuotasReq;
use KuCoin\UniversalSDK\Generate\Account\Transfer\InnerTransferReq;
use KuCoin\UniversalSDK\Generate\Account\Transfer\SubAccountTransferReq;
use KuCoin\UniversalSDK\Generate\Account\Transfer\TransferApi;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AccountTransferTest extends TestCase
{
    /**
     * @var TransferApi $api
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
        $this->api = $kucoinRestService->getAccountService()->getTransferApi();
    }


    /**
     * getTransferQuotas
     * Get Transfer Quotas
     * /api/v1/accounts/transferable
     */
    public function testGetTransferQuotas()
    {
        $builder = GetTransferQuotasReq::builder();
        $builder->setCurrency("USDT")->setType("MAIN")->setTag("");
        $req = $builder->build();
        $resp = $this->api->getTransferQuotas($req);
        self::assertNotNull($resp->currency);
        self::assertNotNull($resp->balance);
        self::assertNotNull($resp->available);
        self::assertNotNull($resp->holds);
        self::assertNotNull($resp->transferable);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * flexTransfer
     * Flex Transfer
     * /api/v3/accounts/universal-transfer
     */
    public function testFlexTransfer()
    {
        $builder = FlexTransferReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setCurrency("USDT")->setAmount("1")->
        setFromUserId("6744227ce235b300012232d6")->setFromAccountType("MAIN")->
        setType("INTERNAL")->setToUserId("6744227ce235b300012232d6")->setToAccountType("TRADE");
        $req = $builder->build();
        $resp = $this->api->flexTransfer($req);
        self::assertNotNull($resp->orderId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * subAccountTransfer
     * Sub-account Transfer
     * /api/v2/accounts/sub-transfer
     */
    public function testSubAccountTransfer()
    {
        $builder = SubAccountTransferReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setCurrency("USDT")->setAmount("1")->setDirection("OUT")
            ->setAccountType("MAIN")->setSubAccountType("MAIN")->setSubUserId("6744227ce235b300012232d6");
        $req = $builder->build();
        $resp = $this->api->subAccountTransfer($req);
        self::assertNotNull($resp->orderId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * innerTransfer
     * Internal Transfer
     * /api/v2/accounts/inner-transfer
     */
    public function testInnerTransfer()
    {
        $builder = InnerTransferReq::builder();
        $builder->setClientOid(Uuid::uuid4()->toString())->setCurrency("USDT")
            ->setAmount("1")->setTo("main")->setFrom("trade");
        $req = $builder->build();
        $resp = $this->api->innerTransfer($req);
        self::assertNotNull($resp->orderId);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getFuturesAccountTransferOutLedger
     * Get Futures Account Transfer Out Ledger
     * /api/v1/transfer-list
     */
    public function testGetFuturesAccountTransferOutLedger()
    {
        $builder = GetFuturesAccountTransferOutLedgerReq::builder();
        $builder->setCurrency("USDT")->setType("MAIN");
        $req = $builder->build();
        $resp = $this->api->getFuturesAccountTransferOutLedger($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->applyId);
            self::assertNotNull($item->currency);
            self::assertNotNull($item->recRemark);
            self::assertNotNull($item->recSystem);
            self::assertNotNull($item->status);
            self::assertNotNull($item->amount);
            self::assertNotNull($item->reason);
            self::assertNotNull($item->offset);
            self::assertNotNull($item->createdAt);
            self::assertNotNull($item->remark);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * futuresAccountTransferOut
     * Futures Account Transfer Out
     * /api/v3/transfer-out
     */
    public function testFuturesAccountTransferOut()
    {
        $builder = FuturesAccountTransferOutReq::builder();
        $builder->setCurrency("USDT")->setAmount("1")->setRecAccountType("MAIN");
        $req = $builder->build();
        $resp = $this->api->futuresAccountTransferOut($req);
        self::assertNotNull($resp->applyId);
        self::assertNotNull($resp->bizNo);
        self::assertNotNull($resp->payAccountType);
        self::assertNotNull($resp->payTag);
        self::assertNotNull($resp->remark);
        self::assertNotNull($resp->recAccountType);
        self::assertNotNull($resp->recTag);
        self::assertNotNull($resp->recRemark);
        self::assertNotNull($resp->recSystem);
        self::assertNotNull($resp->status);
        self::assertNotNull($resp->currency);
        self::assertNotNull($resp->amount);
        self::assertNotNull($resp->fee);
        self::assertNotNull($resp->sn);
        self::assertNotNull($resp->reason);
        self::assertNotNull($resp->createdAt);
        self::assertNotNull($resp->updatedAt);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * futuresAccountTransferIn
     * Futures Account Transfer In
     * /api/v1/transfer-in
     */
    public function testFuturesAccountTransferIn()
    {
        $builder = FuturesAccountTransferInReq::builder();
        $builder->setCurrency("USDT")->setAmount("1")->setPayAccountType("MAIN");
        $req = $builder->build();
        $resp = $this->api->futuresAccountTransferIn($req);
        self::assertNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

}