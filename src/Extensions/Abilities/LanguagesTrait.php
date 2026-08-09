<?php
/**
* The Extension's Languages Trait
* @package Mars
*/

namespace Mars\Extensions\Abilities;

/**
 * The Extension's Languages Trait
 * Trait which allows languages to load language files from the extension's languages dir
 */
trait LanguagesTrait
{
    /**
     * @var string $languages_path The path to the extension's languages dir
     */
    public protected(set) string $languages_path {
        get {
            if (isset($this->languages_path)) {
                return $this->languages_path;
            }

            $this->languages_path = $this->path . '/' . static::DIRS['languages'];

            return $this->languages_path;
        }
    }

    /**
     * @var array $languages_files The list of language files in the extension
     */
    public protected(set) array $languages_files {
        get {
            if (isset($this->languages_files)) {
                return $this->languages_files;
            }

            $this->languages_files = $this->files_cache_list[static::DIRS['languages']] ?? [];

            return $this->languages_files;
        }
    }
}
