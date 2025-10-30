<?php
/**
* The Block Class
* @package Mars
*/

namespace Mars\Extensions\Modules;

use Mars\Content\ContentInterface;
use Mars\Extensions\Modules\Abilities\MvcTrait;
use Mars\Extensions\Extensions;

/**
 * The Block Class
 */
class Block extends Component implements ContentInterface
{
    use MvcTrait;

    /**
     * @internal
     */
    public const array DIRS = [
        'languages' => 'languages',
        'templates' => 'templates',
        'controllers' => 'controllers',
        'models' => 'models',
        'views' => 'views'
    ];

    /**
     * @internal
     */
    public ?Extensions $manager {
        get => null;
    }

    /**
     * @internal
     */
    public protected(set) bool $enabled {
        get => true;
    }

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
    protected static string $base_namespace = "\\Blocks";

}
