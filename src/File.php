<?php
/**
* The File Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The File Class
 * Encapsulates methods for working with files
 */
class File implements \Stringable
{
    use Kernel;

    /**
     * @var string The filename
     */
    public protected(set) string $filename = '';

    /**
     * @var Dir|null The directory of the file. Will be null if the file has no parent dir
     */
    public protected(set) ?Dir $dir {
        get {
            if (isset($this->dir)) {
                return $this->dir;
            }

            $dir = dirname($this->filename);
            if (!$dir || $dir == '.') {
                $this->dir = null;
            } else {
                $this->dir = new Dir(rtrim($dir, '/'));
            }

            return $this->dir;
        }
    }

    /**
     * @var Dir|null $path The directory of the file. Alias of $dir
     */
    public ?Dir $path {
        get => $this->dir;
    }

    /**
     * @var string|null $realpath The real path of the file or null if the file doesn't exist
     */
    public ?string $realpath {
        get {
            if (isset($this->realpath)) {
                return $this->realpath;
            }

            $realpath = realpath($this->filename);
            if ($realpath === false) {
                $realpath = null;
            }

            $this->realpath = $realpath;

            return $this->realpath;
        }
    }

    /**
     * @var string $stem The filename without the extension
     */
    public protected(set) string $stem {
        get {
            if (isset($this->stem)) {
                return $this->stem;
            }

            $this->stem = pathinfo($this->filename, PATHINFO_FILENAME);

            return $this->stem;
        }
    }

    /**
     * @var string $full_stem The full filename without the extension, including the path
     */
    public protected(set) string $full_stem {
        get {
            if (isset($this->full_stem)) {
                return $this->full_stem;
            }

            $this->full_stem = $this->dir ? $this->dir . '/' . $this->stem : $this->stem;

            return $this->full_stem;
        }
    }

    /**
     * @var string $name The basename of the file
     */
    public protected(set) string $name {
        get {
            if (isset($this->name)) {
                return $this->name;
            }

            $this->name = basename($this->filename);

            return $this->name;
        }
    }

    /**
     * @var string The file extension, in lowercase
     */
    public protected(set) string $extension {
        get {
            if (isset($this->extension)) {
                return $this->extension;
            }

            $this->extension = strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));

