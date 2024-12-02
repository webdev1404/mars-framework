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
            $keys = array_map(fn($key) => $prefix . $key, array_keys($strings));
            $strings = array_combine($keys, $strings);
        }

        $this->strings = array_merge($this->strings, $strings);

        return $this;
    }
}
