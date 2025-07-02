<?php
/**
* The Extensions List Reader Class
* @package Mars
*/

namespace Mars\Extensions\List;

use Mars\App\Kernel;

/**
 * The Extensions List Reader Class
 * Reads the list of extensions of this type from the system
 */
class Reader
{
    use Kernel;

    /**
     * @var array $vendor_exclude The list of directories to exclude from the vendor search
     */
    protected array $vendor_exclude = [
        'bin', 'psr', 'composer'
    ];

    /**
     * Returns the list of extensions of the specified type, found in the system
     * @param string $type The type of the extensions
     * @return array The list of extensions of the specified type
     */
    public function get(string $type) : array
    {
        $dirs = array_merge($this->getFromExtensionsDir($type), $this->getFromVendor($type));

        $list = [];
        foreach ($dirs as $dir) {
            $name = basename($dir);

            $list[$name] = $dir;
        }

        return $list;
    }

    /**
     * Returns the list of extensions of the specified type, found in the extensions directory
     * @param string $type The type of the extensions
     * @return array The list of extensions
     */
    protected function getFromExtensionsDir(string $type) : array
    {
        return $this->getFromDir($this->app->extensions_path . '/' . $type, false);
    }

    /**
     * Scans the vendor directory for extensions of the specified type
     * @param string $type The type of the extensions
     * @return array The list of extensions
     */
    protected function getFromVendor(string $type) : array
    {
        $dirs = [];

        $vendors = $this->app->dir->getDirs($this->app->vendor_path, false, true, $this->vendor_exclude);
        foreach ($vendors as $dir) {
            $vendor_packages = $this->app->dir->getDirs($dir, false, true);

            foreach ($vendor_packages as $package_dir) {
                $package_dir = $package_dir . '/' . $type;
                if (!is_dir($package_dir)) {
                    continue;
                }

                $dirs = array_merge($dirs, $this->getFromDir($package_dir, true));
            }
        }

        return $dirs;
    }

    /**
     * Returns the list of extensions from the specified directory
     * @param string $dir The directory to scan
     * @param bool $check_info If true, we'll check for the info.php file in each directory
     * @return array The list of extensions
     */
    protected function getFromDir(string $dir, bool $check_info) : array
    {
        $dirs = $this->app->dir->getDirs($dir, false, true);
        if (!$check_info) {
            return $dirs;
        }

        foreach ($dirs as $key => $dir) {
            $info_file = $dir . '/info.php';

            if (!is_file($info_file)) {
                unset($dirs[$key]);
            }
        }

        return $dirs;
    }
}