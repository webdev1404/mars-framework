<?php
/**
* The Templates Driver Interface
* @package Mars
*/

namespace Mars\Themes\Templates;

/**
 * The Templates Driver Interface
 */
interface TemplateInterface
{
    /**
     * Parses the content, as a template
     * @param string $content The content
     * @param array $params Params to pass to the parser
     */
    public function parse(string $content, array $params) : string;
}
