<?php

class UserControllerTest extends TestCase
{
    private $ctrl;
    private $stubQuery;
    private $mockModel;

    public function setUp()
    {
        parent::setUp();

        $this->ctrl = new \App\Http\Controllers\UserController;

        $this->mockModel = \Mockery::mock('\App\User');
        $this->app->instance('User', $this->mockModel);
    }

    public function testGetUser()
    {
        $this->mockModel->shouldReceive('find')->once()->with('foo');

        $request = new Illuminate\Http\Request(['user' => ['sub' => 'foo']]);

        $user = $this->ctrl->getUser($request);
    }
}
