<?php

namespace KuCoin\UniversalSDK\Internal\Interfaces;


// WsTokenProvider defines a method for retrieving a WebSocket token
interface WsTokenProvider
{
    /**
     * Retrieves the WebSocket token.
     */
    public function getToken(): array;

    /**
     * Closes the token provider.
     */
    public function close();
}