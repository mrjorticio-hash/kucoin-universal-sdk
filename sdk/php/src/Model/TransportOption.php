<?php

namespace KuCoin\UniversalSDK\Model;

/**
 * TransportOption holds configurations for HTTP client behavior.
 */
class TransportOption
{
    /**
     * Enables keep-alive for persistent connections. Defaults to true.
     * @var bool
     */
    public $keepAlive = true;

    /**
     * Maximum concurrent HTTP connections allowed.
     * Applies across all domains and requests.
     * Use -1 to disable the limit (Guzzle default behavior).
     * @var int
     */
    public $maxConnections = -1;

    /**
     * Connection timeout duration in seconds. Defaults to 10.
     * @var float
     */
    public $connectTimeout = 10;

    /**
     * Read timeout duration in seconds. Defaults to 30.
     * @var float
     */
    public $readTimeout = 30;

    /**
     * HTTP(s) proxy. Example: ['http' => '192.168.1.1', 'https' => '192.168.1.1']
     * @var array|null
     */
    public $proxy = null;

    /**
     * Maximum number of retry attempts. Defaults to 3.
     * @var int
     */
    public $maxRetries = 3;

    /**
     * Delay in seconds between retries. Defaults to 2.
     * @var float
     */
    public $retryDelay = 2;

    /**
     * List of HTTP interceptors.
     * @var InterceptorInterface[]
     */
    public $interceptors = [];

    public function __construct()
    {
    }
}
