<?php
/**
* The Dir Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The Dir Class
 * Encapsulates methods for working with directories
 */
class Dir implements \Stringable
{
    use Kernel;

    /**
     * @var string The directory path
     */
    public protected(set) string $path = '';

    /**
     * @var string $realpath The real path of the directory or an empty string if the directory doesn't exist
     */
    public string $realpath {
        get {
            if (isset($this->realpath)) {
                return $this->realpath;
            }

            $realpath = realpath($this->path);
            if ($realpath === false) {
                $realpath = '';
            }

            $this->realpath = $realpath;

            return $this->realpath;
        }
    }

    /**
     * @var bool $exists True if the directory exists, false otherwise
     */
    public bool $exists {
        get => is_dir($this->path);
    }

    /**
     * Builds the Dir object
     * @var string The directory path
     */
    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/');
    }

    /**
     * Returns the current directory path
     * @return string The current directory path
     */
    public function __toString() : string
    {
        return $this->path;
    }

    /**
     * Check that the dir doesn't contain invalid chars. and is located in the right path. Throws a fatal error for an invalid dir
     * @see File::check()
     */
    public function check()
    {
        return new File($this->path)->check();
    }

    /**
     * Checks if a filename is inside the dir
     * It doesn't check if the file or dir actually exist so use with care
     * @param string $filename The filename to check
     * @param bool $check_exists If true, will check if the file actually exists
     * @return bool True if $filename is inside $dir
     */
    public function contains(string $filename, bool $check_exists = true) : bool
    {
        $filename = rtrim($filename, '/');

        if ($filename == $this->realpath) {
            return false;
        }

        if (!$check_exists) {
            return str_starts_with($filename, $this->realpath . '/');
        }

        $real_filename = realpath($filename);
        if ($real_filename === false) {
            return false;
        }

        if (!str_contains($real_filename, $this->realpath . '/')) {
            return false;
        }

        return true;
    }

    /**
     * Returns the dirs and files from the specified folder
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will set will return the file's full path
     * @param array $extensions If specified, will return only the files matching the extensions
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $exclude_extensions If specified, will exclude the files matching the extensions
     * @return array The files
     */
    public function get(bool $recursive = true, bool $full_path = true, array $extensions = [], array $exclude_dirs = [], array $exclude_extensions = []) : array
    {
        return $this->getFiles($recursive, $full_path, $extensions, $exclude_dirs, $exclude_extensions, true);
    }

    /**
     * Returns the dirs from the specified folder
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will set will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @return array The files
     */
    public function getDirs(bool $recursive = true, bool $full_path = true, array $exclude_dirs = []) : array
    {
        $this->check();

        $iterator = $this->getIterator($this->path, $recursive, $exclude_dirs);

        $dirs = [];
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                continue;
            }

            $dirs[] = $full_path ? $file->getPathname() : $file->getSubPathname();
        }

        return $dirs;
    }

    /**
     * Returns the dirs from the specified folder, sorted
     * @see Dir::getDirs()
     */
    public function getDirsSorted(bool $recursive = true, bool $full_path = true, array $exclude_dirs = []) : array
    {
        $dirs = $this->getDirs($recursive, $full_path, $exclude_dirs);

        return $this->getSorted($dirs);
    }

    /**
     * Returns the files from the specified folder
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will set will return the file's full path
     * @param array $extensions If specified, will return only the files matching the extensions
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $exclude_extensions If specified, will exclude the files matching the extensions
     * @param bool $include_dirs If true, will include the directories in the result
     * @return array The files
     */
    public function getFiles(bool $recursive = true, bool $full_path = true, array $extensions = [], array $exclude_dirs = [], array $exclude_extensions = [], bool $include_dirs = false) : array
    {
        $this->check();

        $iterator = $this->getIterator($this->path, $recursive, $exclude_dirs);

        $files = [];
        foreach ($iterator as $file) {
            if (!$include_dirs && $file->isDir()) {
                continue;
            }
            if ($extensions || $exclude_extensions) {
                $extension = $file->getExtension();

                if ($extensions && !in_array($extension, $extensions)) {
                    continue;
                }
                if ($exclude_extensions && in_array($extension, $exclude_extensions)) {
                    continue;
                }
            }

            $files[] = $full_path ? $file->getPathname() : $file->getSubPathname();
        }

        return $files;
    }

    /**
     * Returns the files from the specified folder, sorted
     * @see Dir::getFiles()
     */
    public function getFilesSorted(bool $recursive = true, bool $full_path = true, array $extensions = [], array $exclude_dirs = [], array $exclude_extensions = [], bool $include_dirs = false) : array
    {
        $files = $this->getFiles($recursive, $full_path, $extensions, $exclude_dirs, $exclude_extensions, $include_dirs);

        return $this->getSorted($files);
    }

    /**
     * Sorts an array of files
     * @param array $files The files to sort
     * @return array The sorted files
     */
    protected function getSorted(array $files) : array
    {
        usort($files, 'strnatcasecmp');

        return $files;
    }

    /**
     * Returns the directory tree from the specified folder
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will set will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param string $prefix The prefix to add to each dir (used for indentation)
     * @param bool $sort If true, will sort the dirs
     * @param string|null $dir @internal
     * @param int $level @internal
     * @return array The dirs as a tree
     */
    public function getDirsTree(bool $recursive = true, bool $full_path = false, array $exclude_dirs = [], string $prefix = '', bool $sort = true, ?string $dir = null, int $level = 1) : array
    {
        if ($dir === null) {
            $dir = $this->path;

            $this->check();
        }

        $list = $this->getTreeList($dir, $exclude_dirs, $sort, false, $level);

        $dirs = [];
        $dirs_prefix = str_repeat($prefix, $level);

        foreach ($list as $file) {
            $name = $dirs_prefix . ($full_path ? $file['full_path'] : $file['filename']);

            if ($recursive) {
                $dirs[$name] = $this->getDirsTree(true, $full_path, $exclude_dirs, $dirs_prefix, $sort, $file['full_path'], $level + 1);
            } else {
                $dirs[$name] = [];
            }
        }

        return $dirs;
    }

    /**
     * Returns the files from the specified folder, as a tree
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will set will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $extensions If specified, will return only the files matching the extensions
     * @param string $prefix The prefix to add to each file (used for indentation)
     * @param bool $sort If true, will sort the files
     * @param string|null @internal
     * @param int $level @internal
     * @return array The files as a tree
     */
    public function getFilesTree(bool $recursive = true, bool $full_path = false, array $exclude_dirs = [], array $extensions = [], array $exclude_extensions = [], string $prefix = '', bool $sort = true, ?string $dir = null, int $level = 1) : array
    {
        if ($dir === null) {
            $dir = $this->path;

            $this->check();
        }

        $list = $this->getTreeList($dir, $exclude_dirs, $sort, true, $level);

        $files = [];
        $file_prefix = str_repeat($prefix, $level);

        foreach ($list as $file) {
            $name = $file_prefix . ($full_path ? $file['full_path'] : $file['filename']);

            if ($file['is_dir']) {
                if ($recursive) {
                    $files[$name] = $this->getFilesTree(true, $full_path, $exclude_dirs, $extensions, $exclude_extensions, $prefix, $sort, $file['full_path'], $level + 1);
                } else {
                    $files[$name] = [];
                }
            } else {
                if ($extensions || $exclude_extensions) {
                    $extension = $this->app->file->getExtension($file['full_path']);

                    if ($extensions && !in_array($extension, $extensions)) {
                        continue;
                    }
                    if ($exclude_extensions && in_array($extension, $exclude_extensions)) {
                        continue;
                    }
                }

                $files[] = $name;
            }
        }

        return $files;
    }

    /**
     * Returns the tree list of files and directories
     * @param string $dir The folder to be searched
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param bool $sort If true, will sort the dirs
     * @param bool $include_files If true, will include files
     * @param int $level The current level of recursion (used for indentation)
     * @return array The tree list
     */
    protected function getTreeList(string $dir, array $exclude_dirs = [], bool $sort = true, bool $include_files = true, int $level = 1) : array
    {
        $iterator = $this->getIterator($dir, false, $exclude_dirs);

        //copy the iterator to an array of SplFileInfo objects, so we can sort them
        $list = [];
        foreach ($iterator as $file) {
            if ($file->isDot()) {
                continue;
            }
            if (!$include_files && $file->isFile()) {
                continue;
            }

            $list[] = ['is_dir' => $file->isDir(), 'full_path' => $file->getPathname(), 'filename' => $file->getFilename()];
        }

        //show directories first, order by filename if $sort is true
        uasort($list, function ($a, $b) use ($sort) {
            if ($a['is_dir'] && !$b['is_dir']) {
                return -1;
            } elseif (!$a['is_dir'] && $b['is_dir']) {
                return 1;
            }

            if ($sort) {
                return strnatcasecmp($a['filename'], $b['filename']);
            }

            return 0;
        });

        return $list;
    }

    /**
     * Returns the iterator used to generate the files
     * @param string $dir The folder to be searched
     * @param bool $recursive If true will enum. recursive
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param int $flag Flag to pass to \RecursiveIteratorIterator
     * @return Iterator The iterator
     */
    protected function getIterator(string $dir, bool $recursive = true, array $exclude_dirs = [], int $flag = \RecursiveIteratorIterator::SELF_FIRST) : \Iterator
    {
        $iterator = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::CURRENT_AS_SELF);

        if ($exclude_dirs) {
            $iterator = new \RecursiveCallbackFilterIterator($iterator, function ($current, $key, $dir_iterator) use ($exclude_dirs) {
                if (in_array($dir_iterator->getSubPathname(), $exclude_dirs)) {
                    return false;
                }

                return true;
            });
        }

        if ($recursive) {
            $iterator = new \RecursiveIteratorIterator($iterator, $flag);
        } else {
            $iterator = new \IteratorIterator($iterator);
        }

        return $iterator;
    }

    /**
     * Create the folder. Does nothing if the folder already exists
     * @param int|null $permissions The permissions to set for the folder. If null, no permissions will be set
     * @param bool $recursive If true, will create parent folders if they don't exist
     * @return static
     * @throws Exception if the folder can't be created
     */
    public function create(?int $permissions = null, bool $recursive = false) : static
    {
        if ($this->exists) {
            return $this;
        }

        $this->check();

        $this->app->plugins->run('dir.create', $this);

        if (!mkdir($this->path, recursive: $recursive)) {
            throw new \Exception(App::__('error.dir.create', ['{DIR}' => $this->path]));
        }
        if ($permissions) {
            chmod($this->path, $permissions);
        }

        return $this;
    }

    /**
     * Copies a dir
     * @param string $destination_dir The destination dir
     * @return static The new dir or null if dir doesn't exist
     * @throws Exception If folders can't be created/files can't be copied
     */
    public function copy(string $destination_dir) : ?static
    {
        if (!$this->exists) {
            return null;
        }

        $destination = new static($destination_dir);

        $this->check();
        $destination->check();

        $this->app->plugins->run('dir.copy', $this, $destination);

        $destination->create();

        $iterator = $this->getIterator($this->path);
        foreach ($iterator as $file) {
            $target_file = $destination . '/' . $file->getSubPathname();

            if ($file->isDir()) {
                if (!is_dir($target_file)) {
                    if (!mkdir($target_file)) {
                        throw new \Exception(App::__('error.dir.create', ['{DIR}' => $target_file]));
                    }
                }
            } else {
                if (!copy($file->getPathname(), $target_file)) {
                    throw new \Exception(App::__('error.file.copy', ['{SOURCE}' => $file->getPathname(), '{DESTINATION}' => $target_file]));
                }
            }
        }

        return  $destination;
    }

    /**
     * Moves a dir
     * @param string $destination_dir The destination dir
     * @return static The new dir or null if dir doesn't exist
     * @throws Exception if the dir can't be moved
     */
    public function move(string $destination_dir) : ?static
    {
        if (!$this->exists) {
            return null;
        }

        $destination = new static($destination_dir);

        $this->check();
        $destination->check();

        $this->app->plugins->run('dir.move', $this, $destination);

        if (!rename($this->path, $destination->path)) {
            throw new \Exception(App::__('error.dir.move', ['{SOURCE}' => $this->path, '{DESTINATION}' => $destination->path]));
        }

        return $destination;
    }

    /**
     * Deletes a dir
     * @param bool $delete_dir If true, will delete the dir itself; if false, will clean it
     * @return static|null Will return null if the dir itself was deleted, the dir itself otherwise
     * @throws Exception if the dir can't be deleted
     */
    public function delete(bool $delete_dir = true) : ?static
    {
        if (!$this->exists) {
            return null;
        }

        $this->check();

        $this->app->plugins->run('dir.delete', $this, $delete_dir);

        $iterator = $this->getIterator($this->path, flag: \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                if (!rmdir($file->getPathname())) {
                    throw new \Exception(App::__('error.dir.delete', ['{DIR}' => $file->getPathname()]));
                }
            } else {
                if (!unlink($file->getPathname())) {
                    throw new \Exception(App::__('error.file_delete', ['{FILE}' => $file->getPathname()]));
                }
            }
        }

        if ($delete_dir) {
            if (!rmdir($this->path)) {
                throw new \Exception(App::__('error.dir.delete', ['{DIR}' => $this->path]));
            }

            return null;
        }

        return $this;
    }

    /**
     * Deletes all the files/subdirectories from the directory but does not delete the folder itself
     * @return static
     * @throws Exception if the dir can't be cleaned
     */
    public function clean() : static
    {
        $this->app->plugins->run('dir.clean', $this);

        return $this->delete(false);
    }

    /**
     * Deletes expired files from the dir
     * @param int $expires The threshold timestamp
     * @return static|null Will return null if the dir itself was deleted, the dir itself otherwise
     * @throws Exception If files can't be deleted
     */
    public function cleanExpired(int $expires) : ?static
    {
        if (!$this->exists) {
            return null;
        }

        $this->check();

        $this->app->plugins->run('dir.clean.expired', $this);

        $iterator = $this->getIterator($this->path, flag: \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                if ($file->getCTime() <= $expires) {
                    if (!unlink($file->getPathname())) {
                        throw new \Exception(App::__('error.file_delete', ['{FILE}' => $file->getPathname()]));
                    }
                }
            }
        }

        return $this;
    }
}
