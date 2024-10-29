<?php

use Mars\Extensions\Module;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class ModuleTest extends Base
{
    public function testConstruct()
    {
        $module = new Module('foo');

        $this->assertSame($module->path, $this->app->base_path . '/extensions/modules/foo');
        $this->assertSame($module->url, $this->app->base_url . '/extensions/modules/foo');
        $this->assertSame($module->url_static, $this->app->base_url_static . '/extensions/modules/foo');
    }
}
