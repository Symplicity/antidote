<?php

class CommandServiceProviderTest extends TestCase
{
    /**
     * @dataProvider getCommands
     */
    public function testRegister($command, $class)
    {
        $importer = $this->app[$command];
        $this->assertInstanceOf("\\App\\Console\\Commands\\$class", $importer);
    }

    public function getCommands()
    {
        return [
            ['import.drugs', 'ImportDrugs'],
            ['make.token', 'MakeToken']
        ];
    }
}
