<?php

class DrugRatingTest extends TestCase
{
    public function testRelations()
    {
        $rating = new \App\DrugRating;
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $rating->reviews());
    }
}
