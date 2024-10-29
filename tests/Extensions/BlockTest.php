<?php

use Mars\Extensions\Block;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class BlockTest extends Base
{
    public function testConstruct()
    {
        $block = new Block('foo/bar');

        $this->assertSame($block->path, $this->app->base_path . '/extensions/modules/foo/blocks/bar');
        $this->assertSame($block->url, $this->app->base_url . '/extensions/modules/foo/blocks/bar');
        $this->assertSame($block->url_static, $this->app->base_url_static . '/extensions/modules/foo/blocks/bar');
    }
}
