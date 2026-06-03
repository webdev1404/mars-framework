<?php
/**
 * The Theme's Images Links Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Link\Links as DocumentLinks;
use Mars\Extensions\Themes\Theme;

/**
 * The Theme's Images Links Class
 * @package Mars
 */
class Images extends Links
{
    /**
     * @internal
     */
    public DocumentLinks $urls {
        get => $this->app->document->images;
    }

    /**
     * @internal
     */
    protected string $assets_dir {
        get => Theme::DIRS['images'];
    }
}
