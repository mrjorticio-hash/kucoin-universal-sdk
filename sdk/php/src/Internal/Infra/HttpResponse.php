<?php

namespace KuCoin\UniversalSDK\Internal\Infra;


class HttpResponse
{
    public int $status;
    public array $headers;
    public string $body;

    public function __construct(
        int    $status,
        array  $headers,
        string $body
    )
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }
}
