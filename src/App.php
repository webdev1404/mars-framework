<?php
/**
* The App Class
* @package Mars
*/

namespace Mars;

use Mars\Mvc\Controller;

/**
 * The App Class
 * The system's main object
 */
class App extends \stdClass
{
    use AppUtilsTrait;

    /**
     * @var float $version The version
     */
    public readonly string $version;

    /**
     * @var bool $is_bin True if the app is run as a bin script
     */
    public readonly bool $is_bin;

    /**
     * @var bool $is_https True if the page is loaded with https, false otherwise
     */
    public readonly bool $is_https;

    /**
     * @var string $scheme The page's scheme: http:// or https://
     */
    public readonly string $scheme;

    /**
     * @var string $method The request method. get/post.
     */
    public readonly string $method;

    /**
     * @var string $protocol The server protocol
     */
    public readonly string $protocol;

    /**
     * @var bool $is_https2 True if the page is loaded using HTTP/2
     */
    public readonly bool $is_http2;

    /**
     * @var string $url The url. Eg: http://mydomain.com/mars
     */
    public readonly string $url;

    /**
     * @var string $url_static The url from where static content is served
     */
    public string $url_static = '';

    /**
     * @var string $page_url The url of the current page determined from $_SERVER. Doesn't include the QUERY_STRING
     */
    public readonly string $page_url;

    /**
     * @var string $full_url The url of the current page determined from $_SERVER. Includes the QUERY_STRING
     */
    public readonly string $full_url;

    /**
     * @var string $ip The ip used to make the request
     */
    public readonly string $ip;

    /**
     * @var string $useragent The useragent
     */
    public readonly string $useragent;

    /**
     * @var bool $development If true, the system is run in development mode
     */
    public bool $development = false;

    /**
     * @var string $path The location on the disk where the site is installed Eg: /var/www/mysite
     */
    public readonly string $path;

    /**
     * @var string $log_path The folder where the log files are stored
     */
    public readonly string $log_path;

    /**
     * @var string $tmp_path The folder where the temporary files are stored
     */
    public readonly string $tmp_path;

    /**
     * @var string $cache_path The folder where the cache files are stored
     */
    public readonly string $cache_path;

    /**
     * @var string $libraries_path The folder where the php libraries are stored
     */
    public readonly string $libraries_path;

    /**
     * @var string $cache_url The url of the cache folder
     */
    public readonly string $cache_url;

    /**
     * @var string $extensions_path The folder where the extensions are stored
     */
    public readonly string $extensions_path;

    /**
     * @var string $extensions_url The url of the extensions folder
     */
    public readonly string $extensions_url;

    /**
     * @var bool $is_homepage Set to true if the homepage is currently displayed
     */
    public bool $is_homepage = false;
    
    /**
     * @var string $namespace The root namespace
     */
    public string $namespace = "App\\";

    /**
     * @var string $extensions_namespace The root namespace for extensions
     */
    public string $extensions_namespace = "App\\Extensions\\";

    /**
     * @var App $instance The app instance
     */
    protected static App $instance;

    /**
     * @var array $objects_map The map of the objects defined in ../app-map.php we can lazy load
     */
    protected array $objects_map = [
        'accelerator' => Accelerator::class,
        'bin' => Bin::class,
        'cache' => Cache::class,
        'config' => Config::class,
        'db' => Db::class,
        'debug' => Debug::class,
        'device' => Device::class,
        'dir' => Dir::class,
        'document' => Document::class,
        'errors' => \Mars\Alerts\Errors::class,
        'escape' => Escape::class,
        'filter' => Filter::class,
        'file' => File::class,
        'format' => Format::class,
        'html' => Html::class,
        'info' => \Mars\Alerts\Info::class,
        'json' => Json::class,
        'lang' => \Mars\System\Language::class,
        'log' => Log::class,
        'memcache' => Memcache::class,
        'messages' => \Mars\Alerts\Messages::class,
        'plugins' => \Mars\System\Plugins::class,
        'random' => Random::class,
        'registry' => Registry::class,
        'response' => Response::class,
        'request' => Request::class,
        'router' => Router::class,
        'screens' => Screens::class,
        'serializer' => Serializer::class,
        'session' => Session::class,
        'sql' => Sql::class,
        'datetime' => \Mars\Time\DateTime::class,
        'date' => \Mars\Time\Date::class,
        'time' => \Mars\Time\Time::class,
        'timestamp' => \Mars\Time\Timestamp::class,
        'timer' => Timer::class,
        'text' => Text::class,
        'theme' => \Mars\System\Theme::class,
        'ui' => Ui::class,
        'uri' => Uri::class,
        'unescape' => Unescape::class,
        'validator' => Validator::class,
        'warnings' => \Mars\Alerts\Warnings::class,
    ];

