<?php
/**
 * The Theme's Images Links Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Links\Urls;
use Mars\Extensions\Theme;

/**
 * The Theme's Images Links Class
 * @package Mars
 */
class Images extends Url
{
    /**
     * @internal
     */
    protected Urls $url {
        get => $this->app->document->images;
    }

    /**
     * @internal
     */
    protected string $assets_dir {
        get => Theme::DIRS['images'];
    }
}