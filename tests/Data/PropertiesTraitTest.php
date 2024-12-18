<?php
use Mars\Data\PropertiesTrait;

include_once(dirname(__DIR__) . '/Base.php');

#[\AllowDynamicProperties]
class PropertiesTraitTest extends Base
{
    use PropertiesTrait;

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
