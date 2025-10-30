<?php
/**
* The Config Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\Data\AccessorTrait;

/**
 * The Config Class
 * Stores the system's config options
 */
#[\AllowDynamicProperties]
class Config
{
    use Kernel;
    use AccessorTrait;

    /**
     * List of files to load at startup
     */
    protected $load_files = ['config.php', 'app.php'];

    /**
     * @var int $display_errors Controls whether errors should be displayed or not
     */
    public int $display_errors = 1;

    /**
     * @var int $error_reporting The error reporting level
     */
    public int $error_reporting = E_ALL & ~E_NOTICE & ~E_DEPRECATED;

    /**
     * @var bool $log_errors If true, will log all errors to the log files
     */
    public bool $log_errors =  true;

    /**
     * @var int $log_error_reporting The log error reporting level
     */
    public int $log_error_reporting =  E_ALL;

    /**
     * @var string $log_suffix The suffix format of the log files
     */
    public string $log_suffix = 'd-F-Y';

    /**
     * @var string $log_date_format The format of the log dates
     */
    public string $log_date_format = 'm-d-Y H:i:s';

    /**
     * @var string $public_path The public path of the site
     */
    public string $public_path = '';

    /**
     * @var string $url The url of the site
     */
    public string $url = '';

    /**
     * @var string $url_cdn The url of the CDN, to use for the static assets
     */
    public string $url_cdn = '';

    /**
     * @var string $site_name The name of the site
     */
    public string $site_name = '';

    /**
     * @var string $site_slogan The slogan of the site
     */
    public string $site_slogan = '';

    /**
     * @var string|array $site_emails The site emails
     */
    public string|array $site_emails = [];

    /**
     * @var string $open_basedir If specified, will limit the files which are accessible to the specified folder. If the value is true the installation dir is used. If array, will use the specified paths. If false, no limitation is applied
     */
    public bool|string|array $open_basedir = true;

    /**
     * @var array $trusted_proxies The trusted proxies from which we'll accept the HTTP_X_FORWARDED_FOR header
     */
    public array $trusted_proxies = [];

    /**
     * @var bool $debug Set to true to enable debug mode
     */
    public bool $debug = false;

    /**
     * @var string|array $debug_ips If specified, will enable debug only for the listed IPs. Works only if debug is false
     */
    public string|array $debug_ips = [];

    /**
     * @var bool $debug_db Set to true to enable the db debug data
     */
    public bool $debug_db = false;

    /**
     * @var bool $development Set to true to enable development mode
     */
    public bool $development = false;

    /**
     * @var array $development_extensions Runs only the specified extensions in development mode, even if the development mode is not enabled.
     */
    public array $development_extensions = [
        'languages' => false,
        'themes' => false,
        'plugins' => false,
        'modules' => false,
    ];

    /**
     * @var bool $development_display_errors Controls whether errors should be displayed or not, when development is enabled
     */
    public bool $development_display_errors = true;

    /**
     * @var int $development_error_reporting The error reporting level, when development is enabled
     */
    public int $development_error_reporting = E_ALL;

    /**
     * @var string $development_device Will use this value as device, if specified. Valid values: 'desktop', 'tablet', 'smartphone'
     */
    public string $development_device = '';

    /**
     * @var bool $development_routes If true, will not cache the routes, thus will always load them from the files
     */
    public bool $development_routes = false;

    /**
     * @var array $headers Custom headers to be sent
     */
    public array $headers = [];

    /**
     * @var bool $early_hints_enable If true, will enable the early hints headers
     */
    public bool $early_hints_enable = false;

    /**
     * @var array $early_hints_list Custom scripts to be sent as early hints
     */
    public array $early_hints_list = [];
    
    /**
     * @var bool $csp_enable If true, will enable the Content Security Policy headers
     */
    public bool $csp_enable = false;

    /**
     * @var bool $headers_csp_use_nonce If true, will use a nonce for the csp headers
     */
    public bool $csp_use_nonce = false;

    /**
     * @var array $csp_defaults The default Content Security Policy headers
     */
    public array $csp_defaults = [
        'default-src' => "'self'",
        'script-src' => "'self' 'unsafe-inline'",
        'style-src' => "'self' 'unsafe-inline'",
        'object-src' => "'none'"
    ];

    /**
     * @var array $csp_list The Content Security Policy headers
     */
    public array $csp_list = [];

    /**
     * @var array $preload The urls to preload
     */
    public array $preload = [];

    /**
     * @var array $preconnect The urls to preconnect
     */
    public array $preconnect = [];

    /**
     * @var string $db_driver The db driver. Supported drivers: pdo
     */
    public string $db_driver = 'pdo';

    /**
     * @var string|array $db_hostname The db hostname
     */
    public string|array $db_hostname = 'localhost';

