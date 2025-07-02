<?php
/**
* The Base Setup Extension Class
* @package Mars
*/

namespace Mars\Extensions\Setup;

use Mars\App;
use Mars\App\Kernel;
use Mars\Extensions\Extension as BaseExtension;

/**
 * The Base Setup Extension Class
 * Base class for all basic setup extensions
 */
abstract class Extension
{
    use Kernel;

    /**
     * Returns the base directory where the extensions of this type are located
     * @return string The base directory
     */
    abstract protected function getBaseDir() : string;

    /**
     * Returns the filename used to cache the list of available extensions
     * @return string The filename
     */
    abstract protected function getListFilename() : string;

    /**
     * Returns the extension object for the specified name
     */
    abstract protected function getExtension(string $name) : ?BaseExtension;

    /**
     * Constructor
     * @var App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->lazyLoad($this->app);
    }

    public function enable()
    {

    }

    public function disable()
    {

    }

    public function remove()
    {
        //$this->removeAssetsSymlink();
    }

    /**
     * Sets up the extension. 
     * Caches the list of available extensions and creates symlinks to their assets folders
     */
    public function prepare()
    {
        var_dump("prepare");
        $list = $this->createList();

        foreach ($list as $name => $path) {
            $extension = $this->getExtension($name);
            if (!$extension) {
                break;
            }

            //create the symlink to the assets folder in the public directory
            $this->createAssetsSymlink($extension);
        }
    }

    /**
     * Creates a symlink to the assets folder in the public directory
     * @param BaseExtension $extension The extension object
     */
    protected function createAssetsSymlink(BaseExtension $extension)
    {             
        //if the symlink already exists, we don't need to create it again
        if (is_link($extension->assets_target)) {
            return;
        }

        //if the assets folder doesn't exist, we don't need to create the symlink
        if (!is_dir($extension->assets_path)) {
            return;
        }

        symlink($extension->assets_path, $extension->assets_target);

        if (!is_link($extension->assets_target)) {
            throw new \Exception("Failed to create symlink for assets folder: {$extension->assets_target}");
        }
    }
}