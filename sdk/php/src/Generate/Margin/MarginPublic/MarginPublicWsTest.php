<?php
namespace KuCoin\UniversalSDK\Generate\Margin\MarginPublic;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\WsMessage;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MarginPublicWsTest extends TestCase
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
     * indexPrice
     * Index Price
     * /indexPrice/indicator/index:_symbol_,_symbol_
     */
    public function testIndexPriceResponse()
    {
        $data =
            "{\"id\":\"5c24c5da03aa673885cd67a0\",\"type\":\"message\",\"topic\":\"/indicator/index:USDT-BTC\",\"subject\":\"tick\",\"data\":{\"symbol\":\"USDT-BTC\",\"granularity\":5000,\"timestamp\":1551770400000,\"value\":0.0001092}}";
        $commonResp = WsMessage::jsonDeserialize($data, $this->serializer);
        $resp = IndexPriceEvent::jsonDeserialize(
            $this->serializer->serialize($commonResp->rawData, "json"),
            $this->serializer
        );
        $result = $this->hasAnyNoneNull($resp);
        self::assertTrue($result);
    }
    /**
     * markPrice
     * Mark Price
     * /markPrice/indicator/markPrice:_symbol_,_symbol_
     */
    public function testMarkPriceResponse()
    {
        $data =
            "{\"id\":\"5c24c5da03aa673885cd67aa\",\"type\":\"message\",\"topic\":\"/indicator/markPrice:USDT-BTC\",\"subject\":\"tick\",\"data\":{\"symbol\":\"USDT-BTC\",\"granularity\":5000,\"timestamp\":1551770400000,\"value\":0.0001093}}";
        $commonResp = WsMessage::jsonDeserialize($data, $this->serializer);
        $resp = MarkPriceEvent::jsonDeserialize(
            $this->serializer->serialize($commonResp->rawData, "json"),
            $this->serializer
        );
        $result = $this->hasAnyNoneNull($resp);
        self::assertTrue($result);
    }
}