    /**
     * @var string|array $db_port	The db port
     */
    public string|array $db_port = '3306';

    /**
     * @var string|array $db_username The db username
     */
    public string|array $db_username = '';

    /**
     * @var string|array $db_password The db password
     */
    public string|array $db_password = '';

    /**
     * @var string|array $db_name The db name
     */
    public string|array $db_name = '';

    /**
     * @var bool|array $db_persistent If true, the db connection will be persistent
     */
    public bool|array $db_persistent = false;

    /**
     * @var string $db_charset The db charset
     */
    public string $db_charset = 'utf8mb4';

    /**
     * Change the driver only if you know what you're doing! Preferably at installation time.
     * You might try to unserialize data which has been serialized with a different driver, otherwise
     * @var string $serializer_driver The serializer driver. Supported options: php, igbinary
     */
    public string $serializer_driver = 'php';

    /**
     * @var string $session_driver The session driver. Supported options: php, memcache, db
     */
    public string $session_driver = 'php';

    /**
     * @var string $session_table The table where sessions are stored if the session_driver=db
     */
    public string $session_table = 'sessions';

    /**
     * @var string $session_prefix Prefix to apply to all session keys
     */
    public string $session_prefix = '';

    /**
     * @var string $session_save_path The path where the sessions will be saved
     */
    public string $session_save_path = '';

    /**
     * @var string $session_name The session name
     */
    public string $session_name = '';

    /**
     * @var int $session_cookie_lifetime The lifetime of the session cookie
     */
    public ?int $session_cookie_lifetime = null;

    /**
     * @var string $session_cookie_path The path of the session cookie
     */
    public ?string $session_cookie_path = null;

    /**
     * @var string $session_cookie_domain The domain of the session cookie
     */
    public ?string $session_cookie_domain = null;

    /**
     * @var bool $session_cookie_secure If true the session cookie will only be sent over secure connections.
     */
    public ?bool $session_cookie_secure = null;

    /**
     * @var bool $session_cookie_httponly If true, the session cookie will be accessible only through the HTTP protocol
     */
    public ?bool $session_cookie_httponly = true;

    /**
     * @var string $session_cookie_samesite //The SameSite attribute of the session cookie. Supported options: Lax, Strict, None
     */
    public ?string $session_cookie_samesite = null;

    /**
     * @var string $crypt_driver The crypt driver. Supported options: openssl, sodium
     */
    public string $crypt_driver = 'openssl';

    /**
     * @var array $crypt_keys The secret keys used for encryption. The key used is the last one in the list
     */
    public array $crypt_keys = [];

    /**
     * @var string $device_driver The device detector driver. Supported options: mobile_detect
     */
    public string $device_driver = 'mobile_detect';

    /**
     * @var bool $accelerator_enable If false, will not start the device detection functionality
     */
    public bool $accelerator_enable = false;

    /**
     * @var string $accelerator_driver The accelerator driver. Supported options: varnish
     */
    public string $accelerator_driver = 'varnish';

    /**
     * @var int $cookie_expire_days The interval, in days, for which the cookies will be valid
     */
    public int $cookie_expire_days = 30;

    /**
     * @var string $cookie_path The path on the server in which the cookie will be available
     */
    public string $cookie_path = '/';

    /**
     * @var string $cookie_domain The domain that the cookie is available to
     */
    public string $cookie_domain = '';

    /**
     * @var bool $cookie_secure If true the cookies will only be sent over secure connections.
     */
    public bool $cookie_secure = false;

    /**
     * @var bool $cookie_httponly If true, the cookie will be accessible only through the HTTP protocol.
     */
    public bool $cookie_httponly = true;

    /**
     * @var string $cookie_samesite The samesite value of the cookies
     */
    public string $cookie_samesite = '';

    /**
     * @var string $language The default language
     */
    public string $language = 'english';

    /**
     * @var string $language_fallback The fallback language for modules
     */
    public string $language_fallback = 'english';

    /**
     * @var string $lang The default theme
     */
    public string $theme = 'mars';

    /**
     * @var bool $memcache_enable If true will enable the memory cache functionality
     */
    public bool $memcache_enable = false;

    /**
     * @var string $memcache_key The memcache key
     */
    public string $memcache_key = '';

    /**
     * @var string $memcache_driver The driver used for memcache. Supported options: memcache, memcached, redis
     */
    public string $memcache_driver = '';

    /**
     * @var string $memcache_host The memcache host
     */
    public string $memcache_host = '127.0.0.1';

    /**
     * @var string $memcache_port The memcache port
     */
    public string $memcache_port = '11211';

    /**
     * @var bool $mail_driver The mail driver. Supported options: phpmailer
     */
    public string $mail_driver = 'phpmailer';

