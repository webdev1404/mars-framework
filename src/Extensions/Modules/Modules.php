<?php
/**
* The Modules Class
* @package Mars
*/

namespace Mars\Extensions\Modules;

use Mars\App;
use Mars\Extensions\Extensions;

/**
 * The Modules Class
 */
class Modules extends Extensions
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
    protected static string $list_config_file = 'modules.php';

    /**
     * @internal
     */
    protected static string $instance_class = Module::class;

    /**
     * @see Extensions::install()
     */
    public function install(string $name)
    {
        $extension = $this->get($name);

        $setup = $this->getSetupManager($name);
        if ($setup && method_exists($setup, 'install')) {
            $setup->install();
        }
       
        $this->createSymlink($extension);

        if (!$extension->enabled) {
            $this->addConfig($name);
        }
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

        if ($extension->enabled) {
            $this->removeConfig($name);
        }
    }
}
