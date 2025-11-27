<?php
/**
* The Localization Driver Interface
* @package Mars
*/

namespace Mars\Localization;
/**
 * The Localization Driver Interface
 */
interface LocalizationInterface
{
    /**
     * Retrieves the language code
     * @return string The language code
     * @throws \Exception If the language code cannot be determined
     */
    public function getCode() : string;

    /**
     * Retrieves the base URL for the specified language code
     * @param string $code The language code
     * @return string The URL for the specified language code
     */
    public function getUrl(string $code) : string;

    /**
     * Retrieves the request URI
     * @return string|null The request URI or null if not applicable
     */
    public function getRequestUri() : ?string;
}