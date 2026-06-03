<?php
/**
* The Accelerator Driver Interface
* @package Mars
*/

namespace Mars\Accelerator;

/**
 * The Accelerator Driver Interface
 */
interface AcceleratorInterface
{
    /**
     * Deletes $url from the accelerator's cache
     * @param string $url The url to delete
     * @return bool
     */
    public function delete(string $url) : bool;

    /**
     * Deletes by pattern from the accelerator's cache
     * @param string $pattern The pattern
     * @return bool
     */
    public function deleteByPattern(string $pattern) : bool;

    /**
     * Deletes all the data from the accelerator's cache
     * @return bool
     */
    public function deleteAll() : bool;
}
