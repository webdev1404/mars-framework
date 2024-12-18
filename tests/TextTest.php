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

    public function testParse()
    {
        $this->assertSame($this->app->text->parse('some text without links'), 'some text without links');
        $this->assertSame($this->app->text->parse('some text with a link: https://www.mydomain.com'), 'some text with a link: <a href="https://www.mydomain.com">https://www.mydomain.com</a>');
        $this->assertSame($this->app->text->parse('some text with a link: https://www.mydomain.com and another link: https://somedomain.com'), 'some text with a link: <a href="https://www.mydomain.com">https://www.mydomain.com</a> and another link: <a href="https://somedomain.com">https://somedomain.com</a>');

        $this->assertSame($this->app->text->parse('some text without links', true, true), 'some text without links');
        $this->assertSame($this->app->text->parse('some text with a link: https://www.mydomain.com', true, true), 'some text with a link: <a href="https://www.mydomain.com" rel="nofollow">https://www.mydomain.com</a>');
        $this->assertSame($this->app->text->parse('some text with a link: https://www.mydomain.com and another link: https://somedomain.com', true, true), 'some text with a link: <a href="https://www.mydomain.com" rel="nofollow">https://www.mydomain.com</a> and another link: <a href="https://somedomain.com" rel="nofollow">https://somedomain.com</a>');
    }
}
