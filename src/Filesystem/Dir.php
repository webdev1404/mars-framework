<?php
/**
* The Dir Class
* @package Mars
*/

namespace Mars\Filesystem;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Dir Class
 * Folder Filesystem functionality
 */
class Dir
{
    /**
     * Imports the Kernel class or namespace for use within this file.
     * This allows access to core framework functionality provided by Kernel.
     */
    use Kernel;

    /**
     * Check that the filname [file/folder] doesn't contain invalid chars. and is located in the right path. Throws a fatal error for an invalid filename
     * @see File::checkFilename()
     */
    public function checkFilename(string $filename)
    {
        return $this->app->file->checkFilename($filename);
    }

    /**
     * Builds a path from an array.
     * @see File::buildPath()
     */
    public function buildPath(array $elements) : string
    {
        return $this->app->file->buildPath($elements, true);
    }

    /**
     * Checks if a filename is inside a dir
     * @param string $dir The dir
     * @param string $filename The filename to check
     * @return bool True if $filename is inside $dir
     */
    public function contains(string $dir, string $filename) : bool
    {
        if ($filename == $dir) {
            return false;
        }

        if (!str_contains($filename, $dir)) {
            return false;
        }

        return true;
    }

    /**
     * Returns the dirs and files from the specified folder
     * @param string $dir The folder to be searched
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will set will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $extensions If specified, will return only the files matching the extensions
     * @return array The files
     */
    public function get(string $dir, bool $recursive = true, bool $full_path = true, array $exclude_dirs = [], array $extensions = [], array $exclude_extensions = []) : array
    {
        return $this->getFiles($dir, $recursive, $full_path, $exclude_dirs, $extensions, $exclude_extensions, true);
    }

