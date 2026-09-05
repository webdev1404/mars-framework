<?php

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
 * @ignore
 */
final class ObjectTypeTest extends Base
{
    public function testGetVars()
    {
        $object = (object)['name' => 'John', 'age' => 30];
        $result = $this->app->object->getVars($object);
        $this->assertEquals(['name' => 'John', 'age' => 30], $result);
    }
}
