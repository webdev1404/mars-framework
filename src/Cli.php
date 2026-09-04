<?php
/**
* The Cli Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Handlers;
use Mars\Alerts\Alerts;

/**
 * The Cli Class
 */
class Cli
{
    use Kernel;

    /**
     * @var array $supported_printers The list of supported printers
     */
    public protected(set) array $supported_printers = [
        'list' => \Mars\Cli\Listing::class,
        'list_multi' => \Mars\Cli\ListingMulti::class,
        'table' => \Mars\Cli\Table::class
    ];
        
    /**
     * @var array $colors Array defining the user colors
     */
    public array $colors = [
        '' => '0',
        'default' => '0',
        'message' => '0',
        'error' => '0;41',
        'warning' => '93',
        'notice' => '1;36',
        'success' => '0;32',
        'important' => '1;32',
        'header' => '0;33',
        'list_1' => '0;32',
        'list_2' => '0',
        'white' => '1;37',
        'black' => '0;30',
        'grey' => '1;30',
        'light_grey' => '0;37',
        'red' => '0;31',
        'light_red' => '1;31',
        'green' => '0;32',
        'light_green' => '1;32',
        'brown' => '0;33',
        'yellow' => '1;33',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'magenta' => '0;35',
        'cyan' => '1;36',
    ];

    /**
     * @var Handlers $printers The printers object
     */
    public protected(set) Handlers $printers {
        get {
            if (isset($this->printers)) {
                return $this->printers;
            }

            $this->printers = new Handlers($this->supported_printers);

            return $this->printers;
        }
    }

    /**
     * @var array $args List of arguments passed to the script
     */
    public protected(set) array $args {
        get {
            if (isset($this->args)) {
                return $this->args;
            }

            global $argv;
            $this->args = $argv ?? [];

            //remove the script name
            array_shift($this->args);

            return $this->args;
        }
    }

    /**
     * @var array $params The list of parameters
     */
    public protected(set) array $params {
        get {
            if (isset($this->params)) {
                return $this->params;
            }

            $this->params = [];
            foreach ($this->args as $param) {
                if (!str_starts_with($param, '-')) {
                    $this->params[] = $param;
                }
            }

            return $this->params;
        }
    }

    /**
     * @var array $options List of options
     */
    public protected(set) array $options {
        get {
            if (isset($this->options)) {
                return $this->options;
            }

            $this->options = [];
            foreach ($this->args as $param) {
                if (str_starts_with($param, '--')) {
                    $parts = explode('=', substr($param, 2));

                    $this->options[$parts[0]] = $parts[1] ?? true;
                } elseif (str_starts_with($param, '-')) {
                    $parts = explode('=', substr($param, 1));

                    if (count($parts) > 1) {
                        $this->options[$parts[0]] = $parts[1] ?? '';
                    } else {
                        $name = substr($param, 1);

                        $this->options[$name] = true;
                    }
                }
            }

            return $this->options;
        }
    }

    /**
     * @var string $newline The newline
     */
    public string $newline {
        get {
            if (isset($this->newline)) {
                return $this->newline;
            }

            $this->newline = $this->app->is_cli ? "\n" : '<br>';

            return $this->newline;
        }
    }

    /**
     * Builds the Cli object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->app->plugins->run('cli.boot', $this);
    }

    /**
     * Returns true if a command line option has been defined
     * @param string $name The name of the option
     * @return bool
     */
    public function has(string $name) : bool
    {
        return isset($this->options[$name]);
    }

    /**
     * Returns the value of a command line option
     * @param string|array $name The name of the option or an array of names in the format name => filter. if filter is an array, the option will be filtered as a list
     * @param mixed $default_value The default value to return if the option is not found
     * @param string $filter The filter to apply to the option, if any. See class Filter for a list of filters
     * @param array $filter_options The options to pass to the filter, if any
     * @return mixed The option
     */
    public function get(string|array $name, mixed $default_value = '', string $filter = '', array $filter_options = []) : mixed
    {
        if (is_array($name)) {
            //return an array of options
            $options = [];

            foreach ($name as $option_name => $option_filter) {
                $option = $this->options[$option_name] ?? $default_value;
                
                if (is_array($option_filter)) {
                    $options[$option_name] = $this->app->filter->list($option, $option_filter);
                } else {
                    $options[$option_name] = $this->app->filter->value($option, $option_filter);
                }
            }

            return $options;
        }

        //return a single option
        $option = $this->options[$name] ?? $default_value;
        if ($filter) {
            $option = $this->app->filter->value($option, $filter, $filter_options);
        }

        return $option;
    }

