<?php
/**
* The Templates Parser
* @package Mars
*/

namespace Mars\Templates\Mars;

use Mars\App;

/**
 * The Templates Parser
 * Parses {% template <template_name> %} and includes the template
 */
class TemplatesParser
{
    /**
     * @see \Mars\Templates\DriverInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        return preg_replace_callback('/\{%\s*template +(.*)%\}/U', function (array $match) use ($params) {
            $template_filename = $this->getTemplate($match[1], $params['filename']); 

            return '<?= $this->getFromFilename(\'' . $template_filename . '\') ?>';
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
        $template = trim($value);
        if (!$template) {
            throw new \Exception("Empty template name in {% template %} construct");
        }

        $template_filename = dirname($filename) . '/' . $template . '.' . App::FILE_EXTENSIONS['templates'];

        if (!is_file($template_filename)) {
            throw new \Exception("Template file not found: {$template_filename}");
        }

        return $template_filename;
    }
}
