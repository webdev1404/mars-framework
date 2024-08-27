<?php
/**
* The Block Trait
* @package Mars
*/

namespace Mars\Extensions;

/**
 * The Block Trait
 * Trait implementing the Block functionality
 */
trait BlockTrait
{
    use isInsideAModuleTrait;
    use \Mars\Extensions\Abilities\MvcTrait;
    use \Mars\Extensions\Abilities\LanguagesTrait;
    use \Mars\Extensions\Abilities\TemplatesTrait;

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
    protected static string $namespace = "Blocks";
}
