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
     * @const array DIRS The locations of the used extensions subdirs
     */
    public const array DIRS = [
        'assets' => 'assets',
        'bin' => 'bin',
        'config' => 'config',
        'controllers' => 'Controllers',
        'languages' => 'languages',
        'pages' => 'pages',
        'plugins' => 'plugins',
        'models' => 'Models',
        'routes' => 'routes',
        'templates' => 'templates',
        'src' => 'src',
        'setup' => 'Setup',
        'views' => 'Views'
    ];

    /**
     * @const array CACHE_DIRS The dirs to be cached
     */
    public const array CACHE_DIRS = ['config', 'languages', 'templates'];

    /**
     * @var array $route_params The route params passed to the module, if any
     */
    public protected(set) array $route_params = [];

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
        $this->route_params = $params;

        $this->app->lang->saveLocalKeys();
        
        parent::output();

        $this->app->lang->restoreLocalKeys();

        //unload the loaded language files to save memory
        $this->unloadLanguages();
    }

    /**
     * Loads and executes the module controller
     */
    public function execute()
    {
        [$controller, $method] = $this->getAction();
        if (!$controller) {
            throw new \Exception("No controller defined for module {$this->name}");
        }

        $controller = $this->getController($controller);
        $controller->dispatch($method, $this->route_params);
    }

    /**
     * Gets the action from the params
     * @return array An array with the controller name and method to be called
     */
    protected function getAction() : array
    {
        $action = $this->params['action'] ?? '';
        if (!$action) {
            return [null, null];
        }

        if (is_array($action)) {
            //convert the action array keys to lowercase, in case the request method was provided in uppercase
            $action = array_combine(array_map('strtolower', array_keys($action)), array_values($action));
            $method = $this->app->request->method;
            
            $my_action = $action[$method] ?? null;
            if ($my_action) {
                return $this->getControllerAndMethod($my_action);
            }
            //fallback to 'get' method, if an action for the current method is not defined
            if ($method != 'get') {
                $my_action = $action['get'] ?? null;
                if ($my_action) {
                    return $this->getControllerAndMethod($my_action);
                }
            }

        } else {
            return $this->getControllerAndMethod($action);
        }

        return [null, null];
    }

    /**
     * Gets the controller and method to be executed
     * @param string $action The action string
     * @return array An array with the controller name and method to be called
     */
    protected function getControllerAndMethod(string $action) : array
    {
        $parts = explode('@', $action);
        $controller = $parts[0] ?? null;
        $method = $parts[1] ?? null;

        return [$controller, $method];
    }
}
