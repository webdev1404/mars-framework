<?php
/**
* The Extension's Config Trait
* @package Mars
*/

namespace Mars\Extensions\Abilities;

/**
 * The Extension's Config Trait
 * Trait which allows config files to be loaded from the extension's config dir
 */
trait ConfigTrait
{
    /**
     * @var string $config_path The path to the extension's config dir
     */
    public protected(set) string $config_path {
        get {
            if (isset($this->config_path)) {
                return $this->config_path;
            }

            $this->config_path = $this->path . '/' . static::DIRS['config'];

            return $this->config_path;
        }
    }
}
