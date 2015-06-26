<?php

class DrugFacadeTest extends TestCase
{
    public function testReviews()
    {
        $drug = App\Facades\Drug::create();
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Model', $drug);
    }
}
