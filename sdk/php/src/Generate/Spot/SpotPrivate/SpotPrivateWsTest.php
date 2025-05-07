<?php
namespace KuCoin\UniversalSDK\Generate\Spot\SpotPrivate;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use KuCoin\UniversalSDK\Internal\Utils\JsonSerializedHandler;
use KuCoin\UniversalSDK\Model\WsMessage;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SpotPrivateWsTest extends TestCase
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
     * account
     * Get Account Balance
     * /account/account/balance
     */
    public function testAccountResponse()
    {
        $data =
            "{\"topic\":\"/account/balance\",\"type\":\"message\",\"subject\":\"account.balance\",\"id\":\"354689988084000\",\"userId\":\"633559791e1cbc0001f319bc\",\"channelType\":\"private\",\"data\":{\"accountId\":\"548674591753\",\"currency\":\"USDT\",\"total\":\"21.133773386762\",\"available\":\"20.132773386762\",\"hold\":\"1.001\",\"availableChange\":\"-0.5005\",\"holdChange\":\"0.5005\",\"relationContext\":{\"symbol\":\"BTC-USDT\",\"orderId\":\"6721d0632db25b0007071fdc\"},\"relationEvent\":\"trade.hold\",\"relationEventId\":\"354689988084000\",\"time\":\"1730269283892\"}}";
        $commonResp = WsMessage::jsonDeserialize($data, $this->serializer);
        $resp = AccountEvent::jsonDeserialize(
            $this->serializer->serialize($commonResp->rawData, "json"),
            $this->serializer
        );
        $result = $this->hasAnyNoneNull($resp);
        self::assertTrue($result);
    }
    /**
     * orderV1
     * Get Order(V1)
     * /orderV1/spotMarket/tradeOrders
     */
    public function testOrderV1Response()
    {
        $data =
            "{\"topic\":\"/spotMarket/tradeOrdersV2\",\"type\":\"message\",\"subject\":\"orderChange\",\"userId\":\"633559791e1cbc0001f319bc\",\"channelType\":\"private\",\"data\":{\"canceledSize\":\"0\",\"clientOid\":\"5c52e11203aa677f33e493fb\",\"filledSize\":\"0\",\"orderId\":\"6720ecd9ec71f4000747731a\",\"orderTime\":1730211033305,\"orderType\":\"limit\",\"originSize\":\"0.00001\",\"price\":\"50000\",\"remainSize\":\"0.00001\",\"side\":\"buy\",\"size\":\"0.00001\",\"status\":\"open\",\"symbol\":\"BTC-USDT\",\"ts\":1730211033335000000,\"type\":\"open\"}}";
        $commonResp = WsMessage::jsonDeserialize($data, $this->serializer);
        $resp = OrderV1Event::jsonDeserialize(
            $this->serializer->serialize($commonResp->rawData, "json"),
            $this->serializer
        );
        $result = $this->hasAnyNoneNull($resp);
        self::assertTrue($result);
    }
    /**
     * orderV2
     * Get Order(V2)
     * /orderV2/spotMarket/tradeOrdersV2
     */
    public function testOrderV2Response()
    {
        $data =
            "{\"topic\":\"/spotMarket/tradeOrdersV2\",\"type\":\"message\",\"subject\":\"orderChange\",\"userId\":\"633559791e1cbc0001f319bc\",\"channelType\":\"private\",\"data\":{\"clientOid\":\"5c52e11203aa677f33e493fc\",\"orderId\":\"6720da3fa30a360007f5f832\",\"orderTime\":1730206271588,\"orderType\":\"market\",\"originSize\":\"0.00001\",\"side\":\"buy\",\"status\":\"new\",\"symbol\":\"BTC-USDT\",\"ts\":1730206271616000000,\"type\":\"received\"}}";
        $commonResp = WsMessage::jsonDeserialize($data, $this->serializer);
        $resp = OrderV2Event::jsonDeserialize(
            $this->serializer->serialize($commonResp->rawData, "json"),
            $this->serializer
        );
        $result = $this->hasAnyNoneNull($resp);
        self::assertTrue($result);
    }
    /**
     * stopOrder
     * Get Stop Order
     * /stopOrder/spotMarket/advancedOrders
     */
    public function testStopOrderResponse()
    {
        $data =
            "{\"topic\":\"/spotMarket/advancedOrders\",\"type\":\"message\",\"subject\":\"stopOrder\",\"userId\":\"633559791e1cbc0001f319bc\",\"channelType\":\"private\",\"data\":{\"orderId\":\"vs93gpupfa48anof003u85mb\",\"orderPrice\":\"70000\",\"orderType\":\"stop\",\"side\":\"buy\",\"size\":\"0.00007142\",\"stop\":\"loss\",\"stopPrice\":\"71000\",\"symbol\":\"BTC-USDT\",\"tradeType\":\"TRADE\",\"type\":\"open\",\"createdAt\":1742305928064,\"ts\":1742305928091268493}}";
        $commonResp = WsMessage::jsonDeserialize($data, $this->serializer);
        $resp = StopOrderEvent::jsonDeserialize(
            $this->serializer->serialize($commonResp->rawData, "json"),
            $this->serializer
        );
        $result = $this->hasAnyNoneNull($resp);
        self::assertTrue($result);
    }
}
