<?php

class DrugIndicationTest extends TestCase
{
    public function testRelations()
    {
        $indication = factory('App\DrugIndication')->make();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $indication->drugs());
    }
}
