<?php
use Mars\Data\SetTrait;

include_once(dirname(__DIR__) . '/Base.php');

class ElementsSet
{
    use SetTrait;

    protected static string $property = 'list';
    protected array $list = [];
}

class SetTraitTest extends Base
{
    public function testAddAndExists()
    {
        $elements = new ElementsSet;

        $elements->add('type1', 'value1');
        $this->assertTrue($elements->exists('value1'));
        $this->assertFalse($elements->exists('value2'));

        $elements->add('type1', ['value2', 'value3']);
        $this->assertTrue($elements->exists('value2'));
        $this->assertTrue($elements->exists('value3'));
    }

    public function testGet()
    {
        $elements = new ElementsSet;

        $elements->add('type1', 'value1');
        $elements->add('type2', ['value2', 'value3']);

        $this->assertEquals(['value1'], $elements->get('type1'));
        $this->assertEquals(['value2', 'value3'], $elements->get('type2'));
        $this->assertEquals(['type1' => ['value1'], 'type2' => ['value2', 'value3']], $elements->get());
    }

    public function testRemove()
    {
        $elements = new ElementsSet;

        $elements->add('type1', ['value1', 'value2']);
        $elements->remove('value1', 'type1');
        $this->assertFalse($elements->exists('value1'));
        $this->assertTrue($elements->exists('value2'));

        $elements->add('type2', ['value3', 'value4']);
        $elements->remove(['value2', 'value3']);
        $this->assertFalse($elements->exists('value2'));
        $this->assertFalse($elements->exists('value3'));
        $this->assertTrue($elements->exists('value4'));
    }

    public function testCount()
    {
        $elements = new ElementsSet;

        $elements->add('type1', 'value1');
        $elements->add('type2', ['value2', 'value3']);
        $this->assertEquals(3, $elements->count());
    }

    public function testGetIterator()
    {
        $elements = new ElementsSet;
        
        $elements->add('type1', 'value1');
        $elements->add('type2', ['value2', 'value3']);

        $iterator = $elements->getIterator();
        $this->assertInstanceOf(\RecursiveArrayIterator::class, $iterator);
        $this->assertEquals(['type1' => ['value1'], 'type2' => ['value2', 'value3']], iterator_to_array($iterator));
    }
}