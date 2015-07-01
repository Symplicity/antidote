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

    public function testRelations()
    {
        $drug = factory('App\Drug')->make();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $drug->reviews());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $drug->sideEffects());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $drug->indications());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $drug->alternatives());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $drug->related());
    }

    public function seedTestDrugAndReviews()
    {
        $this->drug = factory('App\Drug')->create();
        $this->drug->reviews()->save(factory('App\DrugReview')->make([
            'is_covered_by_insurance' => '1',
            'rating' => '3'
        ]));

        $this->drug->reviews()->save(factory('App\DrugReview')->make([
            'is_covered_by_insurance' => '0',
            'rating' => '3'
        ]));

        $this->drug->reviews()->save(factory('App\DrugReview')->make([
            'is_covered_by_insurance' => '1',
            'rating' => '2'
        ]));

        $this->drug->reviews()->save(factory('App\DrugReview')->make([
            'is_covered_by_insurance' => '0',
            'rating' => '1'
        ]));
    }

    public function testGetEffectivenessPercentageAttribute()
    {
        $drug = factory('App\Drug')->make();
        $this->assertSame(0, $drug->effectiveness_percentage, 'New records should return zero for stats');

        $drug = Drug::find($this->drug->id);
        $this->assertSame(1.0, $drug->effectiveness_percentage);
    }

    public function testGetInsuranceCoveragePercentageAttribute()
    {
        $drug = factory('App\Drug')->make();
        $this->assertSame(0, $drug->insurance_coverage_percentage, 'New records should return zero for stats');

        $drug = Drug::find($this->drug->id);
        $this->assertSame(1.0, $drug->insurance_coverage_percentage);
    }

    public function testGetTotalReviewsAttribute()
    {
        $drug = factory('App\Drug')->make();
        $this->assertSame(0, $drug->total_reviews, 'New records should return zero for stats');

        $drug = Drug::find($this->drug->id);
        $this->assertSame(4, $drug->total_reviews);
    }

    public function testAttributes()
    {
        $drug = factory('App\Drug')->make();
        $all_fields = array_keys($drug->toArray());

        \App\Drug::$without_appends = true;
        $fields = array_keys($drug->toArray());

        $this->assertGreaterThan(0, count(array_diff($all_fields, $fields)));
        \App\Drug::$without_appends = false;
    }
}
