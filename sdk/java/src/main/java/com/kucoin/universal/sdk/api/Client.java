package com.kucoin.universal.sdk.api;

/** Client interface defines the methods to get REST and WebSocket services. */
public interface Client {

  /**
   * Get RESTful service.
   *
   * @return KucoinRestService
   */
  KucoinRestService restService();

  /**
   * Get WebSocket service.
   *
   * @return KucoinWSService
   */
  KucoinWSService wsService();
}
