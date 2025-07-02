<?php
/**
* The Css Minifier
* @package Mars
*/

namespace Mars\Assets\Minifiers;

/**
 * The Css Minifier
 */
class Css implements MinifierInterface
{
    /**
     * @see MinifierInterface::minify()
     * {@inheritdoc}
     */
    public function minify(string $content) : string
    {
        $minifier = new \MatthiasMullie\Minify\CSS;
        $minifier->add($content);

        return $minifier->minify();
    }
}
