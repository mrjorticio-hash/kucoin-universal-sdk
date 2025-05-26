<?php

namespace KuCoin\UniversalSDK\Internal\Interfaces;

use Evenement\EventEmitterInterface;
use KuCoin\UniversalSDK\Model\WsMessage;
use React\Promise\PromiseInterface;


/**
 * Support Events:
 * message
 * event
 * reconnected
 */
interface WebSocketClient extends EventEmitterInterface
{
    /**
     * starts the WebSocket connection.
     */
    public function start(): PromiseInterface;

    /**
     * Stops the WebSocket connection.
     */
    public function stop(): PromiseInterface;

    /**
     * Writes a message to the WebSocket connection.
     */
    public function write(WsMessage $message, int $timeout): PromiseInterface;

}