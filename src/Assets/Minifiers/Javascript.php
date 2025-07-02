<?php
/**
* The Javascript Minifier
* @package Mars
*/

namespace Mars\Assets\Minifiers;

/**
 * The Javascript Minifier
 */
class Javascript implements MinifierInterface
{
    /**
     * @see MinifierInterface::minify()
     * {@inheritdoc}
     */
    public function minify(string $content) : string
    {
        $minifier = new \MatthiasMullie\Minify\JS;
        $minifier->add($content);

        return $minifier->minify();
    }
}
