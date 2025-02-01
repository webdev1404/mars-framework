<?php
/**
* The App Class
* @package Mars
*/

namespace Mars;

use Mars\App\UtilsTrait;
use Mars\Alerts\Errors;
use Mars\Alerts\Info;
use Mars\Alerts\Warnings;
use Mars\Alerts\Messages;
use Mars\Assets\Minifier;
use Mars\LazyLoad\GhostTrait;
use Mars\LazyLoad\ProxyTrait;
use Mars\Mvc\Controller;
use Mars\Time\DateTime;
use Mars\Time\Date;
use Mars\Time\Time;
use Mars\Time\Timestamp;
use Mars\Time\Timezone;
use Mars\System\Language;
use Mars\System\Plugins;
use Mars\System\Theme;

/**
 * The App Class
 * The system's main object
 */
#[\AllowDynamicProperties]
class App
{
    use UtilsTrait;
    use GhostTrait;
    use ProxyTrait;

    /**
     * @var string $version The version
     */
    public string $version = '1.0.0';

    /**
     * @var Accelerator $accelerator The accelerator object
     */
    #[LazyLoad]
    public Accelerator $accelerator;

    /**
     * @var Bin $bin The bin object
     */
    #[LazyLoad]
    public Bin $bin;

    /**
     * @var Cache $cache The cache object
     */
    #[LazyLoad]
    public Cache $cache;

    /**
     * @var Captcha $captcha The captcha object
     */
    #[LazyLoad]
    public Captcha $captcha;

    /**
     * @var Config $config The config object
     */
    #[LazyLoad]
    public Config $config;

    /**
     * @var Db $db The db object
     */
    #[LazyLoad]
    public Db $db;

    /**
     * @var Debug $debug The debug object
     */
    #[LazyLoad]
    public Debug $debug;

    /**
     * @var Device $device The device object
     */
    #[LazyLoad]
    public Device $device;

    /**
     * @var Dir $dir The dir object
     */
    #[LazyLoad]
    public Dir $dir;

    /**
     * @var Document $document The document object
     */
    #[LazyLoad]
    public Document $document;

    /**
     * @var Errors $errors The errors object
     */
    #[LazyLoad]
    public Errors $errors;

    /**
     * @var Escape $escape The escape object
     */
    #[LazyLoad]
    public Escape $escape;

    /**
     * @var Filter $filter The filter object
     */
    #[LazyLoad]
    public Filter $filter;

    /**
     * @var File $file The file object
     */
    #[LazyLoad]
    public File $file;

    /**
     * @var Format $format The format object
     */
    #[LazyLoad]
    public Format $format;

    /**
     * @var Html $html The html object
     */
    #[LazyLoad]
    public Html $html;

    /**
     * @var Http $http The http object
     */
    #[LazyLoad]
    public Http $http;

    /**
     * @var Info $info The info object
     */
    #[LazyLoad]
    public Info $info;

    /**
     * @var Image $image The image object
     */
    #[LazyLoad]
    public Image $image;

    /**
     * @var Json $json The json object
     */
    #[LazyLoad]
    public Json $json;

    /**
     * @var Language $lang The language object
     */
    #[LazyLoad]
    public Language $lang;

    /**
     * @var Log $log The log object
     */
    #[LazyLoad]
    public Log $log;

    /**
     * @var Mail $mail The mail object
     */
    #[LazyLoad]
    public Mail $mail;

    /**
     * @var Minifier $minifier The minifier object
     */
    #[LazyLoad]
    public Minifier $minifier;

    /**
     * @var Memcache $memcache The memcache object
     */
    #[LazyLoad]
    public Memcache $memcache;

    /**
     * @var Messages $messages The messages object
     */
    #[LazyLoad]
    public Messages $messages;

    /**
     * @var Plugins $plugins The plugins object
     */
    #[LazyLoad]
    public Plugins $plugins;

    /**
     * @var Random $random The random object
     */
    #[LazyLoad]
    public Random $random;

    /**
     * @var Registry $registry The registry object
     */
    #[LazyLoad]
    public Registry $registry;

