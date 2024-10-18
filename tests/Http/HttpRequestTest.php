<?php

use Mars\Http\Request;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class HttpRequestTest extends Base
{
    protected $url = '';

    public function setUp() : void
    {
        parent::setUp();

        $this->url = $this->app->url . '/vendor/webdev1404/mars/tests/data/request-test.php';
    }

    public function testError()
    {
        $req = $this->app->http->request;
        $response = $req->get($this->app->url . '/vendor/webdev1404/mars/tests/data/request-test123.php');

        $this->assertSame($response->code, 404);
        $this->assertFalse($response->ok());
    }

    public function testGet()
    {
        $req = $this->app->http->request;
        $response = $req->get($this->url);

        $this->assertSame($response->body, 'test12345');
        $this->assertSame($response->code, 200);
        $this->assertTrue($response->ok());
    }

    public function testPost()
    {
        $req = $this->app->http->request;
        $response = $req->post($this->url, ['foo' => 'bar', 'faz' => 'baz']);

        $this->assertSame($response->body, '{"foo":"bar","faz":"baz"}');
        $this->assertSame($response->code, 200);
        $this->assertTrue($response->ok());
        $this->assertSame($response->getJson(), ['foo' => 'bar', 'faz' => 'baz']);
    }

    public function testGetFile()
    {
        $url = $this->app->url . '/vendor/webdev1404/mars/tests/data/sample.txt';
        $filename = $this->app->path . '/vendor/webdev1404/mars/tests/data/http-data/sample.txt';

        $req = $this->app->http->request;
        $response = $req->getFile($url, $filename);

        $this->assertSame($response->code, 200);
        $this->assertTrue($response->ok());
        $this->assertTrue(is_file($filename));
        $this->assertSame(file_get_contents($filename), 'test123456');

        unlink($filename);
    }
}
