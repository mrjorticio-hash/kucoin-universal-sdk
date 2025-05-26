<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

include '../vendor/autoload.php';

class KcSigner
{
    private $apiKey;
    private $apiSecret;
    private $apiPassphrase;

    public function __construct(string $apiKey, string $apiSecret, string $apiPassphrase)
    {
        $this->apiKey = $apiKey ?: '';
        $this->apiSecret = $apiSecret ?: '';
        $this->apiPassphrase = $apiPassphrase ?: '';

        if ($apiSecret && $apiPassphrase) {
            $this->apiPassphrase = $this->sign($apiPassphrase, $apiSecret);
        }

        if (!$this->apiKey || !$this->apiSecret || !$this->apiPassphrase) {
            fwrite(STDERR, "Warning: API credentials are empty. Public endpoints only.\n");
        }
    }

    private function sign(string $plain, string $key): string
    {
        return base64_encode(hash_hmac('sha256', $plain, $key, true));
    }

    public function headers(string $payload): array
    {
        $timestamp = (string)(int)(microtime(true) * 1000);
        $signature = $this->sign($timestamp . $payload, $this->apiSecret);

        return [
            'KC-API-KEY' => $this->apiKey,
            'KC-API-PASSPHRASE' => $this->apiPassphrase,
            'KC-API-TIMESTAMP' => $timestamp,
            'KC-API-SIGN' => $signature,
            'KC-API-KEY-VERSION' => '3',
            'Content-Type' => 'application/json',
        ];
    }
}

function processHeaders(KcSigner $signer, string $body, string $rawUrl, string $method): array
{
    $payload = $method . $rawUrl . $body;
    return $signer->headers($payload);
}

function getTradeFees(KcSigner $signer, Client $client): void
{
    $endpoint = 'https://api.kucoin.com';
    $path = '/api/v1/trade-fees';
    $method = 'GET';
    $query = http_build_query(['symbols' => 'BTC-USDT']);
    $rawUrl = $path . '?' . $query;
    $url = $endpoint . $rawUrl;

    $headers = processHeaders($signer, '', $rawUrl, $method);

    try {
        $resp = $client->request($method, $url, ['headers' => $headers]);
        echo $resp->getBody() . PHP_EOL;
    } catch (RequestException $e) {
        echo "Error fetching trade fees: " . $e->getMessage() . PHP_EOL;
    }
}

function addLimitOrder(KcSigner $signer, Client $client): void
{
    $endpoint = 'https://api.kucoin.com';
    $path = '/api/v1/hf/orders';
    $method = 'POST';
    $url = $endpoint . $path;
    $rawUrl = $path;

    $bodyData = [
        'clientOid' => bin2hex(random_bytes(16)),
        'side' => 'buy',
        'symbol' => 'BTC-USDT',
        'type' => 'limit',
        'price' => '10000',
        'size' => '0.001',
    ];

    $bodyJson = json_encode($bodyData, JSON_UNESCAPED_SLASHES);

    $headers = processHeaders($signer, $bodyJson, $rawUrl, $method);

    try {
        $resp = $client->request($method, $url, [
            'headers' => $headers,
            'body' => $bodyJson,
        ]);
        echo $resp->getBody() . PHP_EOL;
    } catch (RequestException $e) {
        echo "Error placing limit order: " . $e->getMessage() . PHP_EOL;
    }
}

function main(): void
{
    $key = getenv('API_KEY') ?: '';
    $secret = getenv('API_SECRET') ?: '';
    $passphrase = getenv('API_PASSPHRASE') ?: '';

    $signer = new KcSigner($key, $secret, $passphrase);
    $client = new Client();

    getTradeFees($signer, $client);
    addLimitOrder($signer, $client);
}

if (php_sapi_name() === 'cli') {
    main();
}
