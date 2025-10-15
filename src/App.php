<?php
/**
* The App Class
* @package Mars
*/

namespace Mars;

use Mars\App\LazyLoad;
use Mars\App\Registry;
use Mars\Alerts\Errors;
use Mars\Alerts\Info;
use Mars\Alerts\Warnings;
use Mars\Alerts\Messages;
use Mars\Assets\Minifier;
use Mars\Db\Sql\Sql;
use Mars\Filesystem\Dir;
use Mars\Filesystem\File;
use Mars\Http\Request;
use Mars\Http\Response;
use Mars\Data\Types\ArrayType;
use Mars\Data\Types\ObjectType;
use Mars\Data\Types\StringType;
use Mars\Time\DateTime;
use Mars\Time\Date;
use Mars\Time\Time;
use Mars\Time\Timestamp;
use Mars\Time\Timezone;
use Mars\System\Language;
use Mars\System\Plugins;
use Mars\System\Modules;
use Mars\System\Theme;
use Mars\System\Uri;

/**
 * The App Class
 * The system's main object
 */
#[\AllowDynamicProperties]
class App
{
    use LazyLoad;
    use Registry;

    /**
     * @var string $version The version
     */
    public protected(set) string $version = '1.0.0';

    /**
     * @var Accelerator $accelerator The accelerator object
     */
    #[LazyLoadProperty]
    public Accelerator $accelerator;

    /**
     * @var ArrayType $array The array object
     */
    #[LazyLoadProperty]
    public ArrayType $array;

    /**
     * @var Cli $cli The cli object
     */
    #[LazyLoadProperty]
    public Cli $cli;

    /**
     * @var Cache $cache The cache object
     */
    #[LazyLoadProperty]
    public Cache $cache;

    /**
     * @var Captcha $captcha The captcha object
     */
    #[LazyLoadProperty]
    public Captcha $captcha;

    /**
     * @var Config $config The config object
     */
    #[LazyLoadProperty]
    public Config $config;

    /**
     * @var Crypt $crypt The crypt object
     */
    #[LazyLoadProperty]
    public Crypt $crypt;

    /**
     * @var Data $data The data object
     */
    #[LazyLoadProperty]
    public Data $data;

    /**
     * @var Db $db The db object
     */
    #[LazyLoadProperty]
    public Db $db;

    /**
     * @var Debug $debug The debug object
     */
    #[LazyLoadProperty]
    public Debug $debug;

    /**
     * @var Device $device The device object
     */
    #[LazyLoadProperty]
    public Device $device;

    /**
     * @var Dir $dir The dir object
     */
    #[LazyLoadProperty]
    public Dir $dir;

    /**
     * @var Document $document The document object
     */
    #[LazyLoadProperty]
    public Document $document;

    /**
     * @var Errors $errors The errors object
     */
    #[LazyLoadProperty]
    public Errors $errors;

    /**
     * @var Escape $escape The escape object
     */
    #[LazyLoadProperty]
    public Escape $escape;

    /**
     * @var Filter $filter The filter object
     */
    #[LazyLoadProperty]
    public Filter $filter;

    /**
     * @var File $file The file object
     */
    #[LazyLoadProperty]
    public File $file;

    /**
     * @var Format $format The format object
     */
    #[LazyLoadProperty]
    public Format $format;

    /**
     * @var Html $html The html object
     */
    #[LazyLoadProperty]
    public Html $html;

    /**
     * @var Info $info The info object
     */
    #[LazyLoadProperty]
    public Info $info;

    /**
     * @var Image $image The image object
     */
    #[LazyLoadProperty]
    public Image $image;

    /**
     * @var Json $json The json object
     */
    #[LazyLoadProperty]
    public Json $json;

    /**
     * @var Language $lang The language object
     */
    #[LazyLoadProperty]
    public Language $lang;

    /**
     * @var Log $log The log object
     */
    #[LazyLoadProperty]
    public Log $log;

    /**
     * @var Mail $mail The mail object
     */
    #[LazyLoadProperty]
    public Mail $mail;

    /**
     * @var Minifier $minifier The minifier object
     */
    #[LazyLoadProperty]
    public Minifier $minifier;

    /**
     * @var Memcache $memcache The memcache object
     */
    #[LazyLoadProperty]
    public Memcache $memcache;

    /**
     * @var Messages $messages The messages object
     */
    #[LazyLoadProperty]
    public Messages $messages;

    /**
     * @var Modules $modules The modules object
     */
    #[LazyLoadProperty]
    public Modules $modules;

    /**
     * @var ObjectType $object The object object
     */
    #[LazyLoadProperty]
    public ObjectType $object;

    /**
     * @var Plugins $plugins The plugins object
     */
    #[LazyLoadProperty]
    public Plugins $plugins;

    /**
     * @var Random $random The random object
     */
    #[LazyLoadProperty]
    public Random $random;

    /**
     * @var Reflection $reflection The reflection object
     */
    #[LazyLoadProperty]
    public Reflection $reflection;

    /**
     * @var Response $response The response object
     */
    #[LazyLoadProperty]
    public Response $response;

