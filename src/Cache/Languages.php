<?php
/**
* The Languages Cache Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\Extensions\Extension;
use Mars\Extensions\Languages\Language;

/**
 * The Languages Cache Class
 * Class which handles the caching of language files
 */
class Languages extends Data
{
    /**
     * @see Cacheable::$driver_name
     * {@inheritDoc}
     */
    protected string $driver_name {
        get => $this->app->config->cache->languages->driver ?? $this->app->config->cache->driver;
    }

    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        true,                    // use files cache
        'cacheable_languages',   // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'languages';

    /**
     * Returns the list of files for a given language
     * @param Language $lang The language object
     * @return array The list of files
     */
    public function getFiles(Language $lang) : array
    {
        $cache_key = $lang->name;

        $files = $this->get($cache_key);
        if ($lang->development) {
            $files = null;
        }

        if ($files) {
            return $files;
        }

        $files = [];
        if ($lang->fallback) {
            //add the fallback language files first
            $this->addFiles($files, $lang->fallback->files_path);
        }
        if ($lang->parent) {
            //add the parent language files
            $this->addFiles($files, $lang->parent->files_path);
        }

        //add the language files
        $this->addFiles($files, $lang->files_path);

        $this->set($cache_key, $files);

        return $files;
    }

    /**
     * Adds a list of files from a given directory to the provided files array
     * @param array $files The array to add the files to
     * @param string $dir The directory to get the files from
     */
    protected function addFiles(array &$files, string $dir)
    {
        $files_list = $this->app->dir->get($dir, false, true, ['php']);

        foreach ($files_list as $file) {
            $name = basename($file, '.php');

            if (!isset($files[$name])) {
                $files[$name] = [$file];
            } else {
                $files[$name][] = $file;
            }
        }
    }

    /**
     * Returns the list of files for a given language and extension
     * @param Language $lang The language
     * @param Extension $extension The extension
     * @return array The list of files
     */
    public function getExtensionFiles(Language $lang, Extension $extension) : array
    {
        $cache_key = $extension->name . '-' . $lang->name;

        $files = $this->get($cache_key);
        if ($lang->development) {
            $files = null;
        }

        if ($files) {
            return $files;
        }
        
        $files = [];
        $files_list = $this->app->dir->get($extension->languages_path, false, false, ['php']);
        foreach ($files_list as $file) {
            $name = basename($file, '.php');

            $files[$name] = $this->getExtensionFilenames($file, $lang, $extension);
        }

        $this->set($cache_key, $files);

        return $files;
    }

    /**
     * Returns the list of filenames which exist for a given file key, language and extension
     * @param string $file The file key
     * @param Language $lang The language
     * @param Extension $extension The extension
     * @return array The list of filenames
     */
    protected function getExtensionFilenames(string $file, Language $lang, Extension $extension) : array
    {
        $filenames = [];

        //do we have the default file?
        $filenames[] = $extension->languages_path . '/' . $file;

        //do we have a file for the fallback language?
        if ($lang->fallback) {
            $filenames[] = $extension->languages_path . '/' . $lang->fallback->name . '/' . $file;
        }

        //do we have a file for the parent language?
        if ($lang->parent) {
            $filenames[] = $extension->languages_path . '/' . $lang->parent->name . '/' . $file;
        }

        //do we have a file for the language?
        $filenames[] = $extension->languages_path . '/' . $lang->name . '/' . $file;
  

        //check if the extension has the file in its languages folder
        $rel_path = $extension->getBaseDir() . '/' . $extension->name . '/' . $file;

        if ($lang->fallback) {
            $filenames[] = $lang->fallback->files_path . '/' . $rel_path;
        }

        if ($lang->parent) {
            $filenames[] = $lang->parent->files_path . '/' . $rel_path;
        }

        $filenames[] = $lang->files_path . '/' . $rel_path;

        $filenames = array_filter($filenames, function($filename) {
            return is_file($filename);
        });

        return $filenames;
    }
}