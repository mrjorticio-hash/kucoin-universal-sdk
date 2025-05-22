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
     * Use 0 to disable the limit
     * @var int
     */
    public $maxConnections = 100;

    /**
     * Total timeout of the request in seconds. Defaults to 30.
     * @var float
     */
    public $totalTimeout = 30;

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
     * Use coroutine-based HTTP transport (Saber + Swoole).
     * Requires `ext-swoole` and `swlib/saber`.
     * Defaults to false (uses Guzzle).
     * @var bool $useCoroutineHttp
     */
    public $useCoroutineHttp = false;

    /**
     * Extra client-specific options.
     *
     * This field allows passing additional configuration parameters that are specific
     * to the selected HTTP client implementation.
     *
     * Example usage:
     * - For Guzzle:
     *   [
     *     'debug' => true,
     *     'verify' => false,
     *   ]
     *
     * - For Saber (Swlib\Saber):
     *   [
     *     'retry_time' => 3,              // Retry times on failure
     *     'ssl_verify_peer' => false,     // Disable SSL verification
     *     'use_pool' => true,             // Enable connection pool
     *   ]
     *
     * These options will be merged into the underlying client's configuration.
     * Please refer to each client’s official documentation for supported parameters:
     * - Guzzle: https://docs.guzzlephp.org/en/stable/request-options.html
     * - Saber:  https://github.com/swlib/saber
     *
     * @var array<string, mixed>
     */
    public $extraOptions = [];

    public function __construct()
    {
    }
}
