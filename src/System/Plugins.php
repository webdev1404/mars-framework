<?php
/**
* The Plugins Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\Extensions\Plugin;
use Mars\Extensions\Plugins as BasePlugins;

/**
 * The Plugins Class
 * Class implementing the Plugins functionality
 */
class Plugins extends BasePlugins
{
    /**
     * @var bool $enabled True, if plugins are enabled
     */
    public bool $enabled {
        get {
            if (isset($this->enabled)) {
                return $this->enabled;
            }
            
            $this->enabled = $this->app->config->plugins->enable;

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

            $files = $this->getFiles();
            foreach ($files as $name => $namespaces) {
                foreach ($namespaces as $class_name) {
                    $plugin = new $class_name($name, [], $this->app);
                    if (!$plugin instanceof Plugin) {
                        throw new \Exception("Plugin class {$class_name} does not extend the Plugin class");
                    }

                    $this->plugins[] = $plugin;
                }
            }

            return $this->plugins;
        }
    }

    /**
     * @var array $hooks Registered hooks
     */
    public protected(set) array $hooks = [];

    /**
     * @var array $hooks_sort The hooks which need to be sorted by priority
     */
    protected array $hooks_sort = [];

    /**
     * @var array $hooks_exec_time The execution time for all hooks. Set only if debug is enabled
     */
    public protected(set) array $hooks_exec_time = [];

    /**
     * @var array $exec_time The execution time for all plugins. Set only if debug is enabled
     */
    public protected(set) array $exec_time = [];

    /**
     * @var float $total_time The total execution time. Set only if debug is enabled
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

        foreach ($hooks as $name => $hook) {
            if (is_string($hook)) {
                $method = $hook;
                $priority = 100;
            } elseif (is_array($hook)) {
                $method = $hook['method'] ?? $name;
                $priority = $hook['priority'] ?? 100;
            }

            if ($priority !== 100) {
                $this->hooks_sort[$name] = true;
            }

            $this->hooks[$name][] = ['plugin' => $plugin, 'method' => $method, 'priority' => $priority];
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

        $hooks_array = $this->hooks[$hook];

        if (isset($this->hooks_sort[$hook])) {
            //sort the hooks by priority, if we have a hook with non-default priority
            usort($hooks_array, function ($a, $b) {
                return $a['priority'] <=> $b['priority'];
            });
        }

        $return_value = null;
        
        foreach ($hooks_array as $hook_data) {
            if ($this->app->config->debug->enable) {
                $this->startTimer();
            }

            ['plugin' => $plugin, 'method' => $method] = $hook_data;

            $plugin_return_value = call_user_func_array([$plugin, $method], $args);

            if ($plugin_return_value !== null) {
                if (isset($args[0])) {
                    $args[0] = $plugin_return_value;
                }

                $return_value = $plugin_return_value;
            }

            if ($this->app->config->debug->enable) {
                $this->endTimer($plugin::class, $hook);
            }
        }

        return $return_value;
    }

    /**
     * Filters a value, by running the hooks. Unlike run(), the args are not passed by reference
     * @param string $hook The name of the hook
     * @param mixed $value The value to be filtered
     * @param mixed $args The arguments to be passed to the plugins
     * @return mixed The filtered value
     */
    public function filter(string $hook, $value, &...$args)
    {
        array_unshift($args, $value);
        
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

        $this->total_time += $time;

        $this->exec_time[$name] = $this->exec_time[$name] ?? 0;
        $this->hooks_exec_time[$hook] = $this->hooks_exec_time[$hook] ?? 0;

        $this->exec_time[$name] += $time;
        $this->hooks_exec_time[$hook] += $time;
    }
}