    /**
     * Returns the dirs from the specified folder
     * @param string $dir The folder to be searched
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will set will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @return array The files
     */
    public function getDirs(string $dir, bool $recursive = true, bool $full_path = true, array $exclude_dirs = []) : array
    {
        $this->checkFilename($dir);

        $iterator = $this->getIterator($dir, $recursive, $exclude_dirs);

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
    public function getDirsSorted(string $dir, bool $recursive = true, bool $full_path = true, array $exclude_dirs = []) : array
    {
        $dirs = $this->getDirs($dir, $recursive, $full_path, $exclude_dirs);

        usort($dirs, 'strnatcasecmp');

        return $dirs;
    }

    /**
     * Returns the files from the specified folder
     * @param string $dir The folder to be searched
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will set will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $extensions If specified, will return only the files matching the extensions
     * @param array $exclude_extensions If specified, will exclude the files matching the extensions
     * @param bool $include_dirs If true, will include the directories in the result
     * @return array The files
     */
    public function getFiles(string $dir, bool $recursive = true, bool $full_path = true, array $exclude_dirs = [], array $extensions = [], array $exclude_extensions = [], bool $include_dirs = false) : array
    {
        $this->checkFilename($dir);

        $iterator = $this->getIterator($dir, $recursive, $exclude_dirs);

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
    public function getFilesSorted(string $dir, bool $recursive = true, bool $full_path = true, array $exclude_dirs = [], array $extensions = [], array $exclude_extensions = []) : array
    {
        $files = $this->getFiles($dir, $recursive, $full_path, $exclude_dirs, $extensions, $exclude_extensions);

        usort($files, 'strnatcasecmp');

        return $files;
    }

    /**
     * Returns the directory tree from the specified folder
     * @param string $dir The folder to be searched
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will set will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param string $prefix The prefix to add to each dir (used for indentation)
     * @param bool $sort If true, will sort the dirs
     * @param int $level The current level of recursion (used for indentation)
     * @return array The dirs as a tree
     */
    public function getDirsTree(string $dir, bool $recursive = true, bool $full_path = false, array $exclude_dirs = [], string $prefix = '', bool $sort = true, int $level = 1) : array
    {
        $list = $this->getTreeList($dir, $exclude_dirs, $sort, false, $level);

        $dirs = [];
        $dirs_prefix = str_repeat($prefix, $level);

        foreach ($list as $file) {
            $name = $dirs_prefix . ($full_path ? $file['full_path'] : $file['filename']);

            if ($recursive) {
                $dirs[$name] = $this->getDirsTree($file['full_path'], true, $full_path, $exclude_dirs, $dirs_prefix, $sort, $level + 1);
            } else {
                $dirs[$name] = [];
            }
        }

        return $dirs;
    }

    /**
     * Returns the files from the specified folder, as a tree
     * @param string $dir The folder to be searched
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will set will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $extensions If specified, will return only the files matching the extensions
     * @param string $prefix The prefix to add to each file (used for indentation)
     * @param bool $sort If true, will sort the files
     * @param int $level The current level of recursion (used for indentation)
     * @return array The files as a tree
     */
    public function getFilesTree(string $dir, bool $recursive = true, bool $full_path = false, array $exclude_dirs = [], array $extensions = [], array $exclude_extensions = [], string $prefix = '', bool $sort = true, int $level = 1) : array
    {
        $list = $this->getTreeList($dir, $exclude_dirs, $sort, true, $level);

        $files = [];
        $file_prefix = str_repeat($prefix, $level);

        foreach ($list as $file) {
            $name = $file_prefix . ($full_path ? $file['full_path'] : $file['filename']);

            if ($file['is_dir']) {
                if ($recursive) {
                    $files[$name] = $this->getFilesTree($file['full_path'], true, $full_path, $exclude_dirs, $extensions, $prefix, $sort, $level + 1);
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
        if ($level === 1) {
            $this->checkFilename($dir);
        }
        
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
    public function getIterator(string $dir, bool $recursive = true, array $exclude_dirs = [], int $flag = \RecursiveIteratorIterator::SELF_FIRST) : \Iterator
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
     * Create a folder. Does nothing if the folder already exists
     * @param string $dir The name of the folder to create
     * @throws Exception if the folder can't be created
     */
    public function create(string $dir)
    {
        $this->app->plugins->run('dir_create', $dir, $this);

        $this->checkFilename($dir);

        if (is_dir($dir)) {
            return;
        }

        if (!mkdir($dir)) {
            throw new \Exception(App::__('error.dir_create', ['{DIR}' => $dir]));
        }
    }

    /**
     * Copies a dir
     * @param string $source The source folder
     * @param string $destination The destination folder
     * @throws Exception If folders can't be created/files can't be copied
     */
    public function copy(string $source, string $destination)
    {
        $this->app->plugins->run('dir_copy', $source, $destination, $this);

        $this->checkFilename($source);
        $this->checkFilename($destination);

        $this->create($destination);

        $iterator = $this->getIterator($source);
        foreach ($iterator as $file) {
            $target_file = $destination . '/' . $file->getSubPathname();

            if ($file->isDir()) {
               $this->create($target_file);
            } else {
                $this->app->file->copy($file->getPathname(), $target_file);
            }
        }
    }

    /**
     * Moves a dir
     * @param string $source The source folder
     * @param string $destination The destination folder
     * @throws Exception if the dir can't be moved
     */
    public function move(string $source, string $destination)
    {
        $this->app->plugins->run('dir_move', $source, $destination, $this);

        $this->checkFilename($source);
        $this->checkFilename($destination);

        if (!rename($source, $destination)) {
            throw new \Exception(App::__('error.dir_move', ['{SOURCE}' => $source, '{DESTINATION}' => $destination]));
        }
    }

    /**
     * Deletes a dir
     * @param string $dir The name of the folder to delete
     * @param bool $delete_dir If true, will delete the dir itself; if false, will clean it
     * @throws Exception if the dir can't be deleted
     */
    public function delete(string $dir, bool $delete_dir = true)
    {
        $this->app->plugins->run('dir_delete', $dir, $delete_dir, $this);

        $this->checkFilename($dir);

        $iterator = $this->getIterator($dir, flag: \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                if (!rmdir($file->getPathname())) {
                    throw new \Exception(App::__('error.dir_delete', ['{DIR}' => $file->getPathname()]));
                }
            } else {
                if (!unlink($file->getPathname())) {
                    throw new \Exception(App::__('error.file_delete', ['{FILE}' => $file->getPathname()]));
                }
            }
        }

        if ($delete_dir) {
            if (!rmdir($dir)) {
                throw new \Exception(App::__('error.dir_delete', ['{DIR}' => $dir]));
            }
        }
    }

    /**
     * Deletes all the files/subdirectories from a directory but does not delete the folder itself
     * @param string $dir The name of the folder to clear
     * @throws Exception if the dir can't be cleaned
     */
    public function clean(string $dir)
    {
        $this->app->plugins->run('dir_clean', $dir, $this);

        $this->delete($dir, false);
    }

    /**
     * Deletes expired files from a dir
     * @param string $dir The name of the folder
     * @param int $expires The threshold timestamp
     * @throws Exception if the dir can't be deleted
     */
    public function cleanExpired(string $dir, int $expires)
    {
        $this->app->plugins->run('dir_clean_old_files', $dir, $this);

        $this->checkFilename($dir);

        $iterator = $this->getIterator($dir, flag: \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                if ($file->getCTime() <= $expires) {
                    if (!unlink($file->getPathname())) {
                        throw new \Exception(App::__('error.file_delete', ['{FILE}' => $file->getPathname()]));
                    }
                }
            }
        }
    }
}
