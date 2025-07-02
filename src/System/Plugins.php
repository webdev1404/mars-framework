<?php
/**
* The Plugins Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\App\Kernel;
use Mars\Extensions\Modules\Plugin;

/**
 * The Plugins Class
 * Class implementing the Plugins functionality
 */
class Plugins
{
    use Kernel;

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
                return [];
            }

            $this->plugins = [];

            $plugins = Plugin::getList();
            foreach ($plugins as $class_name => $module_name) {
                $plugin = new $class_name($module_name, [], $this->app);

                if (!$plugin instanceof Plugin) {
                    throw new \Exception("Plugin {$class_name} must extend class Plugin");
                }

                $this->plugins[$class_name] = $plugin;
            }

            return $this->plugins;
        }
    }

    /**
     * @var bool $debug If true, we'll collect debug data 
     */
    public bool $debug{
        get => $this->app->config->debug_plugins;
    }

    /**
     * @var array $hooks Registered hooks
     */
    public protected(set) array $hooks = [];

    /**
     * @var array $hooks_exec_time The execution time for all hooks. Set only if debug is enabled
     */
    public protected(set) array $hooks_exec_time = [];

    /**
     * @var array $exec_time The execution time for all plugins. Set only if debug is enabled
     */
    public protected(set) array $exec_time = [];

    /**
     * @var array $total_time The total execution time. Set only if debug is enabled
     */
    public protected(set) float $total_time = 0;

    /**
     * Registers hooks for execution
     * @param Plugin $plugin The plugin executing the hook
     * @param array $hooks The list of hooks the plugin will be attached to
     * @return $this
     */
    public function addHooks(Plugin $plugin, array $hooks) : static
    {
        if (!$this->enabled) {
            return $this;
        }

        $hooks = (array)$hooks;
        foreach ($hooks as $hook) {
            if (is_string($hook)) {
                $name = $hook;
                $method = $hook;
                $priority = 100;
            } elseif (is_array($hook)) {
                $name = $hook['name'];
                $method = $hook['method'] ?? $name;
                $priority = $hook['priority'] ?? 100;
            }

            $this->hooks[$name][] = [$plugin::class, $method, $priority];
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

        //sort the hooks by priority
        $hooks_array = $this->hooks[$hook];
        usort($hooks_array, function($a, $b) {
            return $a[2] <=> $b[2];
        });

        $return_value = null;
        
        foreach ($hooks_array as $hook_data) {
            if ($this->debug) {
                $this->startTimer();
            }

            $class_name = $hook_data[0];
            $plugin = $this->plugins[$class_name] ?? null;
            $method = $hook_data[1];

            if (!$plugin) {
                throw new \Exception("Plugin {$class_name} not found on the list of loaded plugins");
            }


            $plugin_return_value = call_user_func_array([$plugin, $method], $args);

            if ($plugin_return_value !== null) {
                if (isset($args[0])) {
                    $args[0] = $plugin_return_value;
                }

                $return_value = $plugin_return_value;
            }

            if ($this->debug) {
                $this->endTimer($class_name, $hook);
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
        $time = $this->app->timer->stop('plugin_run');

        $this->total_time+= $time;

        $this->exec_time[$name] = $this->exec_time[$name] ?? 0;
        $this->hooks_exec_time[$hook] = $this->hooks_exec_time[$hook] ?? 0;

        $this->exec_time[$name]+= $time;
        $this->hooks_exec_time[$hook] += $time;
    }
}
