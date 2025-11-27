<?php
/**
* The Cachable PHP Driver
* @package Mars
*/

namespace Mars\Cache\Cacheable;

/**
 * The Cachable PHP Driver
 * Driver which stores on disk the cached resources as PHP files
 */
class Php extends File
{
    /**
     * @see File::getFilename()
     * {@inheritdoc}
     */
    protected function getFilename(string $filename) : string
    {
        return $filename . '.php';
    }

    /**
     * @see CacheableInterface::get()
     * {@inheritdoc}
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
     * {@inheritdoc}
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
