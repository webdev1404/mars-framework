<?php

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
 * @ignore
 */
final class ObjectTypeTest extends Base
{
    public function testGetProperties()
    {
        $object = (object)['name' => 'John', 'age' => 30];
        $result = $this->app->object->getProperties($object);
        $this->assertEquals(['name' => 'John', 'age' => 30], $result);
    }
}
