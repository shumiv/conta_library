<?php

namespace conta\Curl;

class Curl
{
    private $curl;

    public function __construct()
    {
        $this->curl = curl_init();
        $this->setopt();
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    public function request(string $url, array $params = []): string
    {
        $this->fillRequest($url, $params);
        $answer = curl_exec($this->curl);
        if ($answer === false) throw new \Exception(curl_error($this->curl));
        if ($answer === '')
            throw new \Exception('Curl returned an empty string');
        return $answer;
    }

    private function setopt(): void
    {
        curl_setopt_array(
            $this->curl,
            [
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1
            ]
        );
    }

    private function fillRequest(string $url, array $params): void
    {
        $postFields = http_build_query($params);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postFields);
    }
}