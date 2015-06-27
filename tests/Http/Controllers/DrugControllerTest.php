<?php

class DrugControllerTest extends TestCase
{
    private $ctrl;
    private $stubQuery;
    private $mockModel;

    public function setUp()
    {
        parent::setUp();

        $this->ctrl = new \App\Http\Controllers\DrugController;

        $this->stubQuery = \Mockery::mock('\Illuminate\Database\Eloquent\Builder');
        $this->mockModel = \Mockery::mock('\App\Drug');
        $this->app->instance('Drug', $this->mockModel);
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

    public function testIndexFull()
    {
        $params = [
            'limit' => 20,
            'keywords' => 'foo',
            'alpha' => 'c'
        ];
        $this->stubQuery->shouldReceive('paginate')->once()->with(20);
        $this->stubQuery->shouldReceive('where')->once()->with('label', 'LIKE', '%foo%')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('orWhere')->once()->with('description', 'LIKE', '%foo%')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('where')->once()->with('label', 'LIKE', 'c%')->andReturn($this->stubQuery);
        $this->stubQuery->shouldReceive('orderBy')->once()->with('label', 'ASC')->andReturn($this->stubQuery);

        $this->mockModel->shouldReceive('with')->once()->with('sideEffects')
            ->andReturn($this->stubQuery);

        $request = new Illuminate\Http\Request($params);

        $this->ctrl->index($request);
    }
}
