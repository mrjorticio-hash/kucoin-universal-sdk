<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

use KuCoin\UniversalSDK\Internal\Interfaces\Response;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class TokenResponse implements Response
{
    /**
     * @var string $token
     * @Type("string")
     * @SerializedName("token")
     */
    public $token;

    /**
     * @var WsToken[] $instanceServers
     * @Type("array<KuCoin\UniversalSDK\Internal\Infra\WsToken>")
     * @SerializedName("instanceServers")
     */
    public $instanceServers;


    /**
     * @Exclude()
     * @var RestResponse $commonResponse
     */
    public $commonResponse;

    public function __construct()
    {
        $this->commonResponse = null;
        $this->token = null;
        $this->instanceServers = null;
    }

    public function setCommonResponse($response)
    {
        $this->commonResponse = $response;
    }

    public function jsonSerialize($serializer)
    {
        return $serializer->serialize($this, "json");
    }

    public static function jsonDeserialize($json, $serializer)
    {
        if ($json == null) {
            return new self();
        }
        return $serializer->deserialize(
            $json,
            TokenResponse::class,
            "json"
        );
    }
}