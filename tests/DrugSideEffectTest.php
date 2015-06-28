<?php

class DrugSideEffectTest extends TestCase
{
    public function testRelations()
    {
        $review = factory('App\DrugSideEffect')->make();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $review->drugs());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $review->drugReviews());
    }
}
