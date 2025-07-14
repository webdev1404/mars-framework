<?php

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
 * @ignore
 */
final class StringTypeTest extends Base
{
    public function testGet()
    {
        $this->assertSame('foo', $this->app->string->get('foo'));
        $this->assertSame('123', $this->app->string->get(123));
        $this->assertSame('bar', $this->app->string->get(['bar', 'baz']));
        $this->assertSame('', $this->app->string->get([]));
    }
}
