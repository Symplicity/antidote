<?php

class GuzzleServiceProviderTest extends TestCase
{
    public function testReviews()
    {
        $client = $this->app['Guzzle'];
        $this->assertInstanceOf('\GuzzleHttp\Client', $client);
    }
}
