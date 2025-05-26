<?php

namespace KuCoin\UniversalSDK\Extension\Interceptor;

use Exception;
use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Model\HttpRequest;
use KuCoin\UniversalSDK\Model\HttpResponse;
use KuCoin\UniversalSDK\Model\InterceptorInterface;

class LoggingInterceptor implements InterceptorInterface
{
    public function before(HttpRequest &$request, array &$context = []): void
    {
        $context['_start_time'] = microtime(true);
        $context['_method'] = $request->method;
        $context['_url'] = $request->url;
    }

    public function after(?HttpResponse &$response, ?Exception $exception, array &$context = []): void
    {
        $duration = isset($context['_start_time']) ? (microtime(true) - $context['_start_time']) * 1000 : 0;

        $status = $response ? $response->status : 'ERROR';
        $method = $context['_method'] ?? 'UNKNOWN';
        $url = $context['_url'] ?? 'UNKNOWN';

        if ($exception) {
            Logger::error(sprintf(
                "[Access] method=%s url=%s status=%s cost=%.2fms error=%s",
                $method,
                $url,
                $status,
                $duration,
                $exception->getMessage()
            ));
        } else {
            Logger::info(sprintf(
                "[Access] method=%s url=%s status=%s cost=%.2fms",
                $method,
                $url,
                $status,
                $duration
            ));
        }
    }
}