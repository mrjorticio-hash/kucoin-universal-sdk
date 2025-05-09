<?php

namespace KuCoin\UniversalSDK\Extension\Interceptor;


use KuCoin\UniversalSDK\Common\Logger;
use KuCoin\UniversalSDK\Model\InterceptorInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Logging implements InterceptorInterface
{
    public function middleware(): callable
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $start = microtime(true);
                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use ($request, $start) {
                        $duration = (microtime(true) - $start) * 1000;
                        Logger::info(sprintf(
                            "[Access] method=%s url=%s status=%d cost=%.2fms",
                            $request->getMethod(),
                            (string)$request->getUri(),
                            $response->getStatusCode(),
                            $duration
                        ));
                        return $response;
                    }
                );
            };
        };
    }
}