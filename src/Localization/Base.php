<?php
/**
 * The Base Localization Class
 * @package Mars
 */

namespace Mars\Localization;

use Mars\App\Kernel;

/**
 * The Base Localization Class
 */
class Base
{
    use Kernel;

    /**
     * @var string $code The detected language code
     */
    protected string $code = '';

    /**
     * @see \Mars\Localization\LocalizationInterface::getCode()
     * {@inheritDoc}
     */
    public function getCode() : string
    {
        if (!$this->app->lang->multi) {
            return $this->app->lang->default_code;
        }

        if ($this->code) {
            return $this->code;
        }

        return $this->app->lang->default_code;
    }

    /**
     * @see \Mars\Localization\LocalizationInterface::getUrl()
     * {@inheritDoc}
     */
    public function getUrl(string $code) : string
    {
        return $this->app->config->url->base;
    }

    /**
     * @see \Mars\Localization\LocalizationInterface::getRequestUri()
     * {@inheritDoc}
     */
    public function getRequestUri() : ?string
    {
        return null;
    }
}
