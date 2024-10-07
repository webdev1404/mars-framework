<?php

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class DateTest extends Base
{
    public function testGet()
    {
        $date = $this->app->date;

        $this->assertEquals($date->get('0'), '0000-00-00');
        $this->assertEquals($date->get(''), '0000-00-00');
        $this->assertEquals($date->get('2022-01-18 11:24:32'), '2022-01-18');
        $this->assertEquals($date->get('1642505072'), '2022-01-18');
    }
}
