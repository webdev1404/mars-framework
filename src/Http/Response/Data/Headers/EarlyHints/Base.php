<?php
/**
* The Base Early Hints Class
* @package Mars
*/

namespace Mars\Http\Response\Data\Headers\EarlyHints;

use Mars\App\Kernel;

/**
 * The Base Early Hints Class
 */
abstract class Base
{
    use Kernel;

    /**
     * @var array $urls Array with all the urls to use
     */
    protected array $urls = [];

    /**
     * @internal
     */
    protected static string $property = 'urls';

    /**
     * @var int $links_per_header The number of links to send per header
     */
    protected int $links_per_header = 5;

    /**
     * Processes the list of URLs to send as early hints
     * @param array $urls The list of URLs
     * @return array The processed list of URLs
     */
    public function processUrls(array $urls) : array
    {
        //convert all urls to a ['url' => url, 'crossorigin' => bool] format
        foreach ($urls as $key => $url) {
            if (!is_array($url)) {
                $urls[$key] = ['url' => $url, 'crossorigin' => false];
            }
        }

        //remove the duplicates
        $seen = [];
        $urls_list = [];
        
        foreach ($urls as $url) {
            $key = $url['url'] . (string)$url['crossorigin'];

            if (!isset($seen[$key])) {
                $seen[$key] = true;

                $urls_list[] = $url;
            }
        }

        return $urls_list;
    }
}

