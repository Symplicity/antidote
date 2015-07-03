<?php

use \App\Http\Controllers\DrugController;
use \App\Facades\DrugReview;

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
        $this->stubQuery->shouldReceive('with')
            ->once()
            ->with('indications')
            ->andReturn($this->stubQuery);

        $this->stubQuery->shouldReceive('with')
            ->once()
            ->with('prescriptionTypes')
            ->andReturn($this->stubQuery);

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
        $this->mockModel->shouldReceive('select')->once()->with('id', 'label')->andReturn($this->stubQuery);

        $request = new Illuminate\Http\Request;

        $this->ctrl->index($request);
    }

    public function testAutocompleteSearch()
    {
        $params = ['term' => 'foo'];

        $this->stubQuery->shouldReceive('get')->once()->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('where')->once()->with('label', 'LIKE', '%' . $params['term'] . '%')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('orWhere')->once()->with('generic', 'LIKE', '%' . $params['term'] . '%')->andReturn($this->stubQuery);
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
            'term' => 'foo'
        ];
        $this->stubQuery->shouldReceive('paginate')->once()->with(20);
        $this->stubQuery->shouldReceive('where')->once()->with('label', 'LIKE', 'foo%')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('orderBy')->once()->with('label', 'ASC')->andReturn($this->stubQuery);
        $this->mockModel->shouldReceive('select')->once()->with('id', 'label')->andReturn($this->stubQuery);

        $request = new Illuminate\Http\Request($params);

        $this->ctrl->index($request);
    }

    public function testGetReviews()
    {
        $this->stubQuery->shouldReceive('paginate')->once()->with(15);
        $this->stubQuery->shouldReceive('orderBy')->once()->with('downvotes_cache', 'ASC')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('orderBy')->once()->with('upvotes_cache', 'DESC')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('with')->with('sideEffects')->once()->andReturn($this->stubQuery);

        DrugReview::shouldReceive('where')->once()->with('drug_id', 'foo')->andReturn($this->stubQuery);

        $request = new Illuminate\Http\Request;

        $this->ctrl->getReviews('foo', $request);
    }

    public function testGetReviewsFull()
    {
        $params = [
            'min_age' => 18,
            'max_age' => 35,
            'gender' => 'f'
        ];

        $this->stubQuery->shouldReceive('paginate')->once()->with(15);
        $this->stubQuery->shouldReceive('orderBy')->once()->with('upvotes_cache', 'DESC')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('orderBy')->once()->with('downvotes_cache', 'ASC')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('where')->once()->with('age', '>=', 18)->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('where')->once()->with('age', '<=', 35)->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('where')->once()->with('gender', 'f')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('with')->with('sideEffects')->once()->andReturn($this->stubQuery);

        DrugReview::shouldReceive('where')->once()->with('drug_id', 'foo')->andReturn($this->stubQuery);

        $request = new Illuminate\Http\Request($params);

        $this->ctrl->getReviews('foo', $request);
    }

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
        $request = new Illuminate\Http\Request([
            'user' => ['sub' => 'foo'],
            'rating' => 2,
            'is_covered_by_insurance' => 0,
            'comment' => 'Foo'
        ]);

        $this->mockUser->shouldReceive('getAttribute')->once()->with('age')->andReturn(22);
        $this->mockUser->shouldReceive('getAttribute')->once()->with('gender')->andReturn('m');
        $this->mockUser->shouldReceive('find')->once()->with('foo')->andReturn($this->mockUser);

        $mockReview = \Mockery::mock('\App\DrugReview');
        $mockReview->shouldReceive('setAttribute');
        $mockReview->shouldReceive('toArray')->once();
        $mockReview->shouldReceive('save')->once();

        DrugReview::shouldReceive('getModel')->once()->andReturn($mockReview);

        $response = $this->ctrl->addReview('foo', $request);

        $this->assertEquals(201, $response->getStatusCode());
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
