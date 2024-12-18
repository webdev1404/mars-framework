<?php

include_once(__DIR__ . '/Base.php');

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

    public function testResponseType()
    {
        $this->app->response->type = 'ajax';
        $this->assertSame($this->app->response->type, 'ajax');

        $this->app->response->type = 'json';
        $this->assertSame($this->app->response->type, 'ajax');

        $this->app->response->type = 'html';
        $this->assertSame($this->app->response->type, 'html');
    }

    public function testGetResponse()
    {
        $content = '<p>Test HTML Content</p>';
        $this->assertSame($this->app->response->get($content), $content);

        $this->app->response->type = 'ajax';
        $content = ['status' => 'success', 'message' => 'Test AJAX Content'];
        $this->assertSame($this->app->response->get($content), json_encode($content));
    }

    public function testOutputResponse()
    {
        $content = '<p>Test HTML Content</p>';
        ob_start();
        $this->app->response->output($content);
        $output = ob_get_clean();
        $this->assertSame($output, $content);

        $this->app->response->type = 'ajax';
        $content = ['status' => 'success', 'message' => 'Test AJAX Content'];
        ob_start();
        $this->app->response->output(json_encode($content));
        $output = ob_get_clean();
        $this->assertSame($output, json_encode($content));
    }
}


