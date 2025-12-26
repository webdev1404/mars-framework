<?php
/**
* The Debug Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The Debug Class
 * Contains debug functionality and outputs debug info
 */
class Debug
{
    use Kernel;

    /**
     * Outputs all the debug info
     */
    public function output()
    {
        echo '<div id="debug-info">';

        $this->app->plugins->run('debug.output.step1', $this);

        $this->outputInfo();
        $this->outputExecutionTime();

        $this->app->plugins->run('debug.output.step2', $this);
        
        $this->outputPlugins();
        $this->outputDbQueries();

        $this->app->plugins->run('debug.output.step3', $this);

        $this->outputLoadedTemplates();
        $this->outputOpcacheInfo();
        $this->outputPreloadInfo();

        $this->app->plugins->run('debug.output.step4', $this);

        echo '</div>';
    }

    /**
     * Outputs basic debug info
     */
    public function outputInfo()
    {
        echo '<table class="grid debug-grid">';
        echo '<tr><th colspan="3">Debug Info</th></tr>';
        echo '<tr><td><strong>Execution Time</strong></td><td>' . $this->app->timer->getExecutionTime() . 's</td></tr>';
        echo '<tr><td><strong>Content Size</strong></td><td>' . $this->app->format->filesize($this->app->stats['content_size']) . '</td></tr>';
        echo '<tr><td><strong>Output Size</strong></td><td>' . $this->app->format->filesize($this->app->stats['output_size']) . '</td></tr>';
        echo '<tr><td><strong>Memory Usage</strong></td><td>' . $this->app->format->filesize(\memory_get_usage()) . '</td></tr>';
        echo '<tr><td><strong>Memory Peak Usage</strong></td><td>' . $this->app->format->filesize(\memory_get_peak_usage()) . '</td></tr>';
        echo '<tr><td><strong>Memory Usage (Real)</strong></td><td>' . $this->app->format->filesize(\memory_get_usage(true)) . '</td></tr>';
        echo '<tr><td><strong>Memory Peak Usage (Real)</strong></td><td>' . $this->app->format->filesize(\memory_get_peak_usage(true)) . '</td></tr>';
        echo '<tr><td><strong>DB Queries</strong></td><td>' . count($this->app->db->queries) . '</td></tr>';
        echo '<tr><td><strong>Loaded Templates</strong></td><td>' . count($this->app->theme->templates_loaded) . '</td></tr>';
        echo '<tr><td><strong>Included Files</strong></td><td>' . count(\get_included_files()) . '</td></tr>';
        echo '</table><br><br>';
    }

    /**
     * Outputs execution time info
     */
    public function outputExecutionTime()
    {
        $execution_time = $this->app->timer->getExecutionTime();

        echo '<table class="grid debug-grid">';
        echo '<tr><th colspan="3">Execution Time</th></tr>';
        echo '<tr><td><strong>Execution Time</strong></td><td>' . $execution_time . 's</td><td></td></tr>';
        echo "<tr><td><strong>DB Queries</strong></td><td>{$this->app->db->queries_time}s</td><td>" . $this->app->format->percentage($this->app->db->queries_time, $execution_time) . '%</td></tr>';
        echo "<tr><td><strong>Plugins</strong></td><td>{$this->app->plugins->total_time}s</td><td>" . $this->app->format->percentage($this->app->plugins->total_time, $execution_time) . '%</td></tr>';
        echo "<tr><td><strong>Content Output</strong></td><td>{$this->app->stats['content_time']}s</td><td>" . $this->app->format->percentage($this->app->stats['content_time'], $execution_time) . '%</td></tr>';
        echo "<tr><td><strong>Generate Output</strong></td><td>{$this->app->stats['output_time']}s</td><td>" . $this->app->format->percentage($this->app->stats['output_time'], $execution_time) . '%</td></tr>';
        echo '</table><br><br>';
    }

    /**
     * Outputs plugins debug info
     */
    public function outputPlugins()
    {
        $execution_time = $this->app->timer->getExecutionTime();
        if (!$this->app->plugins->plugins) {
            return;
        }

        echo '<table class="grid debug-grid debug-grid-plugins">';
        echo '<tr><th colspan="3">Plugins</th></tr>';
        foreach ($this->app->plugins->plugins as $name => $plugin) {
            $exec_time = $this->app->plugins->exec_time[$name] ?? 0;
            echo "<tr><td>" . $this->app->escape->html($name) . "</td><td>" . $exec_time . "s</td><td>" . $this->app->format->percentage($exec_time, $execution_time) . '%</td></tr>';
        }
        echo '</table><br><br>';

        echo '<table class="grid debug-grid debug-grid-hooks" style="width:auto;">';
        echo '<tr><th colspan="3">Hooks Execution Time</th></tr>';
        foreach ($this->app->plugins->hooks_exec_time as $hook => $exec_time) {
            echo "<tr><td>" . $this->app->escape->html($hook) . "</td><td>" . $exec_time . "s</td><td>" . $this->app->format->percentage($exec_time, $execution_time) . '%</td></tr>';
        }
        echo '</table><br><br>';
    }

