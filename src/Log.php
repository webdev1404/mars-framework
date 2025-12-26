<?php
/**
* The Log Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The Log Class
 * The system's log object
 */
class Log
{
    use Kernel;

    /**
     * @var string $suffix The log file's suffix
     */
    protected string $suffix {
        get {
            if (isset($this->suffix)) {
                return $this->suffix;
            }

            $ext = 'log';
            if ($this->app->is_cli) {
                $ext = 'cli.log';
            }

            $this->suffix = date($this->app->config->log->suffix) . '.' . $ext;

            return $this->suffix;
        }
    }

    /**
     * @var string $date The log date
     */
    protected string $date {
        get {
            if (isset($this->date)) {
                return $this->date;
            }

            $this->date = date($this->app->config->log->date_format);

            return $this->date;
        }
    }

    /**
     * @var array $handles The log files's handles
     */
    protected array $handles = [];

    /**
     * Builds the log objects
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        
        if ($this->app->config->log->enable) {
            set_error_handler([$this, 'handleError'], $this->app->config->log->reporting);
        }
    }

    /**
     * Destroys the log objects. Closes the log file's handle
     */
    public function __destruct()
    {
        foreach ($this->handles as $handle) {
            fclose($handle);
        }
    }

    /**
     * Callback for set_error_handler
     * @internal
     */
    public function handleError(int $errno, string $str, string $file, int $line) : bool
    {
        ob_start();
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $trace = ob_get_clean();

        $this->log('errors', $str, $file, $line, true, trim($trace));

        return false;
    }

    /**
     * Logs a string by using a basic format
     * @param string $type The log type. Eg: error,warning,info. Any string can be used as type
     * @param string $str The string to log
     * @param string $file The file in which the logging occurred Should be __FILE__
     * @param int $line The line where the logging occurred. Should be __LINE__
     * @param bool $extended If true, will log extended data
     * @param string $trace log trace, if any
     * @return static
     */
    public function log(string $type, string $str, string $file = '', int $line = 0, bool $extended = false, string $trace = '') : static
    {
        if (!isset($this->handles[$type])) {
            $this->open($type);
        }

        $text = "[{$this->date}] {$str}\n";

        if ($extended) {
            if ($trace) {
                $text.= $trace . "\n";
            }

            if (!$this->app->is_cli) {
                $text.= "Url: {$this->app->url->full}\n";
            }
            if ($file) {
                $text.= "Filename: {$file}:{$line}\n";
            }
        }

        $text.= "--------------------------------------------------------------------\n\n";

        fwrite($this->handles[$type], $text);

        return $this;
    }

    /**
     * Opens the log file
     * @param string $type The log type
     */
    protected function open(string $type)
    {
        $this->handles[$type] = fopen($this->getFilename($type), 'a');
        if (!$this->handles[$type]) {
            unset($this->handles[$type]);
            
            throw new \Exception('Error writing the log file. Please make sure the log folder is writeable');
        }
    }

    /**
     * Returns the file where a log string will be stored
     * @param string $type The log type
     * @return string The filename
     */
    public function getFilename(string $type) : string
    {
        return $this->app->log_path . '/' . basename($type) . '-' . $this->suffix;
    }

    /**
     * Logs an error
     * @param string $str The string to log
     * @param string $file The file in which the logging occurred Should be __FILE__
     * @param int $line The line where the logging occurred. Should be __LINE__
     * @return static
     */
    public function error(string $str, string $file = '', int $line = 0) : static
    {
        return $this->log('errors', $str, $file, $line, true);
    }

    /**
     * Logs an exception
     * @param \Exception $e The exception to log
     * @return static
     */
    public function exception(\Error|\Exception|Exception $e) : static
    {
        return $this->log('errors', $e->getMessage(), $e->getFile(), $e->getLine(), true, $e->getTraceAsString());
    }

    /**
     * Logs a message
     * @param string $str The string to log
     * @param string $file The file in which the logging occurred Should be __FILE__
     * @param int $line The line where the logging occurred. Should be __LINE__
     * @return static
     */
    public function message(string $str, string $file = '', int $line = 0) : static
    {
        return $this->log('messages', $str, $file, $line);
    }

    /**
     * Logs a warning
     * @param string $str The string to log
     * @param string $file The file in which the logging occurred Should be __FILE__
     * @param int $line The line where the logging occurred. Should be __LINE__
     * @return static
     */
    public function warning(string $str, string $file = '', int $line = 0) : static
    {
        return $this->log('warnings', $str, $file, $line);
    }

    /**
     * Logs an info
     * @param string $str The string to log
     * @param string $file The file in which the logging occurred Should be __FILE__
     * @param int $line The line where the logging occurred. Should be __LINE__
     * @return static
     */
    public function info(string $str, string $file = '', int $line = 0) : static
    {
        return $this->log('info', $str, $file, $line);
    }

    /**
     * Logs a system message
     * @param string $str The string to log
     * @param string $file The file in which the logging occurred Should be __FILE__
     * @param int $line The line where the logging occurred. Should be __LINE__
     * @return static
     */
    public function system(string $str, string $file = '', int $line = 0) : static
    {
        return $this->log('system', $str, $file, $line);
    }
}
