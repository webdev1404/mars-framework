<?php

use Mars\Ui\Pagination;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class UiTest extends Base
{
    public function testPagination()
    {
        $_REQUEST[$this->app->config->request->page->param] = 1;

        $pag = new Pagination('https://www.mydomain.com/', 10, 1000, 10, $this->app);
        $links = $pag->getLinks();

        $this->assertCount(14, $links);
    }
}