    /**
     * @var Response $response The response object
     */
    #[LazyLoad]
    public Response $response;

    /**
     * @var Request $request The request object
     */
    #[LazyLoad]
    public Request $request;

    /**
     * @var Router $router The router object
     */
    #[LazyLoad]
    public Router $router;

    /**
     * @var Screens $screens The screens object
     */
    #[LazyLoad]
    public Screens $screens;

    /**
     * @var Serializer $serializer The serializer object
     */
    #[LazyLoad]
    public Serializer $serializer;

    /**
     * @var Session $session The session object
     */
    #[LazyLoad]
    public Session $session;

    /**
     * @var Sql $sql The sql object
     */
    #[LazyLoad]
    public Sql $sql;

    /**
     * @var DateTime $datetime The datetime object
     */
    #[LazyLoad]
    public DateTime $datetime;

    /**
     * @var Date $date The date object
     */
    #[LazyLoad]
    public Date $date;

    /**
     * @var Time $time The time object
     */
    #[LazyLoad]
    public Time $time;

    /**
     * @var Timestamp $timestamp The timestamp object
     */
    #[LazyLoad]
    public Timestamp $timestamp;

    /**
     * @var Timezone $timezone The timezone object
     */
    #[LazyLoad]
    public Timezone $timezone;

    /**
     * @var Timer $timer The timer object
     */
    #[LazyLoad]
    public Timer $timer;

    /**
     * @var Text $text The text object
     */
    #[LazyLoad]
    public Text $text;

    /**
     * @var Theme $theme The theme object
     */
    #[LazyLoad]
    public Theme $theme;

    /**
     * @var Ui $ui The ui object
     */
    #[LazyLoad]
    public Ui $ui;

    /**
     * @var Uri $uri The uri object
     */
    #[LazyLoad]
    public Uri $uri;

    /**
     * @var Unescape $unescape The unescape object
     */
    #[LazyLoad]
    public Unescape $unescape;

    /**
     * @var Validator $validator The validator object
     */
    #[LazyLoad]
    public Validator $validator;

    /**
     * @var Warnings $warnings The warnings object
     */
    #[LazyLoad]
    public Warnings $warnings;

    /**
     * @var App $instance The app instance
     */
    protected static App $instance;

    /**
     * @var bool $is_bin True if the app is run as a bin script
     */
    public protected(set) bool $is_bin {        
        get {
            if (isset($this->is_bin)) {
                return $this->is_bin;
            }

            $this->is_bin = php_sapi_name() == 'cli';

            return $this->is_bin;
        }
    }

    /**
     * @var bool $is_web True if the app is run as a web script
     */
    public protected(set) bool $is_web {        
        get {
            if (isset($this->is_web)) {
                return $this->is_web;
            }

            $this->is_web = !$this->is_bin;

            return $this->is_web;
        }
    }

    /**
     * @var bool $is_https True if the page is loaded with https, false otherwise
     */
    public protected(set) bool $is_https {
        get {
            if (isset($this->is_https)) {
                return $this->is_https;
            }

            $this->is_https = false;
            if ($this->is_web) {
                if (isset($_SERVER['HTTPS'])) {
                	$this->is_https = true;
                }
            }

            return $this->is_https;
        }
    }

    /**
     * @var string $scheme The page's scheme: http:// or https://
     */
    public protected(set) string $scheme {
        get {
            if (isset($this->scheme)) {
                return $this->scheme;
            }

            $this->scheme = '';
            if ($this->is_web) {
                $this->scheme = $this->is_https ? 'https://' : 'http://';
            }

            return $this->scheme;
        }
    }

    /**
     * @var int $protocol The server protocol
     */
    public protected(set) int $protocol {
        get {
            if (isset($this->protocol)) {
                return $this->protocol;
            }

            $this->protocol = 0;
            if ($this->is_web) {
                $this->protocol = (int)str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']);
            }

            return $this->protocol;
        }
    }

    /**
     * @var string $base_url The url. Eg: http://mydomain.com/mars
     */
    public protected(set) string $base_url {
        get {
            if (isset($this->base_url)) {
                return $this->base_url;
            }

            $this->base_url = $this->config->url;

            return $this->base_url;
        }
    }

