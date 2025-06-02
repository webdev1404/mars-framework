<?php
/**
* The Language Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
 * The Language Class
 */
class Language extends Extension
{
    /**
     * @var array $strings The language's strings
     */
    public array $strings = [];

    /**
     * @var array $strings_with_prefix The language's strings which have a prefix assigned
     */
    public array $strings_with_prefix = [];

    /**
     * @var array  $string_prefixes The prefixes to to search strings with
     */
    protected array $string_prefixes = [];

    /**
     * @var array $strings_prefix_old The prefixes to to search strings with
     */
    protected array $string_prefixes_old = [];

    /**
     * @var array $loaded_files The list of loaded files
     */
    protected array $loaded_files = [];

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
    protected static string $base_namespace = "Languages";

    /**
     * {@inheritdoc}
     * @see \Mars\Extensions\ExtensionTrait::getRootNamespace()
     */
    protected function getRootNamespace() : string
    {
        return '';
    }

    /**
     * Loads the specified $file from the languages folder
     * @param string $file The name of the file to load
     * @param string $prefix Prefix to apply to the strings, if any
     * @return static
     */
    public function loadFile(string $file, string $prefix = '') : static
    {
        if (isset($this->loaded_files[$file])) {
            return $this;
        }

        $this->loaded_files[$file] = true;

        $this->loadFilename($this->path . '/' . $file . '.' . App::FILE_EXTENSIONS['languages'], $prefix);

        return $this;
    }

    /**
     * Loads the specified filename from anywhere on the disk as a language file
     * @param string $filename The filename to load
     * @param string $prefix Prefix to apply to the strings, if any
     * @return static
     */
    public function loadFilename(string $filename, string $prefix = '') : static
    {
        $strings = include($filename);

        if ($prefix) {
            $this->addPrefix($prefix);

            $this->strings_with_prefix[$prefix] = array_merge(
                $this->strings_with_prefix[$prefix] ?? [],
                $strings
            );
        } else {
            $this->strings = array_merge($this->strings, $strings);
        }

        return $this;
    }

    /**
     * Returns a language string
     * @param string $key The string key as defined in the languages file
     * @param array $replace Array with key & values to be used for to search & replace, if any
     * @return string The language string
     */
    public function get(string $key, array $replace = [], string $prefix = '') : string
    {        
        $string = '';
        $prefixes = $prefix ? [$prefix] : $this->string_prefixes;

        if ($prefixes) {
            foreach ($prefixes as $prefix) {
                if (isset($this->strings_with_prefix[$prefix][$key])) {
                    $string = $this->strings_with_prefix[$prefix][$key];
                    break;
                }
            }

            if (!$string) {
                //fallback to the non-prefixed key, if not found
                $string = $this->strings[$key] ?? $key;
            }
        } else {
            $string = $this->strings[$key] ?? $key;
        }

        if ($replace) {
            $string = str_replace(array_keys($replace), $replace, $string);
        }

        return $string;
    }

    /**
     * Adds a prefix to the prefixes list
     * @param string $prefix The prefix to add
     * @return static
     */
    public function addPrefix(string $prefix) : static
    {
        array_unshift($this->string_prefixes, $prefix);

        return $this;
    }

    /**
     * Saves the current prefixes to the old ones
     */
    public function savePrefix() : static
    {
        $this->string_prefixes_old = $this->string_prefixes;

        return $this;
    }

    /**
     * Restores the prefixes to the previous ones
     */
    public function restorePrefix() : static
    {
        //unload the strings with the current prefixes
        foreach ($this->string_prefixes as $prefix) {
            unset($this->strings_with_prefix[$prefix]);
        }

        $this->string_prefixes = $this->string_prefixes_old;

        return $this;
    }
}
