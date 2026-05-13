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
}
