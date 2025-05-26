<?php

namespace KuCoin\UniversalSDK\Model;


class HttpRequest
{
    /**
     * @var string $method
     */
    public $method;
    /**
     * @var string $url
     */
    public $url;
    /**
     * @var array $headers
     */
    public $headers;

    /**
     * @var string $body
     */
    public $body;

    /**
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param string $body
     */
    public function __construct(string $method, string $url, array $headers, string $body)
    {
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;
    }
}
