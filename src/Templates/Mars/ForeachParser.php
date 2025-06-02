<?php
/**
* The Foreach Hander
* @package Mars
*/

namespace Mars\Templates\Mars;

use Mars\App\InstanceTrait;

/**
 * The Foreach Hander
 */
class ForeachParser
{
    use InstanceTrait;

    /**
     * @see \Mars\Templates\DriverInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        $content = preg_replace_callback('/@foreach\s*\((.*) as (.*)\)/isU', function (array $match) {
            $variable = new VariablesParser($this->app)->replaceVariables($match[1]);
            $expression = trim($match[2]);

            $code = '<?php if(' . $variable . '){ ';
            $code.= 'foreach(' . $variable . ' as ' . $expression . ') {' . "\n";
            $code.= ' ?>' . "\n";
            
            return $code;
        }, $content);

        $content = preg_replace('/@endforeach/U', '<?php }} ?>', $content);

        return $content;
    }
}
