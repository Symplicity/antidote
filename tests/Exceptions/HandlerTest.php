<?php

use \App\Exceptions\Handler;

class HandlerTest extends TestCase
{
    public function testReport()
    {
        $handler = new Handler(false);

        $response = $handler->report(new \Exception('Foo'));

        // TODO: verify logger interface got called
    }

    public function testRender()
    {
        $handler = new Handler(false);

        $response = $handler->render(null, new \Exception('Foo'));

        $this->assertEquals(500, $response->getStatusCode());
    }
}
