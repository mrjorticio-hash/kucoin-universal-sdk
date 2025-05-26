<?php

/**
 * DISCLAIMER:
 * This strategy is provided for educational and illustrative purposes only. It is not intended to be used as financial
 * or investment advice. Trading cryptocurrencies involves significant risk, and you should carefully consider your
 * investment objectives, level of experience, and risk appetite. Past performance of any trading strategy is not
 * indicative of future results.
 *
 * The authors and contributors of this example are not responsible for any financial losses or damages that may occur
 * from using this code. Use it at your own discretion and consult with a professional financial advisor if necessary.
 */


use KuCoin\UniversalSDK\Api\DefaultClient;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Generate\Account\Account\GetCrossMarginAccountReq;
use KuCoin\UniversalSDK\Generate\Account\Account\GetFuturesAccountReq;
use KuCoin\UniversalSDK\Generate\Account\Account\GetSpotAccountListReq;
use KuCoin\UniversalSDK\Generate\Futures\FundingFees\GetCurrentFundingRateReq;
use KuCoin\UniversalSDK\Generate\Futures\Market\GetSymbolReq as FuturesGetSymbolReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\AddOrderReq as FuturesAddOrderReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\CancelOrderByIdReq as FuturesCancelOrderReq;
use KuCoin\UniversalSDK\Generate\Futures\Order\GetOrderByOrderIdReq as FuturesGetOrderByIdReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\AddOrderReq as MarginAddOrderReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\CancelOrderByOrderIdReq as MarginCancelOrderReq;
use KuCoin\UniversalSDK\Generate\Margin\Order\GetOrderByOrderIdReq as MarginGetOrderReq;
use KuCoin\UniversalSDK\Generate\Service\AccountService;
use KuCoin\UniversalSDK\Generate\Service\FuturesService;
use KuCoin\UniversalSDK\Generate\Service\MarginService;
use KuCoin\UniversalSDK\Generate\Service\SpotService;
use KuCoin\UniversalSDK\Generate\Spot\Market\GetTickerReq as SpotGetTickerReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\AddOrderSyncReq as SpotAddOrderSyncReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\CancelOrderByOrderIdSyncReq as SpotCancelOrderReq;
use KuCoin\UniversalSDK\Generate\Spot\Order\GetOrderByOrderIdReq as SpotGetOrderByIdReq;
use KuCoin\UniversalSDK\Model\ClientOptionBuilder;
use KuCoin\UniversalSDK\Model\Constants;
use KuCoin\UniversalSDK\Model\TransportOptionBuilder;

include '../vendor/autoload.php';

const SPOT_SYMBOL = 'DOGE-USDT';
const FUTURES_SYMBOL = 'DOGEUSDTM';
const BASE_CURRENCY = 'USDT';
const MAX_PLACE_ORDER_WAIT_TIME_SEC = 15;

class MarketSide
{
    const BUY = 'buy';
    const SELL = 'sell';
}

class MarketType
{
    const SPOT = 'SPOT';
    const MARGIN = 'MARGIN';
    const FUTURES = 'FUTURES';
}

function initClient(): DefaultClient
{
    $key = getenv('API_KEY') ?: '';
    $secret = getenv('API_SECRET') ?: '';
    $passphrase = getenv('API_PASSPHRASE') ?: '';

    $transportOption = (new TransportOptionBuilder())
        ->setKeepAlive(true)
        ->setMaxConnections(10)
        ->build();

    $clientOption = (new ClientOptionBuilder())
        ->setKey($key)
        ->setSecret($secret)
        ->setPassphrase($passphrase)
        ->setSpotEndpoint(Constants::GLOBAL_API_ENDPOINT)
        ->setFuturesEndpoint(Constants::GLOBAL_FUTURES_API_ENDPOINT)
        ->setBrokerEndpoint(Constants::GLOBAL_BROKER_API_ENDPOINT)
        ->setTransportOption($transportOption)
        ->build();

    return new DefaultClient($clientOption);
}


