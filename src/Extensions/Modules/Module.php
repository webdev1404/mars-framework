<?php
/**
* The Module Class
* @package Mars
*/

namespace Mars\Extensions\Modules;

use Mars\App;
use Mars\Content\ContentInterface;
use Mars\Extensions\Extension;
use Mars\Extensions\Extensions;
use Mars\Extensions\Abilities\FilesCacheTrait;
use Mars\Extensions\Modules\Abilities\MvcTrait;
use Mars\Extensions\Modules\Abilities\ConfigTrait;
use Mars\Extensions\Modules\Abilities\LanguagesTrait;
use Mars\Extensions\Modules\Abilities\TemplatesTrait;

/**
 * The Module Class
 * Base class for all module extensions
 */
class Module extends Extension implements ContentInterface
{
    use FilesCacheTrait;
    use MvcTrait;
    use ConfigTrait;
    use LanguagesTrait;
    use TemplatesTrait;

    /**
     * @const array DIR The locations of the used extensions subdirs
     */
    public const array DIRS = [
        'assets' => 'assets',
        'bin' => 'bin',
        'blocks' => 'blocks',
        'config' => 'config',
        'controllers' => 'controllers',
        'languages' => 'languages',
        'pages' => 'pages',
        'plugins' => 'plugins',
        'models' => 'models',
        'routes' => 'routes',
        'templates' => 'templates',
        'setup' => 'setup',
        'views' => 'views'
    ];

    /**
     * @const array CACHE_DIRS The dirs to be cached
     */
    public const array CACHE_DIRS = ['config', 'languages', 'templates'];

    /**
     * @internal
     */
    public ?Extensions $manager {
        get => $this->app->modules;
    }

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
    protected static string $base_namespace = "\\Modules";

    /**
     * Runs the module and outputs the generated content
     * @param array $params The params of the route
     */
    public function output(array $params = [])
    {
        $this->app->lang->saveLocalKeys();
        
        parent::output();

        $this->app->lang->restoreLocalKeys();

        //unload the loaded language files to save memory
        $this->unloadLanguages();
    }

    /**
     * Loads and executes the block controller
     */
    public function execute()
    {
        [$controller, $method] = $this->getAction();

        $controller = $this->getController($controller);
        $controller->dispatch($method);
    }

    /**
     * Gets the action from the params
     * @return array An array with the controller name and method to be called
     */
    protected function getAction() : array
    {
        $controller = '';
        $method = '';

        $action = $this->params['action'] ?? '';
        if ($action) {
            $parts = explode('@', $action);
            $controller = $parts[0] ?? '';
            $method = $parts[1] ?? '';
        }

        return [$controller, $method];
    }
}
