<?php

use App\Facades\OpenFDA;

class OpenFDATest extends TestCase
{
    public function testGetEmptyLabel()
    {
        $this->mockGuzzle(200, [
            'results' => [
                [
                    'openfda' => [
                        'brand_name' => [
                            'FOO'
                        ]
                    ],
                    'purpose' => [
                        'Health'
                    ]
                ]
            ]
        ]);

        $fda_label = OpenFDA::getLabel(['label' => 'foo'], ['rx123']);

        $this->assertEquals([
            'name' => 'FOO',
            'description' => 'Health',
            'prescription_types' => []
        ], $fda_label);
    }
}
