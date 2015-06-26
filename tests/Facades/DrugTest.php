<?php

class DrugFacadeTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setupDatabase();
    }

    public function testReviews()
    {
        $drug = App\Facades\Drug::create();
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Model', $drug);
    }
}
