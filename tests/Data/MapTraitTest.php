<?php
use Mars\Data\MapTrait;

include_once(dirname(__DIR__) . '/Base.php');

class Elements
{
    use MapTrait;

    protected static string $property = 'list';
    protected array $list = [];
}

/**
 * @ignore
 */
final class MapTraitTest extends Base
{
    public function testHeaders()
    {
        $elements = new Elements;

        $this->assertSame($elements->get(), []);
        $this->assertNull($elements->get('X-Test-Header'));

        $elements->add('X-Test-Header', 'test123');
        $elements->add('X-Test-Header2', 'test345');
        $this->assertSame($elements->get('X-Test-Header'), 'test123');
        $this->assertSame($elements->get(), ['X-Test-Header' => 'test123', 'X-Test-Header2' => 'test345']);

        $elements->remove('X-Test-Header');
        $this->assertNull($elements->get('X-Test-Header'));
    }

    public function testExists()
    {
        $elements = new Elements;

        $this->assertFalse($elements->exists('X-Test-Header'));

        $elements->add('X-Test-Header', 'test123');
        $this->assertTrue($elements->exists('X-Test-Header'));

        $elements->remove('X-Test-Header');
        $this->assertFalse($elements->exists('X-Test-Header'));
    }

    public function testSet()
    {
        $elements = new Elements;

        $elements->set(['X-Test-Header' => 'test123', 'X-Test-Header2' => 'test345']);
        $this->assertSame($elements->get(), ['X-Test-Header' => 'test123', 'X-Test-Header2' => 'test345']);
    }
}
