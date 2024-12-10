<?php
/**
* The Prefetch Class
* @package Mars
*/

namespace Mars\Document;

/**
 * The Prefetch Class
 * Class containing the prefetch functionality used by a document
 */
class Prefetch extends Preload
{
    /**
     * @var string $rel The rel attribute of the preload
     */
    protected string $rel = 'prefetch';    
}
