<?php

namespace Tests\e2e\rest\Futures;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Futures\Positions\AddIsolatedMarginReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\BatchSwitchMarginModeReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\GetCrossMarginLeverageReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\GetCrossMarginRiskLimitReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\GetIsolatedMarginRiskLimitReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\GetMarginModeReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\GetMaxOpenSizeReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\GetMaxWithdrawMarginReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\GetPositionDetailsReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\GetPositionListReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\GetPositionsHistoryReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\ModifyAutoDepositStatusReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\ModifyIsolatedMarginRiskLimtReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\ModifyMarginLeverageReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\PositionsApi;
use KuCoin\UniversalSDK\Generate\Futures\Positions\RemoveIsolatedMarginReq;
use KuCoin\UniversalSDK\Generate\Futures\Positions\SwitchMarginModeReq;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;
use PHPUnit\Framework\TestCase;

class FuturesPositionsTest extends TestCase
{
    /**
     * @var PositionsApi $api
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
        $this->api = $kucoinRestService->getFuturesService()->getPositionsApi();
    }


    /**
     * getMarginMode
     * Get Margin Mode
     * /api/v2/position/getMarginMode
     */
    public function testGetMarginMode()
    {
        $builder = GetMarginModeReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getMarginMode($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->marginMode);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * switchMarginMode
     * Switch Margin Mode
     * /api/v2/position/changeMarginMode
     */
    public function testSwitchMarginMode()
    {
        $builder = SwitchMarginModeReq::builder();
        $builder->setSymbol("XBTUSDTM")->setMarginMode("ISOLATED");
        $req = $builder->build();
        $resp = $this->api->switchMarginMode($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->marginMode);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * batchSwitchMarginMode
     * Batch Switch Margin Mode
     * /api/v2/position/batchChangeMarginMode
     */
    public function testBatchSwitchMarginMode() {
        $builder = BatchSwitchMarginModeReq::builder();
        $builder->setMarginMode("ISOLATED")->setSymbols(['XBTUSDTM', 'DOGEUSDTM']);
        $req = $builder->build();
        $resp = $this->api->batchSwitchMarginMode($req);
        self::assertNotNull($resp->marginMode);
        foreach($resp->errors as $item) {
            self::assertNotNull($item->code);
            self::assertNotNull($item->msg);
            self::assertNotNull($item->symbol);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getMaxOpenSize
     * Get Max Open Size
     * /api/v2/getMaxOpenSize
     */
    public function testGetMaxOpenSize()
    {
        $builder = GetMaxOpenSizeReq::builder();
        $builder->setSymbol("XBTUSDTM")->setPrice("1000")->setLeverage("10");
        $req = $builder->build();
        $resp = $this->api->getMaxOpenSize($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->maxBuyOpenSize);
        self::assertNotNull($resp->maxSellOpenSize);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPositionDetails
     * Get Position Details
     * /api/v1/position
     */
    public function testGetPositionDetails()
    {
        $builder = GetPositionDetailsReq::builder();
        $builder->setSymbol("DOGEUSDTM");
        $req = $builder->build();
        $resp = $this->api->getPositionDetails($req);
        self::assertNotNull($resp->id);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->crossMode);
        self::assertNotNull($resp->delevPercentage);
        self::assertNotNull($resp->openingTimestamp);
        self::assertNotNull($resp->currentTimestamp);
        self::assertNotNull($resp->currentQty);
        self::assertNotNull($resp->currentCost);
        self::assertNotNull($resp->currentComm);
        self::assertNotNull($resp->unrealisedCost);
        self::assertNotNull($resp->realisedGrossCost);
        self::assertNotNull($resp->realisedCost);
        self::assertNotNull($resp->isOpen);
        self::assertNotNull($resp->markPrice);
        self::assertNotNull($resp->markValue);
        self::assertNotNull($resp->posCost);
        self::assertNotNull($resp->posInit);
        self::assertNotNull($resp->posMargin);
        self::assertNotNull($resp->realisedGrossPnl);
        self::assertNotNull($resp->realisedPnl);
        self::assertNotNull($resp->unrealisedPnl);
        self::assertNotNull($resp->unrealisedPnlPcnt);
        self::assertNotNull($resp->unrealisedRoePcnt);
        self::assertNotNull($resp->avgEntryPrice);
        self::assertNotNull($resp->liquidationPrice);
        self::assertNotNull($resp->bankruptPrice);
        self::assertNotNull($resp->settleCurrency);
        self::assertNotNull($resp->isInverse);
        self::assertNotNull($resp->marginMode);
        self::assertNotNull($resp->positionSide);
        self::assertNotNull($resp->leverage);
        self::assertNotNull($resp->maintMarginReq);
        self::assertNotNull($resp->posMaint);
        self::assertNotNull($resp->maintainMargin);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPositionList
     * Get Position List
     * /api/v1/positions
     */
    public function testGetPositionList()
    {
        $builder = GetPositionListReq::builder();
        $builder->setCurrency("USDT");
        $req = $builder->build();
        $resp = $this->api->getPositionList($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->id);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->crossMode);
            self::assertNotNull($item->delevPercentage);
            self::assertNotNull($item->openingTimestamp);
            self::assertNotNull($item->currentTimestamp);
            self::assertNotNull($item->currentQty);
            self::assertNotNull($item->currentCost);
            self::assertNotNull($item->currentComm);
            self::assertNotNull($item->unrealisedCost);
            self::assertNotNull($item->realisedGrossCost);
            self::assertNotNull($item->realisedCost);
            self::assertNotNull($item->isOpen);
            self::assertNotNull($item->markPrice);
            self::assertNotNull($item->markValue);
            self::assertNotNull($item->posCost);
            self::assertNotNull($item->posInit);
            self::assertNotNull($item->posMargin);
            self::assertNotNull($item->realisedGrossPnl);
            self::assertNotNull($item->realisedPnl);
            self::assertNotNull($item->unrealisedPnl);
            self::assertNotNull($item->unrealisedPnlPcnt);
            self::assertNotNull($item->unrealisedRoePcnt);
            self::assertNotNull($item->avgEntryPrice);
            self::assertNotNull($item->liquidationPrice);
            self::assertNotNull($item->bankruptPrice);
            self::assertNotNull($item->settleCurrency);
            self::assertNotNull($item->isInverse);
            self::assertNotNull($item->marginMode);
            self::assertNotNull($item->positionSide);
            self::assertNotNull($item->leverage);
            self::assertNotNull($item->maintMarginReq);
            self::assertNotNull($item->posMaint);
            self::assertNotNull($item->maintainMargin);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getPositionsHistory
     * Get Positions History
     * /api/v1/history-positions
     */
    public function testGetPositionsHistory()
    {
        $builder = GetPositionsHistoryReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getPositionsHistory($req);
        self::assertNotNull($resp->currentPage);
        self::assertNotNull($resp->pageSize);
        self::assertNotNull($resp->totalNum);
        self::assertNotNull($resp->totalPage);
        foreach ($resp->items as $item) {
            self::assertNotNull($item->closeId);
            self::assertNotNull($item->userId);
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->settleCurrency);
            self::assertNotNull($item->leverage);
            self::assertNotNull($item->type);
            self::assertNotNull($item->pnl);
            self::assertNotNull($item->realisedGrossCost);
            self::assertNotNull($item->withdrawPnl);
            self::assertNotNull($item->tradeFee);
            self::assertNotNull($item->fundingFee);
            self::assertNotNull($item->openTime);
            self::assertNotNull($item->closeTime);
            self::assertNotNull($item->openPrice);
            self::assertNotNull($item->closePrice);
            self::assertNotNull($item->marginMode);
            self::assertNotNull($item->realisedGrossCostNew);
            self::assertNotNull($item->tax);
            self::assertNotNull($item->roe);
            self::assertNotNull($item->side);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getMaxWithdrawMargin
     * Get Max Withdraw Margin
     * /api/v1/margin/maxWithdrawMargin
     */
    public function testGetMaxWithdrawMargin()
    {
        $builder = GetMaxWithdrawMarginReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getMaxWithdrawMargin($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getCrossMarginLeverage
     * Get Cross Margin Leverage
     * /api/v2/getCrossUserLeverage
     */
    public function testGetCrossMarginLeverage()
    {
        $builder = GetCrossMarginLeverageReq::builder();
        $builder->setSymbol("XBTUSDTM");
        $req = $builder->build();
        $resp = $this->api->getCrossMarginLeverage($req);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->leverage);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * modifyMarginLeverage
     * Modify Cross Margin Leverage
     * /api/v2/changeCrossUserLeverage
     */
    public function testModifyMarginLeverage()
    {
        $builder = ModifyMarginLeverageReq::builder();
        $builder->setSymbol("XBTUSDTM")->setLeverage("20");
        $req = $builder->build();
        $resp = $this->api->modifyMarginLeverage($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * addIsolatedMargin
     * Add Isolated Margin
     * /api/v1/position/margin/deposit-margin
     */
    public function testAddIsolatedMargin()
    {
        $builder = AddIsolatedMarginReq::builder();
        $builder->setSymbol("DOGEUSDTM")->setMargin(2)->setBizNo("31202362081988608111");
        $req = $builder->build();
        $resp = $this->api->addIsolatedMargin($req);
        self::assertNotNull($resp->id);
        self::assertNotNull($resp->symbol);
        self::assertNotNull($resp->autoDeposit);
        self::assertNotNull($resp->maintMarginReq);
        self::assertNotNull($resp->riskLimit);
        self::assertNotNull($resp->realLeverage);
        self::assertNotNull($resp->crossMode);
        self::assertNotNull($resp->delevPercentage);
        self::assertNotNull($resp->openingTimestamp);
        self::assertNotNull($resp->currentTimestamp);
        self::assertNotNull($resp->currentQty);
        self::assertNotNull($resp->currentCost);
        self::assertNotNull($resp->currentComm);
        self::assertNotNull($resp->unrealisedCost);
        self::assertNotNull($resp->realisedGrossCost);
        self::assertNotNull($resp->realisedCost);
        self::assertNotNull($resp->isOpen);
        self::assertNotNull($resp->markPrice);
        self::assertNotNull($resp->markValue);
        self::assertNotNull($resp->posCost);
        self::assertNotNull($resp->posCross);
        self::assertNotNull($resp->posInit);
        self::assertNotNull($resp->posComm);
        self::assertNotNull($resp->posLoss);
        self::assertNotNull($resp->posMargin);
        self::assertNotNull($resp->posMaint);
        self::assertNotNull($resp->maintMargin);
        self::assertNotNull($resp->realisedGrossPnl);
        self::assertNotNull($resp->realisedPnl);
        self::assertNotNull($resp->unrealisedPnl);
        self::assertNotNull($resp->unrealisedPnlPcnt);
        self::assertNotNull($resp->unrealisedRoePcnt);
        self::assertNotNull($resp->avgEntryPrice);
        self::assertNotNull($resp->liquidationPrice);
        self::assertNotNull($resp->bankruptPrice);
        self::assertNotNull($resp->settleCurrency);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * removeIsolatedMargin
     * Remove Isolated Margin
     * /api/v1/margin/withdrawMargin
     */
    public function testRemoveIsolatedMargin()
    {
        $builder = RemoveIsolatedMarginReq::builder();
        $builder->setSymbol("DOGEUSDTM")->setWithdrawAmount(1);
        $req = $builder->build();
        $resp = $this->api->removeIsolatedMargin($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * getCrossMarginRiskLimit
     * Get Cross Margin Risk Limit
     * /api/v2/batchGetCrossOrderLimit
     */
    public function testGetCrossMarginRiskLimit()
    {
        $builder = GetCrossMarginRiskLimitReq::builder();
        $builder->setSymbol("XBTUSDTM")->setTotalMargin("1000")->setLeverage(1);
        $req = $builder->build();
        $resp = $this->api->getCrossMarginRiskLimit($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->maxOpenSize);
            self::assertNotNull($item->maxOpenValue);
            self::assertNotNull($item->totalMargin);
            self::assertNotNull($item->price);
            self::assertNotNull($item->leverage);
            self::assertNotNull($item->mmr);
            self::assertNotNull($item->imr);
            self::assertNotNull($item->currency);
        }
    }

    /**
     * getIsolatedMarginRiskLimit
     * Get Isolated Margin Risk Limit
     * /api/v1/contracts/risk-limit/{symbol}
     */
    public function testGetIsolatedMarginRiskLimit()
    {
        $builder = GetIsolatedMarginRiskLimitReq::builder();
        $builder->setSymbol('XBTUSDTM');
        $req = $builder->build();
        $resp = $this->api->getIsolatedMarginRiskLimit($req);
        foreach ($resp->data as $item) {
            self::assertNotNull($item->symbol);
            self::assertNotNull($item->level);
            self::assertNotNull($item->maxRiskLimit);
            self::assertNotNull($item->minRiskLimit);
            self::assertNotNull($item->maxLeverage);
            self::assertNotNull($item->initialMargin);
            self::assertNotNull($item->maintainMargin);
        }

        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * modifyIsolatedMarginRiskLimt
     * Modify Isolated Margin Risk Limit
     * /api/v1/position/risk-limit-level/change
     */
    public function testModifyIsolatedMarginRiskLimt()
    {
        $builder = ModifyIsolatedMarginRiskLimtReq::builder();
        $builder->setSymbol("XBTUSDTM")->setLevel(10);
        $req = $builder->build();
        $resp = $this->api->modifyIsolatedMarginRiskLimt($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }

    /**
     * modifyAutoDepositStatus
     * Modify Isolated Margin Auto-Deposit Status
     * /api/v1/position/margin/auto-deposit-status
     */
    public function testModifyAutoDepositStatus()
    {
        $builder = ModifyAutoDepositStatusReq::builder();
        $builder->setSymbol("DOGEUSDTM")->setStatus(true);
        $req = $builder->build();
        $resp = $this->api->modifyAutoDepositStatus($req);
        self::assertNotNull($resp->data);
        Logger::info($resp->jsonSerialize($this->serializer));
    }


}