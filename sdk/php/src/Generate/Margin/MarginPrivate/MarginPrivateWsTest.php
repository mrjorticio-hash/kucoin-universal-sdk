<?php
namespace KuCoin\UniversalSDK\Generate\Margin\MarginPrivate;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\WsMessage;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MarginPrivateWsTest extends TestCase
{
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
    }

    function hasAnyNoneNull($object): bool
    {
        $refClass = new ReflectionClass($object);
        $props = $refClass->getProperties();

        $excludeSize = 0;
        $totalSize = sizeof($props);
        foreach ($props as $prop) {
            $doc = $prop->getDocComment();

            if ($doc !== false && strpos($doc, "@Exclude") !== false) {
                $excludeSize++;
                continue;
            }

            $prop->setAccessible(true);

            $value = $prop->getValue($object);
            if ($value !== null) {
                return true;
            }
        }
        return $excludeSize === $totalSize;
    }

    /**
     * crossMarginPosition
     * Get Cross Margin Position change
     * /crossMarginPosition/margin/position
     */
    public function testCrossMarginPositionResponse()
    {
        $data =
            "{\"topic\":\"/margin/position\",\"subject\":\"debt.ratio\",\"type\":\"message\",\"userId\":\"633559791e1cbc0001f319bc\",\"channelType\":\"private\",\"data\":{\"debtRatio\":0,\"totalAsset\":0.00052431772284080000000,\"marginCoefficientTotalAsset\":\"0.0005243177228408\",\"totalDebt\":\"0\",\"assetList\":{\"BTC\":{\"total\":\"0.00002\",\"available\":\"0\",\"hold\":\"0.00002\"},\"USDT\":{\"total\":\"33.68855864\",\"available\":\"15.01916691\",\"hold\":\"18.66939173\"}},\"debtList\":{\"BTC\":\"0\",\"USDT\":\"0\"},\"timestamp\":1729912435657}}";
        $commonResp = WsMessage::jsonDeserialize($data, $this->serializer);
        $resp = CrossMarginPositionEvent::jsonDeserialize(
            $this->serializer->serialize($commonResp->rawData, "json"),
            $this->serializer
        );
        $result = $this->hasAnyNoneNull($resp);
        self::assertTrue($result);
    }
    /**
     * isolatedMarginPosition
     * Get Isolated Margin Position change
     * /isolatedMarginPosition/margin/isolatedPosition:_symbol_
     */
    public function testIsolatedMarginPositionResponse()
    {
        $data =
            "{\"topic\":\"/margin/isolatedPosition:BTC-USDT\",\"subject\":\"positionChange\",\"type\":\"message\",\"userId\":\"633559791e1cbc0001f319bc\",\"channelType\":\"private\",\"data\":{\"tag\":\"BTC-USDT\",\"status\":\"DEBT\",\"statusBizType\":\"DEFAULT_DEBT\",\"accumulatedPrincipal\":\"5.01\",\"changeAssets\":{\"BTC\":{\"total\":\"0.00043478\",\"hold\":\"0\",\"liabilityPrincipal\":\"0\",\"liabilityInterest\":\"0\"},\"USDT\":{\"total\":\"0.98092004\",\"hold\":\"0\",\"liabilityPrincipal\":\"26\",\"liabilityInterest\":\"0.00025644\"}},\"timestamp\":1730121097742}}";
        $commonResp = WsMessage::jsonDeserialize($data, $this->serializer);
        $resp = IsolatedMarginPositionEvent::jsonDeserialize(
            $this->serializer->serialize($commonResp->rawData, "json"),
            $this->serializer
        );
        $result = $this->hasAnyNoneNull($resp);
        self::assertTrue($result);
    }
}
