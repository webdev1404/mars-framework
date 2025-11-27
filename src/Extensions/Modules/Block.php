<?php
/**
* The Block Class
* @package Mars
*/

namespace Mars\Extensions\Modules;

use Mars\Content\ContentInterface;
use Mars\Extensions\Extensions;
use Mars\Extensions\Abilities\FilesCacheTrait;
use Mars\Extensions\Modules\Abilities\LanguagesTrait;
use Mars\Extensions\Modules\Abilities\MvcTrait;
use Mars\Extensions\Modules\Abilities\TemplatesTrait;

/**
 * The Block Class
 */
class Block extends Component implements ContentInterface
{    
    use FilesCacheTrait;
    use LanguagesTrait {
        LanguagesTrait::loadLanguage as loadLanguageFromTrait;
    }
    use MvcTrait;
    use TemplatesTrait;

    /**
     * @internal
     */
    public const array DIRS = [
        'config' => 'config',
        'controllers' => 'controllers',
        'languages' => 'languages',
        'models' => 'models',
        'templates' => 'templates',
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
    public bool $enabled {
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

    /**
     * @var array $route_params The route params passed to the block, if any
     */
    public protected(set) array $params_route = [];

    /**
     * Runs the extension and outputs the generated content
     * @param array $params The params of the route
     */
    public function output(array $params = [])
    {
        $this->params_route = $params;

        parent::output();
    }

    /**
     * Loads and executes the block controller
     */
    public function execute()
    {
        $controller = $this->getController();
        $controller->dispatch();
    }

    /**
     * @see \Mars\Extensions\Modules\Abilities\ConfigTrait::loadConfig()
     * {@inheritdoc}
     */
    public function loadConfig(?string $file = null)
    {
        $file ??= $this->name . '.php';

        $filename = $this->path . '/' . static::DIRS['config'] . '/' . $file;

        $this->app->config->loadFilename($filename);
    }
}
