<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use KuCoin\UniversalSDK\Model\HttpRequest;
use KuCoin\UniversalSDK\Model\HttpResponse;
use KuCoin\UniversalSDK\Model\TransportOption;

class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var Client|null $client
     */
    private $client;

    /**
     * @var TransportOption $option
     */
    private $option;

    public function __construct(TransportOption $option)
    {
        $this->option = $option;
        $this->initClient();
    }

    private function initClient()
    {
        $handlerOptions = [];
        if ($this->option->maxConnections > 0) {
            $handlerOptions[CURLMOPT_MAX_TOTAL_CONNECTIONS] = $this->option->maxConnections;
        }

        $handler = new CurlMultiHandler($handlerOptions);
        $stack = HandlerStack::create($handler);

        // Retry middleware
        if ($this->option->maxRetries > 0) {
            $stack->push(Middleware::retry(
                function ($retries, $request, $response, $exception) {
                    if ($retries >= $this->option->maxRetries) return false;
                    if ($exception instanceof ConnectException) return true;
                    if ($response && $response->getStatusCode() >= 500) return true;
                    return false;
                },
                function () {
                    return $this->option->retryDelay * 1000;
                }
            ));
        }

        $config = [
            'handler' => $stack,
            'timeout' => $this->option->totalTimeout,
            'headers' => [
                'Connection' => $this->option->keepAlive ? 'keep-alive' : 'close',
            ],
        ];

        $config = array_merge($config, $this->option->extraOptions ?? []);

        $this->client = new Client($config);
    }

    public function request(HttpRequest &$request): HttpResponse
    {
        try {
            $res = $this->client->request($request->method, $request->url, [
                'headers' => $request->headers,
                'body' => $request->body,
            ]);

        } catch (ConnectException $e) {
            if (str_contains($e->getMessage(), 'timed out') || str_contains($e->getMessage(), 'Connection reset')) {
                $this->initClient();
            }
            throw $e;
        }
        return new HttpResponse($res->getStatusCode(), $res->getHeaders(), (string)$res->getBody(), $res);
    }

    public function close(): void
    {
        $this->client = null;
        gc_collect_cycles();
    }
}

