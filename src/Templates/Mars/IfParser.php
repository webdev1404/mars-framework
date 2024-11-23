<?php
/**
* The If Hander
* @package Mars
*/

namespace Mars\Templates\Mars;

/**
 * The If Hander
 */
class IfParser
{
    /**
     * @see \Mars\Templates\DriverInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        $content = preg_replace_callback('/\{\%\s*if(.*)\s*\%\}/iU', function (array $match) {
            $condition = $this->getCondition($match);
            
            return '<?php if(!empty(' . $this->getCondition($match) . ')){ ?>';
        }, $content);

        $content = preg_replace_callback('/\{\%\s*elseif(.*)\s*\%\}/isU', function (array $match) {
            return '<?php } elseif(!empty(' . $this->getCondition($match) . ')){ ?>';
        }, $content);

        $content = preg_replace('/\{\%\s*else\s*\%\}/iU', '<?php } else { ?>', $content);
        $content = preg_replace('/\{\%\s*endif\s*\%\}/iU', '<?php } ?>', $content);

        return $content;
    }

    /**
     * Returns an if condition from $match
     * @param array $match Callback match
     * @return string
     */
    protected function getCondition(array $match) : string
    {
        $condition = $this->trimParentheses($match[1]);

        $variable_parser = new VariableParser($this->app);
        return $variable_parser->replaceVariables($condition);
    }

    /**
     * Trims the parentheses of string, if any
     * @param string $str The string
     * @return string
     */
    protected function trimParentheses(string $str) : string
    {
        $str = trim($str);

        if ($str[0] == '(') {
            $str = substr($str, 1);
            $len = strlen($str);

            if ($str[$len - 1] == ')') {
                $str = substr($str, 0, $len - 1);
            }

            $str = trim($str);
        }

        return $str;
    }
}
