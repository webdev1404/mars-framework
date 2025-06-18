<?php
use Mars\App;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class UriTest extends Base
{
    public function testIsUrl()
    {
        $this->assertTrue($this->app->uri->isUrl('http://www.google.com/'));
        $this->assertTrue($this->app->uri->isUrl('http://www.google.com/?v=1&q=test'));
        $this->assertTrue($this->app->uri->isUrl('https://www.google.com/'));
        $this->assertTrue($this->app->uri->isUrl('https://www.google.com/?v=1&q=test'));
        $this->assertFalse($this->app->uri->isUrl('://www.google.com/?v=1&q=test'));
        $this->assertFalse($this->app->uri->isUrl('www.google.com/?v=1&q=test'));
    }

    public function testIsLocal()
    {
        $this->assertTrue($this->app->uri->isLocal($this->app->base_url));
        $this->assertTrue($this->app->uri->isLocal($this->app->base_url . 'page.php'));
        $this->assertTrue($this->app->uri->isLocal($this->app->base_url . 'page.php?qqq=test'));
        $this->assertFalse($this->app->uri->isLocal('https://localhost/ma'));
        $this->assertFalse($this->app->uri->isLocal('http://www.google.com/'));
    }

    public function testGetFromLocalUrl()
    {
        $this->assertEquals($this->app->uri->getFromLocalUrl('https://google/mars/'), '');
        $this->assertEquals($this->app->uri->getFromLocalUrl($this->app->base_url . 'page.php'), $this->app->base_path . '/page.php');
        $this->assertEquals($this->app->uri->getFromLocalUrl($this->app->base_url . 'dir1/dir2/page.php'), $this->app->base_path . '/dir1/dir2/page.php');
    }

    public function testGetRoot()
    {
        $this->assertSame($this->app->uri->getRoot('https://google.com/'), 'https://google.com');
        $this->assertSame($this->app->uri->getRoot('https://www.google.com/'), 'https://www.google.com');
        $this->assertSame($this->app->uri->getRoot('https://google.com/mypath/script.php'), 'https://google.com');
        $this->assertSame($this->app->uri->getRoot('google.com'), '');
        $this->assertSame($this->app->uri->getRoot('google.com/'), '');
    }

    public function testGetHost()
    {
        $this->assertSame($this->app->uri->getHost('https://google.com/'), 'google.com');
        $this->assertSame($this->app->uri->getHost('https://www.google.com/'), 'www.google.com');
    }

    public function testGetDomain()
    {
        $this->assertSame($this->app->uri->getDomain('https://google.com/'), 'google.com');
        $this->assertSame($this->app->uri->getDomain('https://www.google.com/'), 'google.com');
        $this->assertSame($this->app->uri->getDomain('https://www.something.google.com/'), 'google.com');
    }

    public function testGetSubDomain()
    {
        $this->assertSame($this->app->uri->getSubDomain('https://google.com/'), '');
        $this->assertSame($this->app->uri->getSubDomain('https://www.google.com/'), 'www');
        $this->assertSame($this->app->uri->getSubDomain('https://www.something.google.com/'), 'www.something');
    }

    public function testGetPath()
    {
        $this->assertEquals($this->app->uri->getPath('https://www.google.com/'), '');
        $this->assertEquals($this->app->uri->getPath('https://www.google.com'), '');
        $this->assertEquals($this->app->uri->getPath('https://www.google.com/mypath/script.php'), '/mypath/script.php');
        $this->assertEquals($this->app->uri->getPath('https://www.google.com/mypath/script.php?var=1&var2=2'), '/mypath/script.php');
    }

    public function testGetWithoutQuery()
    {
        $this->assertEquals($this->app->uri->getWithoutQuery('https://localhost/mars/'), 'https://localhost/mars/');
        $this->assertEquals($this->app->uri->getWithoutQuery('https://localhost/mars/index.php'), 'https://localhost/mars/index.php');
        $this->assertEquals($this->app->uri->getWithoutQuery('https://localhost/mars/?query=1'), 'https://localhost/mars/');
        $this->assertEquals($this->app->uri->getWithoutQuery('https://localhost/mars/index.php?query=1'), 'https://localhost/mars/index.php');
    }

    public function testIsInQuery()
    {
        $this->assertFalse($this->app->uri->isInQuery('https://localhost/mars/', 'test'));
        $this->assertFalse($this->app->uri->isInQuery('https://localhost/mars/?v=1&j=test', 'test'));
        $this->assertFalse($this->app->uri->isInQuery('https://localhost/mars/v=1&j=test', 'test'));
        $this->assertTrue($this->app->uri->isInQuery('https://localhost/mars/?v=1&test=test', 'test'));
        $this->assertTrue($this->app->uri->isInQuery('https://localhost/mars/?v=1&test=1', 'test'));
    }

    public function testBuild()
    {
        $this->assertEquals($this->app->uri->build('https://www.google.com/', []), 'https://www.google.com/');
        $this->assertEquals($this->app->uri->build('https://www.google.com/', ['v' => 1, 'q' => 'test']), 'https://www.google.com/?v=1&q=test');
        $this->assertEquals($this->app->uri->build('https://www.google.com/', ['v' => 1, 'q' => 'test', 'y' => '']), 'https://www.google.com/?v=1&q=test');
        $this->assertEquals($this->app->uri->build('https://www.google.com/', ['v' => 1, 'q' => 'test', 'y' => ''], false), 'https://www.google.com/?v=1&q=test&y=');
        $this->assertEquals($this->app->uri->build('https://www.google.com/page.php?x=123', ['v' => 1, 'q' => 'test']), 'https://www.google.com/page.php?x=123&v=1&q=test');
    }

    public function testBuildPath()
    {
        $this->assertEquals($this->app->uri->buildPath('https://localhost/mars', ['test1', 'test2']), 'https://localhost/mars/test1/test2');
        $this->assertEquals($this->app->uri->buildPath('https://localhost/mars', ['te st1', 'te?st2']), 'https://localhost/mars/te%20st1/te%3Fst2');
    }

    public function testNormalizeUrl()
    {
        $this->assertEquals($this->app->uri->normalizeUrl('https://localhost/mars', 'https://localhost'), 'https://localhost/mars');
        $this->assertEquals($this->app->uri->normalizeUrl('http://localhost/mars', 'https://localhost'), 'http://localhost/mars');
        $this->assertEquals($this->app->uri->normalizeUrl('/mars', 'https://localhost'), 'https://localhost/mars');
        $this->assertEquals($this->app->uri->normalizeUrl('mars', 'https://localhost'), 'https://localhost/mars');
        $this->assertEquals($this->app->uri->normalizeUrl('https://localhost/mars', 'http://localhost'), 'https://localhost/mars');
    }

    public function testToHttp()
    {
        $this->assertEquals($this->app->uri->toHttp('https://localhost/mars'), 'http://localhost/mars');
        $this->assertEquals($this->app->uri->toHttp('http://localhost/mars'), 'http://localhost/mars');
        $this->assertEquals($this->app->uri->toHttp('localhost/mars'), 'http://localhost/mars');
    }

    public function testToHtts()
    {
        $this->assertEquals($this->app->uri->toHttps('https://localhost/mars'), 'https://localhost/mars');
        $this->assertEquals($this->app->uri->toHttps('http://localhost/mars'), 'https://localhost/mars');
        $this->assertEquals($this->app->uri->toHttps('localhost/mars'), 'https://localhost/mars');
    }

    public function testAddScheme()
    {
        $this->assertEquals($this->app->uri->addScheme('https://localhost/mars'), 'https://localhost/mars');
        $this->assertEquals($this->app->uri->addScheme('http://localhost/mars'), 'http://localhost/mars');
        $this->assertEquals($this->app->uri->addScheme('https://localhost/mars', 'http'), 'https://localhost/mars');
        $this->assertEquals($this->app->uri->addScheme('http://localhost/mars', 'https'), 'http://localhost/mars');
        $this->assertEquals($this->app->uri->addScheme('localhost/mars', 'https'), 'https://localhost/mars');
        $this->assertEquals($this->app->uri->addScheme('localhost/mars', 'https://'), 'https://localhost/mars');
        $this->assertEquals($this->app->uri->addScheme('localhost/mars', 'http'), 'http://localhost/mars');
        $this->assertEquals($this->app->uri->addScheme('localhost/mars', 'http://'), 'http://localhost/mars');
    }

    public function testRemoveScheme()
    {
        $this->assertEquals($this->app->uri->removeScheme('localhost/mars'), 'localhost/mars');
        $this->assertEquals($this->app->uri->removeScheme('http://localhost/mars'), 'localhost/mars');
        $this->assertEquals($this->app->uri->removeScheme('https://localhost/mars'), 'localhost/mars');
    }
}
