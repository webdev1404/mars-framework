<?php
/**
 * The Theme's Javascript Links Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Link\Links as DocumentLinks;
use Mars\Extensions\Themes\Theme;

/**
 * The Theme's Javascript Links Class
 * @package Mars
 */
class Javascript extends Links
{
    /**
     * @internal
     */
    public DocumentLinks $urls {
        get => $this->app->document->js;
    }

    /**
     * @internal
     */
    protected string $assets_dir {
        get => Theme::DIRS['js'];
    }
}
