<?php

use Illuminate\Support\Facades\Artisan;

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setupDatabase();
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function setupDatabase()
    {
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }
}
