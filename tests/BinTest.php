<?php

include_once(__DIR__ . '/Base.php');


class BinTest extends Base
{
    public function testHasOption()
    {
        global $argv;
        $argv = ['script.php', '--testOption=value'];

        $this->assertTrue($this->app->bin->hasOption('testOption'));
        $this->assertFalse($this->app->bin->hasOption('nonExistentOption'));
    }

    public function testGetOption()
    {
        global $argv;
        $argv = ['script.php', '--testOption=value'];

        $this->assertEquals('value', $this->app->bin->getOption('testOption'));
        $this->assertEquals('default', $this->app->bin->getOption('nonExistentOption', '', 'default'));
    }
}