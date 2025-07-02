<?php
/**
* The Templates Cache Class
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Templates Cache Class
 * Class which handles the caching of templates
 */
class Templates extends Cache
{
    /**
     * @var string $dir The dir where the templates will be cached
     */
    protected string $dir = 'templates';

    /**
     * Returns the filename of the cached template
     * @param string $file The template's name
     * @return string The filename of the cached template
     */
    public function getFilename(string $file) : string
    {
        return $this->path . '/' . $file;
    }

    /**
     * Returns the name under which a template will be cached
     * @param string $template The template's name
     * @param string $type The template's type
     * @return string The name under which the template will be cached
     */
    public function getName(string $template, string $type) : string
    {
        $parts = [
            $this->app->theme->name,
            $template,
            $type
        ];

        $name = implode('-', $parts);

        return md5($name) . '.php';
    }

    /**
     * Determines if a template is cached
     * @param string $name The name of the template
     * @return bool
     */
    public function exists(string $name) : bool
    {
        return is_file($this->getFilename($name));
    }

    /**
     * Writes the parsed content of a template to the cache
     * @param string $filename The filename of the template
     * @param string $content The content of the template
     */
    public function write(string $filename, string $content)
    {
        $filename = $this->path . '/' . $filename;

        $res = file_put_contents($filename, $content);
        if ($res === false) {
            throw new \Exception("Error writing to cache file: {$filename}");
        }
    }        
}
