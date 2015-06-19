<?php

use App\Services\OpenFDA;

class OpenFDATest extends TestCase
{
    public function testGetDrugInfo()
    {
        $client = new OpenFDA();
        $fda_info = json_decode($client->getDrugInfo('50090-0056'), true);
        $this->assertEquals('PILOCARPINE HYDROCHLORIDE', $fda_info['results'][0]['openfda']['generic_name'][0]);
    }

    public function testBadDrugInfo()
    {
        $client = new OpenFDA();
        $response = $client->getDrugInfo('foo');
        $this->assertEquals('Not Found', $response->getReasonPhrase());
    }
}
