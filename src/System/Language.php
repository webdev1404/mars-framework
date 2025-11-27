<?php
/**
* The System's Language Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\App\Drivers;
use Mars\Localization\LocalizationInterface;
use Mars\Extensions\Languages\Language as BaseLanguage;

/**
 * The System's Language Class
 */
class Language extends BaseLanguage
{
    /**
     * @var array $supported_drivers The supported drivers
     */
    protected array $supported_drivers = [
        'cookie' => \Mars\Localization\Cookie::class,
        'domain' => \Mars\Localization\Domain::class,
        'path' => \Mars\Localization\Path::class,
    ];

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, LocalizationInterface::class, 'localization', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var LocalizationInterface $driver The driver object
     */
    public protected(set) ?LocalizationInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->localization_driver);

            return $this->driver;
        }
    }

    /**
     * @var string $name The name of the language
     */
    public protected(set) string $name {
        get {
            if (isset($this->name)) {
                return $this->name;
            }

            $this->name = $this->codes_list[$this->code] ?? null;
            if (!$this->name) {
                throw new \Exception('No language set in the config file.');
            }

            return $this->name;
        }
    }

    /**
     * @var bool $multi If true, multi-language mode is enabled
     */
    public protected(set) bool $multi {
        get {
            if (isset($this->multi)) {
                return $this->multi;
            }

            $this->multi = false;
            if (count($this->codes_list) > 1) {
                $this->multi = true;
            }

            return $this->multi;
        }
    }

    /**
     * @var array $codes_list The list of available languages for multi-language support
     */
    public protected(set) array $codes_list {
        get {
            if (isset($this->codes_list)) {
                return $this->codes_list;
            }

            $this->codes_list = $this->app->config->language_codes;

            return $this->codes_list;
        }
    }

    /**
     * @var array $codes The list of available language codes, if multi-language is enabled
     */
    public protected(set) array $codes {
        get {
            if (isset($this->codes)) {
                return $this->codes;
            }

            $this->codes = array_keys($this->codes_list);

            return $this->codes;
        }
    }

    /**
     * @var string $code The language's code
     */
    public protected(set) string $code {
        get {
            if (isset($this->code)) {
                return $this->code;
            }

            $this->code = $this->driver->getCode();

            return $this->code;
        }
    }

    /**
     * @var string $default_code The code of the default language, if multi-language is enabled
     */
    public protected(set) string $default_code {
        get {
            if (isset($this->default_code)) {
                return $this->default_code;
            }

            $this->default_code = array_find_key($this->codes_list, fn ($value) => $value === $this->app->config->language);
            if (!$this->default_code) {
                throw new \Exception("Default language code not found");
            }

            return $this->default_code;
        }
    }

    /**
     * @var string $url The base URL for the current language
     */
    public protected(set) string $url {
        get {
            if (isset($this->url)) {
                return $this->url;
            }

            $this->url = $this->driver->getUrl($this->code);

            return $this->url;
        }
    }

    /**
     * @var string $request_uri The request URI
     */
    public protected(set) ?string $request_uri {
        get {
            if (isset($this->request_uri)) {
                return $this->request_uri;
            }

            $this->request_uri = $this->driver->getRequestUri();

            return $this->request_uri;
        }
    }

    /**
     * @var bool $can_use_fallback If true, the language can use the fallback language
     */
    public protected(set) bool $can_use_fallback {
        get {
            if (isset($this->can_use_fallback)) {
                return $this->can_use_fallback;
            }

            $this->can_use_fallback = false;
            if ($this->app->config->language_fallback) {
                //if the current language or it's parent language is not the fallback language, we can use the fallback.
                if ($this->name !== $this->app->config->language_fallback) {
                    if (!$this->parent || $this->parent->name !== $this->app->config->language_fallback) {
                        $this->can_use_fallback = true;
                    }
                }
            }

            return $this->can_use_fallback;
        }
    }

    /**
     * @var BaseLanguage $fallback The fallback language
     */
    public protected(set) ?BaseLanguage $fallback {
        get {
            if (isset($this->fallback)) {
                return $this->fallback;
            }

            $this->fallback = null;
            if ($this->can_use_fallback) {
                $this->fallback = new BaseLanguage($this->app->config->language_fallback, [], $this->app);
            }

            return $this->fallback;
        }
    }

    /**
     * @var string $parent_name The name of the parent language, if any
     */
    public protected(set) string $parent_name = '';

    /**
     * @var BaseLanguage $parent The parent language, if any
     */
    public protected(set) ?BaseLanguage $parent {
        get {
            if (isset($this->parent)) {
                return $this->parent;
            }

            $this->parent = null;
            if ($this->parent_name) {
                $this->parent = new BaseLanguage($this->parent_name, [], $this->app);
            }

            return $this->parent;
        }
    }

    /**
     * Builds the language
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        parent::__construct($this->name, [], $this->app);
    }

    /**
     * Prepares the language
     */
    public function prepare()
    {
        if (!$this->parent) {
            return;
        }

        $this->parent->init();

        $properties = ['lang', 'datetime_format', 'date_format', 'time_format', 'datetime_picker_format', 'datetime_picker_desc', 'date_picker_format', 'date_picker_desc', 'time_picker_format', 'time_picker_desc', 'decimal_separator', 'thousands_separator'];
        foreach ($properties as $property) {
            $this->$property = $this->parent->$property;
        }
    }

    /**
     * @see \Mars\Extensions\Language::loadFile()
     * {@inheritdoc}
     */
    public function loadFile(string $file, ?string $key = null) : static
    {
        $key ??= $file;

        //load first the fallback language file, if enabled
        if ($this->fallback) {
            if ($this->fallback->isFile($file)) {
                $this->loadFilename($this->fallback->getFilename($file), $key);
            }
        }

        //load the parent language file, if any
        if ($this->parent) {
            if ($this->parent->isFile($file)) {
                $this->loadFilename($this->parent->getFilename($file), $key);
            }
        }

        return parent::loadFile($file, $key);
    }

    /**
     * @see \Mars\Extensions\Language::getTemplateFilename()
     * {@inheritdoc}
     */
    public function getTemplateFilename(string $template) : ?string
    {
        $template_filename = parent::getTemplateFilename($template);
        if ($template_filename) {
            return $template_filename;
        }

        if ($this->parent) {
            $template_filename = $this->parent->getTemplateFilename($template);
            if ($template_filename) {
                return $template_filename;
            }
        }

        if ($this->fallback) {
            $template_filename = $this->fallback->getTemplateFilename($template);
            if ($template_filename) {
                return $template_filename;
            }
        }

        return null;
    }
}
