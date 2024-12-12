<?php
use Mars\Lists\ListByTypeTrait;

include_once(dirname(__DIR__) . '/Base.php');

class ListByTypeTraitTest extends TestCase
{
    use ListByTypeTrait;

    public function testAddAndExists()
    {
        $this->add('type1', 'value1');
        $this->assertTrue($this->exists('value1'));
        $this->assertFalse($this->exists('value2'));

        $this->add('type1', ['value2', 'value3']);
        $this->assertTrue($this->exists('value2'));
        $this->assertTrue($this->exists('value3'));
    }

    public function testGet()
    {
        $this->add('type1', 'value1');
        $this->add('type2', ['value2', 'value3']);

        $this->assertEquals(['value1'], $this->get('type1'));
        $this->assertEquals(['value2', 'value3'], $this->get('type2'));
        $this->assertEquals(['type1' => ['value1'], 'type2' => ['value2', 'value3']], $this->get());
    }

    public function testRemove()
    {
        $this->add('type1', ['value1', 'value2']);
        $this->remove('value1', 'type1');
        $this->assertFalse($this->exists('value1'));
        $this->assertTrue($this->exists('value2'));

        $this->add('type2', ['value3', 'value4']);
        $this->remove(['value2', 'value3']);
        $this->assertFalse($this->exists('value2'));
        $this->assertFalse($this->exists('value3'));
        $this->assertTrue($this->exists('value4'));
    }

    public function testCount()
    {
        $this->add('type1', 'value1');
        $this->add('type2', ['value2', 'value3']);
        $this->assertEquals(2, $this->count());
    }

    public function testGetIterator()
    {
        $this->add('type1', 'value1');
        $this->add('type2', ['value2', 'value3']);

        $iterator = $this->getIterator();
        $this->assertInstanceOf(\RecursiveArrayIterator::class, $iterator);
        $this->assertEquals(['type1' => ['value1'], 'type2' => ['value2', 'value3']], iterator_to_array($iterator));
    }
}