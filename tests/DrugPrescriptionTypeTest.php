<?php

class DrugPrescriptionTypeTest extends TestCase
{
    public function testRelations()
    {
        $rating = new \App\DrugPrescriptionType;
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $rating->drugs());
    }
}
