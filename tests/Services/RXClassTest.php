<?php

use App\Facades\RXClass;

class RXClassTest extends TestCase
{
    public function testGetRelatedConcepts()
    {
        $this->mockGuzzle([
            [
                'code' => 200,
                'content' => [
                    'rxclassDrugInfoList' => [
                        'rxclassDrugInfo' => [[
                            'rxclassMinConceptItem' => [
                                'classId' => 'foo'
                            ]
                        ]]
                    ]
                ]
            ],
            [
                'code' => 200,
                'content' => [
                    'drugMemberGroup' => [
                        'drugMember' => [[
                            'minConcept' => [
                                'rxcui' => 'bar'
                            ]
                        ]]
                    ]
                ]
            ]
        ]);

        $concepts = RXClass::getRelatedConcepts('rx123');

        $this->assertEquals(['bar'], $concepts);
    }
}
