<?php

namespace KuCoin\UniversalSDK\Internal\Interfaces;

use React\Promise\PromiseInterface;

interface WebSocketService
{
    /**
     * Starts the WebSocket service and handles incoming WebSocket messages.
     */
    public function start(): PromiseInterface;

    /**
     * Stops the WebSocket service.
     */
    public function stop(): PromiseInterface;

    /**
     * Subscribes to a topic with a provided message callback.
     *
     * @param string $prefix The topic to subscribe to
     * @param string[] $args Arguments for the subscription
     * @param WebSocketMessageCallback $callback A callback for handling messages on this topic
     * @return PromiseInterface<string> A promise that resolves to the subscription ID or rejects with an error.
     */
    public function subscribe(string $prefix, array $args, WebSocketMessageCallback $callback): PromiseInterface;

    /**
     * Unsubscribes from a topic.
     *
     * @param string $id The subscription ID
     */
    public function unsubscribe($id): PromiseInterface;
}
