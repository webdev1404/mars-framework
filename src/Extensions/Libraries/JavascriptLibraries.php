<?php
/**
* The Javascript Libraries Class
* @package Mars
*/

namespace Mars\Extensions\Libraries;

/**
 * The Javascript Libraries Class
 * Handles Javascript libraries
 */
class JavascriptLibraries extends Libraries
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
    protected static string $instance_class = JavascriptLibrary::class;
}
