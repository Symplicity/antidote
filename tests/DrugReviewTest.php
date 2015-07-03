<?php

use App\Facades\DrugReview;

class DrugReviewTest extends TestCase
{
    public function testRelations()
    {
        $review = factory('App\DrugReview')->make();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $review->user());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $review->drug());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $review->rating());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $review->votes());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $review->sideEffects());
    }

    public function testGetUpvotesAttribute()
    {
        $review = DrugReview::getModel();
        $this->assertSame(0, $review->getUpvotesAttribute());

        $review->upvotes_cache = 1;
        $this->assertSame(1, $review->getUpvotesAttribute());
    }

    public function testGetDownvotesAttribute()
    {
        $review = DrugReview::getModel();
        $this->assertSame(0, $review->getDownvotesAttribute());

        $review->downvotes_cache = 1;
        $this->assertSame(1, $review->getDownvotesAttribute());
    }

    public function testGetCreatedAtAttribute()
    {
        $review = DrugReview::getModel();
        $this->assertSame(
            date('Y-m-d\TH:i:sO'),
            $review->getCreatedAtAttribute(''),
            'Date should be in ISO 8601 format'
        );
        $this->assertSame(
            '2007-02-10T00:00:00+0000',
            $review->getCreatedAtAttribute('2007-02-10'),
            'Date should be in ISO 8601 format'
        );
    }
}
