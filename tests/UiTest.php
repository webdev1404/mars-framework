<?php

use Mars\Ui\Pagination\Pagination;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class UiTest extends Base
{
    public function testPagination()
    {
        $_REQUEST[$this->app->config->request->page->param] = 1;

        $pag = new Pagination('https://www.mydomain.com/', 1000, false, '');
        $links = $pag->getLinks();

        $this->assertCount(12, $links);
    }
}
