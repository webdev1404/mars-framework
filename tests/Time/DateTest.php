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

        $this->assertEquals($date->get('0'), null);
        $this->assertEquals($date->get(''), null);
        $this->assertEquals($date->get('2022-01-18 11:24:32'), '01/18/2022');
        $this->assertEquals($date->get('1642505072'), '01/18/2022');
    }
}
