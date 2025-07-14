<?php

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
 * @ignore
 */
final class ArrayTypeTest extends Base
{
    public function testGet()
    {
        $array = ['name' => 'John'];
        $result = $this->app->array->get($array);
        $this->assertEquals($array, $result);

        $object = (object)['name' => 'John'];
        $result = $this->app->array->get($object);
        $this->assertEquals(['name' => 'John'], $result);
    }

    public function testFlip()
    {
        $array = ['a', 'b', 'c'];
        $result = $this->app->array->flip($array);
        $this->assertEquals(['a' => true, 'b' => true, 'c' => true], $result);

        $result = $this->app->array->flip($array, 123);
        $this->assertEquals(['a' => 123, 'b' => 123, 'c' => 123], $result);

        $result = $this->app->array->flip([], 'x');
        $this->assertEquals([], $result);
    }

    public function testUnset()
    {
        $array = ['name' => 'John', 'age' => 30];
        $result = $this->app->array->unset($array, 'age');
        $this->assertEquals(['name' => 'John'], $result);
    }

    public function testRemove()
    {
        $array = ['John', 'Doe'];
        $result = $this->app->array->remove($array, 'Doe');
        $this->assertEquals(['John'], $result);
    }

}
