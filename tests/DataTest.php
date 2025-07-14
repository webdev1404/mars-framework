<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class DataTest extends Base
{
    public function testHasProperty()
    {
        $data = ['name' => 'John'];
        $this->assertTrue($this->app->data->hasProperty($data, 'name'));
        $this->assertFalse($this->app->data->hasProperty($data, 'age'));

        $data = (object)['name' => 'John'];
        $this->assertTrue($this->app->data->hasProperty($data, 'name'));
        $this->assertFalse($this->app->data->hasProperty($data, 'age'));
    }

    public function testGetProperty()
    {
        $data = ['name' => 'John'];
        $this->assertEquals('John', $this->app->data->getProperty($data, 'name'));
        $this->assertNull($this->app->data->getProperty($data, 'age'));

        $data = (object)['name' => 'John'];
        $this->assertEquals('John', $this->app->data->getProperty($data, 'name'));
        $this->assertNull($this->app->data->getProperty($data, 'age'));
    }

    public function testGetProperties()
    {
        $data = ['name' => 'John', 'age' => 30];
        $result = $this->app->data->getProperties($data, ['name']);
        $this->assertEquals(['name' => 'John'], $result);

        $data = (object)['name' => 'John', 'age' => 30];
        $result = $this->app->data->getProperties($data, ['name']);
        $this->assertEquals(['name' => 'John'], $result);
    }

    public function testMap()
    {
        $array = [1, 2, 3];
        $result = $this->app->data->map($array, fn ($n) => $n * 2);
        $this->assertEquals([2, 4, 6], $result);

        $value = 2;
        $result = $this->app->data->map($value, fn ($n) => $n * 2);
        $this->assertEquals(4, $result);
    }
}