    /**
     * @var Request $request The request object
     */
    #[LazyLoadProperty]
    public Request $request;

    /**
     * @var Router $router The router object
     */
    #[LazyLoadProperty]
    public Router $router;

    /**
     * @var Screens $screen The screens object
     */
    #[LazyLoadProperty]
    public Screens $screens;

    /**
     * @var Serializer $serializer The serializer object
     */
    #[LazyLoadProperty]
    public Serializer $serializer;

    /**
     * @var Session $session The session object
     */
    #[LazyLoadProperty]
    public Session $session;

    /**
     * @var StringType $string The string object
     */
    #[LazyLoadProperty]
    public StringType $string;

    /**
     * @var Sql $sql The sql object
     */
    #[LazyLoadProperty]
    public Sql $sql;

    /**
     * @var DateTime $datetime The datetime object
     */
    #[LazyLoadProperty]
    public DateTime $datetime;

    /**
     * @var Date $date The date object
     */
    #[LazyLoadProperty]
    public Date $date;

    /**
     * @var Time $time The time object
     */
    #[LazyLoadProperty]
    public Time $time;

    /**
     * @var Timestamp $timestamp The timestamp object
     */
    #[LazyLoadProperty]
    public Timestamp $timestamp;

    /**
     * @var Timezone $timezone The timezone object
     */
    #[LazyLoadProperty]
    public Timezone $timezone;

    /**
     * @var Timer $timer The timer object
     */
    #[LazyLoadProperty]
    public Timer $timer;

    /**
     * @var Text $text The text object
     */
    #[LazyLoadProperty]
    public Text $text;

    /**
     * @var Theme $theme The theme object
     */
    #[LazyLoadProperty]
    public Theme $theme;

    /**
     * @var Ui $ui The ui object
     */
    #[LazyLoadProperty]
    public Ui $ui;

    /**
     * @var Uri $url The url object
     */
    #[LazyLoadProperty]
    public Uri $url;

    /**
     * @var Unescape $unescape The unescape object
     */
    #[LazyLoadProperty]
    public Unescape $unescape;

    /**
     * @var Validator $validator The validator object
     */
    #[LazyLoadProperty]
    public Validator $validator;

    /**
     * @var Warnings $warnings The warnings object
     */
    #[LazyLoadProperty]
    public Warnings $warnings;

    /**
     * @var Web $web The web object
     */
    #[LazyLoadProperty]
    public Web $web;

    /**
     * @var App $instance The app instance
     */
    protected static App $instance;

