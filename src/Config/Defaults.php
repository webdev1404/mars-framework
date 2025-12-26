<?php
/**
* The Config Defaults Class
* @package Mars
*/

namespace Mars\Config;

/**
 * The Config Defaults Class
 * Class listing the default config options
 */
class Defaults
{
    public static array $settings = [
        // string The url of the site
        'url.base' => '',

        // string CDN url for static resources. If empty, the static resources will be served from the same domain
        'url.cdn' => '',


        // string The name of the site
        'site.name' => '',

        // string The slogan of the site
        'site.slogan' => '',

        // string|array The site emails
        'site.emails' => [],

        // string The default timezone
        'site.timezone' => 'UTC',


        // string The public path of the site. If empty, will use the installation directory
        'site.public_path' => '',


        // bool Set to true to display errors, false to hide them
        'errors.display' => false,

        // int The error level to display
        'errors.reporting' => E_ALL & ~E_NOTICE & ~E_DEPRECATED,


        // bool Set to true to log errors to files
        'log.enable' => true,

        // int The error level to log
        'log.reporting' => E_ALL,

        // string The suffix format of the log files
        'log.suffix' => 'd-F-Y',

        // string The format of the log dates
        'log.date_format' => 'm-d-Y H:i:s',


        // string The default theme
        'theme.name' => 'mars',


        // string The default language
        'language.name' => 'english',

        // string The fallback language for modules
        'language.fallback' => 'english',

        // array The language codes mapping. Format: code => name
        'language.codes' => ['en' => 'english'],


        // string The localization driver. Supported drivers: cookie, domain, path
        'localization.driver' => 'path',

        // array The enabled localization_urls, in the format code => url. Eg: 'en' => 'https://en.mysite.com' or 'en' => 'https://mysite.en'
        'localization.urls' => [],

        // string The name of the cookie used to store the locale when using the cookie localization driver
        'localization.cookie_name' => 'locale',


        // bool Set to false to disable the plugins
        'plugins.enable' => true,


        // bool Set to true to enable debug mode
        'debug.enable' => false,

        // bool Set to true to enable the db debug data
        'debug.db' => false,

        // string|array If specified, will enable debug only for the listed IPs. Works only if debug is false
        'debug.ips' => [],


        // bool Set to true to enable development mode
        'development.enable' => false,

        // array Runs only the specified extensions in development mode, even if the development mode is not enabled
        'development.extensions' => [
            'languages' => false,
            'themes' => false,
            'plugins' => false,
            'modules' => false,
        ],

        // string Will use this value as device, if specified. Valid values: 'desktop', 'tablet', 'smartphone'
        'development.device' => '',

        // bool If true, will reload the routes on each request
        'development.routes' => false,

        // bool Set to true to display errors, false to hide them, if development mode is enabled
        'development.errors.display' => true,

        // int The error level to display, if development mode is enabled
        'development.errors.reporting' => E_ALL,


        // string The db driver. Supported drivers: mysql
        'db.driver' => 'mysql',

        // string|array The db hostname. Can be an array for multiple db servers
        'db.hostname' => 'localhost',

        // string|array The db port. Can be an array for multiple db servers
        'db.port' => '3306',

        // string|array The db username. Can be an array for multiple db servers
        'db.username' => '',

        // string|array The db password. Can be an array for multiple db servers
        'db.password' => '',

        // string|array The db name. Can be an array for multiple db servers
        'db.name' => '',

        // bool|array Set to true to use persistent connections, false to disable them. Can be an array for multiple db servers
        'db.persistent' => false,

        // string The db charset
        'db.charset' => 'utf8mb4',


        // bool If true will enable the memory cache functionality
        'memcache.enable' => false,

        // string The key used for memcache. Must be specific to the project
        'memcache.key' => '',

        // string The driver used for memcache. Supported options: memcached, redis
        'memcache.driver' => 'memcached',

        // string The memcache host
        'memcache.host' => '127.0.0.1',

        // string The memcache host port
        'memcache.port' => '11211',


        // string The driver used for caching. Supported options: file, php, memcache. If memcache is used, memcache.enable must be true
        'cache.driver' => 'file',

        // string The hash algorithm used for caching
        'cache.hash' => 'sha256',

        // string|null The driver used for data caching. If null, will use cache.driver
        'cache.data_driver' => null,

        // string|null The driver used for routes caching. If null, will use cache.driver
        'cache.routes_driver' => null,

        // bool If true, will enable the page cache functionality
        'cache.page.enable' => false,

        // string The driver used for page caching. Supported options: file, memcache
        'cache.page.driver' => 'file',

        // int The value - in hours - of the Expires header
        'cache.page.expire_hours' => 24,

        // bool If true will minify the cached content
        'cache.page.minify' => false,


        // string The driver used to send mail. Supported options: phpmailer
        'mail.driver' => 'phpmailer',

        // string The default email address used as the 'From' address
        'mail.from' => '',

        // string The default name address used as the 'From' name
        'mail.from_name' => '',

        // bool Set to true if the mails are to be sent using smtp
        'mail.smtp.enable' => false,

        // string The smtp host
        'mail.smtp.host' => '',

        // string The smtp port
        'mail.smtp.port' => '',

        // string The smtp username
        'mail.smtp.username' => '',

        // string The smtp password
        'mail.smtp.password' => '',

        // string The smtp secure connection. Supported options: tls, ssl
        'mail.smtp.secure' => '',


        // bool If the value is true the installation dir is used as the basedir. If array, will use the specified paths. If string, will use the specified path. If false, no limitation is applied
        'security.open_basedir' => true,

        // array The trusted proxies from which we'll accept the HTTP_X_FORWARDED_FOR header
        'security.trusted_proxies' => [],


        // array Additional headers to send with each HTTP response
        'http.response.headers.list' => [],

        // bool If true, will enable the Content Security Policy header
        'http.response.headers.csp.enable' => false,

        // bool If true, will use a nonce for the Content Security Policy header
        'http.response.headers.csp.use_nonce' => false,

        // array The default Content Security Policy headers
        'http.response.headers.csp.defaults' => [
            'default-src' => "'self'",
            'script-src' => "'self'",
            'style-src' => "'self'",
            'object-src' => "'none'"
        ],

        // array The Content Security Policy headers
        'http.response.headers.csp.list' => [],


        // array The urls to preload
        'hints.preload' => [
            // The css urls to preload
            'css' => [],
            // The javascript urls to preload
            'javascript' => [],
            // The fonts urls to preload
            'fonts' => [],
            // The images urls to preload
            'images' => []
        ],

        // array The urls to preconnect without the crossorigin attribute
        'hints.preconnect.non_cors' => [],

        // array The urls to preconnect using the crossorigin attribute
        'hints.preconnect.cors' => [],

        // bool If true, will enable the Early Hints functionality
        'hints.early_hints.enable' => false,

        // array The Early Hints headers
        'hints.early_hints.list' => [
            'preload' => [
                // The styles to be sent as early hints
                'style' => [],
                // The scripts to be sent as early hints
                'script' => [],
                // The fonts to be sent as early hints
                'font' => [],
                // The images to be sent as early hints
                'image' => []
            ],
            // The preconnect urls to be sent with the response
            'preconnect' => []
        ],


        // int The interval, in days, for which the cookies will be valid
        'cookie.expire_days' => 30,

        // string The path on the server in which the cookie will be available
        'cookie.path' => '/',

        // string The domain that the cookie is available to
        'cookie.domain' => '',

        // bool If true the cookie will only be sent over secure (HTTPS) connections. Should be enabled in production.
        'cookie.secure' => true,

        // bool If true, the cookie will be accessible only through the HTTP protocol.
        'cookie.httponly' => true,

        // string The SameSite attribute of the cookie. Supported options: Lax, Strict, None
        'cookie.samesite' => '',


        // string The session driver. Supported options: php, memcache, db
        'session.driver' => 'php',

        // string The session table, if the session driver is db. It must be created
        'session.table' => 'sessions',

        // string Prefix to apply to all session keys, if any
        'session.prefix' => '',

        // string The path where the sessions will be saved
        'session.save_path' => '',

        // string The session name
        'session.name' => '',

        // int|null The lifetime of the session cookie, in seconds. If null, the session cookie will expire when the browser is closed
        'session.cookie.lifetime' => null,

        // string|null The path of the session cookie
        'session.cookie.path' => null,

        // string|null The domain of the session cookie
        'session.cookie.domain' => null,

        // bool|null If true the session cookie will only be sent over secure connections.
        'session.cookie.secure' => true,

        // bool|null If true, the session cookie will be accessible only through the HTTP protocol
        'session.cookie.httponly' => true,

        // string|null The SameSite attribute of the session cookie. Supported options: Lax, Strict, None
        'session.cookie.samesite' => null,


        // string The crypt driver. Supported options: openssl, sodium
        'crypt.driver' => 'sodium',

        // array The secret keys used for encryption. The key in use is the last one in the list. Indexes must be strings. For sodium the key must be 32 chars long
        'crypt.keys' => [],


        // string The serializer driver. Supported options: php, json, igbinary
        'serializer.driver' => 'php',


        // bool If true, will enable the captcha functionality
        'captcha.enable' => false,

        // string The captcha driver. Supported options: recaptcha2, recaptcha3
        'captcha.driver' => 'recaptcha3',

        // string The recaptcha site key
        'captcha.recaptcha.site_key' => '',

        // string The recaptcha secret key
        'captcha.recaptcha.secret_key' => '',

        // float The minimum score required to consider the captcha valid (recaptcha3 only)
        'captcha.recaptcha.min_score' => 0.5,


        // bool If true, will enable the accelerators functionality
        'accelerator.enable' => false,

        // string The accelerator driver. Supported options: varnish
        'accelerator.driver' => 'varnish',


        // string The device detector driver. Supported options: mobile_detect
        'device.driver' => 'mobile_detect',


        // string The templates driver. Supported options: mars
        'templates.driver' => 'mars',


        // int The length of the prefix used for routing
        'routes.prefix_length' => 1,

        // bool If true, will automatically load all the pages from the app/pages folders as routes
        'routes.pages_autoload' => true,


        // string The prefix of the <title> tag
        'document.title.prefix' => '',

        // string The suffix of the <title> tag
        'document.title.suffix' => '',

        // string The separator of the title parts
        'document.title.separator' => ' - ',

        // string The version of the css files
        'document.css.version' => '1.0.0',

        // string The version of the javascript files
        'document.javascript.version' => '1.0.0',


        // string The name of the CSRF hidden field
        'html.csrf_name' => 'token-csrf',

        // string|null The allowed html elements; used when filtering html. If null, all elements are allowed
        'html.allowed_elements' => null,

        // string The allowed html attributes; used when filtering html
        'html.allowed_attributes' => '*.class,*.style,img.src,img.alt,a.target,a.rel,a.href,a.title',


        // string The request action parameter name
        'request.action.param' => 'action',

        // string The request orderby parameter name
        'request.orderby.param' => 'order_by',

        // string The request order parameter name
        'request.order.param' => 'order',

        // string The request page parameter name
        'request.page.param' => 'page',


        // int The max number of characters allowed in the name
        'files.max_chars' => 300,

        // bool If true, will use the is_file cache functionality
        'files.cache.use' => true,



        // array The curl options to use when making http requests, if any
        'curl.options' => [],


        // int The max number of pagination links to show
        'pagination.max_links' => 10,

        // int The number of items that should be displayed on each page
        'pagination.items_per_page' => 30,


        // bool If true, the images will be optimized when processed/uploaded
        'image.optimize' => false,

        // string The background to apply when resizing/cutting images
        'image.background_color' => 'ffffff',

        // int The quality of jpg images
        'image.jpg.quality' => 80,

        // string The command used to optimize the jpg images
        'image.jpg.optimize_command' => 'jpegoptim --strip-all -m 80 {FILENAME}',

        // int The quality of png images [0-9]
        'image.png.quality' => 6,

        // string The command used to optimize the png images
        'image.png.optimize_command' => 'convert {FILENAME} -strip {FILENAME}',

        // string The command used to optimize gif images
        'image.gif.optimize_command' => 'gifsicle {FILENAME} -o {FILENAME}',

        // int The quality of webp images
        'image.webp.quality' => -1,

        // int The quality of avif images
        'image.avif.quality' => -1,

        // string The color of the watermark's background
        'image.watermark.background' => '000000',

        // string The opacity of the watermark
        'image.watermark.opacity' => '80',

        // bool If true, will render the watermark text using a ttf font
        'image.watermark.text.ttf' => false,

        // string The font used to draw the watermark text
        'image.watermark.text.font' => '5',

        // string The color of the watermark text
        'image.watermark.text.color' => 'ffffff',

        // int The size of the watermark text
        'image.watermark.text.size' => '20',

        // string The angle of the watermark text
        'image.watermark.text.angle' => '0',

        // string The top/bottom padding of the watermark text
        'image.watermark.padding.top' => '10',

        // string The left/right padding of the watermark text
        'image.watermark.padding.left' => '15',

        // string The top/bottom margin of the watermark text
        'image.watermark.margin.top' => '20',

        // string The left/right margin of the watermark text
        'image.watermark.margin.left' => '30',


        // array The drivers configuration
        'drivers' => [
            'accelerators' => [],
            'cacheable' => [],
            'captcha' => [],
            'crypt' => [],
            'db' => [],
            'device' => [],
            'images' => [],
            'localization' => [],
            'mail' => [],
            'memcache' => [],
            'response' => [],
            'serializer' => [],
            'session' => [],
            'templates' => []
        ],
    ];
}
