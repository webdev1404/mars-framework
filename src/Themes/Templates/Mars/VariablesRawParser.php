<?php
/**
* The Raw Variables Parser
* @package Mars
*/

namespace Mars\Themes\Templates\Mars;

use Mars\App;

/**
 * The Raw Variables Parser
 */
class VariablesRawParser extends VariablesParser
{
    /**
     * @see \Mars\Themes\Templates\TemplateInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        return preg_replace_callback('/\{\!(.*)\!\}/U', function (array $match) {
            $value = trim($match[1], '!');

            return $this->parseVariable($value);
        }, $content);
    }

    /**
     * {inheritdoc}
     */
    protected function applyModifiers(string $value, array $modifiers, bool $apply_escape = true) : string
    {
        return parent::applyModifiers($value, $modifiers, false);
    }
}
