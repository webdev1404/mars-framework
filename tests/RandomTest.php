<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class RandomTest extends Base
{
    public function testGetString()
    {
        $string = $this->app->random->getString(20);
        $this->assertSame(strlen($string), 20);

        $string = $this->app->random->getString(32);
        $this->assertSame(strlen($string), 32);
    }

    public function testInt()
    {
        $random_int = $this->app->random->getInt(10, 100);
        $this->assertGreaterThanOrEqual(10, $random_int);
        $this->assertLessThanOrEqual(100, $random_int);
    }

    public function testGetFloat()
    {
        $random_float = $this->app->random->getFloat(1.5, 10.5);
        $this->assertGreaterThanOrEqual(1.5, $random_float);
        $this->assertLessThanOrEqual(10.5, $random_float);
    }
}


