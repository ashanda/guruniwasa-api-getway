<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait HandlesHTTPRequests
{
    /**
     * Send an HTTP request.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE, etc.)
     * @param string $url URL to send the request to
     * @param string|null $accessToken Bearer token for authentication (optional)
     * @param array $data Data to send with the request (optional)
     * @return array HTTP response body
     */
    protected function sendHttpRequest($method, $url, $accessToken = null, $data = [])
    {
        $client = new Client();
        $apiKey = env('API_KEY'); 
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                // Add Authorization header if access token is provided
                'Authorization' => $accessToken ? 'Bearer ' . $accessToken : null,
                'API-Key' => $apiKey,
            ],
            'json' => $data, // Convert data to JSON
        ];

        $response = $client->request($method, $url, $options);

        return json_decode($response->getBody()->getContents(), true);
    }
}