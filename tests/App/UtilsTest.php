<?php

use Mars\App;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class AppUtilsTest extends Base
{
    protected array $data = ['foo' => 'bar', 'abc' => 'baz', 'def' => 'bay'];

    public function testGetClass()
    {
        $this->assertSame(App::getClass('my class'), 'MyClass');
        $this->assertSame(App::getClass('my-class'), 'MyClass');
    }

    public function testGetMethod()
    {
        $this->assertSame(App::getMethod('my method'), 'myMethod');
        $this->assertSame(App::getMethod('my-method'), 'myMethod');
        $this->assertSame(App::getMethod('my_method'), 'myMethod');
        $this->assertSame(App::getMethod('myMethod'), 'myMethod');
    }

    public function testHasProperty()
    {
        $data = ['name' => 'John'];
        $this->assertTrue(App::hasProperty($data, 'name'));
        $this->assertFalse(App::hasProperty($data, 'age'));

        $data = (object)['name' => 'John'];
        $this->assertTrue(App::hasProperty($data, 'name'));
        $this->assertFalse(App::hasProperty($data, 'age'));
    }

    public function testGetProperty()
    {
        $data = ['name' => 'John'];
        $this->assertEquals('John', App::getProperty($data, 'name'));
        $this->assertNull(App::getProperty($data, 'age'));

        $data = (object)['name' => 'John'];
        $this->assertEquals('John', App::getProperty($data, 'name'));
        $this->assertNull(App::getProperty($data, 'age'));
    }

    public function testGetProperties()
    {
        $data = ['name' => 'John', 'age' => 30];
        $result = App::getProperties($data, ['name']);
        $this->assertEquals(['name' => 'John'], $result);

        $data = (object)['name' => 'John', 'age' => 30];
        $result = App::getProperties($data, ['name']);
        $this->assertEquals(['name' => 'John'], $result);
    }

    public function testFilterProperties()
    {
        $data = ['name' => 'John', 'age' => 30];
        $result = App::filterProperties($data, ['age']);
        $this->assertEquals(['name' => 'John'], $result);
    }

    public function testGetObjectProperties()
    {
        $object = (object)['name' => 'John', 'age' => 30];
        $result = App::getObjectProperties($object);
        $this->assertEquals(['name' => 'John', 'age' => 30], $result);
    }

    public function testGetArray()
    {
        $array = ['name' => 'John'];
        $result = App::getArray($array);
        $this->assertEquals($array, $result);

        $object = (object)['name' => 'John'];
        $result = App::getArray($object);
        $this->assertEquals(['name' => 'John'], $result);
    }

    public function testMap()
    {
        $array = [1, 2, 3];
        $result = App::map($array, fn($n) => $n * 2);
        $this->assertEquals([2, 4, 6], $result);

        $value = 2;
        $result = App::map($value, fn($n) => $n * 2);
        $this->assertEquals(4, $result);
    }

    public function testUnset()
    {
        $array = ['name' => 'John', 'age' => 30];
        $result = App::unset($array, 'age');
        $this->assertEquals(['name' => 'John'], $result);
    }

    public function testRemove()
    {
        $array = ['John', 'Doe'];
        $result = App::remove($array, 'Doe');
        $this->assertEquals(['John'], $result);
    }
}
