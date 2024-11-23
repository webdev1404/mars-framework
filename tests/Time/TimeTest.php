<?php

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class TimeTest extends Base
{
    public function testGet()
    {
        $time = $this->app->time;

        $this->assertEquals($time->get('0'), null);
        $this->assertEquals($time->get(''), null);
        $this->assertEquals($time->get('2022-01-18 11:24:32'), '11:24:32');
        $this->assertEquals($time->get('1642505072'), '11:24:32');
    }

    public function testGetMinutes()
    {
        $time = $this->app->time;

        $this->assertEquals($time->getMinutes(60), ['minutes' => 1, 'seconds' => 0]);
        $this->assertEquals($time->getMinutes(70), ['minutes' => 1, 'seconds' => 10]);
        $this->assertEquals($time->getMinutes(133), ['minutes' => 2, 'seconds' => 13]);
    }
}
