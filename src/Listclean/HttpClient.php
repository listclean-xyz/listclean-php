<?php

namespace Listclean;

use Listclean\Exceptions\ApiException;

class HttpClient
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $apiKey;

    public function __construct(string $apiKey, string $baseUrl = 'https://api.listclean.xyz/v1/')
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
    }

    /**
     * @param string     $method
     * @param string     $path
     * @param array      $query
     * @param array|null $body
     * @param bool       $expectsJson
     *
     * @return array|string
     *
     * @throws ApiException
     */
    public function request(string $method, string $path, array $query = [], ?array $body = null, bool $expectsJson = true)
    {
        $url = $this->baseUrl . ltrim($path, '/');

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init($url);

        if ($ch === false) {
            throw new ApiException('Failed to initialize HTTP request.');
        }

        $headers = [
            'Accept: application/json',
            'X-Auth-Token: ' . $this->apiKey,
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($body !== null) {
            $encodedBody = json_encode($body);
            if ($encodedBody === false) {
                curl_close($ch);
                throw new ApiException('Failed to encode request body as JSON.');
            }

            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedBody);
        }

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new ApiException('HTTP request failed: ' . $curlError, $statusCode ?: 0);
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            $errorPayload = $expectsJson ? json_decode($response, true) : ['body' => $response];
            throw new ApiException('API returned an error response.', $statusCode, $errorPayload);
        }

        if (!$expectsJson) {
            return $response;
        }

        $decoded = json_decode($response, true);

        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException('Failed to decode JSON response: ' . json_last_error_msg(), $statusCode);
        }

        return $decoded ?? [];
    }
}
