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
        $uri = $this->app->uri;

        $this->assertTrue($uri->isUrl('http://www.google.com/'));
        $this->assertTrue($uri->isUrl('http://www.google.com/?v=1&q=test'));
        $this->assertTrue($uri->isUrl('https://www.google.com/'));
        $this->assertTrue($uri->isUrl('https://www.google.com/?v=1&q=test'));
        $this->assertFalse($uri->isUrl('://www.google.com/?v=1&q=test'));
        $this->assertFalse($uri->isUrl('www.google.com/?v=1&q=test'));
    }

    public function testIsLocal()
    {
        $uri = $this->app->uri;

        $this->assertTrue($uri->isLocal($this->app->base_url));
        $this->assertTrue($uri->isLocal($this->app->base_url . 'page.php'));
        $this->assertTrue($uri->isLocal($this->app->base_url . 'page.php?qqq=test'));
        $this->assertFalse($uri->isLocal('https://localhost/ma'));
        $this->assertFalse($uri->isLocal('http://www.google.com/'));
    }

    public function testGetFromLocalUrl()
    {
        $uri = $this->app->uri;

        $this->assertEquals($uri->getFromLocalUrl('https://google/mars/'), '');
        $this->assertEquals($uri->getFromLocalUrl($this->app->base_url . 'page.php'), $this->app->base_path . '/page.php');
        $this->assertEquals($uri->getFromLocalUrl($this->app->base_url . 'dir1/dir2/page.php'), $this->app->base_path . '/dir1/dir2/page.php');
    }

    public function testGetRoot()
    {
        $uri = $this->app->uri;

        $this->assertSame($uri->getRoot('https://google.com/'), 'https://google.com');
        $this->assertSame($uri->getRoot('https://www.google.com/'), 'https://www.google.com');
        $this->assertSame($uri->getRoot('https://google.com/mypath/script.php'), 'https://google.com');
        $this->assertSame($uri->getRoot('google.com'), '');
        $this->assertSame($uri->getRoot('google.com/'), '');
    }

    public function testGetHost()
    {
        $uri = $this->app->uri;

        $this->assertSame($uri->getHost('https://google.com/'), 'google.com');
        $this->assertSame($uri->getHost('https://www.google.com/'), 'www.google.com');
    }

    public function testGetDomain()
    {
        $uri = $this->app->uri;

        $this->assertSame($uri->getDomain('https://google.com/'), 'google.com');
        $this->assertSame($uri->getDomain('https://www.google.com/'), 'google.com');
        $this->assertSame($uri->getDomain('https://www.something.google.com/'), 'google.com');
    }

    public function testGetSubDomain()
    {
        $uri = $this->app->uri;

        $this->assertSame($uri->getSubDomain('https://google.com/'), '');
        $this->assertSame($uri->getSubDomain('https://www.google.com/'), 'www');
        $this->assertSame($uri->getSubDomain('https://www.something.google.com/'), 'www.something');
    }

    public function testGetPath()
    {
        $uri = $this->app->uri;

        $this->assertEquals($uri->getPath('https://www.google.com/'), '');
        $this->assertEquals($uri->getPath('https://www.google.com'), '');
        $this->assertEquals($uri->getPath('https://www.google.com/mypath/script.php'), '/mypath/script.php');
        $this->assertEquals($uri->getPath('https://www.google.com/mypath/script.php?var=1&var2=2'), '/mypath/script.php');
    }

    public function testBuild()
    {
        $uri = $this->app->uri;

        $this->assertEquals($uri->build('https://www.google.com/', []), 'https://www.google.com/');
        $this->assertEquals($uri->build('https://www.google.com/', ['v' => 1, 'q' => 'test']), 'https://www.google.com/?v=1&q=test');
        $this->assertEquals($uri->build('https://www.google.com/', ['v' => 1, 'q' => 'test', 'y' => '']), 'https://www.google.com/?v=1&q=test');
        $this->assertEquals($uri->build('https://www.google.com/', ['v' => 1, 'q' => 'test', 'y' => ''], false), 'https://www.google.com/?v=1&q=test&y=');
        $this->assertEquals($uri->build('https://www.google.com/page.php?x=123', ['v' => 1, 'q' => 'test']), 'https://www.google.com/page.php?x=123&v=1&q=test');
    }

    public function testBuildPath()
    {
        $uri = $this->app->uri;

        $this->assertEquals($uri->buildPath('https://localhost/mars', ['test1', 'test2']), 'https://localhost/mars/test1/test2');
        $this->assertEquals($uri->buildPath('https://localhost/mars/', ['test1', 'test2']), 'https://localhost/mars/test1/test2');
        $this->assertEquals($uri->buildPath('https://localhost/mars/', ['te st1', 'te?st2']), 'https://localhost/mars/te%20st1/te%3Fst2');
    }

    public function testIsInQuery()
    {
        $uri = $this->app->uri;

        $this->assertFalse($uri->isInQuery('https://localhost/mars/', 'test'));
        $this->assertFalse($uri->isInQuery('https://localhost/mars/?v=1&j=test', 'test'));
        $this->assertFalse($uri->isInQuery('https://localhost/mars/v=1&j=test', 'test'));
        $this->assertTrue($uri->isInQuery('https://localhost/mars/?v=1&test=test', 'test'));
        $this->assertTrue($uri->isInQuery('https://localhost/mars/?v=1&test=1', 'test'));
    }

    public function testGetWithoutQuery()
    {
        $uri = $this->app->uri;

        $this->assertEquals($uri->getWithoutQuery('https://localhost/mars/'), 'https://localhost/mars/');
        $this->assertEquals($uri->getWithoutQuery('https://localhost/mars/index.php'), 'https://localhost/mars/index.php');
        $this->assertEquals($uri->getWithoutQuery('https://localhost/mars/?query=1'), 'https://localhost/mars/');
        $this->assertEquals($uri->getWithoutQuery('https://localhost/mars/index.php?query=1'), 'https://localhost/mars/index.php');
    }

    public function testToHttp()
    {
        $uri = $this->app->uri;

        $this->assertEquals($uri->toHttp('https://localhost/mars'), 'http://localhost/mars');
        $this->assertEquals($uri->toHttp('http://localhost/mars'), 'http://localhost/mars');
        $this->assertEquals($uri->toHttp('localhost/mars'), 'http://localhost/mars');
    }

    public function testToHtts()
    {
        $uri = $this->app->uri;

        $this->assertEquals($uri->toHttps('https://localhost/mars'), 'https://localhost/mars');
        $this->assertEquals($uri->toHttps('http://localhost/mars'), 'https://localhost/mars');
        $this->assertEquals($uri->toHttps('localhost/mars'), 'https://localhost/mars');
    }

    public function testAddScheme()
    {
        $uri = $this->app->uri;

        $this->assertEquals($uri->addScheme('https://localhost/mars'), 'https://localhost/mars');
        $this->assertEquals($uri->addScheme('http://localhost/mars'), 'http://localhost/mars');
        $this->assertEquals($uri->addScheme('https://localhost/mars', 'http'), 'https://localhost/mars');
        $this->assertEquals($uri->addScheme('http://localhost/mars', 'https'), 'http://localhost/mars');
        $this->assertEquals($uri->addScheme('localhost/mars', 'https'), 'https://localhost/mars');
        $this->assertEquals($uri->addScheme('localhost/mars', 'https://'), 'https://localhost/mars');
        $this->assertEquals($uri->addScheme('localhost/mars', 'http'), 'http://localhost/mars');
        $this->assertEquals($uri->addScheme('localhost/mars', 'http://'), 'http://localhost/mars');
    }

    public function testRemoveScheme()
    {
        $uri = $this->app->uri;

        $this->assertEquals($uri->removeScheme('localhost/mars'), 'localhost/mars');
        $this->assertEquals($uri->removeScheme('http://localhost/mars'), 'localhost/mars');
        $this->assertEquals($uri->removeScheme('https://localhost/mars'), 'localhost/mars');
    }
}
