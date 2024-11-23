<?php
/**
* The Module Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;
use Mars\Extensions\Abilities\LanguagesTrait;
use Mars\Extensions\Abilities\TemplatesTrait;

/**
 * The Module Class
 * Base class for all module extensions
 */
class Module extends Extension
{
    use LanguagesTrait;
    use TemplatesTrait;

    /**
     * @internal
     */
    protected static string $type = 'module';

    /**
     * @internal
     */
    protected static string $base_dir = 'modules';

    /**
     * @internal
     */
    protected static string $base_namespace = "Modules";

    /**
     * {@inheritdoc}
     * @see \Mars\Extensions\ExtensionTrait::getRootNamespace()
     */
    protected function getRootNamespace() : string
    {
        return '';
    }
}
