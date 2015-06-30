<?php

use \App\Http\Controllers\DrugController;

class DrugControllerTest extends TestCase
{
    /**
     * @var DrugController
     */
    private $ctrl;
    private $stubQuery;
    private $mockModel;
    private $mockUser;

    public function setUp()
    {
        parent::setUp();

        $this->ctrl = new DrugController;

        $this->stubQuery = \Mockery::mock('\Illuminate\Database\Eloquent\Builder');
        $this->mockModel = \Mockery::mock('\App\Drug');
        $this->app->instance('Drug', $this->mockModel);

        $this->mockUser = \Mockery::mock('\App\User');
        $this->app->instance('User', $this->mockUser);
    }

    public function testShow()
    {
        $this->stubQuery->shouldReceive('find')->once()->with(1);
        $this->stubQuery->shouldReceive('with')->once()->with('indications')->andReturn($this->stubQuery);
        $this->mockModel->shouldReceive('with')
            ->once()
            ->with('sideEffects')
            ->andReturn($this->stubQuery);

        $this->ctrl->show(1);
    }

    public function testIndex()
    {
        $this->stubQuery->shouldReceive('paginate')->once()->with(15);
        $this->stubQuery->shouldReceive('orderBy')->once()->with('label', 'ASC')->andReturn($this->stubQuery);
        $this->mockModel->shouldReceive('with')->once()->with('sideEffects')->andReturn($this->stubQuery);

        $request = new Illuminate\Http\Request;

        $this->ctrl->index($request);
    }

    public function testAutocompleteSearch()
    {
        $params = ['term' => 'foo'];
        $this->stubQuery->shouldReceive('get')->once()->with('label', 'id');
        $this->stubQuery->shouldReceive('where')->once()->with('label', 'LIKE', '%' . $params['term'] . '%')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('limit')->once()->with(15)->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('orderBy')->once()->with('label', 'ASC')->andReturn($this->stubQuery);
        $this->mockModel->shouldReceive('select')->once()->with('id', 'label', 'generic')->andReturn($this->stubQuery);

        $request = new Illuminate\Http\Request($params);

        $this->ctrl->autocompleteSearch($request);
    }

    public function testIndexFull()
    {
        $params = [
            'limit' => 20,
            'keywords' => 'foo'
        ];
        $this->stubQuery->shouldReceive('paginate')->once()->with(20);
        $this->stubQuery->shouldReceive('where')->once()->with('label', 'LIKE', '%foo%')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('orWhere')->once()->with('description', 'LIKE', '%foo%')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('orderBy')->once()->with('label', 'ASC')->andReturn($this->stubQuery);

        $this->mockModel->shouldReceive('with')->once()->with('sideEffects')
            ->andReturn($this->stubQuery);

        $request = new Illuminate\Http\Request($params);

        $this->ctrl->index($request);
    }

    /*public function testGetReviews() TODO: fix this test for new query but since that is in flux wait to change this
    {
        $this->stubQuery->shouldReceive('paginate')->once()->with(15);
        $this->stubQuery->shouldReceive('orderBy')->once()->with('created_at', 'DESC')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('with')->with('sideEffects')->once()->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('with')->with('drug')->once()->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('with')->with('user')->once()->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('reviews')->once()->andReturn($this->stubQuery);
        $this->mockModel->shouldReceive('find')->once()->with('foo')->andReturn($this->stubQuery);

        $request = new Illuminate\Http\Request;

        $this->ctrl->getReviews('foo', $request);
    }*/

    public function testAddReviewSansRating()
    {
        $request = new Illuminate\Http\Request([
            'user' => ['sub' => 'foo'],
            'comment' => 'Foo'
        ]);

        $response = $this->ctrl->addReview('foo', $request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('rating field is required', $response->getContent());
    }

    public function testAddReview()
    {
        $this->setupDatabase();

        $request = new Illuminate\Http\Request([
            'user' => ['sub' => 'foo'],
            'rating' => 2,
            'is_covered_by_insurance' => 0,
            'comment' => 'Foo'
        ]);

        $this->mockUser->shouldReceive('getAttribute')->once()->with('age')->andReturn(22);
        $this->mockUser->shouldReceive('getAttribute')->once()->with('gender')->andReturn('m');
        $this->mockUser->shouldReceive('find')->once()->with('foo')->andReturn($this->mockUser);

        $response = $this->ctrl->addReview('foo', $request);

        $this->assertEquals(201, $response->getStatusCode());
        $review = json_decode($response->getContent());
        $this->assertEquals('Foo', $review->comment);
    }

    public function testGetAlternatives()
    {
        $this->stubQuery->shouldReceive('paginate')->once()->with(15);
        $this->stubQuery->shouldReceive('orderBy')->once()->with('label', 'DESC')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('with')->with('sideEffects')->once()->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('alternatives')->once()->andReturn($this->stubQuery);
        $this->mockModel->shouldReceive('find')->once()->with('foo')->andReturn($this->stubQuery);

        $request = new Illuminate\Http\Request;

        $this->ctrl->getAlternatives('foo', $request);
    }
}
