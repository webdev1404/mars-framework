<?php
/**
* The Special Parser
* @package Mars
*/

namespace Mars\Themes\Templates\Mars;

use Mars\App\Kernel;

/**
 * The Special Parser
 * Parses special template variables
 */
class SpecialParser
{
    use Kernel;

    /**
     * @see \Mars\Themes\Templates\TemplateInterface::parse()
     * {@inheritDoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        $content = preg_replace_callback('/(@[a-z0-9_\.]+)\s*(?:=\s(.*))?\v/i', function (array $match) {
            $name = $match[1];
            $value = empty($match[2]) ? '' : new VariablesParser($this->app)->get($match[2]);

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
        $subname = '';

        $pos = strpos($name, '.');
        if ($pos !== false) {
            $subname = substr($name, $pos + 1);
            $name = substr($name, 0, $pos);
        }

        switch ($name) {
            case '@set':
                return "<?php \${$subname} = {$value} ?>";
            case '@data':
                return "<?php \$this->data->{$subname} = {$value} ?>";
            case '@csrf':
                return '<?= $app->html->csrf() ?>';
            case '@title':
                return "<?php \$this->app->document->title->set({$value}) ?>";
            case '@heading':
                return "<?php \$this->app->document->heading->set({$value}) ?>";
            case '@meta_title':
                return "<?php \$this->app->document->meta_title->set({$value}) ?>";
            case '@meta':
                return "<?php \$this->app->document->meta->set('{$subname}', {$value}) ?>";
            case '@breadcrumbs':
                return "<?php \$this->app->ui->breadcrumbs->set({$value}) ?>";
            default:
                return $this->app->plugins->filter('template.special.get', $name, $subname, $this);
        }
    }
}
