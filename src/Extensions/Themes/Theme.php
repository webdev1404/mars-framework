<?php
/**
* The Theme Class
* @package Mars
*/

namespace Mars\Extensions\Themes;

use Mars\App;
use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
use Mars\App\HiddenProperty;
use Mars\Document;
use Mars\Extensions\Extension;
use Mars\Extensions\Extensions;
use Mars\Extensions\Themes\Links\Css;
use Mars\Extensions\Themes\Links\Javascript;
use Mars\Extensions\Themes\Links\Favicon;
use Mars\Extensions\Themes\Links\Fonts;
use Mars\Extensions\Themes\Links\Images;
use Mars\Extensions\Abilities\ConfigTrait;
use Mars\Extensions\Abilities\LanguagesTrait;

/**
 * The Theme Class
 */
class Theme extends Extension
{
    use LazyLoad;
    use ConfigTrait;
    use LanguagesTrait;

    /**
     * @internal
     */
    public const array DIRS = [
        ...parent::DIRS,
        'images' => 'images',
        'css' => 'css',
        'fonts' => 'fonts',
        'js' => 'js',
        'libraries' => 'libraries',
    ];

    /**
     * @var Document $document The document object
     */
    public Document $document {
        get => $this->app->document;
    }

    /**
     * @var Css $css The css object
     */
    #[LazyLoadProperty]
    public Css $css;

    /**
     * @var Javascript $js The javascript object
     */
    #[LazyLoadProperty]
    public Javascript $js;

    /**
     * @var Favicon $favicon The favicon object
     */
    #[LazyLoadProperty]
    public Favicon $favicon;
    
    /**
     * @var Fonts $fonts The fonts object
     */
    #[LazyLoadProperty]
    public Fonts $fonts;

    /**
     * @var Images $images The images object
     */
    #[LazyLoadProperty]
    public Images $images;

    /**
     * @internal
     */
    protected static array $lazyload_add_this = [
       Css::class,
       Javascript::class,
       Favicon::class,
       Fonts::class,
       Images::class,
    ];

    /**
     * @var string $images_path The path for the theme's images folder
     */
    public protected(set) string $images_path {
        get {
            if (isset($this->images_path)) {
                return $this->images_path;
            }

            $this->images_path = $this->assets_path . '/' . static::DIRS['images'];

            return $this->images_path;
        }
    }

    /**
     * @var string $images_url The url of the theme's images folder
     */
    public protected(set) string $images_url {
        get {
            if (isset($this->images_url)) {
                return $this->images_url;
            }

            $this->images_url = $this->assets_url . '/' . rawurlencode(static::DIRS['images']);

            return $this->images_url;
        }
    }

    /**
     * @var string $css_path The path for the theme's css folder
     */
    public protected(set) string $css_path {
        get {
            if (isset($this->css_path)) {
                return $this->css_path;
            }

            $this->css_path = $this->assets_path . '/' . static::DIRS['css'];

            return $this->css_path;
        }
    }
    /**
     * @var string $css_url The url of the theme's css folder
     */
    public protected(set) string $css_url {
        get {
            if (isset($this->css_url)) {
                return $this->css_url;
            }

            $this->css_url = $this->assets_url . '/' . rawurlencode(static::DIRS['css']);

            return $this->css_url;
        }
    }

    /**
     * @var string $js_path The path for the theme's js folder
     */
    public protected(set) string $js_path {
        get {
            if (isset($this->js_path)) {
                return $this->js_path;
            }

            $this->js_path = $this->assets_path . '/' . static::DIRS['js'];

            return $this->js_path;
        }
    }

    /**
     * @var string $js_url The url of the theme's js folder
     */
    public protected(set) string $js_url {
        get {
            if (isset($this->js_url)) {
                return $this->js_url;
            }

            $this->js_url = $this->assets_url . '/' . rawurlencode(static::DIRS['js']);

            return $this->js_url;
        }
    }

    /**
     * @var string $library_path The path for the currently loaded library. It will be set when loading a library with loadLibrary() method.
     */
    public protected(set) string $library_path = '';

    /**
     * @var string $library_url The url for the currently loaded library. It will be set when loading a library with loadLibrary() method.
     */
    public protected(set) string $library_url = '';

    /**
     * @var string $templates_path The path for the theme's templates folder
     */
    public protected(set) string $templates_path {
        get {
            if (isset($this->templates_path)) {
                return $this->templates_path;
            }

            $this->templates_path = $this->path . '/' . static::DIRS['templates'];

            return $this->templates_path;
        }
    }

    /**
     * @internal
     */
    protected static string $manager_class = Themes::class;

    /**
     * @internal
     */
    protected static ?Extensions $manager_instance = null;

    /**
     * @internal
     */
    protected static string $type = 'theme';

    /**
     * @internal
     */
    protected static string $base_dir = 'themes';

    /**
     * @internal
     */
    protected static string $base_namespace = "\\Themes";

    /**
     * Builds the theme
     * @param string $name The name of the exension
     * @param array $params The params passed to the theme, if any
     * @param App $app The app object
     */
    public function __construct(string $name, array $params = [], ?App $app = null)
    {
        $this->lazyLoad($app);

        parent::__construct($name, $params, $app);
    }

    /**
     * Prepares the theme. It will be called before output the theme
     */
    public function prepare()
    {
        $app = $this->app;

        if (is_file($this->path . '/prepare.php')) {
            include($this->path . '/prepare.php');
        }
    }

    /**
     * Loads a library
     * @param string $name The name of the library
     * @return static
     */
    public function loadLibrary(string $name, $base_dir = 'node_modules') : static
    {
        $this->library_path = $this->assets_path . '/' . static::DIRS['libraries'] . '/' . $base_dir . '/' . $name;
        $this->library_url = $this->assets_url . '/' . urlencode(static::DIRS['libraries']) . '/' . urlencode($base_dir) . '/' . $name;

        require($this->library_path . '/boot.php');

        $this->library_path = '';
        $this->library_url = '';

        return $this;
    }
}
