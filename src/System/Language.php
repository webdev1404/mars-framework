<?php
/**
* The System's Language Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;

/**
 * The System's Language Class
 */
class Language extends \Mars\Extensions\Language
{
    /**
     * @var string $encoding The encoding of the language
     */
    public string $encoding = 'UTF-8';

    /**
     * @var string $code The language's code
     */
    public string $code = 'en';

    /**
     * @var string $datetime_format The format in which a timestamp will be displayed
     */
    public string $datetime_format = 'm/d/Y h:i:s a';

    /**
     * @var string $date_format The format in which a date will be displayed
     */
    public string $date_format = 'm/d/Y';

    /**
     * @var string $time_format The format in which the time of the day will be displayed
     */
    public string $time_format = 'h:i:s a';

    /**
     * @var string datetime_picker_format The format of the datetime picker
     */
    public string $datetime_picker_format = 'm-d-Y H:i:s';

    /**
     * @var string date_picker_format The format of the date picker
     */
    public string $date_picker_format = 'm-d-Y';

    /**
     * @var string time_picker_format The format of the time picker
     */
    public string $time_picker_format = 'H:i:s';

    /**
     * @var string $decimal_separator The language's decimal_separator
     */
    public string $decimal_separator = '.';

    /**
     * @var string $thousands_separator The language's thousands_separator
     */
    public string $thousands_separator = ',';

    /**
     * Builds the language
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        if (!$app->config->language) {
            return;
        }

        parent::__construct($app->config->language, $app);

        include($this->path . '/init.php');

        $this->loadFile('errors');
    }
}
