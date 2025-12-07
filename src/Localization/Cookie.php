<?php
/**
* The Cookie Localization Driver Interface
* @package Mars
*/

namespace Mars\Localization;

/**
 * The Cookie Localization Driver Interface
 * Implements language detection based on cookie
 */
class Cookie extends Base implements LocalizationInterface
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

            $code = $this->app->request->cookies->get($this->app->config->localization->cookie_name);
            if (isset($this->app->lang->codes_list[$code])) {
                $this->code = $code;
            }

            return $this->code;
        }
    }
}