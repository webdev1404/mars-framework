<?php
/**
* The If Hander
* @package Mars
*/

namespace Mars\Themes\Templates\Mars;

use Mars\App\Kernel;
use Mars\Themes\Templates\TemplateInterface;

/**
 * The If Hander
 */
class IfParser
{
    use Kernel;
    
    /**
     * @see TemplateInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        //$content = preg_replace_callback('/\{%\s*if(.*)\s*%\}/iU', function (array $match) {
        $content = preg_replace_callback('/@if\s*\((.*)\)/i', function (array $match) {
            return '<?php if(' . $this->getCondition($match[1]) . '){ ?>';
        }, $content);

        $content = preg_replace_callback('/@elseif\s*\((.*)\)/is', function (array $match) {
            return '<?php } elseif(' . $this->getCondition($match[1]) . '){ ?>';
        }, $content);

        $content = preg_replace('/@else/iU', '<?php } else { ?>', $content);
        $content = preg_replace('/@endif/iU', '<?php } ?>', $content);

        return $content;
    }

    /**
     * Returns an if condition from $match
     * @param array $match Callback match
     * @return string
     */
    protected function getCondition(string $value) : string
    {
        $value = trim($value);

        //remove the start/end brackets
        if (str_starts_with($value, '(') && str_ends_with($value, ')')) {
            $value = substr($value, 1, -1);
        }

        return new VariablesParser($this->app)->replaceVariables($value);
    }
}
