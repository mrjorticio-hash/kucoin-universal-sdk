<?php

namespace KuCoin\UniversalSDK\Model;

/**
 * Builder pattern for constructing TransportOption instances.
 */
class TransportOptionBuilder
{
    /**
     * @var TransportOption
     */
    private $option;

    public function __construct()
    {
        $this->option = new TransportOption();
    }

    /**
     * Enable or disable keep-alive for persistent connections.
     *
     * @param bool $keepAlive
     * @return self
     */
    public function setKeepAlive(bool $keepAlive): self
    {
        $this->option->keepAlive = $keepAlive;
        return $this;
    }


    /**
     * Use coroutine-based HTTP transport (Saber + Swoole).
     * Requires `ext-swoole` and `swlib/saber`.
     * Defaults to false (uses Guzzle).
     * @param bool $useCoroutineHttp
     * @return self
     */
    public function setUseCoroutineHttp(bool $useCoroutineHttp): self
    {
        $this->option->useCoroutineHttp = $useCoroutineHttp;
        return $this;
    }

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
     * @param array<string, mixed> $extraOptions
     */
    public function setExtraOptions(array $extraOptions): self
    {
        $this->option->extraOptions = $extraOptions;
        return $this;
    }

    /**
     * Set maximum concurrent HTTP connections allowed.
     * Applies across all domains and requests.
     * Use -1 to disable the limit (Guzzle default behavior).
     * @param int $maxConnections
     * @return self
     */
    public function setMaxConnections(int $maxConnections): self
    {
        $this->option->maxConnections = $maxConnections;
        return $this;
    }


    /**
     * Total timeout of the request in seconds.
     *
     * @param float $totalTimeout
     * @return self
     */
    public function setTotalTimeout(float $totalTimeout): self
    {
        $this->option->totalTimeout = $totalTimeout;
        return $this;
    }

    /**
     * Set the delay between retry attempts (in seconds).
     *
     * @param float $retryDelay
     * @return self
     */
    public function setRetryDelay(float $retryDelay): self
    {
        $this->option->retryDelay = $retryDelay;
        return $this;
    }

    /**
     * Set the maximum number of retry attempts.
     *
     * @param int $maxRetries
     * @return self
     */
    public function setMaxRetries(int $maxRetries): self
    {
        $this->option->maxRetries = $maxRetries;
        return $this;
    }

    /**
     * Set the list of HTTP interceptors.
     *
     * @param InterceptorInterface[] $interceptors
     * @return self
     */
    public function setInterceptors(array $interceptors): self
    {
        $this->option->interceptors = $interceptors;
        return $this;
    }

    /**
     * Build and return the TransportOption object with configured values.
     *
     * @return TransportOption
     */
    public function build(): TransportOption
    {
        return $this->option;
    }
}
