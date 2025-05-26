<?php

namespace KuCoin\UniversalSDK\Model;


class HttpResponse
{
    /**
     * @var int $status
     */
    public $status;
    /**
     * @var array $headers
     */
    public $headers;
    /**
     * @var string $body
     */
    public $body;
    /**
     * @var mixed $originalResponse
     */
    public $originalResponse;

    /**
     * @param int $status
     * @param array $headers
     * @param string $body
     * @param mixed $originalResponse
     */
    public function __construct(int $status, array $headers, string $body, $originalResponse)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
        $this->originalResponse = $originalResponse;
    }

}
