<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

use KuCoin\UniversalSDK\Model\HttpRequest;
use KuCoin\UniversalSDK\Model\HttpResponse;

interface HttpClientInterface
{
    /**
     * @param HttpRequest $request
     * @return HttpResponse Custom response object
     */
    public function request(HttpRequest &$request): HttpResponse;


    /**
     * close http client
     */
    public function close(): void;
}

