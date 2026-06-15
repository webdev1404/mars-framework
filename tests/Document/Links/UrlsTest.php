<?php

use Mars\Document\Links\Css;
use Mars\Document\Links\Javascript;

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
 * @ignore
 */
final class UrlsTest extends Base
{
    public function setUp() : void
    {
        parent::setUp();
    }

    public function testCss()
    {
        $this->app->document->css->add('https://mydomain/css/style-1.css', 'head', 100);
        $this->app->document->css->add('https://mydomain/css/style-2.css', 'head', 200);

        $urls = $this->app->document->css->get('head');

        $this->assertSame($urls->urls[0]->url, 'https://mydomain/css/style-2.css');
        $this->assertSame($urls->urls[0]->is_local, false);
        $this->assertSame($urls->urls[0]->priority, 200);

        $this->assertSame($urls->urls[1]->url, 'https://mydomain/css/style-1.css');
        $this->assertSame($urls->urls[1]->is_local, false);
        $this->assertSame($urls->urls[1]->priority, 100);

        $this->assertSame($this->app->document->css->get('invalidlocation'), null);
    }

    public function testJs()
    {
        $this->app->document->js->add('https://mydomain/js/script-1.js', 'head', 100);
        $this->app->document->js->add('https://mydomain/js/script-2.js', 'head', 200);

        $urls = $this->app->document->js->get('head');

        $this->assertSame($urls->urls[0]->url, 'https://mydomain/js/script-2.js');
        $this->assertSame($urls->urls[0]->is_local, false);
        $this->assertSame($urls->urls[0]->priority, 200);

        $this->assertSame($urls->urls[1]->url, 'https://mydomain/js/script-1.js');
        $this->assertSame($urls->urls[1]->is_local, false);
        $this->assertSame($urls->urls[1]->priority, 100);

        $this->assertSame($this->app->document->js->get('invalidlocation'), null);
    }
}
