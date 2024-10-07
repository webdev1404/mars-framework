<?php

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class TimestampTest extends Base
{
    public function testGetTimestamp()
    {
        $timestamp = $this->app->timestamp;

        $this->assertEquals($timestamp->get(0), 0);
        $this->assertEquals($timestamp->get(''), 0);
        $this->assertEquals($timestamp->get('1642505072'), 1642505072);
        $this->assertEquals($timestamp->get('2022-01-18 13:25:39'), 1642512339);
    }
}
