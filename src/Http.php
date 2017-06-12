<?php

namespace Socialite\SocialiteManager;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Http
{
    /**
     * The HTTP Client instance.
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * The custom Guzzle configuration options.
     *
     * @var bool
     */
    protected $guzzle = [];

    /**
     * Guzzle client default settings.
     *
     * @var array
     */
    protected static $defaults = [];

    /**
     * Http construct.
     *
     * @param array $guzzle
     */
    public function __construct($guzzle = [])
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Set the Guzzle HTTP client instance.
     *
     * @param \GuzzleHttp\Client $client
     *
     * @return Http
     */
    public function setClient(HttpClient $client)
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * Get a instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new HttpClient($this->guzzle);
        }

        return $this->httpClient;
    }

    /**
     * Set guzzle default settings.
     *
     * @param array $defaults
     */
    public static function setDefaultOptions($defaults = [])
    {
        self::$defaults = $defaults;
    }

    /**
     * Return current guzzle default settings.
     *
     * @return array
     */
    public static function getDefaultOptions()
    {
        return self::$defaults;
    }

    /**
     * The HTTP GET request.
     *
     * @param string $url
     * @param array  $params
     *
     * @return ResponseInterface
     */
    public function get($url, array $params = [])
    {
        return $this->request($url, 'GET', ['query' => $params]);
    }

    /**
     * The HTTP POST request.
     *
     * @param string                                $url
     * @param array|string|resource|StreamInterface $params
     *
     * @return ResponseInterface
     */
    public function post($url, $params = [])
    {
        $key = is_array($options) ? 'form_params' : 'body';

        return $this->request($url, 'POST', [$key => $params]);
    }

    /**
     * Make a HTTP request.
     *
     * @param string $url
     * @param string $method
     * @param array  $options
     *
     * @return ResponseInterface
     */
    public function request($url, $method = 'GET', array $options = [])
    {
        $method = strtoupper($method);
        $options = array_merge(self::$defaults, $options);
        $response = $this->getClient()->request($method, $url, $options);

        return $response;
    }

    /**
     * Set the accept of header.
     *
     * @param string $header
     *
     * @return mixed
     */
    public function accept($header)
    {
        return $this->withHeaders(['Accept' => $header]);
    }

    /**
     * Set the specified header.
     *
     * @param array $headers
     *
     * @return mixed
     */
    public function withHeaders($headers)
    {
        return tap($this, function ($request) use ($headers) {
            return self::$defaults = array_merge_recursive(self::$options, [
                'headers' => $headers,
            ]);
        });
    }

    /**
     * Parse the json.
     *
     * @param ResponseInterface|string $body
     *
     * @return array
     */
    public function parseJson($body)
    {
        return json_decode($body, true);
    }

    /**
     * Call the given Closure with the given value then return the value.
     *
     * @param mixed    $value
     * @param callable $callback
     *
     * @return mixed
     */
    public function tap($value, $callback)
    {
        $callback($value);

        return $value;
    }
}
