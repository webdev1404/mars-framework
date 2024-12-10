<?php
/**
* The Date Class
* @package Mars
*/

namespace Mars\Time;

/**
 * The Date Class
 * Date related functions
 */
class Date extends Base
{
    /**
     * {@inheritdoc}
     */
    protected string $format {
        get => $this->app->lang->date_format;
    }
}
