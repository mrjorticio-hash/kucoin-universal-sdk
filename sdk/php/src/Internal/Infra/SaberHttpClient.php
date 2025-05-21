<?php

namespace KuCoin\UniversalSDK\Internal\Infra;

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
        $options = [
            'timeout' => $this->option->totalTimeout,
            'connect_timeout' => $this->option->connectTimeout,
            'headers' => [
                'Connection' => $this->option->keepAlive ? 'keep-alive' : 'close',
            ],
            'keep_alive' => $this->option->keepAlive,
        ];

        // Add proxy support
        if (is_array($this->option->proxy)) {
            if (!empty($this->option->proxy['http'])) {
                $options['proxy'] = $this->option->proxy['http'];
            } elseif (!empty($this->option->proxy['https'])) {
                $options['proxy'] = $this->option->proxy['https'];
            }
        }

        $this->client = Saber::create($options);
    }

    public function request(string $method, string $url, array $headers = [], ?string $body = null): HttpResponse
    {
        if (!$this->client) {
            throw new \RuntimeException("Saber client has been closed.");
        }

        $res = $this->client->request([
            'method' => $method,
            'uri' => $url,
            'headers' => $headers,
            'data' => $body ?? '',
        ]);

        return new HttpResponse(
            $res->getStatusCode(),
            $res->getHeaders(),
            (string)$res->getBody()
        );
    }

    public function close(): void
    {
        $this->client = null;
    }
}
