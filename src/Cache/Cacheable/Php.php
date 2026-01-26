<?php
/**
* The Cacheable PHP Driver
* @package Mars
*/

namespace Mars\Cache\Cacheable;

/**
 * The Cacheable PHP Driver
 * Driver which stores on disk the cached resources as PHP files
 */
class Php extends File
{
    /**
     * @see File::getFilename()
     * {@inheritDoc}
     */
    protected function getFilename(string $filename) : string
    {
        return $filename . '.php';
    }

    /**
     * @see CacheableInterface::get()
     * {@inheritDoc}
     */
    public function get(string $filename, bool $unserialize) : mixed
    {
        $filename = $this->getFilename($filename);

        if (!$this->isFile($filename)) {
            return null;
        }

        return include $filename;
    }

    /**
     * @see CacheableInterface::set()
     * {@inheritDoc}
     */
    public function set(string $filename, mixed $content, bool $serialize) : bool
    {
        $filename = $this->getFilename($filename);

        $this->setIsFile($filename);

        $data = "<?php\n\nreturn ";
        $data.= var_export($content, true);
        $data.= ";\n";

        return file_put_contents($filename, $data);
    }
}