    /**
     * @var bool $mail_from The default email address used as the 'From' address
     */
    public string $mail_from = '';

    /**
     * @var bool $mail_from_name The default name address used as the 'From' name
     */
    public string $mail_from_name = '';

    /**
     * @var bool $mail_smtp Set to true if the mails are to be sent using smtp
     */
    public bool $mail_smtp = false;

    /**
     * @var bool $mail_smtp_host The smtp host
     */
    public string $mail_smtp_host = '';

    /**
     * @var bool $mail_smtp_port The smtp port
     */
    public string $mail_smtp_port = '';

    /**
     * @var bool $mail_smtp_username The smtp username
     */
    public string $mail_smtp_username = '';

    /**
     * @var bool $mail_smtp_password The smtp password
     */
    public string $mail_smtp_password = '';

    /**
     * @var bool $mail_smt_secure The smtp secure connection. Supported options: tls, ssl
     */
    public bool $mail_smtp_secure = false;

    /**
     * @var string $cache_hash The hash algorithm used to generate the cache filenames
     */
    public string $cache_hash = 'sha256';

    /**
     * @var string $cache_driver
     */
    public string $cache_driver = 'file';

    /**
     * @var bool $cache_page_enable If true, will enable the content cache functionality
     */
    public bool $cache_page_enable = false;

    /**
     * @var string $cache_page_driver
     */
    public string $cache_page_driver = 'file';

    /**
     * @var int $cache_page_expire_hours The value - in hours - of the Expires header
     */
    public int $cache_page_expire_hours = 72;

    /**
     * @var bool $cache_page_minify If true will minify the cached content
     */
    public bool $cache_page_minify = true;

    /**
     * @var int $routes_prefix_length The length of the prefix used for routing
     */
    public int $routes_prefix_length = 1;

    /**
     * @var bool $routes_pages_autoload If true, will automatically load all the pages from the app/pages folders as routes
     */
    public bool $routes_pages_autoload = true;

    /**
     * @var string $templates_driver The templates driver. Supported options: mars
     */
    public string $templates_driver = 'mars';

    /**
     * @var string $css_version Version param to apply to all css stylesheets
     */
    public string $css_version = '1';

    /**
     * @var string $javascript_version Version param to apply to all JS scripts
     */
    public string $javascript_version = '1';

    /**
     * @var string $html_allowed_elements The allowed html elements; used when filtering html. If null, all elements are allowed
     */
    public ?string $html_allowed_elements = null;

    /**
     * @var string $html_allowed_attributes The allowed html attributes; used when filtering html
     */
    public ?string $html_allowed_attributes = '*.class,*.style,img.src,img.alt,a.target,a.rel,a.href,a.title';

    /**
     * @var string $html_csrf_name The name of the CSRF hidden field
     */
    public string $html_csrf_name = 'token_csrf';

    /**
     * @var string $request_action_param The name of the action request param
     */
    public string $request_action_param = 'action';

    /**
     * @var string $request_orderby_param The name of the orderby request param
     */
    public string $request_orderby_param = 'order_by';

    /**
     * @var string $request_order_param The name of the order request param
     */
    public string $request_order_param = 'order';

    /**
     * @var string $request_page_param The name of the page request param
     */
    public string $request_page_param = 'page';

    /**
     * @var bool $plugins_enable True, if plugins are enabled
     */

    /**
     * @var int $pagination_max_links The max number of pagination links to show
     */
    public int $pagination_max_links = 10;

    /**
     * @var int $pagination_items_per_page The number of items that should be displayed on each page
     */
    public int $pagination_items_per_page = 30;

    /**
     * @var bool $image_optimize If true, the images will be optimized when processed/uploaded
     */
    public bool $image_optimize = true;

    /**
     * @var string $image_background The background to apply when resizing/cutting.. images...
     */
    public string $image_background_color = 'ffffff';

    /**
     * @var int $image_jpg_quality The quality of jpg images
     */
    public int $image_jpg_quality = 80;

    /**
     * @var string $image_jpg_optimize_command The command used to optimize the jpg images
     */
    public string $image_jpg_optimize_command = 'jpegoptim --strip-all -m 80 {FILENAME}';

    /**
     * @var int $image_png_quality The quality of png images
     */
    public int $image_png_quality = 6;

    /**
     * @var string $image_png_optimize_command The command used to optimize the png images
     */
    public string $image_png_optimize_command = 'convert {FILENAME} -strip {FILENAME}';

    /**
     * @var string $image_gif_optimize_command The command used to optimize gif images
     */
    public string $image_gif_optimize_command = 'gifsicle {FILENAME} -o {FILENAME}';

    /**
     * @var int $image_webp_quality The quality of webp images
     */
    public int $image_webp_quality = -1;

