<?php
/**
* The Tags Class
* @package Mars
*/

namespace Mars\Document\Tags;

use Mars\App\Kernel;
use Mars\Data\MapTrait;

/**
 * The Document Tags Class
 * Stores the custom header html tags used by a document
 */
abstract class Tags implements \Countable, \IteratorAggregate
{
    use Kernel;
    use MapTrait;

    /**
     * @var array $list The list of tags
     */
    protected array $list = [];
    
    /**
     * @internal
     */
    protected static string $property = 'list';

    /**
     * Renders a tag
     * @param string $name The name of the tag
     * @param string $value The value of the tag
     */
    abstract public function renderTag(string $name, string $value);

    /**
     * Renders the tags
     */
    public function render()
    {
        foreach ($this->list as $name => $value) {
            $this->renderTag($name, $value);
        }
    }
}
