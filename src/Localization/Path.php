<?php
/**
* The Path Localization Driver Interface
* @package Mars
*/

namespace Mars\Localization;

/**
 * The Path Localization Driver Interface
 * Implements language detection based on path in the URL
 */
class Path extends Base implements LocalizationInterface
{
    /**
     * @var string $code The detected language code
     */
    protected string $code {
        get {
            if (isset($this->code)) {
                return $this->code;
            }

            $this->code = '';

            $code = array_first($this->app->url->parts_full);
            if (isset($this->app->lang->codes_list[$code])) {
                $this->code = $code;
            }

            return $this->code;
        }
    }

    /**
     * @see \Mars\Localization\LocalizationInterface::getUrl()
     * {@inheritdoc}
     */    
    public function getUrl(string $code) : string
    {
        if ($code == $this->app->lang->default_code) {
            return $this->app->config->url;
        }

        return $this->app->config->url . '/' . $code;
    }

    /**
     * @see \Mars\Localization\LocalizationInterface::getRequestUri()
     * {@inheritdoc}
     */
    public function getRequestUri() : ?string
    {
        if (!$this->code) {
            return null;
        }

        $parts = $this->app->url->parts_full;
        array_shift($parts);

        return implode('/', $parts);
    }
}