    /**
     * Returns the value of a command line option as an integer
     * @param string $name The name of the variable
     * @param int $default_value The default value to return if the variable is not set
     * @return int|array The option
     */
    public function getInt(string $name, ?int $default_value = 0) : int|array
    {
        return $this->get($name, $default_value, 'int');
    }

    /**
     * Returns the value of a command line option as a float
     * @param string $name The name of the variable
     * @param float $default_value The default value to return if the variable is not set
     * @return float|array The option
     */
    public function getFloat(string $name, ?float $default_value = 0) : float|array
    {
        return $this->get($name, $default_value, 'float');
    }

    /**
     * Returns the list of parameters starting from a specific index
     * @param int $start The starting index
     * @param int|null $length The number of params to return
     * @return array The list of params
     */
    public function getParams($start = 1, $length = null) : array
    {
        return array_slice($this->params, $start, $length);
    }

    /**
     * Returns a param by index
     * @param int $index The index of the param
     * @param mixed $default_value The default value to return if the param is not found
     * @param string $filter The filter to apply to the param, if any. See class Filter for a list of filters
     * @param array $filter_options The options to pass to the filter, if any
     * @return mixed The param
     */
    public function getParam(int $index, mixed $default_value = '', string $filter = '', array $filter_options = []) : mixed
    {
        $param = $this->params[$index] ?? $default_value;
        if ($filter) {
            $param = $this->app->filter->value($param, $filter, $filter_options);
        }

        return $param;
    }

    /**
     * Returns a color, based on type
     * @param string $color The color
     * @return string The color
     */
    public function getColor(string $color) : string
    {
        return $this->colors[$color] ?? $color;
    }

    /****************STDIN/STDOUT***********************************/

    /**
     * Outputs a question and returns the answer from stdin
     * @param string $question The question
     * @param string $color The color of the question
     * @param bool $password If true, the answer will be hidden (for passwords)
     * @return string The answer
     */
    public function ask(string $question, string $color = '', bool $password = false) : string
    {
        $this->print($question . ': ', $color, false);

        return $this->read($password);
    }

    /**
     * Reads a line from stdin and returns it
     * @param bool $password If true, the answer will be hidden (for passwords)
     * @return string
     */
    public function read(bool $password = false) : string
    {
        if ($password) {
            system('stty -echo');
        }

        $input = trim(fgets(STDIN));

        if ($password) {
            system('stty echo');
            echo "\n";
        }

        return $input;
    }

    /**
     * Outputs a newline
     * @param int $times The number of newlines to print
     */
    public function printLn(int $times = 1)
    {
        echo str_repeat($this->newline, $times);

        $this->flush();
    }

    /**
     * Prints a text, by repeating $text
     * @param string $text The text to print
     * @param string $color The color to print the text with
     * @param bool $newline If true will also output a newline
     */
    public function printRepeat(string $text, int $repeat, string $color = '', bool $newline = true)
    {
        $this->print(str_repeat($text, $repeat), $color, $newline);
    }

    /**
     * Outputs a delimitator
     * @param int $chars The number of chars to print
     */
    public function printDel(int $chars = 60)
    {
        $this->printRepeat('-', $chars);
    }

    /**
     * Outputs text
     * @param string $text The text to output
     * @param string $color The color to print the text with
     * @param bool $newline If true will also output a newline
     * @return static
     */
    public function print(string $text, string $color = '', bool $newline = true) : static
    {
        //don't show colors if not in a terminal
        if (!$this->app->is_cli) {
            $color = '';
        }

        if ($color) {
            $color = $this->getColor($color);
            echo "\e[{$color}m{$text}\e[0m";
        } else {
            echo $text;
        }

        if ($newline) {
            echo $this->newline;
        }

        $this->flush();

        return $this;
    }

    /**
     * Outputs a header
     * @param string $text The text to output
     * @return static
     */
    public function header(string $text) : static
    {
        return $this->print($text, $this->colors['header']);
    }


    /**
     * Outputs a message
     * @param string $text The text to output
     * @return static
     */
    public function message(string $text) : static
    {
        $this->print($text, $this->colors['message']);

        return $this;
    }