function checkAvailableBalance(AccountService $accountService, float $price, float $amount, string $marketType): bool
{
    $tradeValue = $price * $amount;

    if ($marketType === MarketType::SPOT) {
        $request = GetSpotAccountListReq::builder()
            ->setType("trade")
            ->setCurrency(BASE_CURRENCY)
            ->build();

        $resp = $accountService->getAccountApi()->getSpotAccountList($request);
        $available = array_reduce($resp->data, function ($sum, $item) {
            return $sum + floatval($item->available);
        }, 0.0);

        Logger::info(sprintf("[SPOT] Available %s balance: %.2f, Required: %.2f", BASE_CURRENCY, $available, $tradeValue));
        return $available >= $tradeValue;

    } elseif ($marketType === MarketType::FUTURES) {
        $request = GetFuturesAccountReq::builder()
            ->setCurrency(BASE_CURRENCY)
            ->build();

        $resp = $accountService->getAccountApi()->getFuturesAccount($request);
        $available = floatval($resp->availableBalance);

        Logger::info(sprintf("[FUTURES] Available %s balance: %.2f, Required: %.2f", BASE_CURRENCY, $available, $tradeValue));
        return $available >= $tradeValue;

    } elseif ($marketType === MarketType::MARGIN) {
        $request = GetCrossMarginAccountReq::builder()
            ->setQueryType("MARGIN")
            ->setQuoteCurrency("USDT")
            ->build();

        $resp = $accountService->getAccountApi()->getCrossMarginAccount($request);
        $available = floatval($resp->totalAssetOfQuoteCurrency);

        Logger::info(sprintf("[MARGIN] Available %s balance: %.2f, Required: %.2f", BASE_CURRENCY, $available, $tradeValue));
        return $available >= $tradeValue;
    }

    return false;
}


function getLastTradedPrice(SpotService $spotService, FuturesService $futuresService): array
{
    $spotPriceResp = $spotService->getMarketApi()->getTicker(
        SpotGetTickerReq::builder()->setSymbol(SPOT_SYMBOL)->build()
    );
    $spotPrice = floatval($spotPriceResp->price);

    $futuresSymbolResp = $futuresService->getMarketApi()->getSymbol(
        FuturesGetSymbolReq::builder()->setSymbol(FUTURES_SYMBOL)->build()
    );
    $futuresPrice = floatval($futuresSymbolResp->lastTradePrice);

    Logger::info(sprintf("[PRICE] Spot Price: %.5f, Futures Price: %.5f", $spotPrice, $futuresPrice));

    return [$spotPrice, $futuresPrice];
}

/**
 * Executes the funding rate arbitrage strategy.
 */
function fundingRateArbitrageStrategy(FuturesService $futuresService, SpotService $spotService, MarginService $marginService, $accountService, float $amount, float $threshold): void
{
    try {
        // Step 1: Fetch funding rate
        $fundingRateReq = GetCurrentFundingRateReq::builder()
            ->setSymbol(FUTURES_SYMBOL)
            ->build();

        $fundingRateResp = $futuresService->getFundingFeesApi()->getCurrentFundingRate($fundingRateReq);
        $fundingRate = floatval($fundingRateResp->value) * 100;

        Logger::info(sprintf("[STRATEGY] Funding rate for %s: %.6f%%", FUTURES_SYMBOL, $fundingRate));

        // Step 2: Check if funding rate meets threshold
        if (abs($fundingRate) < $threshold) {
            Logger::info(sprintf(
                "[STRATEGY] No arbitrage opportunity: Funding rate (%.6f%%) below threshold (%.3f%%).",
                $fundingRate, $threshold
            ));
            return;
        }

        // Step 3: Get spot and futures prices
        [$spotPrice, $futuresPrice] = getLastTradedPrice($spotService, $futuresService);

        // Get futures multiplier
        $futuresSymbolResp = $futuresService->getMarketApi()->getSymbol(
            FuturesGetSymbolReq::builder()->setSymbol(FUTURES_SYMBOL)->build()
        );
        $multiplier = floatval($futuresSymbolResp->multiplier);
        $futuresAmount = ceil($amount / $multiplier);

        if ($fundingRate > 0) {
            Logger::info("[STRATEGY] Positive funding rate. Executing LONG spot and SHORT futures arbitrage.");

            if (!checkAvailableBalance($accountService, $spotPrice, $amount, MarketType::SPOT)) {
                Logger::warn("[STRATEGY] Insufficient balance in spot account.");
                return;
            }
            if (!checkAvailableBalance($accountService, $futuresPrice, $amount, MarketType::FUTURES)) {
                Logger::warn("[STRATEGY] Insufficient balance in futures account.");
                return;
            }

            if (!addSpotOrderWaitFill($spotService, SPOT_SYMBOL, MarketSide::BUY, $amount, $spotPrice)) {
                Logger::warn("[STRATEGY] Failed to execute spot order.");
                return;
            }
            if (!addFuturesOrderWaitFill($futuresService, FUTURES_SYMBOL, MarketSide::SELL, $futuresAmount, $futuresPrice)) {
                Logger::warn("[STRATEGY] Failed to execute futures order.");
                return;
            }

        } else {
            Logger::info("[STRATEGY] Negative funding rate. Executing SHORT margin and LONG futures arbitrage.");

            if (!checkAvailableBalance($accountService, $spotPrice, $amount, MarketType::MARGIN)) {
                Logger::warn("[STRATEGY] Insufficient balance in margin account.");
                return;
            }
            if (!checkAvailableBalance($accountService, $futuresPrice, $amount, MarketType::FUTURES)) {
                Logger::warn("[STRATEGY] Insufficient balance in futures account.");
                return;
            }

            if (!addMarginOrderWaitFill($marginService, SPOT_SYMBOL, $amount, $spotPrice)) {
                Logger::warn("[STRATEGY] Failed to execute margin order.");
                return;
            }
            if (!addFuturesOrderWaitFill($futuresService, FUTURES_SYMBOL, MarketSide::BUY, $futuresAmount, $futuresPrice)) {
                Logger::warn("[STRATEGY] Failed to execute futures order.");
                return;
            }
        }

        Logger::info("[STRATEGY] Arbitrage execution completed successfully.");

    } catch (Throwable $e) {
        Logger::error("[STRATEGY] Error executing arbitrage strategy: " . $e->getMessage());
    }
}

