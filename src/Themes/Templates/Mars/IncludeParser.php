<?php
/**
* The Include Parser
* @package Mars
*/

namespace Mars\Themes\Templates\Mars;

use Mars\App;
use Mars\App\Kernel;
use Mars\Themes\Templates\TemplateInterface;

/**
 * The Include Parser
 * Parses @include (<template_name>) and includes the template
 */
class IncludeParser
{
    /**
     * @see TemplateInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        return preg_replace_callback('/@include\s*\((.*)\)/U', function (array $match) use ($params) {
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
        $template = trim($value, ' \'"');
        if (!$template) {
            throw new \Exception("Empty template name in {% template %} construct");
        }

        $template_filename = dirname($filename) . '/' . $template . '.php';

        if (!is_file($template_filename)) {
            throw new \Exception("Template file not found: {$template_filename}");
        }

        return $template_filename;
    }
}
