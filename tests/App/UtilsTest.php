<?php

use Mars\App;

include_once(dirname(__DIR__) . '/Base.php');

class TestObj
{
    public function __construct(App $app)
    {
    }
}

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
        $this->assertTrue(App::hasProperty($this->data, 'abc'));
        $this->assertFalse(App::hasProperty($this->data, 'abcde'));

        $obj = (object)$this->data;
        $this->assertTrue(App::hasProperty($obj, 'abc'));
        $this->assertFalse(App::hasProperty($obj, 'abcde'));
    }

    public function testGetProperty()
    {
        $this->assertSame(App::getProperty($this->data, 'abc'), 'baz');
        $this->assertNull(App::getProperty($this->data, 'abcde'));

        $obj = (object)$this->data;
        $this->assertSame(App::getProperty($obj, 'abc'), 'baz');
        $this->assertNull(App::getProperty($obj, 'abcde'));
    }

    public function testGetProperties()
    {
        $this->assertSame(App::getProperties($this->data), $this->data);
        $this->assertSame(App::getProperties((object)$this->data), $this->data);

        $this->assertSame(App::getProperties($this->data, ['foo', 'abc']), ['foo' => 'bar', 'abc' => 'baz']);
        $this->assertSame(App::getProperties((object)$this->data, ['foo', 'abc']), ['foo' => 'bar', 'abc' => 'baz']);
    }

    public function testFilterProperties()
    {
        $this->assertSame(App::filterProperties($this->data, ['def']), ['foo' => 'bar', 'abc' => 'baz']);
        $this->assertSame(App::filterProperties($this->data, ['foo', 'abc']), ['def' => 'bay']);

        $this->assertSame(App::filterProperties((object)$this->data, ['def']), ['foo' => 'bar', 'abc' => 'baz']);
        $this->assertSame(App::filterProperties((object)$this->data, ['foo', 'abc']), ['def' => 'bay']);
    }

    public function testgetArray()
    {
        $this->assertIsArray(App::getArray(null));
        $this->assertIsArray(App::getArray(12));
        $this->assertIsArray(App::getArray('my string'));
        $this->assertIsArray(App::getArray(['my string']));
    }

    public function testgetObject()
    {
        $obj = App::getObject(TestObj::class);
        $this->assertInstanceOf(TestObj::class, $obj);

        $obj = App::getObject(function(){
            return new TestObj(App::get());
        });
        $this->assertInstanceOf(TestObj::class, $obj);        
    }

    public function testUnset()
    {
        $this->assertSame(App::unset(['a' => 1, 'b' => 2, 'c' => 3], ['a', 'b']), ['c' => 3]);
        $this->assertSame(App::unset(['a' => 1, 'b' => 2, 'c' => 3], 'a'), ['b' => 2, 'c' => 3]);
    }

    public function testremove()
    {
        $this->assertSame(App::remove(['a', 'b', 'c'], ['a', 'b']), ['c']);
        $this->assertSame(App::remove(['a', 'b', 'c'], 'c'), ['a', 'b']);
    }
}
