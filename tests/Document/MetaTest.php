<?php

use Mars\Document\Meta;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class MetaTest extends Base
{
    public function testMeta()
    {
        $this->app->document->meta->add('author', 'John Doe');
        $this->app->document->meta->add('keywords', 'k1, k2');

        $this->expectOutputString(
            '<meta name="author" content="John Doe">' . "\n" .
            '<meta name="keywords" content="k1, k2">' . "\n"
        );
        $this->app->document->meta->output();
    }
}
