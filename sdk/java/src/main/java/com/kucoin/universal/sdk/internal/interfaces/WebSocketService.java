package com.kucoin.universal.sdk.internal.interfaces;

public interface WebSocketService {

    /**
     * Starts the WebSocket service and handles incoming messages.
     */
    void start();

    /**
     * Stops the WebSocket service.
     */
    void stop();

    /**
     * Subscribes to a topic with a callback handler.
     * @param prefix The topic prefix
     * @param args The arguments to be included in the topic
     * @param callback Callback to handle incoming messages
     * @return CompletableFuture resolving to the subscription ID (string)
     */
    String subscribe(String prefix, String[] args, WebSocketMessageCallback<?> callback);

    /**
     * Unsubscribes from a topic.
     */
    void unsubscribe(String id);
}
