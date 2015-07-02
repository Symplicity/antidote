<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Exceptions\Handler;

abstract class RestAPI
{
    protected $client;
    protected $api_base_uri = null;
    protected $rate_limit = 0;

    private $options = [];
    private $verbose = false;
    private $requests = 0;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->api_class = $this->getAPIClass();
        $this->rate_limit =env(strtoupper($this->api_class) . '_RATE_LIMIT', $this->rate_limit);
    }

    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    private function getAPIClass()
    {
        $class = explode('\\', get_called_class());
        return array_pop($class);
    }

    protected function limitRate()
    {
        if ($this->rate_limit && ++$this->requests % $this->rate_limit === 0) {
            if ($this->verbose) {
                echo " Sleeping due to rate limit on [{$this->api_class}]: {$this->requests}\n";
            }
            sleep(1);
        }
    }

    protected function get($query)
    {
        $results = [];

        try {
            $this->limitRate();

            $uri = $this->api_base_uri . $query;
            if ($this->verbose) {
                echo "[" . $this->api_class . "]: " . $uri . "\n";
            }

            $response = $this->client->get($uri, $this->options);
            $json = $response->getBody()->getContents();

            $results = json_decode($json, true);
        } catch (\Exception $e) {
            //skip errors
        }

        return $results;
    }
}
