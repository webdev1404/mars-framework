<?php
use Mars\Data\AccessorTrait;

include_once(dirname(__DIR__) . '/Base.php');

#[\AllowDynamicProperties]
class AccessorTraitTest extends Base
{
    use AccessorTrait;

    public function testExists()
    {
        $this->assign(['property1' => 'value1', 'property2' => 'value2']);

        $this->assertTrue($this->exists('property1'));
        $this->assertFalse($this->exists('property3'));
    }

    public function testAssign()
    {
        $data = ['property1' => 'value1', 'property2' => 'value2'];
        $this->assign($data);

        $this->assertEquals('value1', $this->property1);
        $this->assertEquals('value2', $this->property2);
    }
}
