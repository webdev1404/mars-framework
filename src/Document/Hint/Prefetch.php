<?php
/**
* The Prefetch Class
* @package Mars
*/

namespace Mars\Document\Hint;

/**
 * The Prefetch Class
 * Class containing the prefetch functionality used by a document
 */
class Prefetch extends Preconnect
{
    /**
     * @var string $rel The rel attribute
     */
    protected string $rel = 'prefetch';

    /**
     * @internal
     */
    protected function load()
    {
    }
}
