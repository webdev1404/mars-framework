<?php
/**
* The Themes Class
* @package Mars
*/

namespace Mars\Extensions\Themes;

use Mars\Extensions\Extensions;

/**
 * The Themes Class
 */
class Themes extends Extensions
{
    /**
     * @internal
     */
    protected static ?array $list_enabled = null;

    /**
     * @internal
     */
    protected static ?array $list_all = null;

    /**
     * @internal
     */
    protected static string $list_config_file = '';

    /**
     * @internal
     */
    protected static string $instance_class = Theme::class;

    /**
     * @see Extensions::getEnabled()
     * {@inheritdoc}
     */
    public function getEnabled(bool $use_cache = true): array
    {
        if (static::$list_enabled !== null) {
            return static::$list_enabled;
        }

        static::$list_enabled = $this->getAll($use_cache);

        return static::$list_enabled;
    }

    /**
     * @see Extensions::install()
     * {@inheritdoc}
     */
    public function install(string $name)
    {
        $extension = $this->get($name);

        $setup = $this->getSetupManager($name);
        if ($setup && method_exists($setup, 'install')) {
            $setup->install();
        }
       
        $this->createSymlink($extension);
    }

    /**
     * @see Extensions::uninstall()
     */
    public function uninstall(string $name)
    {
        $extension = $this->get($name);

        $setup = $this->getSetupManager($name);
        if ($setup && method_exists($setup, 'uninstall')) {
            $setup->uninstall();
        }

        $this->removeSymlink($extension);
    }
}
