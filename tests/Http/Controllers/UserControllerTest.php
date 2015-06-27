<?php

use App\Http\Controllers\UserController;

class UserControllerTest extends TestCase
{
    /**
     * @var UserController
     */
    private $ctrl;
    private $mockModel;

    public function setUp()
    {
        parent::setUp();

        $this->ctrl = new UserController;

        $this->mockModel = \Mockery::mock('\App\User');
        $this->app->instance('User', $this->mockModel);
    }

    public function testGetUser()
    {
        $this->mockModel->shouldReceive('find')->once()->with('foo');

        $request = new Illuminate\Http\Request(['user' => ['sub' => 'foo']]);

        $this->ctrl->getUser($request);
    }

    public function testUpdateUserSansPassword()
    {
        $this->mockModel->shouldReceive('find')->once()->with('foo')
            ->andReturn((object) ['password' => 'foobar']);

        $request = new Illuminate\Http\Request(['user' => ['sub' => 'foo']]);

        Hash::shouldReceive('check')->once()->andReturn(false);

        $response = $this->ctrl->updateUser($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertContains('enter correct current password', $response->getContent());
    }

    public function testUpdateUser()
    {
        $this->mockModel->shouldReceive('getAttribute')->with('password');
        $this->mockModel->shouldReceive('setAttribute');
        $this->mockModel->shouldReceive('save')->once();
        $this->mockModel->shouldReceive('find')->once()->with('foo')->andReturn($this->mockModel);

        $request = new Illuminate\Http\Request(['user' => ['sub' => 'foo']]);

        Hash::shouldReceive('check')->once()->andReturn(true);

        $user = $this->ctrl->updateUser($request);

        $this->assertInstanceOf('\App\User', $user);
    }
}
