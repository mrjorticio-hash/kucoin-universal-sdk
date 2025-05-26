<?php

namespace KuCoin\UniversalSDK\Internal\Infra;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
class WsToken
{
    /**
     * @var string $token
     * @Type("string")
     * @SerializedName("$token")
     */
    public $token;
    /**
     * Websocket domain URL. It is recommended to use a dynamic URL, as the URL may change.
     * @var string $endpoint
     * @Type("string")
     * @SerializedName("endpoint")
     */
    public $endpoint;
    /**
     * Whether to encrypt. Currently only supports wss, not ws
     * @var bool $encrypt
     * @Type("bool")
     * @SerializedName("encrypt")
     */
    public $encrypt;
    /**
     * Network Protocol
     * - 'websocket' : Websocket
     * @var string $protocol
     * @Type("string")
     * @SerializedName("protocol")
     */
    public $protocol;
    /**
     * Recommended ping interval (milliseconds)
     * @var int $pingInterval
     * @Type("int")
     * @SerializedName("pingInterval")
     */
    public $pingInterval;
    /**
     * Heartbeat timeout (milliseconds)
     * @var int $pingTimeout
     * @Type("int")
     * @SerializedName("pingTimeout")
     */
    public $pingTimeout;

    public function __construct(
        $token = '',
        $pingInterval = 0,
        $endpoint = '',
        $protocol = '',
        $encrypt = false,
        $pingTimeout = 0
    )
    {
        $this->token = $token;
        $this->pingInterval = $pingInterval;
        $this->endpoint = $endpoint;
        $this->protocol = $protocol;
        $this->encrypt = $encrypt;
        $this->pingTimeout = $pingTimeout;
    }
}
