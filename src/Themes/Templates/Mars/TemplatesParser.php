<?php
/**
* The TemplatesParser
* @package Mars
*/

namespace Mars\Themes\Templates\Mars;

use Mars\App\Kernel;

/**
 * The Templates Parser
 * Parses @template (<template_name>) and includes the template from the theme's templates dir
 */
class TemplatesParser
{
    use Kernel;

    /**
     * @see \Mars\Themes\Templates\TemplateInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params = []) : string
    {
        return preg_replace_callback('/@template\s*\((.*)\)/U', function (array $match) {
            $template = $this->getTemplate($match[1]);

            return '<?= $this->get(\'' . $template . '\') ?>';
        }, $content);
    }

    /**
     * Returns the template name
     * @param string $value The template name from the match
     * @return string The template name
     * @throws \Exception
     */
    protected function getTemplate(string $value) : string
    {
        $template = trim($value, ' \'"');
        if (!$template) {
            throw new \Exception("Empty template name in {% template %} construct");
        }

        $filename = $this->app->theme->template->getFilename($template);
        if (!is_file($filename)) {
            throw new \Exception("Template file not found: {$filename}");
        }

        return $template;
    }
}
