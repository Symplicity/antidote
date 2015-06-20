<?php

use App\Facades\OpenFDA;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class OpenFDATest extends TestCase
{
	private static $client;

	public static function setUpBeforeClass()
	{
		self::$client = self::mockGuzzle();
	}

    public function testGetDrugInfo()
    {
		$this->app->instance('Guzzle', self::$client);
		$fda_info = json_decode(OpenFDA::getDrugInfo('42893-030'), true);
        $this->assertEquals('FOO DIOXIDE', $fda_info['results'][0]['openfda']['generic_name'][0]);
    }

    public function testBadDrugInfo()
	{
		$this->app->instance('Guzzle', self::$client);
		$response = OpenFDA::getDrugInfo('foo');
		$this->assertEquals('Not Found', $response->getReasonPhrase());
    }

	private static function mockGuzzle()
	{

		$successful_response = [
			'results' => [
				[
					'openfda' => [
						'generic_name' => [
							'FOO DIOXIDE'
						]
					]
				]
			]
		];

		// Create a mock and queue two responses.
		$mock = new MockHandler([
			new Response(200, ['X-Foo' => 'Bar'], json_encode($successful_response)),
			new Response(404, ['Content-Length' => 0])
		]);

		$handler = HandlerStack::create($mock);
		$client = new Client(['handler' => $handler]);

		return $client;
	}
}
