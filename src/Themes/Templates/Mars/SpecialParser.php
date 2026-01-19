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
 * Parses special template variables
 */
class SpecialParser extends Params
{
    use Kernel;

    /**
     * @see \Mars\Themes\Templates\TemplateInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        $content = preg_replace_callback('/(@[a-z0-9\.]+)\s*(\(([\'"])(.*)\3\))?/i', function (array $match) {
            $name = $match[1];
            $value = empty($match[4]) ? '' : $this->getValue($match[4], $match[3]);
            return $this->get($name, $value);
        }, $content);

        return $content;
    }

    /**
     * Sets a special variable
     * @param string $name The name of the variable
     */
    protected function get(string $name, string $value) : string
    {
        switch ($name) {
            case '@csrf':
                return '<?= $app->html->csrf() ?>';
            case '@title':
                return "<?php \$this->app->document->title->set({$value}) ?>";
            case '@meta.description':
                return "<?php \$this->app->document->meta->set('description', {$value}) ?>";
            case '@meta.keywords':
                return "<?php \$this->app->document->meta->set('keywords', {$value}) ?>";
            case '@meta.robots':
                return "<?php \$this->app->document->meta->set('robots', {$value}) ?>";
            default:
                return $this->app->plugins->filter('theme.special.get', $name, $this);
        }
    }
}
