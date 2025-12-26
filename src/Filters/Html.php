<?php
/**
* The Html Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
 * The Html Filter Class
 */
class Html extends Filter
{
    /**
     * @see \Mars\Filter::html()
     */
    public function filter(string $html, ?string $allowed_elements = null, ?string $allowed_attributes = null, string $encoding = 'UTF-8') : string
    {
        $allowed_elements ??= $this->app->config->html->allowed_elements;
        $allowed_attributes ??= $this->app->config->html->allowed_attributes;
 
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', $encoding);
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('AutoFormat.RemoveEmpty', true);
        $config->set('Attr.AllowedRel', 'nofollow,follow');
        $config->set('Attr.AllowedFrameTargets', '_blank');
        $config->set('Attr.EnableID', true);

        if ($allowed_elements !== null) {
            $config->set('HTML.AllowedElements', $allowed_elements);
        }
        if ($allowed_attributes !== null) {
            $config->set('HTML.AllowedAttributes', $allowed_attributes);
        }

        $this->app->plugins->run('filter.html.config', $config, $allowed_attributes, $allowed_elements, $this);

        $purifier = new \HTMLPurifier($config);
        $html = $purifier->purify($html);

        return $this->app->plugins->filter('filter.html', $html, $this);
    }
}
