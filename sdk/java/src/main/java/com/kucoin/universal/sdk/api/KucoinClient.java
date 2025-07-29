package com.kucoin.universal.sdk.api;

/** Client interface defines the methods to get REST and WebSocket services. */
public interface KucoinClient {

  /**
   * Get RESTful service.
   *
   * @return KucoinRestService
   */
  KucoinRestService getRestService();

  /**
   * Get WebSocket service.
   *
   * @return KucoinWSService
   */
  KucoinWSService getWsService();
}
