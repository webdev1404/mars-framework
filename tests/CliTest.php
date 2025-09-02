<?php

include_once(__DIR__ . '/Base.php');


class CliTest extends Base
{
    public function testHasOption()
    {
        global $argv;
        $argv = ['script.php', '--testOption=value'];

        $this->assertTrue($this->app->cli->has('testOption'));
        $this->assertFalse($this->app->cli->has('nonExistentOption'));
    }

    public function testGetOption()
    {
        global $argv;
        $argv = ['script.php', '--testOption=value'];

        $this->assertEquals('value', $this->app->cli->get('testOption'));
        $this->assertEquals('default', $this->app->cli->get('nonExistentOption', 'default'));
    }
}
