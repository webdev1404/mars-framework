<?php
/**
* The Css Libraries Class
* @package Mars
*/

namespace Mars\Extensions\Libraries;

/**
 * The Css Libraries Class
 * Handles CSS libraries
 */
class CssLibraries extends Libraries
{
    /**
     * @internal
     */
    protected static bool $list_use_all = true;

    /**
     * @internal
     */
    protected static ?array $list_all = null;

    /**
     * @internal
     */
    protected static string $instance_class = CssLibrary::class;
}
