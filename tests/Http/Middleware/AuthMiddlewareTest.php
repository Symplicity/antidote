<?php

use \App\Http\Middleware\AuthMiddleware;

class AuthMiddlewareTest extends TestCase
{
    /**
     * @var AuthMiddleware
     */
    private $middle;
    /**
     * @var Illuminate\Auth\Guard
     */
    private $guard;
    /**
     * @var Illuminate\Http\Request
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->guard = \Mockery::mock('Illuminate\Auth\Guard');
        $this->request = \Mockery::mock('Illuminate\Http\Request');

        $this->middle = new AuthMiddleware($this->guard);
    }

    public function testHandleSansAuthorization()
    {
        $this->request->shouldReceive('header')->once()->with('Authorization')->andReturn(false);

        $response = $this->middle->handle($this->request, function () {});

        $this->assertContains('make sure your request has an Authorization header', $response->getContent());
    }

    public function testHandleSansToken()
    {
        $this->request->shouldReceive('header')->once()->with('Authorization')->andReturn('foo bar');

        $response = $this->middle->handle($this->request, function () {});

        $this->assertContains('Token could not be decoded', $response->getContent());
    }
}