    /**
     * @const array DIRS The locations of the used dirs
     */
    public const array DIRS = [
        'log_path' => 'log',
        'tmp_path' => 'tmp',
        'cache_path' => 'cache',
        'libraries_path' => 'libraries',
        'extensions_path' => 'extensions'
    ];

    /**
     * @const array URLS The locations of the used urls
     */
    public const array URLS = [
        'extensions' => 'extensions',
        'cache' => 'cache'
    ];

    /**
     * @const array EXTENSIONS_DIR The locations of the used extensions subdirs
     */
    public const array EXTENSIONS_DIRS = [
        'languages' => 'languages',
        'templates' => 'templates',
        'modules' => 'modules',

        'images' => 'images',
        'controllers' => 'controllers',
        'models' => 'models',
        'views' => 'views'
    ];

    /**
     * @const array MOBILE_DORS The locations of the used mobile subdirs
     */
    public const array MOBILE_DIRS = [
        'desktop' => 'desktop',
        'mobile' => 'mobile',
        'tablets' => 'tablets',
        'smartphones' => 'smartphones'
    ];

    /**
     * @const array CACHE_DIRS The locations of the cache subdirs
     */
    public const array CACHE_DIRS = [
        'templates' => 'templates',
        'css' => 'css',
        'js' => 'js'
    ];

    /**
     * @const array FILE_EXTENSIONS Common file extensions
     */
    public const array FILE_EXTENSIONS = [
        'templates' => 'php'
    ];

    /**
     * @var array The objects container
     */
    protected static array $container = [
        'mail' => '\Mars\Mail',
        'minifier' => '\Mars\Helper\Minifier',
        'http_request' => '\Mars\Http\Request'
    ];

    /**
     * Protected constructor
     */
    protected function __construct()
    {
        $this->version = '1.0';
        $this->path = $this->getPath();
        $this->is_bin = $this->getIsBin();

        if (!$this->is_bin) {
            $this->is_https = $this->getIsHttps();
            $this->scheme = $this->getScheme();
            $this->method = $this->getRequestMethod();
            $this->protocol = $this->getProtocol();
            $this->page_url = $this->getPageUrl();
            $this->full_url = $this->getFullUrl();
            $this->is_http2 = $this->getIsHttp2();
            $this->ip = $this->getIp();
            $this->useragent = $this->getUseragent();
        }

        $this->development = $this->config->development;

        $this->setDirs();
        $this->setUrls();

        if (!$this->is_bin) {
            $this->is_homepage = $this->isHomepage();
        }
    }

    /**
     * Instantiates the App object
     * @return App The app instance
     */
    public static function instantiate() : App
    {
        static::$instance = new static;

        return static::$instance;
    }

    /**
     * Bootstraps the app
     */
    public function boot()
    {
        //load the plugins
        $this->plugins->load();
    }

    /**
     * Magic method to get an object. It will lazy load the object, if defined in the app-map.php file
     * @param string $name The name of the object
     * @return object The object
     */
    public function __get(string $name)
    {
        if (isset($this->objects_map[$name])) {
            if (is_string($this->objects_map[$name])) {
                $class = $this->objects_map[$name];
                $this->$name = new $class($this);
            } elseif(is_callable($this->objects_map[$name])) {
                $this->name = $this->objects_map[$name]();
            }
        }
        if (!isset($this->$name)) {
            throw new \Exception("Invalid object: {$name}");
        }
       
        return $this->$name;
    }

    /**
     * Adds objects to lazy loading objects map
     * @param array $objects_map The objects map
     * @return static
     */
    public function addObjectsMap(array $objects_map) : static
    {
        $this->objects_map = array_merge($this->objects_map, $objects_map);

        return $this;
    }

    /**
     * Returns an object
     * @param string $name The name of the object. If empty, the app object is returned
     * @return object The object
     */
    public static function get(string $name = '', ...$params)
    {
        if (!$name) {
            return static::$instance;
        }
        if (isset(static::$container[$name])) {
            if (is_string($name)) {
                $class = static::$container[$name];
                return new $class(static::$instance);
            } elseif(is_callable(static::$container[$name])) {
                $func = static::$container[$name];
                return $func();
            } else {
                return static::$container[$name];
            }
        } else {
            return static::create($name, ...$params);
        }

        return null;
    }

