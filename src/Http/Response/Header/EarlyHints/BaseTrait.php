<?php
/**
* The Base Early Hints Class
* @package Mars
*/

namespace Mars\Http\Response\Header\EarlyHints;

use Mars\Document\Url;

/**
 * The Base Early Hints Class
 */
trait BaseTrait
{
    /**
     * @var int $links_per_header The number of links to send per header
     */
    protected int $links_per_header = 5;

    /**
     * Returns the links to send as early hints
     * @return array The links to send as early hints
     */
    abstract protected function getLinks() : array;

    /**
     * Returns the headers to send as early hints
     * @return array The headers
     */
    public function getHeaders() : array
    {
        $headers = [];
        $links = $this->getLinks();

        $chunks = array_chunk($links, $this->links_per_header);
        foreach ($chunks as $chunk) {
            $headers[] = 'Link: ' . implode(', ', $chunk);
        }

        return $headers;
    }
}

