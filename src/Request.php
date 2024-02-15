<?php

declare(strict_types=1);

namespace LabelcampAPI;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
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

        if (isset($errors[0]) && isset($errors[0]->detail)) {
            // It's an Auth error
            throw new LabelcampAPIException($errors[0]->detail, $status);
        }

        // Something went really wrong, we don't know what
        throw new LabelcampAPIException('An unknown error occurred.', $status);
    }

    /**
     * Handle server errors.
     *
     * @param string $body The raw, unparsed response body.
     * @param int $status The HTTP status code, passed along to any exceptions thrown.
     *
     * @throws LabelcampAPIException
     *
     * @return void
     */
    protected function handleServerError(string $body, int $status): void {
        $parsedBody = json_decode($body);

        $message = $parsedBody->message ?? null;

        if ($message) {
            throw new LabelcampAPIException('API error: ' . $message, $status);
        }

        throw new LabelcampAPIException('API error: Internal Server Error', $status);
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
        } catch (ServerException $exception) {
            $response = $exception->getResponse();
            $this->handleServerError($exception->getResponse()->getBody()->getContents(), $exception->getResponse()->getStatusCode());
        }

        $body = $parsedBody = $response->getBody();
        $status = $response->getStatusCode();
        $parsedHeaders = $response->getHeaders();

        if (in_array('application/vnd.api+json', $response->getHeader('Content-Type'))) {
            $parsedBody = json_decode($body->getContents(), true);
        }

        if (in_array('application/json; charset=utf-8', $response->getHeader('Content-Type'))) {
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
