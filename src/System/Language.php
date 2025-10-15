<?php
/**
* The System's Language Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\Extensions\Languages\Language as BaseLanguage;

/**
 * The System's Language Class
 */
class Language extends BaseLanguage
{
    /**
     * @var string $encoding The encoding of the language
     */
    public string $encoding = 'UTF-8';

    /**
     * @var string $lang The language's html lang attribute
     */
    public string $lang = '';

    /**
     * @var string $datetime_format The format in which a timestamp will be displayed
     */
    public string $datetime_format = 'm/d/Y h:i:s a';

    /**
     * @var string $date_format The format in which a date will be displayed
     */
    public string $date_format = 'm/d/Y';

    /**
     * @var string $time_format The format in which the time of the day will be displayed
     */
    public string $time_format = 'h:i:s a';

    /**
     * @var string datetime_picker_format The format of the datetime picker
     */
    public string $datetime_picker_format = 'm-d-Y H:i:s';

    /**
     * @var string datetime_picker_desc The description of the datetime picker
     */
    public string $datetime_picker_desc = 'mm-dd-yyyy hh:mm:ss';

    /**
     * @var string date_picker_format The format of the date picker
     */
    public string $date_picker_format = 'm-d-Y';

    /**
     * @var string date_picker_desc The description of the date picker
     */
    public string $date_picker_desc = 'mm-dd-yyyy';

    /**
     * @var string time_picker_format The format of the time picker
     */
    public string $time_picker_format = 'H:i:s';

    /**
     * @var string time_picker_desc The description of the time picker
     */
    public string $time_picker_desc = 'hh:mm:ss';

    /**
     * @var string $decimal_separator The language's decimal_separator
     */
    public string $decimal_separator = '.';

    /**
     * @var string $thousands_separator The language's thousands_separator
     */
    public string $thousands_separator = ',';

    /**
     * @var string $name The name of the language
     */
    public protected(set) string $name {
        get {
            if (isset($this->name)) {
                return $this->name;
            }

            $this->name = $this->app->config->language;

            //read the language name from the url, if multi-language is enabled
            if ($this->multi) {
                $code = $this->app->url->lang;

                if (isset($this->multi_list[$code])) {
                    $this->name = $this->multi_list[$code];
                }
            }

            if (!$this->name) {
                throw new \Exception('No language set in the config file.');
            }

            return $this->name;
        }
    }

    /**
     * @var array $multi_list The list of available languages for multi-language support
     */
    public protected(set) array $multi_list {
        get {
            if (isset($this->multi_list)) {
                return $this->multi_list;
            }

            $this->multi_list = $this->app->config->read('languages.php');

            return $this->multi_list;
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
            if (count($this->multi_list) > 1) {
                $this->multi = true;
            }

            return $this->multi;
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

            $this->code = $this->name;

            if ($this->multi) {
                $this->code = $this->getCode($this->name);

                if (!$this->code) {
                    throw new \Exception("Language code not found for language: {$this->name}");
                }
            }

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

            $this->default_code = $this->code;
            if ($this->multi) {
                $this->default_code = $this->getCode($this->app->config->language);

                if (!$this->default_code) {
                    throw new \Exception("Default language code not found");
                }
            }

            return $this->default_code;
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

            if ($this->multi) {
                $this->codes = array_keys($this->multi_list);
            } else {
                $this->codes = [$this->code];
            }

            return $this->codes;
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

        include($this->path . '/init.php');
    }

    /**
     * Returns the code for the specified language name
     * @param string $name The language name
     * @return string|null The code of the language, or null if not found
     */
    public function getCode(string $name) : ?string
    {
        return array_find_key($this->multi_list, fn ($value) => $value === $name);
    }

    /**
     * @see \Mars\Extensions\Language::loadFile()
     * {@inheritdoc}
     */
    public function loadFile(string $file) : static
    {
        //load first the fallback language file, if enabled
        if ($this->fallback) {
            if ($this->fallback->isFile($file)) {
                $this->loadFilename($this->fallback->getFilename($file), $file);
            }
        }

        //load the parent language file, if any
        if ($this->parent) {
            if ($this->parent->isFile($file)) {
                $this->loadFilename($this->parent->getFilename($file), $file);
            }
        }

        return parent::loadFile($file);
    }
}
