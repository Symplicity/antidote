<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;

class OpenFDA
{
    private $client = null;
    private $api_base_uri = '';

    public function __construct(Client $client = null)
    {
        // Create a client with a base URI
        $this->client = $client;
        $this->api_base_uri = Config::get('openfda.api_base_uri');
    }

    public function getDrugInfo($ndc)
    {
        try {
            $res = $this->client->get($this->api_base_uri . 'drug/label.json', [
                'query' => [
                    'api_key' => env('OPENFDA_API_KEY'),
                    'search' => 'product_ndc:'.$ndc,
                ],
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return $e->getResponse();
            }
        }

        return $res->getBody()->getContents();
    }
}
