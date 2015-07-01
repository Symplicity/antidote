<?php

use App\DrugReview;
use App\DrugReviewVote;

class DrugReviewVoteTest extends TestCase
{
    private $drug_review;

    public function setUp()
    {
        parent::setUp();

        $this->setupDatabase();

        $this->seedTestDrugReview();
    }

    public function seedTestDrugReview()
    {
        $this->drug_review = factory('App\DrugReview')->create();
    }

    public function testSetVoteAttributeWithInvalidValueSetsDefault()
    {
        $vote = $this->drug_review->votes()->save(factory('App\DrugReviewVote')->make(['user_id' => 1, 'vote'=> 2]));
        $this->assertSame(1, $vote->vote);
    }

    public function testSetVoteAttributeUpdatesDrugReviewVotesCache()
    {
        DrugReviewVote::flushModelEvents();

        $this->drug_review->votes()->save(factory('App\DrugReviewVote')->make(['user_id' => 1, 'vote'=> 1]));
        $this->drug_review->votes()->save(factory('App\DrugReviewVote')->make(['user_id' => 2, 'vote'=> 1]));

        $drug_review = DrugReview::find($this->drug_review->id);
        $this->assertSame(2, $drug_review->upvotes_cache);

        $this->drug_review->votes()->save(factory('App\DrugReviewVote')->make(['user_id' => 3, 'vote'=> -1]));
        $drug_review = DrugReview::find($this->drug_review->id);
        $this->assertSame(1, $drug_review->downvotes_cache);
    }

    public function testSetVoteAttributeOnExistingVoteUpdatesDrugReviewVotesCache()
    {
        DrugReviewVote::flushModelEvents();

        $vote = $this->drug_review->votes()->save(factory('App\DrugReviewVote')->make(['user_id' => 1, 'vote'=> 1]));

        $drug_review = DrugReview::find($this->drug_review->id);
        $this->assertSame(1, $drug_review->upvotes_cache);

        $vote->vote = -1;
        $vote->save();

        $drug_review = DrugReview::find($this->drug_review->id);
        $this->assertSame(0, $drug_review->upvotes_cache);
        $this->assertSame(1, $drug_review->downvotes_cache);
    }

    public function testRelations()
    {
        $review = factory('App\DrugReviewVote')->make();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $review->user());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $review->drugReview());
    }
}
