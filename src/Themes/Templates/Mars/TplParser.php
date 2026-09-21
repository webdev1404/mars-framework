<?php
/**
* The Tpl Parser
* @package Mars
*/

namespace Mars\Themes\Templates\Mars;

/**
 * The Tpl Parser
 * Parses @tpl (<template_name>) and includes the template
 */
class TplParser
{
    /**
     * @see \Mars\Themes\Templates\TemplateInterface::parse()
     * {@inheritDoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        return preg_replace_callback('/@tpl\s*\((.*)(?:,(.*))?\)/U', function (array $match) use ($params) {
            $template_filename = $this->getTemplate($match[1], $params['filename']);

            $vars = '[]';
            if (!empty($match[2])) {
                $vars = trim($match[2]);
            }

            return '<?= $this->get(\'' . $template_filename . '\', ' . $vars . ') ?>';
        }, $content);
    }

    /**
     * Returns the template name
     * @param string $value The template name from the match
     * @param string $filename The filename of the current file
     * @return string The template name
     * @throws \Exception
     */
    protected function getTemplate(string $value, string $filename) : string
    {
        $template = trim($value, ' \'"');
        if (!$template) {
            throw new \Exception("Empty template name in @tpl() construct");
        }

        $template_filename = dirname($filename) . '/' . $template . '.tpl.php';

        if (!is_file($template_filename)) {
            throw new \Exception("Template file not found: {$template_filename}");
        }

        return $template_filename;
    }
}

