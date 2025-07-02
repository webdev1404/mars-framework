<?php
/**
* The Email Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
 * The Email Filter Class
 */
class Email extends Filter
{
    /**
     * @see \Mars\Filters\Filter::email()
     */
    public function filter(string $email) : string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
}
