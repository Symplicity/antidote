<?php

use App\Services\MedicalTranslator;

class MedicalTranslatorTest extends TestCase
{
    /**
     * @dataProvider getMedicalTerms
     */
    public function testTranslate($term, $correct_translation)
    {
        $translator = new MedicalTranslator;
        $translation = $translator->translate($term);
        $this->assertEquals($correct_translation, $translation);
    }

    public function getMedicalTerms()
    {
        return [
            ['hiv infection', 'aids'],
            ['situational anaemia', 'situational anemia'],
            ['blood test, abnormal', 'abnormal blood test'],
            ['accidental drug intake by child', ''],
            ['something random', 'something random'],
            ['blood cholesterol increased', 'increased cholesterol'],
            ];
    }
}
