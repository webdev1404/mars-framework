<?php

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class DateTimeTest extends Base
{
    public function testGet()
    {
        $datetime = $this->app->datetime;

        $this->assertEquals($datetime->get('0'), null);
        $this->assertEquals($datetime->get(''), null);
        $this->assertEquals($datetime->get('2022-01-18 11:24:32'), '2022-01-18 11:24:32');
        $this->assertEquals($datetime->get('1642505072'), '2022-01-18 11:24:32');
    }

    public function testAdd()
    {
        $datetime = $this->app->datetime;

        $this->assertEquals($datetime->add(1, 'dayxx', '2022-01-18 11:24:32'), $datetime->get('2022-01-18 11:24:32'));
        $this->assertEquals($datetime->add(1, 'day', '2022-01-18 11:24:32'), $datetime->get('2022-01-19 11:24:32'));
        $this->assertNotEquals($datetime->add(1, 'day', '2022-01-18 11:24:32'), $datetime->get('2022-01-10 11:24:32'));
        $this->assertEquals($datetime->add(10, 'days', '2022-01-18 11:24:32'), $datetime->get('2022-01-28 11:24:32'));
        $this->assertEquals($datetime->add(1, 'month', '2022-01-18 11:24:32'), $datetime->get('2022-02-18 11:24:32'));
        $this->assertEquals($datetime->add(10, 'months', '2022-01-18 11:24:32'), $datetime->get('2022-11-18 11:24:32'));
        $this->assertEquals($datetime->add(1, 'year', '2022-01-18 11:24:32'), $datetime->get('2023-01-18 11:24:32'));
        $this->assertEquals($datetime->add(10, 'years', '2022-01-18 11:24:32'), $datetime->get('2032-01-18 11:24:32'));
    }

    public function testSub()
    {
        $datetime = $this->app->datetime;

        $this->assertEquals($datetime->sub(1, 'dayxx', '2022-01-18 11:24:32'), $datetime->get('2022-01-18 11:24:32'));
        $this->assertEquals($datetime->sub(1, 'day', '2022-01-18 11:24:32'), $datetime->get('2022-01-17 11:24:32'));
        $this->assertNotEquals($datetime->sub(1, 'day', '2022-01-18 11:24:32'), $datetime->get('2022-01-10 11:24:32'));
        $this->assertEquals($datetime->sub(10, 'days', '2022-01-18 11:24:32'), $datetime->get('2022-01-08 11:24:32'));
        $this->assertEquals($datetime->sub(1, 'month', '2022-01-18 11:24:32'), $datetime->get('2021-12-18 11:24:32'));
        $this->assertEquals($datetime->sub(10, 'months', '2022-01-18 11:24:32'), $datetime->get('2021-03-18 11:24:32'));
        $this->assertEquals($datetime->sub(1, 'year', '2022-01-18 11:24:32'), $datetime->get('2021-01-18 11:24:32'));
        $this->assertEquals($datetime->sub(10, 'years', '2022-01-18 11:24:32'), $datetime->get('2012-01-18 11:24:32'));
    }
}
