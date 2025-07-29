package com.kucoin.universal.sdk.api;

import com.kucoin.universal.sdk.generate.futures.futuresprivate.FuturesPrivateWs;
import com.kucoin.universal.sdk.generate.futures.futurespublic.FuturesPublicWs;
import com.kucoin.universal.sdk.generate.margin.marginprivate.MarginPrivateWs;
import com.kucoin.universal.sdk.generate.margin.marginpublic.MarginPublicWs;
import com.kucoin.universal.sdk.generate.spot.spotprivate.SpotPrivateWs;
import com.kucoin.universal.sdk.generate.spot.spotpublic.SpotPublicWs;

/** KucoinWSService provides WebSocket interfaces for Spot, Margin, and Futures trading. */
public interface KucoinWSService {
  /**
   * Returns the interface to interact with the Spot Trading WebSocket (public channel) API of
   * KuCoin.
   *
   * @return SpotPublicWs
   */
  SpotPublicWs newSpotPublicWS();

  /**
   * Returns the interface to interact with the Spot Trading WebSocket (private channel) API of
   * KuCoin.
   *
   * @return SpotPrivateWs
   */
  SpotPrivateWs newSpotPrivateWS();

  /**
   * Returns the interface to interact with the Margin Trading WebSocket (public channel) API of
   * KuCoin.
   *
   * @return MarginPublicWs
   */
  MarginPublicWs newMarginPublicWS();

  /**
   * Returns the interface to interact with the Margin Trading WebSocket (private channel) API of
   * KuCoin.
   *
   * @return MarginPrivateWs
   */
  MarginPrivateWs newMarginPrivateWS();

  /**
   * Returns the interface to interact with the Futures Trading WebSocket (public channel) API of
   * KuCoin.
   *
   * @return FuturesPublicWs
   */
  FuturesPublicWs newFuturesPublicWS();

  /**
   * Returns the interface to interact with the Futures Trading WebSocket (private channel) API of
   * KuCoin.
   *
   * @return FuturesPrivateWs
   */
  FuturesPrivateWs newFuturesPrivateWS();
}
