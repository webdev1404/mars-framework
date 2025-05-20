<?php
/**
* The Bin Base Class
* @package Mars
*/

namespace Mars\Bin;

use Mars\App\InstanceTrait;

/**
 * The Bin Base Class
 * Base class for all bin classes
 */
abstract class Base implements BinInterface
{
    use InstanceTrait;

    /**
     * @var array $actions The actions the class is responsible for
     */
    public protected(set) array $actions;

    /**
     * @var array $commands The commands the class can handle
     */
    public protected(set) array $commands = [];

    /**
     * @var array $command_descriptions The command_descriptions The command descriptions
     */
    public protected(set) array $command_descriptions = [];

    /**
     * @var bool $show_done Whether to show the done message after executing a command
     */
    protected bool $show_done = true;

    /**
     * Executes the command
    */
    public function execute(string $command)
    {
        if (!isset($this->commands[$command])) {
            $this->app->cli->error("Command {$command} not found", false);

            $help = new Help($this->app);
            $help->showCommands($this);
            die;
        }

        $method = $this->commands[$command];
        if (!method_exists($this, $method)) {
            throw new \Exception("Method {$method} not found in class " . get_class($this));
        }

        call_user_func([$this, $method]);

        if ($this->show_done) {
            $this->done();
        }
    }

    /**
     * Prints the start message
     * @param string $message The message to print
     * @param string $color The color of the message
     */
    public function doing(string $message, string $color = 'blue')
    {
        $this->app->cli->print($message, $color);
    }

    /**
     * Prints the done message
     * @param string $message The message to print
     * @param string $color The color of the message
     */
    public function done(string $message = 'Done!', $color = 'green')
    {
        $this->app->cli->print($message, $color);
    }

    /**
     * Prints a message
     * @param string $message The message to print
     * @param string $color The color of the message
     */
    public function print(string $message, string $color = '')
    {
        $this->app->cli->print($message, $color);
    }
}