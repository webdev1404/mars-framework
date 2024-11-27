<?php
/**
* The Plugins Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Extensions\Module;
use Mars\Extensions\Plugin;

/**
 * The Plugins Class
 * Class implementing the Plugins functionality
 */
class Plugins
{
    use InstanceTrait;

    /**
     * @var bool $enabled True, if plugins are enabled
     */
    public bool $enabled {
        get {
            if (isset($this->enabled)) {
                return $this->enabled;
            }
            
            $this->enabled = $this->app->config->plugins_enable;

            return $this->enabled;
        }
    }

    /**
     * @var array $plugins Array holding the plugin objects
     */
    public protected(set) array $plugins {
        get {            
            if (isset($this->plugins)) {
                return $this->plugins;
            }
            if (!$this->enabled) {
                return $this->plugins;
            }

            $this->plugins = [];

            $modules_namespace = '\\' . Module::getBaseNamespace() . '\\';

            $plugins = $this->app->config->plugins ?? [];                
            foreach ($plugins as $class_name) {                
                if (!str_starts_with($class_name, $modules_namespace)) {
                    throw new \Exception("Plugin {$class_name} must belong to the {$modules_namespace} namespace");
                }

                $plugin = new $class_name($this->app);
                if (!$plugin instanceof Plugin) {
                    throw new \Exception("Plugin {$class_name} must extend class Plugin");
                }

                $this->plugins[$class_name] = $plugin;
            }

            return $this->plugins;
        }
    }

    /**
     * @var array $hooks Registered hooks
     */
    public protected(set) array $hooks = [];

    /**
     * @var array $hooks_exec Executed hooks. Set only if debug is enabled
     */
    public protected(set) array $hooks_exec = [];

    /**
     * @var array $hooks_exec_time The execution time for all hooks is stored here. Set only if debug is enabled
     */
    public protected(set) array $hooks_exec_time = [];

    /**
     * @var array $exec_time The execution time for all plugins is stored here. Set only if debug is enabled
     */
    public protected(set) array $exec_time = [];

    /**
     * Registers hooks for execution
     * @param Plugin $plugin The plugin executing the hook
     * @param string|array $hooks The names of the hooks at which the plugin will be attached
     * @return $this
     */
    public function addHooks(Plugin $plugin, string|array $hooks) : static
    {
        if (!$this->enabled) {
            return $this;
        }

        $hooks = (array)$hooks;
        foreach ($hooks as $hook => $method) {
            $this->hooks[$hook][] = [$plugin, $method];
        }

        return $this;
    }

    /**
     * Runs a hooks
     * @param string $hook The name of the hook
     * @param mixed $args The arguments to be passed to the plugins. The arguments are passed by reference
     * @return mixed The value returned by the plugin
     */
    public function run(string $hook, &...$args)
    {    
        if (!$this->enabled || !$this->plugins || !isset($this->hooks[$hook])) {
            return $args[0] ?? null;
        }

        if ($this->app->config->debug) {
            $this->hooks_exec[] = $hook;
        }

        $return_value = null;

        foreach ($this->hooks[$hook] as $hook_data) {
            if ($this->app->config->debug) {
                $this->startTimer();
            }

            $plugin_return_value = call_user_func_array($hook_data, $args);

            if ($plugin_return_value !== null) {
                if (isset($args[0])) {
                    $args[0] = $plugin_return_value;
                }

                $return_value = $plugin_return_value;
            }

            if ($this->app->config->debug) {
                $this->endTimer($hook_data[0]->name, $hook);
            }
        }

        return $return_value;
    }

    /**
     * Filters a value, by running the hooks. Unlike run(), the args are not passed by reference
     * @param string $hook The name of the hook
     * @param mixed $args The arguments to be passed to the plugins
     * @return mixed The filtered value
     */
    public function filter(string $hook, &...$args)
    {
        return $this->run($hook, ...$args);
    }

    /**
     * Starts the timer, if debug is on
     */
    protected function startTimer()
    {
        $this->app->timer->start('plugin_run');
    }

    /**
     * Ends the timer and stores the elapsed time in exec_time, if debug is on
     * @param string $name The plugin's name
     * @param string $hook The hook's name
     */
    protected function endTimer(string $name, string $hook)
    {
        $time = $this->app->timer->end('plugin_run');

        $this->exec_time[$name] = $this->exec_time[$name] ?? 0;
        $this->hooks_exec_time[$hook] = $this->hooks_exec_time[$hook] ?? 0;

        $this->exec_time[$name]+= $time;
        $this->hooks_exec_time[$hook] += $time;
    }
}
