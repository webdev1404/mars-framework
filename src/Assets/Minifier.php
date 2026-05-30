<?php
/**
* The Minifier Class
* @package Mars
*/

namespace Mars\Assets;

use Mars\App\Kernel;
use Mars\App\Handlers;
use Mars\Assets\Minifiers\MinifierInterface;

/**
 * The Asset Minifier Class
 * Minifies assets content
 */
class Minifier
{
    use Kernel;

    /**
     * @var array $minifiers_list The list of supported minifiers
     */
    public protected(set) array $minifiers_list = [
        'css' => \Mars\Assets\Minifiers\Css::class,
        'js' => \Mars\Assets\Minifiers\Javascript::class
    ];

    /**
     * @var Handlers $minifiers The minifier handlers
     */
    public protected(set) Handlers $minifiers {
        get {
            if (isset($this->minifiers)) {
                return $this->minifiers;
            }

            $this->minifiers = new Handlers($this->minifiers_list, MinifierInterface::class, $this->app);

            return $this->minifiers;
        }
    }

    /**
     * Minifies css code
     * @param string $code The code to minify
     * @return string The minified code
     */
    public function minifyCss(string $code) : string
    {
        $handler = $this->minifiers->get('css');

        return $handler->minify($code);
    }

    /**
     * Minifies javascript code
     * @param string $code The code to minify
     * @return string The minified code
     */
    public function minifyJs(string $code) : string
    {
        $handler = $this->minifiers->get('js');

        return $handler->minify($code);
    }
}
