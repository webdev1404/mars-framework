<?php
/**
* The Html Minifier
* @package Mars
*/

namespace Mars\Assets\Minifiers;

/**
 * The Html Minifier
 */
class Html implements MinifierInterface
{
    /**
     * @see MinifierInterface::minify()
     * {@inheritDoc}
     */
    public function minify(string $content) : string
    {
        $minifier = new \voku\helper\HtmlMin;

        return $minifier->minify($content);
    }
}