    /**
     * @var bool $is_cli True if the app is run as a cli script
     */
    public protected(set) bool $is_cli {
        get {
            if (isset($this->is_cli)) {
                return $this->is_cli;
            }

            $this->is_cli = php_sapi_name() == 'cli';

            return $this->is_cli;
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

            $this->is_web = !$this->is_cli;

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
     * @var Url $base_url The url. Eg: http://mydomain.com
     */
    public Url $base_url {
        get => $this->url->base;
    }

    /**
     * @var string $root_url The url. Includes the language code, if languages_multi is enabled. Eg: http://mydomain.com/en
     */
    public string $root_url {
        get => $this->url->root;
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
                        //HTTP_X_FORWARDED_FOR can contain multiple IPs. Use only the first one
                        $this->ip = trim(reset(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
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
     * @var string $public_path The folder where the public files are stored
     */
    public protected(set) string $public_path {
        get {
            if ($this->config->public_path) {
                return $this->config->public_path;
            }

            return $this->public_path;
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
     * @var string $extensions_path The folder where the extensions are stored
     */
    public protected(set) string $extensions_path;

    /**
     * @var string $assets_path The folder where the assets files are stored
     */
    public protected(set) string $assets_path = '';

    /**
     * @var string $assets_url The url of the assets folder
     */
    public protected(set) string $assets_url {
        get {
            if (isset($this->assets_url)) {
                return $this->assets_url;
            }

            $base_url = $this->base_url;
            if ($this->config->url_cdn) {
                $base_url = $this->config->url_cdn;
            }

            $this->assets_url = $base_url . '/' . rawurlencode(basename($this->assets_path));
            
            return $this->assets_url;
        }
    }

    /**
     * @var string $vendor_path The folder where the vendor files are stored
     */
    public protected(set) string $vendor_path;

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
     * Stats set if debug is on
     * @var array $stats The stats array
     */
    public protected(set) array $stats = [
        'content_size' => 0,
        'content_time' => 0,
        'output_size' => 0,
        'output_time' => 0
    ];

    /**
     * @const array DIRS The locations of the used dirs
     */
    public const array DIRS = [
        'app_path' => 'app',
        'config_path' => 'config',
        'libraries_path' => 'libraries',
        'extensions_path' => 'extensions',
        'public_path' => 'public',
        'assets_path' => 'public/assets',
        'vendor_path' => 'vendor',
        'cache_path' => 'data/cache',
        'log_path' => 'data/log',
        'tmp_path' => 'data/tmp',
    ];
    
    /**
     * Protected constructor
     */
    protected function __construct()
    {
        $this->lazyLoad($this);
    }

    /**
     * Boots the app
     */
    public function boot()
    {
        $this->assignDirs(static::DIRS);

        $this->setErrorReporting();

        //send the early hints headers as soon as possible
        if ($this->config->early_hints_enable && $this->config->early_hints_list) {
            $this->response->headers->early_hints->output();
        }
        
        //output the cached content if it exists
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
     * Returns the App object
     * @return App The App object
     */
    public static function obj() : static
    {
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
     * Outputs the content if the page is cached
     */
    protected function outputIfCached()
    {
        if ($this->config->cache_page_enable) {
            $this->cache->pages->output();
        }
    }

    /**
     * Sends a json response
     * @param mixed $content The content to send
     */
    public function send($content)
    {
        $this->response->type = 'json';

        $this->response->output($content);
        die;
    }

    /**
     * Outputs the content
     * @param string $content The content
     */
    public function output(string $content)
    {
        $this->response->type = 'html';

        echo $content;
    }

    /**
     * Starts the output buffering.
     */
    public function start()
    {
        $this->plugins->run('app_start', $this);

        if ($this->config->debug) {
            $this->timer->start('app_content_time');
        }

        ob_start();
    }

    /**
     * Generates the output and sends it to the browser.
     */
    public function end()
    {
        $content = ob_get_clean();
        
        $content = $this->plugins->filter('app_filter_content', $content, $this);

        if ($this->config->debug) {
            $this->stats['content_size'] = strlen($content);
            $this->stats['content_time'] = $this->timer->stop('app_content_time');

            $this->timer->start('app_output_time');
        }

        $output = $this->buildOutput($content);

        $output = $this->plugins->filter('app_filter_output', $output, $this);

        if ($this->config->cache_page_enable) {
            //cache the page output, if caching is enabled
            $this->cache->pages->store($output);
        }

        if ($this->config->debug) {
            $this->stats['output_size'] = strlen($output);
            $this->stats['output_time'] = $this->timer->stop('app_output_time');

            $output.= $this->getDebugOutput();
        }

        $this->response->output($output);

        $this->plugins->run('app_end', $this);
    }

    /**
     * Builds the output
     * @param string $content The content to build the output for
     * @return string The output
     */
    protected function buildOutput(string $content) : string
    {
        if (!$this->config->theme) {
            return $content;
        }

        ob_start();
        $this->theme->renderHeader();
        $this->theme->renderContent($content);
        $this->theme->renderFooter();
        return ob_get_clean();
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

    /**********************SCREENS METHODS***************************************/

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

    /**********************UTILS METHODS***************************************/

    /**
     * Returns a language string
     * Alias for $app->lang->get()
     * @see \Mars\Extensions\Language::get()
     * {@inheritdoc}
     */
    public static function __(string $str, array $replace = [], string $key = '') : string
    {
        return static::$instance->lang->get($str, $replace, $key);
    }

    /**
     * Returns a html escaped language string
     * @param string $str The string index as defined in the languages file
     * @param array $replace Array with key & values to be used for to search & replace, if any
     * @param string $key The key where the string is defined, if any
     * @return string The language string
     */
    public static function __e(string $str, array $replace = [], string $key = '') : string
    {
        $str = static::__($str, $replace, $key);

        return static::$instance->escape->html($str);
    }

    /**
     * Html escapes a string. Shorthand for $app->escape->html($value)
     * @param string $value The value to escape
     * @return string The escaped value
     */
    public static function e(string $value) : string
    {
        return static::$instance->escape->html($value);
    }

    /**
     * Converts a string to a class name. Eg: some-action => SomeAction
     * @param string $str The string to convert
     * @return string The class name
     */
    public static function getClass(string $str) : string
    {
        $str = preg_replace('/[^a-z0-9\- ]/i', '', $str);
        $str = str_replace(' ', '-', $str);

        $str = ucwords($str, '-');
        $str = str_replace('-', '', $str);

        return $str;
    }

    /**
     * Converts a string to a method name. Eg: some-action => someAction
     * @param string $str The string to convert
     * @return string The method name
     */
    public static function getMethod(string $str) : string
    {
        $str = preg_replace('/[^a-z0-9\-_ ]/i', '', $str);
        $str = str_replace('_', '-', $str);
        $str = str_replace(' ', '-', $str);

        $str = ucwords($str, '-');
        $str = lcfirst($str);
        $str = str_replace('-', '', $str);

        return $str;
    }
    
    /********************** DEBUG FUNCTIONS ***************************************/

    /**
     * Does a print_r on $var and outputs <pre> tags
     * @param mixed $var The variable
     * @param bool $die If true, will call die after
     */
    public static function pp($var, bool $die = true)
    {
        echo '<pre>';
        \print_r($var);
        echo '</pre>';

        if ($die) {
            die;
        }
    }

    /**
     * Alias for dd
     * @see App::pp()
     */
    public static function dd($var, bool $die = true)
    {
        static::pp($var, $die);
    }

    /**
     * Prints the debug backtrace
     */
    public static function backtrace()
    {
        echo '<pre>';
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        echo '</pre>';

        die;
    }
}
