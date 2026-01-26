<?php
/**
* The Data Parser
* @package Mars
*/

namespace Mars\Themes\Templates\Mars;

/**
 * The Data Parser
 */
class DataParser extends Params
{
    /**
     * @see \Mars\Themes\Templates\TemplateInterface::parse()
     * {@inheritDoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        $content = preg_replace_callback('/\@data\s*(\(.*?\))/iU', function (array $match) {
            if (preg_match('/\(\s*([\'"])(.*)\1\s*,\s*([\'"]?)(.*)\3\s*\)/', $match[1], $matches)) {
                $name = $this->getName($matches[2], trim($matches[1]));
                $value = $this->getValue($matches[4], trim($matches[3]));

                return '<?php $this->data["' . $name . '"] = ' . $value . ';?>';
            }
            
            return '';
        }, $content);

        return $content;
    }
}
