<?php
/**
 * The Theme's Javascript Links Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Links\Urls as DocumentUrls;
use Mars\Extensions\Themes\Theme;

/**
 * The Theme's Javascript Links Class
 * @package Mars
 */
class Javascript extends Urls
{
    /**
     * @internal
     */
    protected DocumentUrls $urls {
        get => $this->app->document->javascript;
    }

    /**
     * @internal
     */
    protected string $assets_dir {
        get => Theme::DIRS['js'];
    }
}
