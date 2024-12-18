<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class EscapeTest extends Base
{
    public function testHtml()
    {
        $this->assertSame($this->app->escape->html('<b>test\' test " test<b>'), '&lt;b&gt;test&apos; test &quot; test&lt;b&gt;');
    }

    public function testHtmlx2()
    {
        $this->assertSame($this->app->escape->htmlx2("<b>test' test \" test<b>"), "&amp;lt;b&amp;gt;test&amp;apos; test &amp;quot; test&amp;lt;b&amp;gt;");
        $this->assertSame($this->app->escape->htmlx2("<b>test' test \"\n test<b>"), "&amp;lt;b&amp;gt;test&amp;apos; test &amp;quot;<br />\n test&amp;lt;b&amp;gt;");
        $this->assertSame($this->app->escape->htmlx2("<b>test' test \"\n test<b>", false), "&amp;lt;b&amp;gt;test&amp;apos; test &amp;quot;\n test&amp;lt;b&amp;gt;");
    }

    public function testJs()
    {
        $this->assertSame($this->app->escape->js("alert('test')"), 'alert(&apos;test&apos;)');
    }

    public function testJsString()
    {
        $this->assertSame($this->app->escape->jsString("joe's"), "joe\'s");
    }

    public function testPath()
    {
        $filename = $this->app->base_path . 'dir/somefile.txt';

        $this->assertSame($this->app->escape->path($filename), 'dir/somefile.txt');
    }
}
