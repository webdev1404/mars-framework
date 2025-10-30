<?php
/**
* The Special Parser
* @package Mars
*/

namespace Mars\Themes\Templates\Mars;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Special Parser
 */
class SpecialParser
{
    use Kernel;

    /**
     * @see \Mars\Themes\Templates\TemplateInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        $content = preg_replace_callback('/(@\w*)/', function (array $match) {
            return $this->get($match[1]);
        }, $content);

        return $content;
    }

    /**
     * Sets a special variable
     * @param string $name The name of the variable
     */
    protected function get(string $name)
    {
        switch ($name) {
            case '@csrf':
                return '<?= $app->html->csrf() ?>';
            default:
                return $this->app->plugins->filter('theme_special_get', $name, $this);
        }
    }
}
