<?php
/**
* The Cli Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Handlers;

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
        'info' => '32',
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
     * @var Handlers $handlers The printers object
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
     * @var array $argv List of arguments
     */
    public protected(set) array $argv {
        get {
            if (isset($this->argv)) {
                return $this->argv;
            }

            global $argv;
            $this->argv = $argv ?? [];

            //remove the script name
            array_shift($this->argv);

            return $this->argv;
        }
    }

    /**
     * @var array $commands List of commands
     */
    public protected(set) array $commands {
        get {
            if (isset($this->commands)) {
                return $this->commands;
            }

            $this->commands = [];
            foreach ($this->argv as $arg) {
                if (str_starts_with($arg, '-')) {
                    continue;
                }

                $this->commands = explode(':', $arg);
                break;
            }

            return $this->commands;
        }
    }

    /**
     * @var string $command_name The name of the command
     */
    public protected(set) string $command_name {
        get {
            if (isset($this->command_name)) {
                return $this->command_name;
            }

            $this->command_name = $this->commands[0] ?? '';
            
            return $this->command_name;
        }
    }

    /**
     * @var string $command_action The action of the command
     */
    public protected(set) string $command_action {
        get {
            if (isset($this->command_action)) {
                return $this->command_action;
            }

            $slice = array_slice($this->commands, 1);

            $this->command_action = implode(':', $slice);

            return $this->command_action;
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
            foreach ($this->argv as $arg) {
                if (str_starts_with($arg, '--')) {
                    $parts = explode('=', substr($arg, 2));

                    $this->options[$parts[0]] = $parts[1] ?? true;
                } elseif (str_starts_with($arg, '-')) {
                    $parts = explode('=', substr($arg, 1));

                    if (count($parts) > 1) {
                        $this->options[$parts[0]] = $parts[1] ?? '';
                    } else {
                        $name = substr($arg, 1);

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

        $this->app->plugins->run('boot_cli', $this);
    }

    /**
     * Returns true if a command line option has been defined
     * @param string $name The name of the option
     * @return bool
     */
    public function hasOption(string $name) : bool
    {
        return isset($this->options[$name]);
    }

    /**
     * Returns the value of a command line option
     * @param string $name The name of the option
     * @param string $filter The filter to apply to the option, if any. See class Filter for a list of filters
     * @param mixed $default_value The default value to return if the option is not found
     * @return string The option
     */
    public function getOption(string $name, string $filter = '', mixed $default_value = '') : mixed
    {
        $option = $this->options[$name] ?? $default_value;
        if ($filter) {
            $option = $this->app->filter->value($option, $filter);
        }

        return $option;
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
     * @return string The answer
     */
    public function ask(string $question) : string
    {
        echo $question . ': ';

        return $this->read();
    }

    /**
     * Reads a line from stdin and returns it
     * @return string
     */
    public function read() : string
    {
        return trim(fgets(STDIN));
    }

    /**
     * Outputs a newline
     * @param int $times The number of newlines to print
     */
    public function printNewline(int $times = 1)
    {
        echo str_repeat($this->newline, $times);
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
    public function message($text) : static
    {
        $this->print($text, $this->colors['message']);

        return $this;
    }

    /**
     * Outputs an error and dies
     * @param string $text The text to output
     * @param bool $die If true, will exit the script after outputing the error
     */
    public function error($text, bool $die = true)
    {
        echo "\n";
        $this->print($text, $this->colors['error']);
        echo "\n";

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
     * Outputs an info string
     * @param string $text The text to output
     * @return static
     */
    public function info(string $text) : static
    {
        $this->print($text, $this->colors['info']);

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
     * @param array $paddings_right The number of left chars to apply, if any
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
     * @param array $paddings_right The number of left chars to apply, if any
     * @return static
     */
    public function printTable(array $headers, array $data, array $colors = [], array $align = [], array $paddings_left = [], array $paddings_right = []) : static
    {
        $printer = $this->printers->get('table');
        $printer->print($headers, $data, $colors, $align, $paddings_left, $paddings_right);

        return $this;
    }
}
