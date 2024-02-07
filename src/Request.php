<?php

declare(strict_types=1);

namespace LabelcampAPI;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GrahamCampbell\GuzzleFactory\GuzzleFactory;

class Request {

    public const TOKEN_ENDPOINT = 'https://api.idol.io/oauth';
    public const API_ENDPOINT = 'https://api.idol.io/api/v2';

    protected ClientInterface $client;

    protected array $lastResponse = [];

    /**
     * Constructor
     * Set client.
     *
     * @param ClientInterface $client Optional. Client to set.
     */
    public function __construct(ClientInterface $client = null) {
        $this->client = $client ?? new Client(['handler' => GuzzleFactory::handler()]);
    }

    /**
     * Handle response errors.
     *
     * @param string $body The raw, unparsed response body.
     * @param int $status The HTTP status code, passed along to any exceptions thrown.
     *
     * @throws LabelcampAPIException
     *
     * @return void
     */
    protected function handleResponseError(string $body, int $status): void {
        $parsedBody = json_decode($body);
        $errors = $parsedBody->errors ?? null;
        $error_message = $errors[0]->status == 401 ? $errors[0]->detail : null;
        $user_message = $errors[0]->title ?? null;

        if ($error_message) {
            // It's an Auth error
            throw new LabelcampAPIException($this->parseError($error_message), $status);
        } elseif (isset($parsedBody->error_description) && is_string($parsedBody->error)) {
            // It's an auth call error
            throw  new LabelcampAPIException($parsedBody->error_description, $status);
        } elseif ($user_message) {
            // It's a user error
            throw new LabelcampAPIException($user_message, $status);
        } else {
            // Something went really wrong, we don't know what
            throw new LabelcampAPIException('An unknown error occurred.', $status);
        }
    }

    protected function parseError($summary = null) {
        if (stripos($summary, 'access token expired') !== false) {
            return 'The access token expired';
        } elseif (stripos($summary, 'access token is invalid') !== false) {
            return 'Invalid refresh token';
        }
    }

    /**
     * Get the latest full response from the Labelcamp API.
     *
     * @return array Response data.
     * - array body The response body.
     * - array headers Response headers.
     * - int status HTTP status code.
     * - string url The requested URL.
     */
    public function getLastResponse(): array {
        return $this->lastResponse;
    }

    /**
     * Make a request to the "token" endpoint.
     *
     * @param string $method The HTTP method to use.
     * @param string $uri The URI to request.
     * @param array $options
     *
     * @throws LabelcampAPIException
     *
     * @return array Response data.
     * - array body The response body.
     * - array headers Response headers.
     * - int status HTTP status code.
     * - string url The requested URL.
     */
    public function token(string $method, string $uri, array $options): array {
        return $this->send($method, self::TOKEN_ENDPOINT . $uri, $options);
    }

    /**
     * Make a request to the endpoint.
     *
     * @param string $method The HTTP method to use.
     * @param string $uri The URI to request.
     * @param array $options
     *
     * @throws LabelcampAPIException
     *
     * @return array Response data.
     * - array body The response body.
     * - array headers Response headers.
     * - int status HTTP status code.
     * - string url The requested URL.
     */
    public function api(string $method, string $uri, array $options): array {
        return $this->send($method, self::API_ENDPOINT . $uri, $options);
    }

    /**
     * Make a request to Labelcamp.
     * You'll probably want to use one of the convenience methods instead.
     *
     * @param string $method The HTTP method to use.
     * @param string $url The URL to request.
     * @param array $options
     *
     * @throws LabelcampAPIException
     * 
     * @return array Response data.
     * - array body The response body.
     * - array headers Response headers.
     * - int status HTTP status code.
     * - string url The requested URL.
     */
    public function send(string $method, string $url, array $options): array {
        // Reset any old responses
        $this->lastResponse = [];

        try {
            $response = $this->client->request($method, $url, $options);
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            $this->handleResponseError($exception->getResponse()->getBody()->getContents(), $exception->getResponse()->getStatusCode());
        }

        $body = $parsedBody = $response->getBody();
        $status = $response->getStatusCode();
        $parsedHeaders = $response->getHeaders();

        if (in_array('application/vnd.api+json', $response->getHeader('Content-Type'))) {
            $parsedBody = json_decode($body->getContents(), true);
        }

        $this->lastResponse = [
            'body' => $parsedBody,
            'headers' => $parsedHeaders,
            'status' => $status,
            'url' => $url,
        ];

        return $this->lastResponse;
    }
}
