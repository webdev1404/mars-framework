<?php
use Mars\App;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class FilterTest extends Base
{
    public function testString()
    {
        $this->assertSame($this->app->filter->string(12), '12');
        $this->assertSame($this->app->filter->string('test'), 'test');
        $this->assertSame($this->app->filter->string([12, 'test']), ['12', 'test']);
    }

    public function testInt()
    {
        $this->assertSame($this->app->filter->int(12), 12);
        $this->assertSame($this->app->filter->int(12.78), 12);
        $this->assertSame($this->app->filter->int('12.78'), 12);
        $this->assertSame($this->app->filter->int([12, 5.67]), [12, 5]);
    }

    public function testFloat()
    {
        $this->assertSame($this->app->filter->float(12), 12.0);
        $this->assertSame($this->app->filter->float(12.67), 12.67);
        $this->assertSame($this->app->filter->float('12.78'), 12.78);
        $this->assertSame($this->app->filter->float([12, 5.67]), [12.0, 5.67]);
    }

    public function testAbs()
    {
        $this->assertSame($this->app->filter->abs(12), 12);
        $this->assertSame($this->app->filter->abs(-12), 12);
        $this->assertSame($this->app->filter->abs(12.67), 12.67);
        $this->assertSame($this->app->filter->abs(-12.67), 12.67);
    }

    public function testAbsInt()
    {
        $this->assertSame($this->app->filter->absint(12), 12);
        $this->assertSame($this->app->filter->absint(-12), 12);
        $this->assertSame($this->app->filter->absint([12, -5]), [12, 5]);
    }

    public function testAbsFloat()
    {
        $this->assertSame($this->app->filter->absfloat(12.67), 12.67);
        $this->assertSame($this->app->filter->absfloat(-12.67), 12.67);
        $this->assertSame($this->app->filter->absfloat([12.67, -5.67]), [12.67, 5.67]);
    }

    public function testTrim()
    {
        $this->assertSame($this->app->filter->trim('  test  '), 'test');
        $this->assertSame($this->app->filter->trim(['  test  ', '  test2  ']), ['test', 'test2']);
    }

    public function testTags()
    {
        $this->assertSame($this->app->filter->tags('<p>test</p>'), 'test');
        $this->assertSame($this->app->filter->tags('<p>test</p>', '<p>'), '<p>test</p>');
        $this->assertSame($this->app->filter->tags(['<p>test</p>', '<div>test2</div>'], '<p>'), ['<p>test</p>', 'test2']);
    }

    public function testFilename()
    {
        $this->assertEquals($this->app->filter->filename('some filename.jpg'), 'some-filename.jpg');
        $this->assertEquals($this->app->filter->filename('../../some filename.jpg'), 'some-filename.jpg');
        $this->assertEquals($this->app->filter->filename('/dir/sub dir/some filename.jpg'), 'some-filename.jpg');
    }

    public function testFilepath()
    {
        $filter = $this->app->filter;

        $this->assertEquals($this->app->filter->filepath('some filename.jpg'), 'some-filename.jpg');
        $this->assertEquals($this->app->filter->filepath('/dir/sub dir/some filename.jpg'), '/dir/sub dir/some-filename.jpg');
    }

    public function testEmail()
    {
        $filter = $this->app->filter;

        $this->assertEquals($this->app->filter->email('myaddress-123@something.com'), 'myaddress-123@something.com');
        $this->assertEquals($this->app->filter->email('myaddress 123@something.com'), 'myaddress123@something.com');
    }

    public function testAlpha()
    {
        $filter = $this->app->filter;

        $this->assertEquals($this->app->filter->alpha('some text# 12345 xyz'), 'sometextxyz');
        $this->assertEquals($this->app->filter->alpha('some text# 12345 xyz', true), 'some text  xyz');
    }

    public function testAlnum()
    {
        $filter = $this->app->filter;

        $this->assertEquals($this->app->filter->alnum('some text# 12345 xyz'), 'sometext12345xyz');
        $this->assertEquals($this->app->filter->alnum('some text# 12345 xyz', true), 'some text 12345 xyz');
    }

    public function testSlug()
    {
        $filter = $this->app->filter;

        $this->assertEquals($this->app->filter->slug('some url 12'), 'some-url-12');
        $this->assertEquals($this->app->filter->slug('some_url--12'), 'some-url-12');
        $this->assertEquals($this->app->filter->slug('some_url()12'), 'some-url12');
        $this->assertEquals($this->app->filter->slug('some_url/12'), 'some-url12');
        $this->assertEquals($this->app->filter->slug('some_url/12', true), 'some-url/12');
    }

    public function testHtml()
    {
        $filter = $this->app->filter;

        $this->assertEquals($this->app->filter->html('<a href="http://www.sss.com" data-attr="12345">xxxx</a>'), '<a href="http://www.sss.com">xxxx</a>');
        $this->assertEquals($this->app->filter->html('<a href="http://www.sss.com" data-attr="12345"><span>xxxx</span></a>', 'a'), '<a href="http://www.sss.com">xxxx</a>');
        $this->assertEquals($this->app->filter->html('<a href="http://www.sss.com" data-attr="12345"><span>xxxx</span></a>', 'a,span,img'), '<a href="http://www.sss.com"><span>xxxx</span></a>');
        $this->assertEquals($this->app->filter->html('<a href="http://www.sss.com" data-attr="12345"><span>xxxx</span></a>', null, 'a.href,img.src'), '<a href="http://www.sss.com"><span>xxxx</span></a>');
    }

    public function testInterval()
    {
        $filter = $this->app->filter;

        $this->assertEquals($this->app->filter->interval(12, 10, 20, 2), 12);
        $this->assertEquals($this->app->filter->interval(5, 10, 20, 2), 2);
        $this->assertEquals($this->app->filter->interval(25, 10, 20, 2), 2);
    }

    public function testRemove()
    {
        $filter = $this->app->filter;

        $this->assertEquals($this->app->filter->remove(['a', 'b', 'c', '12'], 12), ['a', 'b', 'c']);
        $this->assertEquals($this->app->filter->remove(['a', 'b', 'c', '12'], '12'), ['a', 'b', 'c']);
        $this->assertEquals($this->app->filter->remove(['a', 'b', 'c', '12'], [12, 'b', 'c']), ['a']);
        $this->assertEquals($this->app->filter->remove(['a', 'b', 'c', '12'], ['a', '12', 'b', 'c']), []);
    }

    public function testAllowed()
    {
        $filter = $this->app->filter;

        $this->assertEqualsCanonicalizing($this->app->filter->allowed(['a', 'b', 'd', 12, 13], ['a', 'b', 'c', '12']), ['a', 'b', 12]);
        $this->assertEqualsCanonicalizing($this->app->filter->allowed(['a', 'b', 'd', 12, 13], 'd'), ['d']);
        $this->assertEqualsCanonicalizing($this->app->filter->allowed(['a', 'b', 'd', 12, 13], 'z'), []);

        $this->assertEquals($this->app->filter->allowed('b', ['a', 'b', 'c']), 'b');
        $this->assertEquals($this->app->filter->allowed('b', 'b'), 'b');
        $this->assertEquals($this->app->filter->allowed('b', ['a'], 'c'), 'c');
    }
}
