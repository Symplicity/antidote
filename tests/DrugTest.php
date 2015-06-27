<?php

use App\Drug;

class DrugTest extends TestCase
{
    private $drug;

    public function setUp()
    {
        parent::setUp();

        $this->setupDatabase();
        $this->seedTestDrugAndReviews();
    }

    public function seedTestDrugAndReviews()
    {
        $this->drug = factory('App\Drug')->create();
        $this->drug->reviews()->save(factory('App\DrugReview')->make([
            'is_covered_by_insurance' => '1',
            'rating' => '1'
        ]));

        $this->drug->reviews()->save(factory('App\DrugReview')->make([
            'is_covered_by_insurance' => '0',
            'rating' => '1'
        ]));

        $this->drug->reviews()->save(factory('App\DrugReview')->make([
            'is_covered_by_insurance' => '1',
            'rating' => '2'
        ]));

        $this->drug->reviews()->save(factory('App\DrugReview')->make([
            'is_covered_by_insurance' => '0',
            'rating' => '3'
        ]));
    }

    public function testGetEffectivenessPercentageAttribute()
    {
        $drug = Drug::find($this->drug->id);
        $this->assertSame(0.5, $drug->effectiveness_percentage);
    }

    public function testGetInsuranceCoveragePercentageAttribute()
    {
        $drug = Drug::find($this->drug->id);
        $this->assertSame(0.5, $drug->insurance_coverage_percentage);
    }

    public function testGetTotalReviewsAttribute()
    {
        $drug = Drug::find($this->drug->id);
        $this->assertSame(4, $drug->total_reviews);
    }
}
