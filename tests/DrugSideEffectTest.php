<?php

class DrugSideEffectTest extends TestCase
{
    public function testRelations()
    {
        $effect = factory('App\DrugSideEffect')->make();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $effect->drugs());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $effect->drugReviews());
    }
}
