<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;

class OpenFDA {

	private $client;

	public function __construct() {
		// Create a client with a base URI
		$this->client = new Client(['base_uri' => Config::get('openfda.api_base_uri')]);
	}

	public function getDrugInfo($ndc) {

		try {
			$res = $this->client->get('drug/label.json', [
				'query' => [
					'api_key' => env('OPENFDA_API_KEY'),
					'search' => 'product_ndc:' . $ndc
				]
			]);
		} catch (RequestException $e) {
			echo $e->getRequest();
			if ($e->hasResponse()) {
				return $e->getResponse();
			}
		}

		return $res->getBody()->getContents();
	}
}