/**
 * Places a spot order and waits for it to be filled.
 *
 * @return bool True if the order was filled, False if it was cancelled or failed.
 */
function addSpotOrderWaitFill(SpotService $spotService, string $symbol, string $side, float $amount, float $price): bool
{
    $orderReq = SpotAddOrderSyncReq::builder()
        ->setClientOid(bin2hex(random_bytes(16)))
        ->setSide($side === MarketSide::BUY ? "buy" : "sell")
        ->setSymbol($symbol)
        ->setType("limit")
        ->setRemark("arbitrage")
        ->setPrice(number_format($price, 4, '.', ''))
        ->setSize(number_format($amount, 4, '.', ''))
        ->build();

    $orderResp = $spotService->getOrderApi()->addOrderSync($orderReq);

    Logger::info(sprintf(
        "[SPOT ORDER] Placed %s order for %.4f %s at %.6f. Order ID: %s",
        strtoupper($side), $amount, $symbol, $price, $orderResp->orderId
    ));

    $deadline = microtime(true) + MAX_PLACE_ORDER_WAIT_TIME_SEC;

    while (microtime(true) < $deadline) {
        sleep(1);
        Logger::info("[SPOT ORDER] Checking order status...");

        $detailReq = SpotGetOrderByIdReq::builder()
            ->setSymbol($symbol)
            ->setOrderId($orderResp->orderId)
            ->build();

        $orderDetail = $spotService->getOrderApi()->getOrderByOrderId($detailReq);

        if (!$orderDetail->active) {
            Logger::info(sprintf(
                "[SPOT ORDER] Order filled successfully: %s %.4f %s. Order ID: %s",
                strtoupper($side), $amount, $symbol, $orderResp->orderId
            ));
            return true;
        }
    }

    Logger::warn(sprintf(
        "[SPOT ORDER] Order not filled within %d seconds. Cancelling order...",
        MAX_PLACE_ORDER_WAIT_TIME_SEC
    ));

    $cancelReq = SpotCancelOrderReq::builder()
        ->setOrderId($orderResp->orderId)
        ->setSymbol($symbol)
        ->build();

    $cancelResp = $spotService->getOrderApi()->cancelOrderByOrderIdSync($cancelReq);

    if ($cancelResp->status !== "done") {
        throw new RuntimeException("[SPOT ORDER] Failed to cancel order. Order ID: " . $orderResp->orderId);
    }

    Logger::info("[SPOT ORDER] Order cancelled successfully. Order ID: " . $orderResp->orderId);
    return false;
}


/**
 * Places a futures order and waits for it to be filled.
 *
 * @return bool True if the order was filled, False if cancelled or failed.
 */
