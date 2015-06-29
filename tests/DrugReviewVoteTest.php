<?php

class DrugReviewVoteTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setupDatabase();
    }

    public function testSetVoteAttributeWithInvalidValueSetsDefault()
    {
        $drug_review = factory('App\DrugReviewVote')->create(
            [
                'user_id' => 1,
                'drug_review_id' => 1,
                'vote' => -2
            ]);
        $this->assertSame(1, $drug_review->vote);
    }
}
