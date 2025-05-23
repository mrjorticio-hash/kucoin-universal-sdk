<?php

namespace KuCoin\UniversalSDK\Api;

/*
Client
### REST API Notes

#### Client Features
- **Advanced HTTP Handling**:
  - Supports retries, keep-alive connections, and configurable concurrency limits for robust request execution.
  - Supports both [Guzzle](https://github.com/guzzle/guzzle) and [Saber (Swoole)](https://github.com/swlib/saber) as underlying HTTP clients.
  - Use `useCoroutineHttp=true` to enable high-performance coroutine HTTP requests (requires `ext-swoole` and `swlib/saber`).

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

use KuCoin\UniversalSDK\Internal\Rest\DefaultKucoinRestAPIImpl;
use KuCoin\UniversalSDK\Internal\Ws\DefaultKucoinWsImpl;
use KuCoin\UniversalSDK\Model\ClientOption;
use React\EventLoop\LoopInterface;


/**
 * DefaultClient provides the default implementation of the Client interface.
 */
class DefaultClient implements Client
{
    /**
     * @var KucoinRestService
     */
    private $restImpl;

    /**
     * @var KucoinWSService
     */
    private $wsImpl;

    /**
     * DefaultClient constructor.
     *
     * @param ClientOption $op
     */
    public function __construct(ClientOption $op, ?LoopInterface $loop = null)
    {
        $this->restImpl = new DefaultKucoinRestAPIImpl($op);
        $this->wsImpl = new DefaultKucoinWsImpl($op, $loop);
    }

    /**
     * Get RESTful service implementation.
     *
     * @return KucoinRestService
     */
    public function restService(): KucoinRestService
    {
        return $this->restImpl;
    }

    /**
     * Get WebSocket service implementation.
     *
     * @return KucoinWSService
     */
    public function wsService(): KucoinWSService
    {
        return $this->wsImpl;
    }
}
