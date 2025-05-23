<?php

namespace KuCoin\UniversalSDK\Model;

use Exception;

interface InterceptorInterface
{
    /**
     * Called before the HTTP request is sent.
     * Allows modifying the request or injecting context.
     *
     * @param HttpRequest $request
     * @param array $context Shared context between before/after
     */
    public function before(HttpRequest &$request, array &$context = []): void;

    /**
     * Called after the HTTP response is received or an exception occurs.
     *
     * @param HttpResponse|null $response
     * @param Exception|null $exception
     * @param array $context Shared context between before/after
     */
    public function after(?HttpResponse &$response, ?Exception $exception, array &$context = []): void;
}
