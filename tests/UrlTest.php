<?php
use Mars\App;
use Mars\Url;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class UrlTest extends Base
{
    public function testToString()
    {
        $url = new Url('https://example.com/test/path?foo=bar#frag');
        $this->assertEquals('https://example.com/test/path?foo=bar#frag', (string)$url);
    }

    public function testProperties()
    {
        $url = new Url('https://sub.example.com:8080/test/path?foo=bar#frag');
        $this->assertTrue($url->is_valid);
        $this->assertFalse($url->is_local);
        $this->assertEquals('https://', $url->scheme);
        $this->assertEquals('sub.example.com', $url->host);
        $this->assertEquals('8080', $url->port);
        $this->assertEquals('https://sub.example.com:8080', $url->root);
        $this->assertEquals('example.com', $url->domain);
        $this->assertEquals('sub', $url->subdomain);
        $this->assertEquals('test/path', $url->path);
        $this->assertEquals('https://sub.example.com:8080/test/path', $url->path_name);
        $this->assertEquals('foo=bar', $url->query);
        $this->assertEquals('frag', $url->fragment);

        $url = new Url('https://example.com/test?foo=bar');
        $this->assertTrue($url->is_valid);
        $this->assertFalse($url->is_local);
        $this->assertEquals('https://', $url->scheme);
        $this->assertEquals('example.com', $url->host);
        $this->assertEquals('', $url->port);
        $this->assertEquals('https://example.com', $url->root);
        $this->assertEquals('example.com', $url->domain);
        $this->assertEquals('', $url->subdomain);
        $this->assertEquals('test', $url->path);
        $this->assertEquals('https://example.com/test', $url->path_name);
        $this->assertEquals('foo=bar', $url->query);
        $this->assertEquals('', $url->fragment);

        // Test with no scheme (should not be valid)
        $url = new Url('example.com/path');
        $this->assertFalse($url->is_valid);
        
        // Test with only domain
        $url = new Url('https://example.com');
        $this->assertTrue($url->is_valid);
        $this->assertEquals('https://', $url->scheme);
        $this->assertEquals('example.com', $url->host);
        $this->assertEquals('', $url->port);
        $this->assertEquals('https://example.com', $url->root);
        $this->assertEquals('example.com', $url->domain);
        $this->assertEquals('', $url->subdomain);
        $this->assertEquals('', $url->path);
        $this->assertEquals('https://example.com/', $url->path_name);
        $this->assertEquals('', $url->query);
        $this->assertEquals('', $url->fragment);
    }

    public function testContains()
    {
        $url = new Url('https://example.com/path?foo=bar&baz=qux');
        $this->assertTrue($url->contains('foo'));
        $this->assertTrue($url->contains('baz'));
        $this->assertFalse($url->contains('notfound'));
    }

    public function testBuildAndAdd()
    {
        $url = new Url('https://example.com/path');
        $newUrl = $url->build(['more', 'parts']);
        $this->assertEquals('https://example.com/path/more/parts', (string)$newUrl);

        $url2 = new Url('https://example.com/path');
        $newUrl2 = $url2->add(['foo' => 'bar', 'baz' => 'qux']);
        $this->assertEquals('https://example.com/path?foo=bar&baz=qux', (string)$newUrl2);

        $url3 = new Url('https://example.com/path?x=1');
        $newUrl3 = $url3->add(['y' => '2']);
        $this->assertEquals('https://example.com/path?x=1&y=2', (string)$newUrl3);
    }

    public function testNormalize()
    {
        $url = new Url('/relative/path', false);
        $normalized = $url->normalize('https://example.com');
        $this->assertEquals('https://example.com/relative/path', (string)$normalized);

        $url2 = new Url('https://example.com/absolute/path');
        $normalized2 = $url2->normalize('https://base.com');
        $this->assertEquals('https://example.com/absolute/path', (string)$normalized2);
    }
}
