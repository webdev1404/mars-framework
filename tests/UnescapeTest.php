<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class UnescapeTest extends Base
{
    public function testHtml()
    {
        $this->assertSame($this->app->unescape->html('&lt;b&gt;test&apos; test &quot; test&lt;b&gt;'), '<b>test\' test " test<b>');
    }
}
