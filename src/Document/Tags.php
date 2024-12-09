<?php
/**
* The Tags Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App\InstanceTrait;
use Mars\Lists\ListTrait;

/**
 * The Document Tags Class
 * Stores the custom header html tags used by a document
 */
abstract class Tags
{
    use InstanceTrait;
    use ListTrait;

    /**
     * Outputs a tag
     * @param string $name The name of the tag
     * @param string $value The value of the tag
     */
    abstract public function outputTag(string $name, string $value);

    /**
     * Outputs the tags
     */
    public function output()
    {
        foreach ($this->list as $name => $value) {
            $this->outputTag($name, $value);
        }
    }
}
