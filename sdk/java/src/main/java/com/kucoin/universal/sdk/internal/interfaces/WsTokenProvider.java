package com.kucoin.universal.sdk.internal.interfaces;

import java.util.List;

public interface WsTokenProvider {
  /** Retrieves the WebSocket token. */
  List<WsToken> getToken();

  /** Closes the token provider. */
  void close();
}
