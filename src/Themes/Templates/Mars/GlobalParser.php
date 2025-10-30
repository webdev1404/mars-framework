<?php
/**
* The Global Parser
* @package Mars
*/

namespace Mars\Themes\Templates\Mars;

/**
 * The Global Parser
 */
class GlobalParser extends Params
{
    /**
     * @see \Mars\Themes\Templates\TemplateInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        $content = preg_replace_callback('/\@global\s*(\(.*?\))/iU', function (array $match) {
            if (preg_match('/\(\s*([\'"])(.*)\1\s*,\s*([\'"]?)(.*)\3\s*\)/', $match[1], $matches)) {
                $name = $this->getName($matches[2], trim($matches[1]));
                $value = $this->getValue($matches[4], trim($matches[3]));

                return $this->get($name, $value);
            }
            
            return '';
        }, $content);

        return $content;
    }

    /**
     * Sets a global variable
     * @param string $name The name of the variable
     * @param string $value The value of the variable
     */
    protected function get(string $name, string $value)
    {
        switch ($name) {
            case 'title':
                return "<?php \$this->app->document->title->set({$value}) ?>";
            case 'description':
            case 'meta_description':
            case 'meta-description':
                return "<?php \$this->app->document->meta->set('description', {$value}) ?>";
            case 'keywords':
            case 'meta_keywords':
            case 'meta-keywords':
                return "<?php \$this->app->document->meta->set('keywords', {$value}) ?>";
            case 'robots':
            case 'meta_robots':
            case 'meta-robots':
                return "<?php \$this->app->document->meta->set('robots', {$value}) ?>";
            default:
                return $this->app->plugins->filter('theme_global_get', $name, $value, $this);
        }
    }
}