    /**
     * @var string $base_url_static The url from where static content is served
     */
    public protected(set) string $base_url_static {
        get {
            if (isset($this->base_url_static)) {
                return $this->base_url_static;
            }

            $this->base_url_static = $this->base_url;
            if ($this->config->url_static) {
                $this->base_url_static = $this->config->url_static;
            }
            

            return $this->base_url_static;
        }
    }

    /**
     * @var string $url The current url. Does not include the query string
     */
    public protected(set) string $url {
        get {
            if (isset($this->url)) {
                return $this->url;
            }

            $this->url = $this->uri->getRoot($this->base_url) . $this->uri->getPath($_SERVER["REQUEST_URI"] ?? '');
            $this->url = filter_var($this->url, FILTER_SANITIZE_URL);

            return $this->url;
        }
    }

    /**
     * @var string $url_full The full url. Includes the query string
     */
    public protected(set) string $url_full {
        get {
            if (isset($this->url_full)) {
                return $this->url_full;
            }

            $query_string = $_SERVER['QUERY_STRING'] ?? '';
            if ($query_string) {
                $this->url_full = $this->url . '?' . $query_string;
            } else {
                $this->url_full = $this->url;
            }

            $this->url_full = filter_var($this->url_full, FILTER_SANITIZE_URL);

            return $this->url_full;
        }
    }

