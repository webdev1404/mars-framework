<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class TextTest extends Base
{
    public function testCut()
    {
        $this->assertSame($this->app->text->cut('abcdefgh'), 'abcdefgh');
        $this->assertSame($this->app->text->cut('abc<b>def</b>gh'), 'abcdefgh');
        $this->assertSame($this->app->text->cut('abcdefgh', 5), 'abcde...');
        $this->assertSame($this->app->text->cut('abc<b>def</b>gh', 5), 'abcde...');
    }

    public function testMiddle()
    {
        $this->assertSame($this->app->text->cutMiddle('abcdefgh'), 'abcdefgh');
        $this->assertSame($this->app->text->cutMiddle('abc<b>def</b>gh'), 'abcdefgh');
        $this->assertSame($this->app->text->cutMiddle('abcdefgh', 5), 'abc...gh');
        $this->assertSame($this->app->text->cutMiddle('abc<b>def</b>gh', 5), 'abc...gh');
    }
}
