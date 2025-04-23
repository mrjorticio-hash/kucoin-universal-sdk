<?php
namespace KuCoin\UniversalSDK\Generate\Spot\SpotPublic;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\RestResponse;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SpotPublicApiWsTest extends TestCase
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

    public function testAllTickersResponse() {}
    public function testCallAuctionInfoResponse() {}
    public function testCallAuctionOrderbookLevel50Response() {}
    public function testKlinesResponse() {}
    public function testMarketSnapshotResponse() {}
    public function testOrderbookIncrementResponse() {}
    public function testOrderbookLevel1Response() {}
    public function testOrderbookLevel50Response() {}
    public function testOrderbookLevel5Response() {}
    public function testSymbolSnapshotResponse() {}
    public function testTickerResponse() {}
    public function testTradeResponse() {}
}
