<?php
use Mars\App\Kernel;
use Mars\Data\ListGroupTrait;

include_once(dirname(__DIR__) . '/Base.php');

class ElementsSet
{
    use Kernel;
    use ListGroupTrait;

    protected static string $property = 'list';
    protected array $list = [];
}

class ListGroupTraitTest extends Base
{
    public function testAddAndExists()
    {
        $elements = new ElementsSet;

        $elements->add('type1', 'value1');
        $this->assertTrue($elements->has('type1', 'value1'));
        $this->assertFalse($elements->has('type1', 'value2'));

        $elements->addMany('type1', ['value2', 'value3']);
        $this->assertTrue($elements->has('type1', 'value2'));
        $this->assertTrue($elements->has('type1', 'value3'));
    }

    public function testGet()
    {
        $elements = new ElementsSet;

        $elements->add('type1', 'value1');
        $elements->addMany('type2', ['value2', 'value3']);

        $this->assertEquals(['value1'], $elements->get('type1'));
        $this->assertEquals(['value2', 'value3'], $elements->get('type2'));
        $this->assertEquals(['type1' => ['value1'], 'type2' => ['value2', 'value3']], $elements->get());
    }

    public function testRemove()
    {
        $elements = new ElementsSet;

        $elements->addMany('type1', ['value1', 'value2']);
        $elements->remove('type1', 'value1');
        $this->assertFalse($elements->has('type1', 'value1'));
        $this->assertTrue($elements->has('type1','value2'));

        $elements->addMany('type2', ['value3', 'value4']);
        $elements->remove('type1', ['value2']);
        $elements->remove('type2', ['value3']);
        $this->assertFalse($elements->has('type1', 'value2'));
        $this->assertFalse($elements->has('type2', 'value3'));
        $this->assertTrue($elements->has('type2', 'value4'));
    }

    public function testCount()
    {
        $elements = new ElementsSet;

        $elements->add('type1', 'value1');
        $elements->addMany('type2', ['value2', 'value3']);
        $this->assertEquals(1, $elements->count('type1'));
        $this->assertEquals(2, $elements->count('type2'));
    }

    public function testGetIterator()
    {
        $elements = new ElementsSet;
        
        $elements->add('type1', 'value1');
        $elements->addMany('type2', ['value2', 'value3']);

        $iterator = $elements->getIterator();
        $this->assertInstanceOf(\RecursiveArrayIterator::class, $iterator);
        $this->assertEquals(['type1' => ['value1'], 'type2' => ['value2', 'value3']], iterator_to_array($iterator));
    }
}
