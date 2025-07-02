<?php
/**
* The Url Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
 * The Url Filter Class
 */
class Url extends Filter
{
    /**
     * @see \Mars\Filters\Filter::url()
     */
    public function filter(string $url) : string
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}
