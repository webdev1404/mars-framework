<?php
/**
* The List Group Class
* @package Mars
*/

namespace Mars\Data;

use Mars\App\Kernel;

/**
 * The List Group Class
 * Contains a list of elements, grouped by type
 */
class ListGroup
{
    use Kernel;
    use ListGroupTrait;

    /**
     * @var array $list The list of elements
     */
    public protected(set) array $list = [];

    /**
     * @internal
     */
    protected static string $property = 'list';
}
