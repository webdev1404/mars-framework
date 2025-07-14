<?php
use Mars\App\Kernel;
use Mars\Data\ListTrait;

include_once(dirname(__DIR__) . '/Base.php');

class ElementsSimple
{
    use Kernel;
    use ListTrait;

    protected static string $property = 'list';
    protected array $list = [];
}

/**
 * @ignore
 */
final class ListTraitTest extends Base
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

    public function testAdd()
    {
        $elements = new ElementsSimple;

        $elements->add('test123');
        $this->assertSame($elements->get(), ['test123']);

        $elements->add(['test345', 'test678']);
        $this->assertSame($elements->get(), ['test123', 'test345', 'test678']);
    }

    public function testSet()
    {
        $elements = new ElementsSimple;

        $elements->set('test123');
        $this->assertSame($elements->get(), ['test123']);

        $elements->set(['test345', 'test678']);
        $this->assertSame($elements->get(), ['test345', 'test678']);
    }

    public function testGet()
    {
        $elements = new ElementsSimple;

        $this->assertSame($elements->get(), []);

        $elements->add('test123');
        $this->assertSame($elements->get(), ['test123']);

        $elements->add(['test345', 'test678']);
        $this->assertSame($elements->get(), ['test123', 'test345', 'test678']);
    }

    public function testGetFirst()
    {
        $elements = new ElementsSimple;

        $this->assertSame($elements->getFirst(), '');

        $elements->add(['test123', 'test345', 'test678']);
        $this->assertSame($elements->getFirst(), 'test123');
    }

    public function testGetLast()
    {
        $elements = new ElementsSimple;

        $this->assertSame($elements->getLast(), '');

        $elements->add(['test123', 'test345', 'test678']);
        $this->assertSame($elements->getLast(), 'test678');
    }

    public function testReset()
    {
        $elements = new ElementsSimple;

        $elements->add(['test123', 'test345', 'test678']);
        $elements->reset();
        $this->assertSame($elements->get(), []);
    }

    public function testRemove()
    {
        $elements = new ElementsSimple;

        $elements->add(['test123', 'test345', 'test678']);
        $elements->remove('test123');
        $this->assertSame($elements->get(), [1 => 'test345', 2 => 'test678']);

        $elements->remove(['test345', 'test678']);
        $this->assertSame($elements->get(), []);
    }

    public function testCount()
    {
        $elements = new ElementsSimple;

        $this->assertSame($elements->count(), 0);

        $elements->add('test123');
        $this->assertSame($elements->count(), 1);

        $elements->add(['test345', 'test678']);
        $this->assertSame($elements->count(), 3);

        $elements->remove('test123');
        $this->assertSame($elements->count(), 2);
    }

    public function testGetIterator()
    {
        $elements = new ElementsSimple;

        $elements->add(['test123', 'test345', 'test678']);
        $iterator = $elements->getIterator();

        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
        $this->assertSame(iterator_to_array($iterator), ['test123', 'test345', 'test678']);
    }
}
