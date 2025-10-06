<?php
/**
* The Global Parser
* @package Mars
*/

namespace Mars\Themes\Templates\Mars;

use Mars\App;
use Mars\App\Kernel;
use Mars\Themes\Templates\TemplateInterface;

/**
 * The Global Parser
 */
class GlobalParser
{
    use Kernel;

    /**
     * @see TemplateInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        $content = preg_replace_callback('/\@global\s*(\(.*?\))/iU', function (array $match) {
            if (preg_match('/\(\s*([\'"])(.*)\1\s*,\s*([\'"]?)(.*)\3\s*\)/', $match[1], $matches)) {
                $name = trim($matches[2]);
                $value = $this->getValue(trim($matches[4]), trim($matches[3]));

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
                return $this->app->plugins->filter('theme_global_get', $name, $value);
        }
    }

    /**
     * Gets the value for the global variable
     * @param string $value The value
     * @param string $delim The delimiter, if any
     * @return string The PHP code for the value
     */
    protected function getValue(string $value, string $delim) : string
    {
        if ($delim) {
            //we have a string
            if ($delim === '"') {
                $value = str_replace("\\\"", "\"", $value);
            } else {
                $value = str_replace("\\'", "'", $value);
            }

            return "'" . str_replace("'", "\\'", $value) . "'";
        } else {
            //we have a lang string or a variable
            if (str_starts_with($value, '$')) {
                return $value;
            } elseif (str_starts_with($value, '{{') && str_ends_with($value, '}}')) {
                $value = trim(substr($value, 2, -2));
                return "\$lang->get('{$value}')";
            }

            return $value;
        }
    }
}
