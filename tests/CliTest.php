<?php

include_once(__DIR__ . '/Base.php');


class CliTest extends Base
{
    public function testHasOption()
    {
        global $argv;
        $argv = ['script.php', '--testOption=value'];

        $this->assertTrue($this->app->cli->hasOption('testOption'));
        $this->assertFalse($this->app->cli->hasOption('nonExistentOption'));
    }

    public function testGetOption()
    {
        global $argv;
        $argv = ['script.php', '--testOption=value'];

        $this->assertEquals('value', $this->app->cli->getOption('testOption'));
        $this->assertEquals('default', $this->app->cli->getOption('nonExistentOption', '', 'default'));
    }
}