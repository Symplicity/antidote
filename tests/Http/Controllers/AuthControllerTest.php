<?php

use App\Http\Controllers\AuthController;
use App\Facades\User;

class AuthControllerTest extends TestCase
{
    /**
     * @var AuthController
     */
    private $ctrl;

    public function setUp()
    {
        parent::setUp();
        $this->setupDatabase();

        factory('App\User')->create(['username' => 'existing_user', 'password' => Hash::make('bar')]);

        $this->ctrl = new AuthController;
    }

    /**
     * @dataProvider getUserNames
     */
    public function testBadLogin($username)
    {
        $request = new Illuminate\Http\Request(['username' => $username]);

        $response = $this->ctrl->login($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertContains('Wrong username', $response->getContent());
    }

    public function getUserNames()
    {
        return [
            ['existing_user'],
            ['non_existing_user']
        ];
    }

    public function testGoodLogin()
    {
        $request = new Illuminate\Http\Request(['username' => 'existing_user', 'password' => 'bar']);

        $response = $this->ctrl->login($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('token', $response->getContent());
    }

    public function testBadSignup()
    {
        $request = new Illuminate\Http\Request([
            'username' => 'new_user',
            'password' => 'foobar'
        ]);

        $response = $this->ctrl->signup($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('email field is required', $response->getContent());
    }

    public function testBadEmail()
    {
        $request = new Illuminate\Http\Request([
            'username' => 'new_user',
            'email' => 'foo',
            'password' => 'foobar'
        ]);

        $response = $this->ctrl->signup($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('email must be a valid', $response->getContent());
    }

    public function testGoodSignup()
    {
        $request = new Illuminate\Http\Request([
            'username' => 'new_user',
            'email' => 'foo@bar.com',
            'password' => 'foobar'
        ]);

        $response = $this->ctrl->signup($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('token', $response->getContent());
    }

    public function testBadPasswordUpdate()
    {
        $request = new Illuminate\Http\Request([
            'password' => 'foobar'
        ]);

        $response = $this->ctrl->updatePasswordFromResetToken('foo', $request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('token is invalid', $response->getContent());
    }

    public function testBadForgotPassword()
    {
        $request = new Illuminate\Http\Request([
            'username' => 'non_existing_user'
        ]);

        $response = $this->ctrl->processForgotPassword($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('No account with that username', $response->getContent());
    }

    public function testForgotPassword()
    {
        $request = new Illuminate\Http\Request([
            'username' => 'existing_user'
        ]);

        Mail::shouldReceive('send')
            ->with('emails.forgot-password', \Mockery::type('array'), \Mockery::type('callable'))
            ->once();

        $_SERVER['HTTP_HOST'] = 'test.com';
        $response = $this->ctrl->processForgotPassword($request);

        $this->assertContains('An email has been sent', $response->getContent());
    }

    public function testVerifyBadResetPasswordToken()
    {
        $response = $this->ctrl->verifyResetPasswordToken('foo');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/password/reset/invalid', $response->getTargetUrl());
    }

    public function testVerifyResetPasswordToken()
    {
        $stubQuery = \Mockery::mock('\Illuminate\Database\Eloquent\Builder');
        $stubQuery->shouldReceive('first')->andReturn(true);
        User::shouldReceive('whereRaw')->once()->andReturn($stubQuery);

        $response = $this->ctrl->verifyResetPasswordToken('foo');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains('/password/reset/foo', $response->getTargetUrl());
    }
}
