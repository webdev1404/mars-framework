<?php

include_once(dirname(__DIR__) . '/Base.php');

use Mars\Http\Response\Body\Data\Html;
use Mars\Http\Response\Body\Data\Json;

/**
 * @ignore
 */
final class ResponseTest extends Base
{
    public function testHeaders()
    {
        $this->assertSame($this->app->response->headers->get(), []);
        $this->assertNull($this->app->response->headers->get('X-Test-Header'));

        $this->app->response->headers->add('X-Test-Header', 'test123');
        $this->app->response->headers->add('X-Test-Header2', 'test345');
        $this->assertSame($this->app->response->headers->get('X-Test-Header'), 'test123');
        $this->assertSame($this->app->response->headers->get(), ['X-Test-Header' => 'test123', 'X-Test-Header2' => 'test345']);

        $this->app->response->headers->remove('X-Test-Header');
        $this->assertNull($this->app->response->headers->get('X-Test-Header'));
    }

    public function testOutputResponse()
    {
        $content = '<p>Test HTML Content</p>';
        $html = new Html('<p>Test HTML Content</p>');

        ob_start();
        $this->app->response->send($html);
        $output = ob_get_clean();

        $this->assertSame($content, $output);

        $content = ['status' => 'success', 'message' => 'Test AJAX Content'];
        $expected = [
            'success' => true,
            'data' => $content,
        ];

        $json = new Json($content);

        ob_start();
        $this->app->response->send($json);
        $output = ob_get_clean();

        $this->assertSame($output, json_encode($expected));
    }
}
