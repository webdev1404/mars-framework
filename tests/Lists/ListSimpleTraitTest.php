<?php

include_once(dirname(__DIR__) . '/Base.php');

class ElementsSimple
{
    use \Mars\Lists\ListTrait;
}

/**
 * @ignore
 */
final class ElementsSimpleTest extends Base
{
    public function testHeaders()
    {
        $elements = new ElementsSimple;

        $this->assertSame($elements->get(), []);

        $elements->add('test123');
        $elements->add(['test345', 'test678']);

        $this->assertTrue($elements->exists('test123'));
        $this->assertTrue($elements->exists('test345'));
        $this->assertFalse($elements->exists('test'));

        $elements->remove('test123');
        $this->assertFalse($elements->exists('test123'));
    }
}
