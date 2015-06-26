<?php

use App\Http\Controllers\AuthController;

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

        factory('App\User')->create(['username' => 'foo', 'password' => Hash::make('bar')]);

        $this->ctrl = new AuthController;
    }

    public function testBadLogin()
    {
        $request = new Illuminate\Http\Request(['username' => 'foo']);

        $response = $this->ctrl->login($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertContains('Wrong username', $response->getContent());
    }

    public function testGoodLogin()
    {
        $request = new Illuminate\Http\Request(['username' => 'foo', 'password' => 'bar']);

        $response = $this->ctrl->login($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('token', $response->getContent());
    }
}
