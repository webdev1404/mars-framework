<?php
/**
* The Language Class
* @package Mars
*/

namespace Mars\Extensions\Languages;

use Mars\App;
use Mars\Extensions\Extension;
use Mars\Extensions\Extensions;
use Mars\Extensions\Abilities\FilesCacheTrait;

/**
 * The Language Class
 */
class Language extends Extension
{
    use FilesCacheTrait;
    
    /**
     * @internal
     */
    public const array DIRS = [
        'assets' => 'assets',
        'files' => 'files',
        'templates' => 'templates',
        'setup' => 'setup',
    ];

    /**
     * @const array CACHE_DIRS The dirs to be cached
     */
    public const array CACHE_DIRS = ['files', 'templates'];

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
     * @var string $files_path The path for the language's files folder
     */
    public protected(set) string $files_path {
        get {
            if (isset($this->files_path)) {
                return $this->files_path;
            }

            $this->files_path = $this->path . '/' . static::DIRS['files'];

            return $this->files_path;
        }
    }

    /**
     * @var array $files Array with the list of available files
     */
    public array $files {
        get => $this->files_cache_list['files'] ?? [];
    }

    /**
     * @var string $templates_path The path for the language's templates folder
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
     * @var array $templates Array with the list of available templates
     */
    public array $templates {
        get => $this->files_cache_list['templates'] ?? [];
    }

    /**
     * @internal
     */
    protected static string $manager_class = Languages::class;

    /**
     * @internal
     */
    protected static ?Extensions $manager_instance = null;

    /**
     * @internal
     */
    protected static string $type = 'language';

    /**
     * @internal
     */
    protected static string $base_dir = 'languages';

    /**
     * @internal
     */
    protected static string $base_namespace = "\\Languages";
}