            return $this->extension;
        }
    }

    /**
     * @var string The MIME type of the file
     */
    public protected(set) string $type {
        get {
            if (isset($this->type)) {
                return $this->type;
            }

            $this->type = mime_content_type($this->filename);

            return $this->type;
        }
    }

    /**
     * @var int The size of the file
     */
    public protected(set) int $size {
        get {
            if (isset($this->size)) {
                return $this->size;
            }

            $this->size = filesize($this->filename);

            return $this->size;
        }
    }

    /**
     * @var bool True if the file exists
     */
    public bool $exists {
        get => is_file($this->filename);
    }

    /**
     * @var bool|array|null The open_basedir setting
     */
    protected static bool|array|null $open_basedirs = null;

    /**
     * Builds the File object
     * @var string The filename
     */
    public function __construct(string $filename)
    {
        $this->filename = rtrim($filename, '/');
        if (static::$open_basedirs === null) {
            static::$open_basedirs = $this->getOpenBaseDirs();
        }
    }

    /**
     * Returns the current filename
     * @return string The current filename
     */
    public function __toString() : string
    {
        return $this->filename;
    }

    /**
     * Returns the open_basedirs
     * @return bool|array
     */
    protected function getOpenBaseDirs() : bool|array
    {
        if ($this->app->config->security->open_basedir === false) {
            return false;
        }

        $dir = ($this->app->config->security->open_basedir === true) ? $this->app->base_path : $this->app->config->security->open_basedir;
        $dirs = $this->app->array->get($dir);

        array_walk($dirs, function (&$item) {
            $item = new Dir($item);
        });

        return $dirs;
    }

    /**
     * Check that the filname doesn't contain invalid chars. and is located in the right path. Throws a fatal error for an invalid filename
     * @return static
     * @throws Exception if the filename is not valid
     */
    public function check() : static
    {
        if (strlen(basename($this->filename)) > $this->app->config->files->max_chars) {
            throw new \Exception(App::__('error.file.invalid_maxchars', ['{FILE}' => $this->filename]));
        }

        $this->checkForInvalidChars();

        if (static::$open_basedirs) {
            //The filename must be inside the secure dir. If it's not it will be treated as an invalid file
            $real_filename = $this->realpath;
            if (!$real_filename) {
                $real_filename = $this->filename;
            }

            $contains = false;
            foreach (static::$open_basedirs as $basedir) {
                if ($basedir->contains($real_filename)) {
                    $contains = true;
                    break;
                }
            }

            if (!$contains) {
                throw new \Exception(App::__('error.file.invalid_basedir', ['{FILE}' => $this->filename]));
            }
        }

        return $this;
    }

    /**
     * Checks a filename for invalid characters. Throws a fatal error if it founds invalid chars.
     * @return static
     * @throws Exception if the filename contains invalid chars
     */
    public function checkForInvalidChars() : static
    {
        if (str_contains($this->filename, '../') || str_contains($this->filename, './')
            || str_contains($this->filename, '..\\') || str_contains($this->filename, '.\\')
            || str_starts_with($this->filename, strtolower('php:'))) {
            throw new \Exception(App::__('error.file.invalid_chars', ['{FILE}' => $this->filename]));
        }

        return $this;
    }

    /**
     * Returns the file extension
     * @param bool $add_dot If true, it will add a dot before the extension
     * @return string The file extension
     */
    public function getExtension(bool $add_dot = false) : string
    {
        if (!$add_dot || !$this->extension) {
            return $this->extension;
        }

        return '.' . $this->extension;
    }

    /**
     * Adds extension to filename
     * @param string $extension The extension
     * @return static Returns a new File instance with the new extension
     */
    public function addExtension(string $extension) : static
    {
        $filename = $extension ? $this->filename . '.' . $extension : $this->filename;

        return new static($filename);
    }

    /**
     * Returns the relative path of the filename. Eg: /var/www/mars/dir/some_file.txt => dir/some_file.txt
     * @param string $path The path to test against. If empty $this->app->base_path is used
     * @return string The relative path
     */
    public function getRel(string $path = '') : string
    {
        if (!$path) {
            $path = $this->app->base_path;
        }

        return rtrim(str_replace($path . '/', '', $this->filename), '/');
    }

    /**
     * Returns the prefix of a file
     * @param int $chars The number of chars of the returned prefix
     * @return string The prefix
     */
    public function getPrefix(int $chars = 4) : string
    {
        $name = substr($this->filename, 0, $chars);
        $name = str_replace(['.'], [''], $name);
        $name = strtolower($name);

        return $name;
    }

    /**
     * Appends a string to the filename before the extension
     * @param string $append The string to append
     * @return static Returns a new File instance
     */
    public function append(string $append) : static
    {
        return new static($this->full_stem . $append . $this->getExtension(true));
    }

    /**
     * Determines if the file is an image, based on extension.
     * It only checks the extension, use $app->image->isValid to check if the file is really an image
     * @return bool
     */
    public function isImage(): bool
    {
        return in_array($this->extension, Image::EXTENSIONS);
    }

    /**
     * Reads the content of the file
     * @return string The contents of the file
     * @throws Exception if the file can't be read
     */
    public function read() : string
    {
        $this->check();

        $this->app->plugins->run('file_read', $this);

        $content = file_get_contents($this->filename);
        if ($content === false) {
            throw new \Exception(App::__('error.file.read', ['{FILE}' => $this->filename]));
        }

        return $content;
    }

    /**
     * Writes to the file
     * @param string $content The content to write
     * @param bool $append If true will append the data to the file rather than create the file
     * @return bool Returns the number of written bytes
     * @throws Exception if the file can't be written
     */
    public function write(string $content, bool $append = false) : int
    {
        $this->check();

        $this->app->plugins->run('file_write', $this, $content, $append);

        $flags = 0;
        if ($append) {
            $flags = FILE_APPEND;
        }

        $bytes = file_put_contents($this->filename, $content, $flags);
        if ($bytes === false) {
            throw new \Exception(App::__('error.file.write', ['{FILE}' => $this->filename]));
        }

        return $bytes;
    }

    /**
     * Deletes the file
     * @return null
     * @throws Exception if the file can't be deleted
     */
    public function delete() : null
    {
        if (!$this->exists) {
            return null;
        }

        $this->check();

        $this->app->plugins->run('file_delete', $this);

        if (unlink($this->filename) === false) {
            throw new \Exception(App::__('error.file.delete', ['{FILE}' => $this->filename]));
        }

        return null;
    }

    /**
     * Copies the file
     * @param string $destination_filename The destination file
     * @return static|null Returns the new file or null if the dir doesn't exist
     * @throws Exception if the file can't be copied
     */
    public function copy(string $destination_filename) : ?static
    {
        if (!$this->exists) {
            return null;
        }

        $destination = new static($destination_filename);

        $this->check();
        $destination->check();

        $this->app->plugins->run('file_copy', $this, $destination);

        if (copy($this->filename, $destination->filename) === false) {
            throw new \Exception(App::__('error.file.copy', ['{SOURCE}' => $this->filename, '{DESTINATION}' => $destination->filename]));
        }

        return $destination;
    }

    /**
     * Moves the file
     * @param string $destination_filename The destination file
     * @return static|null Returns the new file or null if the dir doesn't exist
     * @throws Exception if the file can't be moved
     */
    public function move(string $destination_filename) : ?static
    {
        if (!$this->exists) {
            return null;
        }

        $destination = new static($destination_filename);

        $this->check();
        $destination->check();
        
        $this->app->plugins->run('file_move', $this, $destination);

        if (rename($this->filename, $destination->filename) === false) {
            throw new \Exception(App::__('error.file.move', ['{SOURCE}' => $this->filename, '{DESTINATION}' => $destination->filename]));
        }

        return $destination;
    }
}
