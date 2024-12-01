<?php
/**
* The Minifier Class
* @package Mars
*/

namespace Mars\Assets;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Handlers;
use Mars\Assets\Minifiers\DriverInterface;

/**
 * The Asset Minifier Class
 * Minifies assets content
 */
class Minifier
{
    use InstanceTrait;

    /**
     * @var Handlers $minifiers The screens handlers
     */
    public protected(set) Handlers $minifiers {
        get {
            if (isset($this->minifiers)) {
                return $this->minifiers;
            }

            $this->minifiers = new Handlers($this->minifiers_list, DriverInterface::class, $this->app);

            return $this->minifiers;
        }
    }

    /**
     * @var array $minifiers_list The list of supported minifiers
     */
    protected array $minifiers_list = [
        'html' => \Mars\Assets\Minifiers\Html::class,
        'css' => \Mars\AssetsMinifiers\Css::class,
        'js' => \Mars\AssetsMinifiers\Javascript::class            
    ];

    /**
     * Minifies html code
     * @param string $code The code to minify
     * @return string The minified code
     */
    public function minifyHtml(string $code) : string
    {
        $handler = $this->minifiers->get('html');

        return $handler->minify($code);
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
     * Minifies css code
     * @param string $code The code to minify
     * @return string The minified code
     */
    public function minifyJs(string $code) : string
    {
        $handler = $this->minifiers->get('js');

        return $handler->minify($code);
    }
}
