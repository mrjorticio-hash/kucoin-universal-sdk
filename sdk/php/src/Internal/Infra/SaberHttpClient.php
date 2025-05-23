<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

use KuCoin\UniversalSDK\Model\HttpRequest;
use KuCoin\UniversalSDK\Model\HttpResponse;
use KuCoin\UniversalSDK\Model\TransportOption;
use Swlib\Saber;

class SaberHttpClient implements HttpClientInterface
{
    /**
     * @var TransportOption
     */
    private TransportOption $option;

    /**
     * @var Saber|null
     */
    private ?Saber $client = null;

    public function __construct(TransportOption $option)
    {
        $this->option = $option;
        $this->initClient();
    }

    private function initClient(): void
    {
        $this->client = Saber::create();
    }

    public function request(HttpRequest &$request): HttpResponse
    {
        if (!$this->client) {
            throw new \RuntimeException("Saber client has been closed.");
        }

        $config = [
            'method' => $request->method,
            'uri' => $request->url,
            'headers' => $request->headers,
            'data' => $request->body ?? '',
            'use_pool' => $this->option->maxConnections === 0 ? true : $this->option->maxConnections,
            'timeout' => $this->option->totalTimeout,
            'keep_alive' => $this->option->keepAlive,
            'retry_time' => $this->option->maxRetries,
        ];

        $config = array_merge($config, $this->option->extraOptions ?? []);

        $res = $this->client->request($config);

        return new HttpResponse(
            $res->getStatusCode(),
            $res->getHeaders(),
            (string)$res->getBody(),
            $res
        );
    }

    public function close(): void
    {
        $this->client = null;
    }
}
