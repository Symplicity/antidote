<?php

class ExampleTest extends TestCase
{
    public function testEnvironment() {
        $this->assertEquals('testing', $this->app->environment());
    }
}
