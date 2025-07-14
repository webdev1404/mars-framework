<?php
/**
 * The Theme's Javascript Links Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Links\Urls;
use Mars\Extensions\Theme;

/**
 * The Theme's Javascript Links Class
 * @package Mars
 */
class Javascript extends Url
{
    /**
     * @internal
     */
    protected Urls $url {
        get => $this->app->document->javascript;
    }

    /**
     * @internal
     */
    protected string $assets_dir {
        get => Theme::DIRS['js'];
    }
}
