<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use KuCoin\UniversalSDK\Model\TransportOption;

class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var Client|null $client
     */
    private $client;

    public function __construct(TransportOption $option)
    {
        $handlerOptions = [];
        if ($option->maxConnections > 0 && defined('CURLMOPT_MAX_TOTAL_CONNECTIONS')) {
            $handlerOptions[CURLMOPT_MAX_TOTAL_CONNECTIONS] = $option->maxConnections;
        }

        $handler = new CurlMultiHandler($handlerOptions);
        $stack = HandlerStack::create($handler);

        // Retry middleware
        if ($option->maxRetries > 0) {
            $stack->push(Middleware::retry(
                function ($retries, $request, $response, $exception) use ($option) {
                    if ($retries >= $option->maxRetries) return false;
                    if ($exception instanceof ConnectException) return true;
                    if ($response && $response->getStatusCode() >= 500) return true;
                    return false;
                },
                function () use ($option) {
                    return $option->retryDelay * 1000;
                }
            ));
        }

        $config = [
            'handler' => $stack,
            'timeout' => $option->totalTimeout,
            'headers' => [
                'Connection' => $option->keepAlive ? 'keep-alive' : 'close',
            ],
        ];

        $config = array_merge($config, $option->extraOptions ?? []);

        $this->client = new Client($config);
    }

    public function request(string $method, string $url, array $headers = [], ?string $body = null): HttpResponse
    {
        $res = $this->client->request($method, $url, [
            'headers' => $headers,
            'body' => $body,
        ]);

        return new HttpResponse($res->getStatusCode(), $res->getHeaders(), (string)$res->getBody());
    }


    public function close(): void
    {
        $this->client = null;
    }
}

