<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

use Exception;
use KuCoin\UniversalSDK\Internal\Interfaces\Transport;
use KuCoin\UniversalSDK\Internal\Interfaces\WsTokenProvider;

/**
 * Default implementation of the WebSocket token provider
 * Handles token retrieval and management for WebSocket connections
 */
class DefaultWsTokenProvider implements WsTokenProvider
{
    private const PATH_PRIVATE = '/api/v1/bullet-private';
    private const PATH_PUBLIC = '/api/v1/bullet-public';

    /** @var Transport $transport */
    private $transport;

    /** @var string */
    private $domainType;

    /** @var bool */
    private $isPrivate;

    public function __construct(Transport $transport, string $domainType, bool $isPrivate)
    {
        $this->transport = $transport;
        $this->domainType = $domainType;
        $this->isPrivate = $isPrivate;
    }

    /**
     * Retrieves WebSocket tokens from the server
     * @return WsToken[]
     * @throws Exception
     */
    public function getToken(): array
    {
        $path = $this->isPrivate ? $this::PATH_PRIVATE : $this::PATH_PUBLIC;

        /** @var TokenResponse $result */
        $result = $this->transport->call(
            $this->domain,
            false,
            'POST',
            $path,
            null,
            TokenResponse::class,
            false
        );

        if (!empty($result->instanceServers) && !empty($result->token)) {
            foreach ($result->instanceServers as $server) {
                $server->token = $result->token;
            }
            return $result->instanceServers;
        }

        return [];
    }

    /**
     * Closes the token provider and its associated transport
     */
    public function close()
    {
        $this->transport->close();
    }
}