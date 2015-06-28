<?php

class DrugReviewTest extends TestCase
{
    public function testRelations()
    {
        $review = factory('App\DrugReview')->make();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $review->user());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $review->drug());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $review->sideEffects());
    }
}
