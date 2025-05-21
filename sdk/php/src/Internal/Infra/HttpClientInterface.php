<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

interface HttpClientInterface
{
    /**
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $url Full URL
     * @param array $headers Request headers
     * @param string|null $body Raw body content
     * @return HttpResponse Custom response object
     */
    public function request(string $method, string $url, array $headers = [], ?string $body = null): HttpResponse;


    /**
     * close http client
     */
    public function close(): void;
}

