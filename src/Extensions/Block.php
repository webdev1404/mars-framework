<?php
/**
* The Block Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\Content\ContentInterface;
use Mars\Extensions\Abilities\MvcTrait;

/**
 * The Block Class
 */
class Block extends SubModule implements ContentInterface
{
    use MvcTrait;

    /**
     * @internal
     */
    protected static string $type = 'block';

    /**
     * @internal
     */
    protected static string $base_dir = 'blocks';

    /**
     * @internal
     */
    protected static string $base_namespace = "Blocks";
}
