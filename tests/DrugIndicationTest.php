<?php

class DrugIndicationTest extends TestCase
{
    public function testRelations()
    {
        $review = factory('App\DrugIndication')->make();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $review->drugs());
    }
}
