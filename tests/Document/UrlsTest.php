<?php

use Mars\Document\Css;
use Mars\Document\Javascript;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class UrlsTest extends Base
{
    public function setUp() : void
    {
        parent::setUp();

        $this->app->config->css_version = '123';
    }

    public function testCss()
    {
        $this->app->document->css->load('https://mydomain/css/style-1.css', 'head', 100);
        $this->app->document->css->load('https://mydomain/css/style-2.css', 'head', 200);

        $this->assertSame($this->app->document->css->get('head'), [
            'https://mydomain/css/style-2.css' => ['url' => 'https://mydomain/css/style-2.css', 'priority' => 200, 'attributes' => [], 'is_local' => false],
            'https://mydomain/css/style-1.css' => ['url' => 'https://mydomain/css/style-1.css', 'priority' => 100, 'attributes' => [], 'is_local' => false],
        ]);

        $this->assertSame($this->app->document->css->get('invalidlocation'), []);
    }

    public function testJavascript()
    {
        $this->app->document->javascript->load('https://mydomain/js/script-1.css', 'head', 100);
        $this->app->document->javascript->load('https://mydomain/js/script-2.css', 'head', 200);

        $this->assertSame($this->app->document->javascript->get('head'), [
            'https://mydomain/js/script-2.css' => ['url' => 'https://mydomain/js/script-2.css', 'priority' => 200, 'attributes' => [], 'is_local' => false],
            'https://mydomain/js/script-1.css' => ['url' => 'https://mydomain/js/script-1.css', 'priority' => 100, 'attributes' => [], 'is_local' => false],
        ]);

        $this->assertSame($this->app->document->javascript->get('invalidlocation'), []);
    }
}
