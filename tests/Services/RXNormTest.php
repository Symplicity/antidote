<?php

use App\Facades\RXNorm;

class RXNormTest extends TestCase
{
    public function testGetRelatedConcepts()
    {
        $this->mockGuzzle([
            [
                'code' => 200,
                'content' => [
                    'relatedGroup' => [
                        'conceptGroup' => [[
                            'conceptProperties' => [[
                                'rxcui' => 'foo'
                            ]]
                        ]]
                    ]
                ]
            ]
        ]);

        $concepts = RXNorm::getRelatedConcepts('rx123');

        $this->assertEquals(['foo'], $concepts);
    }
}