    /**
     * Create a new instance of a class and store it in the App container.
     * @param string $class The fully qualified class name
     * @param mixed ...$params The parameters to be passed to the class constructor
     * @return object The created instance of the class
     */
    public static function create(string $class, ...$params) : object
    {
        $params = array_merge($params, [static::$instance]);
        $object = new $class(...$params);
       
        static::set($class, $object);

        return $object;
    }

    /**
     * Sets a container object
     * @param string $name The name of the object
     * @param string|object|callable $object The object
     * @return static
     */
    public static function set(string $name, string|object|callable $object) : static
    {
        static::$container[$name] = $object;

        return static::$instance;
    }

    /**
     * Returns the location on the disk where the site is installed
     * @return string
     */
    protected function getPath() : string
    {
        return dirname(__DIR__, 4);
    }

    /**
     * Returns true if this is a bin script
     * @return bool
     */
    protected function getIsBin() : bool
    {
        if (php_sapi_name() == 'cli') {
            return true;
        }

        return false;
    }

    /**
     * Returns true if this is a https request
     * @return bool
     */
    protected function getIsHttps() : bool
    {
        if (empty($_SERVER['HTTPS'])) {
            return false;
        }

        return true;
    }

    /**
     * Returns the scheme: http/https
     * @return string
     */
    protected function getScheme() : string
    {
        if ($this->is_https) {
            return 'https://';
        }

        return 'http://';
    }

    /**
     * Returns the request method: get/post/put
     * @return string
     */
    protected function getRequestMethod() : string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Returns the server protocol
     */
    protected function getProtocol() : string
    {
        return $_SERVER['SERVER_PROTOCOL'];
    }

    /**
     * Returns true if the protocol is HTTP/2
     */
    protected function getIsHttp2() : bool
    {
        $version = (int)str_replace('HTTP/', '', $this->protocol);

        return $version == 2;
    }

    /**
     * Returns the page url of the current page
     * @return string
     */
    protected function getPageUrl() : string
    {
        $request_uri = explode('?', $_SERVER["REQUEST_URI"], 2);

        $url = $this->scheme . $_SERVER['SERVER_NAME'] . $request_uri[0];

        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Returns the full url of the current page
     * @return string
     */
    protected function getFullUrl() : string
    {
        $url = $this->scheme . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Returns true if the loaded page is the homepage
     * @return bool
     */
    protected function isHomepage() : bool
    {
        if ($this->page_url == $this->url || $this->page_url == $this->url . '/') {
            return true;
        } elseif ($this->page_url == $this->url . '/index.php') {
            return true;
        }
        
        return false;
    }

    /**
     * Prepares the base dirs
     */
    protected function setDirs()
    {
        $this->assignDirs(static::DIRS);
    }

    /**
     * Sets the urls
     */
    protected function setUrls()
    {
        $this->url = $this->config->url;
        $this->url_static = $this->url;

        if ($this->config->url_static) {
            $this->url_static = $this->config->url_static;
        }

        $this->assignUrls(static::URLS);
    }

    /**
     * Returns the static url of a dir
     * @param string $url The url key as defined in App::URLS
     * @return string The static url
     */
    public function getStaticUrl(string $url) : string
    {
        return $this->url_static . '/' . rawurlencode(static::URLS[$url]);
    }

    /**
     * Returns the user's IP
     * @return string The ip
     */
    public function getIp() : string
    {
        if (!empty($this->ip)) {
            return $this->ip;
        }

        $ip = $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if (in_array($_SERVER['REMOTE_ADDR'], $this->config->trusted_proxies)) {
                //HTTP_X_FORWARDED_FOR can contain multiple IPs. Use only the last one
                $proxy_ip = trim(end(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));

                if (filter_var($proxy_ip, FILTER_VALIDATE_IP)) {
                    return $proxy_ip;
                }
            }
        }

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }

        throw new \Exception("Invalid IP: {$ip}");
    }

