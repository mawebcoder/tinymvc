<?php

namespace System\Services\Http;

use CURLFile;
use Exception;
use CurlHandle;
use JsonException;
use System\Router\HttpVerbsEnum;
use System\Exceptions\HttpServerException;
use System\Exceptions\InvalidJsonFormatException;

class Http
{

    private const array ERROR_STATUS_CODES = [
        500,
        501,
        502,
        503,
        504,
        505,
        506,
        507,
        508,
        510,
        511,
    ];
    public CurlHandle $curlHandle {
        get {
            return $this->curlHandle;
        }
    }


    private string $url {
        get {
            return trim($this->url, '/');
        }

        set {
            $this->url = trim($value, '/');
        }
    }


    private HttpVerbsEnum $httpVerb;

    private int $retry {
        get {
            return $this->retry ?? 1;
        }
    }
    private array $attach = [];

    private array $data = [];

    private int $timeout {
        get {
            return $this->timeout ?? 30;
        }
    }

    private int $connectionTimeout {
        get {
            return $this->connectionTimeout ?? 10;
        }
    }

    private int $responseTimeout {
        get {
            return $this->responseTimeout ?? 10;
        }
    }

    private int $backoff {
        get {
            return $this->backoff ?? 0;
        }
    }


    private array $headers = [];
    private string $response;

    private array $responseInformation;

    public function __construct()
    {
        $this->curlHandle = curl_init();
    }


    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function attach(array $files): static
    {
        foreach ($files as $key => $file) {
            $this->attach[$key] = new CURLFile($file);
        }
        return $this;
    }


    /**
     * @throws JsonException
     */
    public function post(string $url, array $data = []): static
    {
        $this->httpVerb = HttpVerbsEnum::POST;

        return $this->sendRequest($url, $data);
    }


    /**
     * @throws JsonException
     */
    public function sendRequest(string $url, array $data = []): static
    {
        $this->url = $url;

        $parsedUrl = parse_url($this->url);

        $urlWithoutQuery = ($parsedUrl['scheme'] ?? 'http') . '://' . $parsedUrl['host'] . ':' . ($parsedUrl['port'] ?? ($parsedUrl['scheme'] === 'https' ? '443' : '80')) . $parsedUrl['path'];

        $queryParams = $parsedUrl['query'] ?? null;

        $this->data = $data;

        if (!$this->httpVerb->hasBody()) {
            $arrayQueries = [];
            if (!is_null($queryParams)) {
                parse_str($queryParams, $arrayQueries);
            }
            $arrayQueries = array_merge($data, $arrayQueries);

            $queries = http_build_query($arrayQueries);

            $this->url = $urlWithoutQuery . '?' . $queries;
        }

        foreach (range(1, $this->retry) as $try) {
            try {
                $contentType = $this->attach ? 'multipart/form-data' : 'application/json';

                $options = [
                    CURLOPT_URL => $this->url,
                    CURLOPT_TIMEOUT => $this->timeout,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_CUSTOMREQUEST => $this->httpVerb->value,
                    CURLOPT_SERVER_RESPONSE_TIMEOUT => $this->responseTimeout,
                    CURLOPT_CONNECTTIMEOUT => $this->connectionTimeout,
                    CURLOPT_HTTPHEADER => [
                        ...[
                            'Content-Type: ' . $contentType
                        ],
                        ...$this->headers
                    ]
                ];

                if ($this->httpVerb->hasBody()) {
                    $options[CURLOPT_POSTFIELDS] = $this->attach ? [
                        ...$data,
                        ...$this->attach
                    ] : json_encode($data, JSON_THROW_ON_ERROR);
                }

                curl_setopt_array($this->curlHandle, $options);

                $this->response = curl_exec($this->curlHandle);

                $this->responseInformation = curl_getinfo($this->curlHandle);

                curl_close($this->curlHandle);
            } catch (Exception $exception) {
                if ($try === $this->retry) {
                    throw $exception;
                }

                sleep($this->backoff);

                continue;
            }
        }
        return $this;
    }

    public function retry(int $retry): static
    {
        $this->retry = $retry;
        return $this;
    }

    public function withHeaders(array $headers): static
    {
        foreach ($headers as $key => $value) {
            $this->headers[] = "$key: $value";
        }

        return $this;
    }

    public function acceptJson(): static
    {
        $this->headers[] = 'Accept: application/json';
        return $this;
    }

    /**
     * @throws Exception
     */
    public function throw(): void
    {
        $httpCode = (int)$this->responseInformation['http_code'];

        if (in_array($httpCode, static::ERROR_STATUS_CODES)) {
            throw new HttpServerException($this->body());
        }
    }

    public function backoff(int $backoff): static
    {
        $this->backoff = $backoff;
        return $this;
    }


    /**
     * @throws InvalidJsonFormatException
     * @throws JsonException
     */
    public function json()
    {
        if (json_validate($this->response)) {
            return json_decode($this->body(), true, 512, JSON_THROW_ON_ERROR);
        }

        throw new InvalidJsonFormatException('Invalid JSON response');
    }

    public function body(): string
    {
        return $this->response;
    }

    public function timeout(int $seconds): static
    {
        $this->timeout = $seconds;
        return $this;
    }

    public function connectTimeout(int $seconds): static
    {
        $this->connectionTimeout = $seconds;
        return $this;
    }

    public function responseTimeout(): static
    {
        $this->responseTimeout = 10;
        return $this;
    }

    /**
     * @throws JsonException
     */
    public function put(string $url, array $data = []): static
    {
        $this->httpVerb = HttpVerbsEnum::PUT;

        return $this->sendRequest($url, $data);
    }

    /**
     * @throws JsonException
     */
    public function delete(string $url, array $data = []): static
    {
        $this->httpVerb = HttpVerbsEnum::DELETE;

        return $this->sendRequest($url, $data);
    }

    /**
     * @throws JsonException
     */
    public function patch(string $url, array $data = []): static
    {
        $this->httpVerb = HttpVerbsEnum::PATCH;

        return $this->sendRequest($url, $data);
    }

    /**
     * @throws JsonException
     */
    public function get(string $url, array $data = []): static
    {
        $this->httpVerb = HttpVerbsEnum::GET;

        return $this->sendRequest($url, $data);
    }


}