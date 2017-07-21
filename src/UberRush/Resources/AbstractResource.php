<?php

namespace UberRush\Resources;

use UberRush\Client;
use GuzzleHttp\Exception\ClientException;
use UberRush\UberRushException;

/**
 * @package UberRush\Resources
 */
abstract class AbstractResource
{
    /**
     * HTTP Client
     *
     * @var Client
     */
    protected $client;

    /**
     * Endpoint URL
     *
     * @var
     */
    protected $endpoint;

    /**
     * Request method
     *
     * @var
     */
    protected $method;

    /**
     * Request parameters
     *
     * @var array
     */
    protected $params = [];

    /**
     * Constructor
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Handle API Calls
     *
     * @return mixed
     * @throws UberRushException
     */
    protected function send()
    {
        try {

            $response = $this->client->request($this->getMethod(), $this->getEndpoint(), $this->getParams());

        } catch (ClientException $e) {

            $status_code = $e->getResponse()->getStatusCode();
            $response = $this->client->parseJsonResponse($e->getResponse());

            if($status_code == 422) {

                if(isset($response['meta']['code']) == 'validation_failed' && isset($response['meta']['message'])) {
                    throw new UberRushException($response['meta']['message'], 0, null, $response['meta']['fields']);
                }

            }

            throw new UberRushException($e->getMessage(), $e->getMessage());

        } catch (\Exception $e) {

            throw new UberRushException($e->getMessage(), $e->getCode());
        }

        if ($response === null) {

            throw new UberRushException('Empty body response.');
        }

        return $response;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     *
     * @return AbstractResource
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     *
     * @return AbstractResource
     */
    protected function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return AbstractResource
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }
}