    /**
     * Outputs an error and dies
     * @param string $text The text to output
     * @param bool $die If true, will exit the script after outputting the error
     */
    public function error(string $text, bool $die = true)
    {
        $this->print($text, $this->colors['error']);
        echo "\n";

        $this->flush();

        if ($die) {
            die;
        }
    }

    /**
     * Outputs multiple errors and dies
     * @param array|Alerts $errors The errors to output
     * @param bool $die If true, will exit the script after outputting the errors
     */
    public function errors(array|Alerts $errors, bool $die = true)
    {
        if ($errors instanceof Alerts) {
            $errors = $errors->getStrings();
        }

        foreach ($errors as $error) {
            $this->print($error, $this->colors['error']);
        }
        echo "\n";

        $this->flush();

        if ($die) {
            die;
        }
    }

    /**
     * Outputs a warning
     * @param string $text The text to output
     * @return static
     */
    public function warning(string $text) : static
    {
        $this->print($text, $this->colors['warning']);

        return $this;
    }

    /**
     * Outputs multiple warnings
     * @param array|Alerts $warnings The warnings to output
     * @return static
     */
    public function warnings(array|Alerts $warnings) : static
    {
        if ($warnings instanceof Alerts) {
            $warnings = $warnings->getStrings();
        }

        foreach ($warnings as $warning) {
            $this->print($warning, $this->colors['warning']);
        }

        return $this;
    }

    /**
     * Outputs a notice string
     * @param string $text The text to output
     * @return static
     */
    public function notice(string $text) : static
    {
        $this->print($text, $this->colors['notice']);

        return $this;
    }

    /**
     * Outputs multiple notices
     * @param array|Alerts $notices The notices to output
     * @return static
     */
    public function notices(array|Alerts $notices) : static
    {
        if ($notices instanceof Alerts) {
            $notices = $notices->getStrings();
        }

        foreach ($notices as $notice) {
            $this->print($notice, $this->colors['notice']);
        }

        return $this;
    }

    /**
     * Outputs a success message
     * @param string $text The text to output
     * @return static
     */
    public function success(string $text) : static
    {
        $this->print($text, $this->colors['success']);

        return $this;
    }

    /**
     * Outputs an important message
     * @param string $text The text to output
     * @return static
     */
    public function important(string $text) : static
    {
        $this->print($text, $this->colors['important']);

        return $this;
    }

    /**
     * Prints a list
     * @param array $data The data to print
     * @param array $colors The colors to use
     * @param array $paddings_right The number of left chars to apply, if any
     * @param array $paddings_left The number of left chars to apply, if any
     * @return static
     */
    public function printList(array $data, array $colors = [], array $paddings_right = [], array $paddings_left = []) : static
    {
        $printer = $this->printers->get('list');
        $printer->print($data, $colors, $paddings_right, $paddings_left);

        return $this;
    }

    /**
     * Prints a list, with multiple sections
     * @param array $data The data to print
     * @param array $colors The colors to use
     * @param array $paddings_right The number of right chars to apply, if any
     * @param array $paddings_left The number of left chars to apply, if any
     * @return static
     */
    public function printListMulti(array $data, array $colors = [], array $paddings_right = [], array $paddings_left = []) : static
    {
        $printer = $this->printers->get('list_multi');
        $printer->print($data, $colors, $paddings_right, $paddings_left);

        return $this;
    }

    /**
     * Prints a table
     * @param array $headers The header data
     * @param array $data The data to print
     * @param array $colors The colors to use. $colors[0] is the header's color
     * @param array $align Determines how the headers/cells are align. $align[0] is the header's alignment
     * @param array $paddings_left The number of left chars to apply, if any
     * @param array $paddings_right The number of right chars to apply, if any
     * @return static
     */
    public function printTable(array $headers, array $data, array $colors = [], array $align = [], array $paddings_left = [], array $paddings_right = []) : static
    {
        $printer = $this->printers->get('table');
        $printer->print($headers, $data, $colors, $align, $paddings_left, $paddings_right);

        return $this;
    }

    /**
     * Flushes the output buffer
     */
    public function flush() : static
    {
        flush();
        if (ob_get_level() > 0) {
            ob_flush();
        }

        return $this;
    }
}
