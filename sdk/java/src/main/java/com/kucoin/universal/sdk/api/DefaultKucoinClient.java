package com.kucoin.universal.sdk.api;

import com.kucoin.universal.sdk.internal.rest.DefaultKucoinRestAPIImpl;
import com.kucoin.universal.sdk.internal.ws.DefaultKucoinWsImpl;
import com.kucoin.universal.sdk.model.ClientOption;

/*
Client
### REST API Notes

#### Client Features
- **Advanced HTTP Handling**:
  - Supports keep-alive connections, and configurable concurrency limits for robust request execution.
- **Rich Response Details**:
    - Includes rate-limiting information and raw response data in API responses for better debugging and control.
- **Public API Access**:
    - For public endpoints, API keys are not required, simplifying integration for non-authenticated use cases.

---

### WebSocket API Notes

#### Client Features
- **Flexible Service Creation**:
    - Supports creating services for public/private channels in Spot, Futures, or Margin trading as needed.
    - Multiple services can be created independently.
- **Service Lifecycle**:
    - If a service is closed, create a new service instead of reusing it to avoid undefined behavior.
- **Connection-to-Channel Mapping**:
    - Each WebSocket connection corresponds to a specific channel type. For example:
        - Spot public/private and Futures public/private services require 4 active WebSocket connections.

#### Threading and Callbacks
- **Thread Model**:
    - WebSocket services follow a simple thread model, ensuring callbacks are handled on a single thread.
- **Subscription Management**:
    - A subscription is considered successful only after receiving an acknowledgment (ACK) from the server.
    - Each subscription has a unique ID, which can be used for unsubscribe.

#### Data and Message Handling
- **Framework-Managed Threads**:
    - Data messages are handled by a single framework-managed thread, ensuring orderly processing.
- **Duplicate Subscriptions**:
    - Avoid overlapping subscription parameters. For example:
        - Subscribing to `["BTC-USDT", "ETH-USDT"]` and then to `["ETH-USDT", "DOGE-USDT"]` may result in undefined behavior.
        - Identical subscriptions will raise an error for duplicate subscriptions.

*/

/** DefaultClient provides the default implementation of the {@link KucoinClient} interface. */
public final class DefaultKucoinClient implements KucoinClient {

  /** REST-side facade. */
  private final KucoinRestService restImpl;

  /** WebSocket-side facade. */
  private final KucoinWSService wsImpl;

  public DefaultKucoinClient(ClientOption option) {
    this.restImpl = new DefaultKucoinRestAPIImpl(option);
    this.wsImpl = new DefaultKucoinWsImpl(option);
  }

  @Override
  public KucoinRestService getRestService() {
    return restImpl;
  }

  @Override
  public KucoinWSService getWsService() {
    return wsImpl;
  }
}
