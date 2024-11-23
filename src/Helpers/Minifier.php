<?php
/**
* The Minifier Class
* @package Mars
*/

namespace Mars\Helpers;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Handlers;

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
    public readonly Handlers $minifiers;

    /**
     * @var array $minifiers_list The list of supported minifiers
     */
    protected array $minifiers_list = [
        'html' => '\Mars\Minifiers\Html',
        'css' => '\Mars\Minifiers\Css',
        'js' => '\Mars\Minifiers\Javascript'
    ];

    /**
     * Constructs the screens object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->minifiers = new Handlers($this->minifiers_list, $this->app);
    }

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
