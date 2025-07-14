<?php

use Mars\App;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class AppTest extends Base
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
}