function addFuturesOrderWaitFill(FuturesService $futuresService, string $symbol, string $side, int $amount, float $price): bool
{
    $orderReq = FuturesAddOrderReq::builder()
        ->setClientOid(bin2hex(random_bytes(16)))
        ->setSide($side === MarketSide::BUY ? "buy" : "sell")
        ->setSymbol($symbol)
        ->setType("limit")
        ->setMarginMode("CROSS")
        ->setRemark("arbitrage")
        ->setPrice(number_format($price, 4, '.', ''))
        ->setLeverage(1)
        ->setSize($amount)
        ->build();

    $orderResp = $futuresService->getOrderApi()->addOrder($orderReq);

    Logger::info(sprintf(
        "[FUTURES ORDER] Placed %s order for %d %s at %.6f. Order ID: %s",
        strtoupper($side), $amount, $symbol, $price, $orderResp->orderId
    ));

    $deadline = microtime(true) + MAX_PLACE_ORDER_WAIT_TIME_SEC;

    while (microtime(true) < $deadline) {
        sleep(1);
        Logger::info("[FUTURES ORDER] Checking order status...");

        $detailReq = FuturesGetOrderByIdReq::builder()
            ->setOrderId($orderResp->orderId)
            ->build();

        $orderDetail = $futuresService->getOrderApi()->getOrderByOrderId($detailReq);

        if ($orderDetail->status === "done") {
            Logger::info(sprintf(
                "[FUTURES ORDER] Order filled successfully: %s %d %s. Order ID: %s",
                strtoupper($side), $amount, $symbol, $orderResp->orderId
            ));
            return true;
        }
    }

    Logger::warn(sprintf(
        "[FUTURES ORDER] Order not filled within %d seconds. Cancelling order...",
        MAX_PLACE_ORDER_WAIT_TIME_SEC
    ));

    $cancelReq = FuturesCancelOrderReq::builder()
        ->setOrderId($orderResp->orderId)
        ->build();

    $cancelResp = $futuresService->getOrderApi()->cancelOrderById($cancelReq);

    if (!in_array($orderResp->orderId, $cancelResp->cancelledOrderIds)) {
        throw new RuntimeException("[FUTURES ORDER] Failed to cancel order. Order ID: " . $orderResp->orderId);
    }

    Logger::info("[FUTURES ORDER] Order cancelled successfully. Order ID: " . $orderResp->orderId);
    return false;
}

/**
 * Places a margin (cross) order and waits for it to be filled.
 *
 * @return bool True if the order was filled, False if cancelled or failed.
 */
function addMarginOrderWaitFill(MarginService $marginService, string $symbol, float $amount, float $price): bool
{
    $orderReq = MarginAddOrderReq::builder()
        ->setClientOid(bin2hex(random_bytes(16)))
        ->setSide("buy")
        ->setSymbol($symbol)
        ->setType("limit")
        ->setIsIsolated(false)
        ->setAutoBorrow(true)
        ->setAutoRepay(true)
        ->setPrice(number_format($price, 4, '.', ''))
        ->setSize(number_format($amount, 4, '.', ''))
        ->build();

    $orderResp = $marginService->getOrderApi()->addOrder($orderReq);

    Logger::info(sprintf(
        "[MARGIN ORDER] Placed BUY order for %.4f %s at %.6f. Order ID: %s",
        $amount, $symbol, $price, $orderResp->orderId
    ));

    $deadline = microtime(true) + MAX_PLACE_ORDER_WAIT_TIME_SEC;

    while (microtime(true) < $deadline) {
        sleep(1);
        Logger::info("[MARGIN ORDER] Checking order status...");

        $detailReq = MarginGetOrderReq::builder()
            ->setSymbol($symbol)
            ->setOrderId($orderResp->orderId)
            ->build();

        $orderDetail = $marginService->getOrderApi()->getOrderByOrderId($detailReq);

        if (!$orderDetail->active) {
            Logger::info(sprintf(
                "[MARGIN ORDER] Order filled successfully: BUY %.4f %s. Order ID: %s",
                $amount, $symbol, $orderResp->orderId
            ));
            return true;
        }
    }

    Logger::warn(sprintf(
        "[MARGIN ORDER] Order not filled within %d seconds. Cancelling order...",
        MAX_PLACE_ORDER_WAIT_TIME_SEC
    ));

    $cancelReq = MarginCancelOrderReq::builder()
        ->setOrderId($orderResp->orderId)
        ->setSymbol($symbol)
        ->build();

    $cancelResp = $marginService->getOrderApi()->cancelOrderByOrderId($cancelReq);

    if (!$cancelResp->orderId) {
        throw new RuntimeException("[MARGIN ORDER] Failed to cancel order. Order ID: " . $orderResp->orderId);
    }

    Logger::info("[MARGIN ORDER] Order cancelled successfully. Order ID: " . $orderResp->orderId);
    return false;
}


function main(): void
{
    Logger::info("Initializing APIs...");

    $client = initClient();
    $restService = $client->restService();

    $futuresService = $restService->getFuturesService();
    $spotService = $restService->getSpotService();
    $marginService = $restService->getMarginService();
    $accountService = $restService->getAccountService();

    $amount = 100.0;      // Amount to trade
    $threshold = 0.005;   // 0.5% minimum funding rate difference

    Logger::info("Starting funding rate arbitrage strategy...");
    fundingRateArbitrageStrategy($futuresService, $spotService, $marginService, $accountService, $amount, $threshold);
}


if (php_sapi_name() === 'cli') {
    try {
        main();
    } catch (Throwable $e) {
        Logger::error("Error running arbitrage strategy: " . $e->getMessage());
    }
}
