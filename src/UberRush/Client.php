<?php

namespace UberRush;

use GuzzleHttp\Client as HttpClient;
use UberRush\Authentication\AccessToken;

class Client
{
    /**
     * API Base URL
     */
    const API_HOST = 'api.uber.com';

    /**
     * API Version
     */
    const API_VERSION = 'v1';

    /**
     * HTTP Client
     *
     * @var
     */
    private $http;

    /**
     * Config
     *
     * @var array
     */
    private $config;

    /**
     * Is Sadnbox mode
     *
     * @var
     */
    private $sandbox;

    /**
     * Access Tokens to make calls
     *
     * @var
     */
    private $accessToken;

    /**
     * Client constructor.
     *
     * @param array $config
     * @throws \UberRush\UberRushException
     * @internal param \GuzzleHttp\ClientInterface|null $client
     */
    public function __construct(array $config = [])
    {
        if (! isset($config['client_secret'])) {
            throw new UberRushException('client_secret not provided.');
        }

        if (! isset($config['client_id'])) {
            throw new UberRushException('client_id not provided.');
        }

        $this->config = array_merge([
            'client_secret' => '',
            'client_id'     => '',
            'grant_type'    => 'client_credentials',
            'scope'         => 'delivery',
            'sandbox'       => false,

        ], $config);

        $this->setSandbox($this->config['sandbox']);

        $this->http = new HttpClient;
    }

    /**
     * Request Access Token from the server return and set to the client for future requests
     *
     * @return \UberRush\Authentication\AccessToken
     *
     */
    public function getAccessToken()
    {

        $raw_response = $this->http->post('https://login.uber.com/oauth/v2/token', ['form_params' => $this->config]);

        $response = $this->parseJsonResponse($raw_response);

        $accessToken = new AccessToken($response['access_token'], $response['expires_in']);
        $this->setAccessToken($accessToken);

        return $accessToken;
    }

    /**
     * @param \UberRush\Authentication\AccessToken $accessToken
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Parse json response from Guzzle Response
     *
     * @param $raw_response
     * @return mixed
     */
    public function parseJsonResponse($raw_response)
    {
        return json_decode($raw_response->getBody(), true);
    }

    /**
     * Request method to call API endpoints
     *
     * @param $method
     * @param $path
     * @param array $parameters
     * @return mixed
     */
    public function request($method, $path, $parameters = [])
    {
        $url = $this->prepareRequestUrl($path);

        $method = strtolower($method);

        $parameters = $this->prepareRequestParameters($method, $parameters);

        $response = $this->http->$method($url, $parameters);

        return $this->parseJsonResponse($response);
    }

    /**
     * Prepare URL for different environments
     *
     * @param string $path
     * @return string
     */
    public function prepareRequestUrl($path = '')
    {
        return 'https://'.($this->sandbox ? 'sandbox-' : '').self::API_HOST.'/'.self::API_VERSION.'/'.ltrim($path, '/');
    }

    /**
     * Prepare request paramers for Guzzle
     *
     * @param $method
     * @param $parameters
     * @return array
     */
    public function prepareRequestParameters($method, $parameters)
    {

        $prepared_parameters = [
            'headers' => $this->getHeaders(),
        ];

        if (! empty($parameters)) {

            if ($method == 'get') {

                $prepared_parameters['query'] = $parameters;
            } elseif ($method == 'post') {

                $prepared_parameters['json'] = $parameters;
            }
        }

        return $prepared_parameters;
    }

    /**
     * Get request headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Authorization' => $this->getAuthorizationHeader(),
            'Content-Type'  => 'application/json',
        ];
    }

    /**
     * Get Authorization header
     *
     * @return string
     * @throws \UberRush\UberRushException
     */
    private function getAuthorizationHeader()
    {
        if ($this->accessToken) {
            return 'Bearer '.$this->accessToken->getValue();
        }

        throw new UberRushException('Access Token is not set.');
    }

    /**
     * Get current config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $mode
     */
    public function setSandbox($mode)
    {
        $this->sandbox = $mode;
    }

    /**
     * @return mixed
     */
    public function getSandbox()
    {
        return $this->sandbox;
    }
}