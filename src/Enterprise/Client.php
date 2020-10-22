<?php
namespace conta\Enterprise;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    private GuzzleClient $client;

    public function __construct(string $uri)
    {
        $baseUri = 'http://192.168.7.2/' . $uri;
        $this->client = new GuzzleClient(['base_uri' => $baseUri]);
    }

    public function get(string $uri): array
    {
        $response = $this->client->get($uri);
        return json_decode($response->getBody(), true);
    }

    public function delete(string $uri): void
    {
        $this->client->delete($uri);
    }
}