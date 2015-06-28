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
        $this->request->shouldReceive('header')->twice()->with('Authorization')->andReturn('foo bar');

        $response = $this->middle->handle($this->request, function () {});

        $this->assertContains('Token could not be decoded', $response->getContent());
    }

    public function testHandleWithToken()
    {
        $payload = [
            'sub' => 'foo',
            'iat' => time() - 1,
            'exp' => time() + 10
        ];

        $token = JWT::encode($payload, env('APP_KEY'));
        $this->request->shouldReceive('header')->twice()->with('Authorization')->andReturn('foo ' . $token);
        $this->request->shouldReceive('offsetSet')->once();
        $this->request->shouldReceive('getResponse')->once();

        $response = $this->middle->handle($this->request, function ($request) {
            $request->getResponse();
        });
        $this->assertNull($response);
    }

    public function testExpiredToken()
    {
        $payload = [
            'sub' => 'foo',
            'iat' => time() - 10,
            'exp' => time() - 1
        ];

        $token = JWT::encode($payload, env('APP_KEY'));
        $this->request->shouldReceive('header')->twice()->with('Authorization')->andReturn('foo ' . $token);

        $response = $this->middle->handle($this->request, function () {});

        $this->assertContains('Token has expired', $response->getContent());
    }
}
