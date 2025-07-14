package com.kucoin.universal.sdk.api;

import com.kucoin.universal.sdk.internal.rest.DefaultKucoinRestAPIImpl;
import com.kucoin.universal.sdk.internal.ws.DefaultKucoinWsImpl;
import com.kucoin.universal.sdk.model.ClientOption;

/*
Client
TODO
*/

/** DefaultClient provides the default implementation of the {@link Client} interface. */
public final class DefaultClient implements Client {

  /** REST-side facade. */
  private final KucoinRestService restImpl;

  /** WebSocket-side facade. */
  private final KucoinWSService wsImpl;

  public DefaultClient(ClientOption option) {
    this.restImpl = new DefaultKucoinRestAPIImpl(option);
    this.wsImpl = new DefaultKucoinWsImpl(option);
  }

  @Override
  public KucoinRestService restService() {
    return restImpl;
  }

  @Override
  public KucoinWSService wsService() {
    return wsImpl;
  }
}
