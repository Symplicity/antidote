<?php

use \App\Http\Controllers\DrugReviewsController;

class DrugReviewsControllerTest extends TestCase
{
    /**
     * @var DrugReviewsController
     */
    private $ctrl;

    public function setUp()
    {
        parent::setUp();

        $this->ctrl = new DrugReviewsController;
    }

    public function testVote()
    {
        $this->setupDatabase();

        $request = new Illuminate\Http\Request([
            'user' => ['sub' => 'foo'],
            'vote' => -1
        ]);

        $response = $this->ctrl->vote(1, $request);

        $this->assertEquals(201, $response->getStatusCode());
        $vote = json_decode($response->getContent());
        $this->assertEquals(-1, $vote->vote);
        $vote_id = $vote->id;

        $request = new Illuminate\Http\Request([
            'user' => ['sub' => 'foo'],
            'vote' => 1
        ]);

        $response = $this->ctrl->vote(1, $request);

        $this->assertEquals(201, $response->getStatusCode());
        $vote = json_decode($response->getContent());
        $this->assertEquals(1, $vote->vote, 'Repeated vote should override previous one');
        $this->assertEquals($vote_id, $vote->id, 'Repeated vote should keep the id');

        $request = new Illuminate\Http\Request([
            'user' => ['sub' => 'bar'],
            'vote' => 1
        ]);

        $response = $this->ctrl->vote(1, $request);

        $this->assertEquals(201, $response->getStatusCode());
        $vote = json_decode($response->getContent());
        $this->assertEquals(1, $vote->vote, 'Repeated vote should override previous one');
        $this->assertNotEquals($vote_id, $vote->id, 'Vote by another user should be new');

        $response = $this->ctrl->vote(1, $request);
        $this->assertEquals(400, $response->getStatusCode(), 'Exact same vote by the same user is rejected');
        $this->assertContains('already voted on this review', $response->getContent());
    }
}