    /**
     * @var string $ip The ip used to make the request
     */
    public protected(set) string $ip {
        get {
            if (isset($this->ip)) {
                return $this->ip;
            }

            $this->ip = '';
            if ($this->is_web) {
                $this->ip = $_SERVER['REMOTE_ADDR'];

                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    if (in_array($this->ip, $this->config->trusted_proxies)) {

                        //HTTP_X_FORWARDED_FOR can contain multiple IPs. Use only the last one
                        $this->ip = trim(end(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));                       
                    }
                }

                if (!filter_var($this->ip, FILTER_VALIDATE_IP)) {
                    throw new \Exception("Invalid IP: {$ip}");
                }
            }

            return $this->ip;
        }
    }

    /**
     * @var string $useragent The useragent
     */
    public protected(set) string $useragent {
        get {
            if (isset($this->useragent)) {
                return $this->useragent;
            }

            $this->useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';

            return $this->useragent;
        }
    }

    /**
     * @var string $base_path The location on the disk where the site is installed Eg: /var/www/mysite
     */
    public protected(set) string $base_path {
        get {
            if (isset($this->base_path)) {
                return $this->base_path;
            }

            $this->base_path = dirname(__DIR__, 4);

            return $this->base_path;
        }
    }

    /**
     * @var string $config_path The folder where the config files are stored
     */
    public protected(set) string $config_path;

    /**
     * @var string $log_path The folder where the log files are stored
     */
    public protected(set) string $log_path;

    /**
     * @var string $tmp_path The folder where the temporary files are stored
     */
    public protected(set) string $tmp_path;

    /**
     * @var string $cache_path The folder where the cache files are stored
     */
    public protected(set) string $cache_path;

    /**
     * @var string $libraries_path The folder where the php libraries are stored
     */
    public protected(set) string $libraries_path;

    /**
     * @var string $app_path The folder where the app files are stored
     */
    public protected(set) string $app_path;

    /**
     * @var string $cache_url The url of the cache folder
     */
    public protected(set) string $cache_url;

    /**
     * @var string $extensions_path The folder where the extensions are stored
     */
    public protected(set) string $extensions_path;

    /**
     * @var string $extensions_url The url of the extensions folder
     */
    public protected(set) string $extensions_url;

    /**
     * @var string $app_url The url of the app folder
     */
    public protected(set) string $app_url;

    /**
     * @var string $nonce The nonce token
     */
    public protected(set) string $nonce {
        get {
            if (isset($this->nonce)) {
                return $this->nonce;
            }

            $this->nonce = $this->random->getString(32);

            return $this->nonce;
        }
    }

    /**
     * @var bool $is_homepage Set to true if the homepage is currently displayed
     */
    public protected(set) bool $is_homepage {
        get {
            if (isset($this->is_homepage)) {
                return $this->is_homepage;
            }

            $this->is_homepage = false;
            if ($this->is_web) {
                if ($this->url == $this->base_url) {
                    $this->is_homepage = true;
                } elseif ($this->url == $this->base_url . '/index.php') {
                    $this->is_homepage = true;
                }
            }            

            return $this->is_homepage;
        }
    }

    /**
     * @var bool $development If true, the system is run in development mode
     */
    public bool $development {
        get {
            if (isset($this->development)) {
                return $this->development;
            }

            $this->development = $this->config->development;

            return $this->development;
        }
    }

    /**
     * @var string $namespace The root namespace
     */
    public protected(set) string $namespace = "\\App";

    /**
     * @var string $extensions_namespace The root namespace for extensions
     */
    public protected(set) string $extensions_namespace = "\\App\\Extensions";

    /**
     * @var array $objects The objects added to the app
     */
    protected static array $objects = [];

    /**
     * @var array $objects_map The map of the objects we can return with get()
     */
    protected static array $objects_map = [];

    /**
     * @var int $output_size The size of the output. Set if debug is on
     */
    public protected(set) int $output_size = 0;

    /**
     * @var int $output_time The time it took to generate the output. Set if debug is on
     */
    public protected(set) float $output_time = 0;

    /**
     * @const array DIRS The locations of the used dirs
     */
    public const array DIRS = [
        'config_path' => 'config',
        'log_path' => 'log',
        'tmp_path' => 'tmp',        
        'cache_path' => 'cache',
        'libraries_path' => 'libraries',
        'extensions_path' => 'extensions',
        'app_path' => 'app',
    ];

    /**
     * @const array URLS The locations of the used urls
     */
    public const array URLS = [
        'extensions_url' => 'extensions',
        'cache_url' => 'cache',
        'app_url' => 'app',
    ];

    /**
     * @const array EXTENSIONS_DIR The locations of the used extensions subdirs
     */
    public const array EXTENSIONS_DIRS = [
        'modules' => 'modules',
        'languages' => 'languages',
        'templates' => 'templates',

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
        'data' => 'data',
        'templates' => 'templates',
        'css' => 'css',
        'js' => 'js'
    ];

    /**
     * @const array FILE_EXTENSIONS Common file extensions
     */
    public const array FILE_EXTENSIONS = [
        'templates' => 'php',
        'languages' => 'php'
    ];

    /**
     * Protected constructor
     */
    protected function __construct()
    {
        $this->lazyLoad($this);
        
        $this->assignDirs(static::DIRS);
        $this->assignUrls(static::URLS);

        $this->setErrorReporting();
    }

    /**
     * Boots the app
     */
    public function boot()
    {
        //send the early hints headers as soon as possible
        if ($this->config->headers_early_hints_enable && $this->config->headers_early_hints) {
            $this->response->headers->early_hints->output();
        }
        
        $this->outputIfCached();
    }

    /**
     * Instantiates the App object
     * @return App The app instance
     */
    public static function instantiate() : App
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }
        
        static::$instance = new static;

        return static::$instance;
    }

    /**
     * Loads objects from the list
     * @param array $list The list of the objects to add
     * @return static
     */
    public function load(array $list) : static
    {
        $ghost_list = [];
        $proxy_list = [];
        foreach ($list as $name => $class) {
            if (is_callable($class)) {
                $proxy_list[$name] = $class;
            } else {
                $ghost_list[$name] = $class;
            }
        }

        if ($ghost_list) {
            $this->lazyLoadByGhost($ghost_list, $this);
        }
        if ($proxy_list) {
            $this->lazyLoadByProxy($proxy_list, $this);
        }

        return $this;
    }

    /**
     * Returns the App object or an object added to the App
     * @param string $name The name of the object. If empty, will return the App object
     * @return App The App object
     */
    public static function get(string $name = '') : static
    {
        if ($name) {
            if (isset(static::$objects[$name])) {
                return static::$objects[$name];
            }

            static::$objects[$name] = static::resolve($name);

            return static::$objects[$name];
            
        }

        return static::$instance;
    }

    /**
     * Resolves an object
     * @param string $name The name of the object
     * @return object The object
     * @throws \Exception If the object can't be resolved
     */
    protected function resolve(string $name) : object
    {
        if (isset(static::$objects_map[$name])) {
            return static::getObject(static::$objects_map[$name]);
        }

        throw new \Exception("Can't resolve object. Invalid name: {$name}");
    }

    /**
     * Adds an object to the App
     * @param string|array $name The name of the object
     * @param string|callable $class The class of the object of the callable which returns it
     * @return static
     */
    public static function set(string|array $name, string|callable $class = '') : static
    {
        if (is_array($name)) {
            foreach ($name as $name2 => $class2) {
                static::$objects_map[$name2] = $class2;
            }
        } else {
            static::$objects_map[$name] = $class;
        }

        return static::$instance;
    }
    
    /**
     * Removes an object from the App
     * @param string $name The name of the object
     * @return static
     */
    public function delete(string $name) : static
    {
        if (isset(static::$objects[$name])) {
            unset(static::$objects[$name]);
            unset(static::$objects_map[$name]);
        }

        return static::$instance;
    }

    /**
     * Sets the error reporting
     */
    protected function setErrorReporting()
    {
        $display_errors = $this->config->display_errors;
        $error_reporting = $this->config->error_reporting;

        if ($this->config->development) {
            $display_errors = $this->config->development_display_errors;
            $error_reporting = $this->config->development_error_reporting;
        }

        ini_set('display_errors', $display_errors);
        error_reporting($error_reporting);
    }

    /**
     * Assigns the dirs as app properties
     * @param array $dirs The dirs to assign
     */
    protected function assignDirs(array $dirs)
    {
        foreach ($dirs as $name => $dir) {
            $this->$name = $this->base_path . '/' . $dir;
        }
    }

    /**
     * Assigns the urls as app properties
     * @param array $urls The urls to assign
     */
    protected function assignUrls(array $urls)
    {
        foreach ($urls as $name => $url) {
            $this->$name = $this->base_url . '/' . rawurlencode($url);
        }
    }

    /**
     * Returns the static url of a dir
     * @param string $url The url key as defined in App::URLS
     * @return string The static url
     */
    public function getStaticUrl(string $url) : string
    {
        return $this->base_url_static . '/' . rawurlencode(static::URLS[$url]);
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
        $this->response->type = $type;

        echo $this->response->get($content);
    }

    /**
     * Outputs the content if the page is cached
     */
    protected function outputIfCached()
    {
        if ($this->config->cache_page_enable) {
            $this->cache->pages->output();
        }
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
     * Ends the output and sets $this-content
     */
    public function end()
    {
        $content = ob_get_clean();

        $content = $this->plugins->filter('app_filter_content', $content, $this);

        $output = $this->getOutput($content);

        $this->plugins->run('app_end', $output);

        //cache the output, if required
        if ($this->config->cache_page_enable) {
            $this->cache->pages->store($output);
        }
        
        $output = $this->response->output($output);
    }

    /**
     * Builds the output from the content
     * @param string $content The content
     * @return string The output
     */
    protected function getOutput(string $content) : string
    {
        if ($this->response->type != 'html') {
            return $content;
        }

        if ($this->config->theme) {
            ob_start();
            $this->theme->renderHeader();
            $this->theme->renderContent($content);
            $this->theme->renderFooter();
            $output = ob_get_clean();
        } else {
            $output = $content;
        }

        $output = $this->plugins->filter('app_filter_output', $output, $this);        

        if ($this->config->debug) {
            $this->output_size = strlen($output);
            $this->output_time = $this->timer->stop('app_output_content');

            $output.= $this->getDebugOutput();
        }

        return $output;
    }

    /**
     * Returns the debug output, if debug is on
     * @return string
     */
    protected function getDebugOutput() : string
    {
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
            $url = $this->base_url . '/';
        }

        header('Location: ' . $url);
        die;
    }
}
