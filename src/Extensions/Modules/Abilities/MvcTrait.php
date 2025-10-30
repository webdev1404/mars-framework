<?php
/**
* The Extension's MVC Trait
* @package Venus
*/

namespace Mars\Extensions\Modules\Abilities;

use Mars\App;
use Mars\Mvc\Controller;
use Mars\Mvc\Model;
use Mars\Mvc\View;

/**
 * The Extension's MVC Trait
 * Trait implementing the MVC patter for extensions
 */
trait MvcTrait
{
    /**
     * @var Controller $controller The currently loaded controller of this extension
     */
    public Controller $controller;

    /**
     * Returns a MVC class name
     * @param string $dir The dir from where to load the class
     * @param string $class_name The class name
     * @return string The class name
     */
    protected function getMvcClass(string $dir, string $class_name) : string
    {
        $namespace_path = str_replace("/", "\\", ucfirst($dir) . '/');

        return $namespace_path . App::getClass($class_name);
    }

    /**
     * Loads the controller and returns the instance
     * @param string $controller The name of the controller
     * @param array $allowed_controllers Array with the allowed controler names
     * @return Controller The controller object
     */
    public function getController(string $controller = '', array $allowed_controllers = []) : Controller
    {
        if ($allowed_controllers) {
            if (!in_array($controller, $allowed_controllers)) {
                $controller = '';
            }
        }

        if (!$controller) {
            $controller = $this->name;
        }

        $controller_class = $this->getMvcClass(static::DIRS['controllers'], $controller);

        $class_name = $this->namespace . '\\' . $controller_class;

        $controller = new $class_name($this, $this->app);

        $controller = $this->app->plugins->filter('get_controller', $controller, $class_name, $this);

        return $controller;
    }

    /**
     * Loads the model and returns the instance
     * @param string $model The name of the model
     * @param Controller|null $controller The controller the model belongs to, if any
     * @return object The model
     */
    public function getModel(string $model = '', ?Controller $controller = null) : object
    {
        if (!$model) {
            $model = $this->name;
        }

        $model_class = $this->getMvcClass(static::DIRS['models'], $model);

        $class_name = $this->namespace . '\\' . $model_class;

        $model = new $class_name($this->app, $controller);

        $model = $this->app->plugins->filter('get_model', $model, $class_name, $this);

        return $model;
    }

    /**
     * Loads the view and returns the instance
     * @param string $view The name of the view
     * @param Controller|null $controller The controller the view belongs to, if any
     * @return View The view
     */
    public function getView(string $view = '', ?Controller $controller = null) : View
    {
        if (!$view) {
            $view = $this->name;
        }

        $view_class = $this->getMvcClass(static::DIRS['views'], $view);

        $class_name = $this->namespace . '\\' . $view_class;

        $view = new $class_name($this->app, $controller);

        $view = $this->app->plugins->filter('get_view', $view, $class_name, $this);

        return $view;
    }
}