    /**
     * @var int $image_avif_quality The quality of avif images
     */
    public int $image_avif_quality = -1;

    /**
     * @var string $image_watermark_background The color of the watermark's background
     */
    public string $image_watermark_background = '000000';

    /**
     * @var string $image_watermark_opacity The opacity of the watermark
     */
    public string $image_watermark_opacity = '80';

    /**
     * @var bool $image_watermark_text_ttf If true, will render the watermark text using a ttf font
     */
    public bool $image_watermark_text_ttf = false;

    /**
     * @var string $image_watermark_text_font The font used to draw the watermark text
     */
    public string $image_watermark_text_font = '5';

    /**
     * @var string $image_watermark_text_color The color of the watermark text
     */
    public string $image_watermark_text_color = 'ffffff';

    /**
     * @var int $image_watermark_text_size The size of the watermark text
     */
    public string $image_watermark_text_size = '20';

    /**
     * @var string $image_watermark_text_angle The angle of the watermark text
     */
    public string $image_watermark_text_angle = '0';

    /**
     * @var string $image_watermark_padding_top The top/bottom padding of the watermark text
     */
    public string $image_watermark_padding_top = '10';

    /**
     * @var string $image_watermark_padding_left The left/right padding of the watermark text
     */
    public string $image_watermark_padding_left = '15';

    /**
     * @var string $image_watermark_margin_top The top/bottom margin of the watermark text
     */
    public string $image_watermark_margin_top = '20';

    /**
     * @var string $image_watermark_margin_left The left/right margin of the watermark text
     */
    public string $image_watermark_margin_left = '30';

    /**
     * @var bool $captcha_enable If true, will enable captcha
     */
    public bool $captcha_enable = false;

    /**
     * @var string $captcha_driver The captca driver to use
     */
    public string $captcha_driver = 'recaptcha3';

    /**
     * @var string $captcha_recaptcha_site_key The recaptcha public key
     */
    public string $captcha_recaptcha_site_key = '';

    /**
     * @var string $captcha_recaptcha_secret_key The recaptcha private key
     */
    public string $captcha_recaptcha_secret_key = '';

    /**
     * @var float $captcha_recaptcha_min_score The minimum score required to consider the captcha valid
     */
    public float $captcha_recaptcha_min_score = 0.5;

    /**
     * @var array $curl_options The curl options to use when making http requests
     */
    public array $curl_options = [];

    /**
     * @var int $file_max_chars The max number of characters allowed in the name
     */
    public int $file_max_chars = 300;

    /**
     * @var bool $use_is_file_cache If true, will use the is_file cache functionality
     */
    public bool $use_is_file_cache = true;

    /**
     * @var string $title_prefix The prefix of the <title> tag
     */
    public string $title_prefix = '';

    /**
     * @var string $title_suffix The suffix of the <title> tag
     */
    public string $title_suffix = '';

    /**
     * @var string $title_separator The separator of the title parts
     */
    public string $title_separator = ' - ';

    /**
     * @var string $timezone The timezone
     */
    public string $timezone = 'UTC';

    /**
     * Builds the Config object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->init();

        $this->normalize();

        $this->check();
    }

    /**
     * Reads the config settings from the config.php file and the autoload files
     * @return static
     */
    protected function init() : static
    {
        foreach ($this->load_files as $file) {
            $this->load($file);
        }

        return $this;
    }

    /**
     * Reads the config settings from the specified $file and returns it
     * @param string $file The file
     * @return array
     */
    public function read(string $file) : array
    {
        $app = $this->app;

        return require($this->app->config_path . '/' . basename($file));
    }

    /**
     * Writes the specified $data to the specified config $file
     * @param string $file The file
     * @param array $data The data to write
     * @return static
     */
    public function write(string $file, array $data) : static
    {
        $filename = $this->app->config_path . '/' . basename($file);
        if (!is_writable($filename)) {
            throw new \Exception("The config file '{$file}' is not writable");
        }

        $content = "<?php\n\nreturn " . var_export($data, true) . ";";

        file_put_contents($filename, $content);

        return $this;
    }

    /**
     * Reads the config settings from the specified $filename and loads it
     * @param string $filename The filename
     * @return static
     */
    public function load(string $filename) : static
    {
        $this->assign($this->read($filename));

        return $this;
    }

    /**
     * Normalizes the config options
     */
    protected function normalize()
    {
        if ($this->development) {
            $this->css_version = time();
            $this->javascript_version = time();
        }

        if ($this->debug_ips) {
            if (in_array($this->app->ip, $this->debug_ips)) {
                $this->debug = true;
            }
        }
    }

    /**
     * Checks if the proper config options are set
     */
    protected function check()
    {
        if (!$this->url) {
            throw new \Exception("The url config option must be set");
        }
    }
}