    /**
     * Outputs mysql info
     */
    public function outputDbQueries()
    {
        $execution_time = $this->app->timer->getExecutionTime();

        echo '<table class="grid debug-grid debug-db-grid">';
        echo '<tr><th colspan="4">Queries</th></tr>';

        $i = 1;
        foreach ($this->app->db->queries as $query) {
            echo "<tr><td>{$i}</td><td><div class=\"debug-query\">" . $this->app->escape->html($query[0]) . "</div></td><td>{$query[1]}s</td><td>" . $this->app->format->percentage($query[1], $this->app->db->queries_time) . '%</td></tr>';
            $i++;
        }

        echo '</table><br><br>';
    }

    /**
     * Outputs info about the loaded templates
     */
    public function outputLoadedTemplates()
    {
        echo '<table class="grid debug-grid debug-grid-templates">';
        echo '<tr><th colspan="1">Loaded templates</th></tr>';
        echo '<tr><td class="left">';
        App::pp($this->app->theme->templates_loaded, false, false);
        echo '</td></tr>';
        echo '</table><br><br>';
    }

    /**
     * Outputs opcache info
     */
    public function outputOpcacheInfo()
    {
        $info = opcache_get_status(true);
        if (!$info) {
            return;
        }
        
        echo '<table class="grid debug-grid debug-grid-opcache">';
        echo '<tr><th colspan="3">Opcache Info</th></tr>';
        echo '<tr><td><strong>Enabled</strong></td><td>' . ($info['opcache_enabled'] ? 'Yes' : 'No') . '</td></tr>';

        if (!$info['opcache_enabled']) {
            echo '</table><br><br>';
            return;
        }

        $files = get_included_files();
        $cached_files = $info['scripts'];

        $uncached_files = [];
        foreach ($files as $file) {
            if (isset($cached_files[$file])) {
                continue;
            }

            $uncached_files[] = $file;
        }

        $from_cache = count($files) - count($uncached_files);
        $from_disk = count($uncached_files);

        echo '<tr><td><strong>Cached Scripts</strong></td><td>' . $info['opcache_statistics']['num_cached_scripts'] . '</td></tr>';
        echo '<tr><td><strong>Cache Hits</strong></td><td>' . $info['opcache_statistics']['hits'] . '</td></tr>';
        echo '<tr><td><strong>Cache Misses</strong></td><td>' . $info['opcache_statistics']['misses'] . '</td></tr>';
        echo '<tr><td><strong>Memory: Used</strong></td><td>' . $this->app->format->filesize($info['memory_usage']['used_memory']) . '</td></tr>';
        echo '<tr><td><strong>Memory: Free</strong></td><td>' . $this->app->format->filesize($info['memory_usage']['free_memory']) . '</td></tr>';
        echo '<tr><td><strong>Memory: Wasted</strong></td><td>' . $this->app->format->filesize($info['memory_usage']['wasted_memory']) . '</td></tr>';
        echo '<tr><td><strong>Total Files</strong></td><td>' . count($files) . '</td></tr>';
        echo '<tr><td><strong>From Cache</strong></td><td>' . $from_cache . '</td></tr>';
        echo '<tr><td><strong>From Disk</strong></td><td>' . $from_disk . '</td></tr>';
        echo '</table><br><br>';

        if ($uncached_files) {
            echo '<table class="grid debug-grid debug-grid-opcache-uncached">';
            echo '<tr><th>Files Read From Disk</th></tr>';
            foreach ($uncached_files as $file) {
                echo '<tr><td>' . $this->app->escape->html($file) . '</td></tr>';
            }
            echo '</table><br><br>';
        }
    }

    public function outputPreloadInfo()
    {
        $info = opcache_get_status(true);
        if (!$info) {
            return;
        }

        echo '<table class="grid debug-grid preload-grid">';
        echo '<tr><th colspan="3">Preload Info</th></tr>';
        echo '<tr><td><strong>Enabled</strong></td><td>' . (isset($info['preload_statistics']) ? 'Yes' : 'No') . '</td></tr>';

        if (isset($info['preload_statistics'])) {
            if (isset($info['preload_statistics']['functions'])) {
                echo '<tr><td><strong>Preloaded Functions</strong></td><td>' . count($info['preload_statistics']['functions']) . '</td></tr>';
            }
            if (isset($info['preload_statistics']['classes'])) {
                echo '<tr><td><strong>Preloaded Scripts</strong></td><td>' . count($info['preload_statistics']['classes']) . '</td></tr>';
            }
            echo '<tr><td><strong>Memory: Used</strong></td><td>' . $this->app->format->filesize($info['preload_statistics']['memory_consumption']) . '</td></tr>';
        }

        echo '</table><br><br>';
    }
}
