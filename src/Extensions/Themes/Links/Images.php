<?php
/**
 * The Theme's Images Links Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Links\Urls as DocumentUrls;
use Mars\Extensions\Themes\Theme;

/**
 * The Theme's Images Links Class
 * @package Mars
 */
class Images extends Urls
{
    /**
     * @internal
     */
    protected DocumentUrls $urls {
        get => $this->app->document->images;
    }

    /**
     * @internal
     */
    protected string $assets_dir {
        get => Theme::DIRS['images'];
    }
}
