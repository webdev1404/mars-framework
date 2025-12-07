<?php
/**
* The Domain Localization Driver Interface
* @package Mars
*/

namespace Mars\Localization;

/**
 * The Domain Localization Driver Interface
 * Implements language detection based on domain
 */
class Domain extends Base implements LocalizationInterface
{
    /**
     * @see \Mars\Localization\LocalizationInterface::getCode()
     * {@inheritdoc}
     */
    public function getCode() : string
    {
        if (!$this->app->config->localization->urls) {
            throw new \Exception('Localization urls are not configured. It must be specified in the \'localization_urls\' config value.');
        }

        $host = $_SERVER['HTTP_HOST'] ?? '';
        $host = trim($host);
        if (!$host) {
            throw new \Exception('Cannot detect the language code from the domain because the HTTP_HOST server variable is empty.');
        }

        $code = array_find_key($this->app->config->localization->urls, function($url) use ($host) {
            return $this->app->url->getHost($url) == $host;
        });

        if (!$code || !isset($this->app->lang->codes_list[$code])) {
            return $this->app->lang->default_code;
        }

        return $code;
    }

    /**
     * @see \Mars\Localization\LocalizationInterface::getUrl()
     * {@inheritdoc}
     */    
    public function getUrl(string $code) : string
    {
        return $this->app->config->localization->urls[$code] ?? $this->app->config->url->base;
    }
}