    /**
     * Returns the user's useragent
     * @return string The useragent
     */
    public function getUseragent() : string
    {
        if (!empty($this->useragent)) {
            return $this->useragent;
        }

        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Assigns the dirs as app properties
     * @param array $dirs The dirs to assign
     */
    protected function assignDirs(array $dirs)
    {
        foreach ($dirs as $name => $dir) {
            $this->$name = $this->path . '/' . $dir;
        }
    }

    /**
     * Assigns the urls as app properties
     * @param array $urls The urls to assign
     * @param string $base_url The base url
     * @param string $prefix Prefix to place before the url
     * @param string $suffix Suffix to append to the url, to the url, if any
     */
    protected function assignUrls(array $urls, string $base_url = '', string $prefix = '', string $suffix = 'url')
    {
        if (!$base_url) {
            $base_url = $this->url;
        }
        
        if ($prefix) {
            $prefix.= '_';
        }
        if ($suffix) {
            $suffix = '_' . $suffix;
        }

        foreach ($urls as $name => $url) {
            $name = $prefix . $name . $suffix;

            $this->$name = $base_url . '/' . rawurlencode($url);
        }
    }

    /**
     * Returns true if no errors have been generated
     * @return bool
     */
    public function success() : bool
    {
        if ($this->errors->count()) {
            return false;
        }

        return true;
    }

    /**
     * Outputs the content
     * @param mixed $content The content
     * @param string $type The content's type: html/ajax
     */
    public function output($content, string $type = '')
    {
        echo $this->response->setType($type)->get($content);
    }

    /**
     * Starts the output buffering.
     */
    public function start()
    {
        $this->plugins->run('app_start', $this);

        if ($this->config->debug) {
            $this->timer->start('app_output_content');
        }

        ob_start();
    }

    /**
     * Ends the output and sets $this->app->content
     */
    public function end()
    {
        $content = ob_get_clean();

        $content = $this->plugins->filter('app_filter_content', $content, $this);

        $output = $this->getOutput($content);

        $this->plugins->run('app_end', $output);

        //cache the output, if required
        $this->cache->store($output);
        
        $output = $this->response->output($output);
    }

    /**
     * Builds the output from the content
     * @param string $content The content
     * @return string The output
     */
    protected function getOutput(string $content) : string
    {
        if ($this->response->getType() != 'html') {
            return $content;
        }

        ob_start();
        $this->theme->renderHeader();
        $this->theme->renderContent($content);
        $this->theme->renderFooter();
        $output = ob_get_clean();

        $output = $this->plugins->filter('app_filter_output', $output, $this);

        if ($this->config->debug) {
            $output.= $this->getDebugOutput($output);
        }

        return $output;
    }

    /**
     * Returns the debug output, if debug is on
     * @param string $output The generated output
     * @return string
     */
    protected function getDebugOutput(string $output) : string
    {
        $this->debug->info['output_size'] = strlen($output);
        $this->debug->info['output_content_time'] = $this->timer->end('app_output_content');
        $this->debug->info['execution_time'] = $this->timer->getExecutionTime();

        ob_start();
        $this->debug->output();
        return ob_get_clean();
    }

    /**
     * Outputs debug information.
     * @param string $output The generated output
     * @return void
     */
    public function outputDebug(string $output = '')
    {
        echo $this->getDebugOutput($output);
    }

    /**
     * Renders/Outputs a template
     * @param string $template The name of the template
     * @param array $vars Vars to pass to the template, if any
     */
    public function render(string $template, array $vars = [])
    {
        $this->start();

        $this->theme->render($template, $vars);

        $this->end();
    }

    /**
     * Renders a controller
     * @param Controller $controller The controller
     * @param string $action The action to perform. If null, it will be read from the request data
     */
    public function renderController(Controller $controller, ?string $action = null)
    {
        if ($action === null) {
            $action = $this->request->getAction();
        }

        $this->start();

        $controller->dispatch($action);

        $this->end();
    }

    /**********************SCREENS FUNCTIONS***************************************/

    /**
     * Displays a fatal error screen
     * @param $text The error's text
     * @param bool $escape_html If true will escape the error message
     */
    public function fatalError(string $text, ?bool $escape_html = null)
    {
        $this->screens->fatalError($text, $escape_html);
    }

    /**
     * Displays an error screen
     * @param string $text The error's text
     * @param string $title The error's title, if any
     * @param bool $escape_html If true will escape the title and error message
     */
    public function error(string $text, string $title = '', bool $escape_html = true)
    {
        $this->screens->error($text, $title, $escape_html);
    }

    /**
     * Displayes a message screen
     * @param string $text The text of the message
     * @param string $title The title of the message, if any
     * @param bool $escape_html If true will escape the title and message
     */
    public function message(string $text, string $title = '', bool $escape_html = true)
    {
        $this->screens->message($text, $title, $escape_html);
    }

    /**
     * Displays the Permission Denied screen
     * @see \Mars\Document\Screen::permissionDenied()
     */
    public function permissionDenied()
    {
        $this->screens->permissionDenied();
    }

    /**
     * Redirects the user to the specified page
     * @param string $url The url where the user will be redirected
     */
    public function redirect(string $url = '')
    {
        if (!$url) {
            $url = $this->url . '/';
        }

        header('Location: ' . $url);
        die;
    }
}
