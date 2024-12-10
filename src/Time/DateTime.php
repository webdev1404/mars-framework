<?php
/**
* The DateTime Class
* @package Mars
*/

namespace Mars\Time;

/**
 * The DateTime Class
 * DateTime related functions
 */
class DateTime extends Base
{
    /**
     * {@inheritdoc}
     */
    protected string $format {
        get => $this->app->lang->datetime_format;
    }
}
