<?php
/**
* The Dir Class
* @package Mars
*/

namespace Mars\Filesystem;

use Mars\App\Kernel;
use Mars\Dir as DirObj;

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
     * Check that the filename [file/folder] doesn't contain invalid characters and is located in the right path. Throws a fatal error for an invalid filename
     * @see File::check()
     */
    public function check(string $dir) : static
    {
        new DirObj($dir)->check();

        return $this;
    }

    /**
     * Checks if a dir exists
     * @param string $dir The dir
     * @return bool True if the dir exists
     */
    public function exists(string $dir) : bool
    {
        return new DirObj($dir)->exists;
    }

    /**
     * Builds a path from its parts
     * @param array $parts The parts to build the path from
     * @return \Mars\Dir The new Dir instance
     */
    public function build(array $parts) : DirObj
    {
        $parts = array_filter($parts);

        $path = '/' . trim(implode('/', $parts), '/');

        return new DirObj($path);
    }

    /**
     * Checks if a filename is inside a dir
     * @param string $dir The dir
     * @param string $filename The filename to check
     * @param bool $check_exists If true, will check if the file actually exists
     * @return bool True if $filename is inside $dir
     */
    public function contains(string $dir, string $filename, bool $check_exists = true) : bool
    {
        return new DirObj($dir)->contains($filename, $check_exists);
    }

    /**
     * Returns the dirs and files from the specified folder
     * @param string $dir The dir
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will return the file's full path
     * @param array $extensions If specified, will return only the files matching the extensions
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $exclude_extensions If specified, will exclude the files matching the extensions
     * @return array The files
     */
    public function get(string $dir, bool $recursive = true, bool $full_path = true, array $extensions = [], array $exclude_dirs = [], array $exclude_extensions = []) : array
    {
        return new DirObj($dir)->get($recursive, $full_path, $extensions, $exclude_dirs, $exclude_extensions);
    }

    /**
     * Returns the dirs from the specified folder
     * @param string $dir The dir
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @return array The dirs
     */
    public function getDirs(string $dir, bool $recursive = true, bool $full_path = true, array $exclude_dirs = []) : array
    {
        return new DirObj($dir)->getDirs($recursive, $full_path, $exclude_dirs);
    }

    /**
     * Returns the dirs from the specified folder, sorted
     * @see Dir::getDirs()
     */
    public function getDirsSorted(string $dir, bool $recursive = true, bool $full_path = true, array $exclude_dirs = []) : array
    {
        return new DirObj($dir)->getDirsSorted($recursive, $full_path, $exclude_dirs);
    }

    /**
     * Returns the files from the specified folder
     * @param string $dir The dir
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will return the file's full path
     * @param array $extensions If specified, will return only the files matching the extensions
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $exclude_extensions If specified, will exclude the files matching the extensions
     * @param bool $include_dirs If true, will include the directories in the result
     * @return array The files
     */
    public function getFiles(string $dir, bool $recursive = true, bool $full_path = true, array $extensions = [], array $exclude_dirs = [], array $exclude_extensions = [], bool $include_dirs = false) : array
    {
        return new DirObj($dir)->getFiles($recursive, $full_path, $extensions, $exclude_dirs, $exclude_extensions, $include_dirs);
    }

    /**
     * Returns the files from the specified folder, sorted
     * @see Dir::getFiles()
     */
    public function getFilesSorted(string $dir, bool $recursive = true, bool $full_path = true, array $extensions = [], array $exclude_dirs = [], array $exclude_extensions = [], bool $include_dirs = false) : array
    {
        return new DirObj($dir)->getFilesSorted($recursive, $full_path, $extensions, $exclude_dirs, $exclude_extensions, $include_dirs);
    }

    /**
     * Returns the directory tree from the specified folder
     * @param string $dir The dir
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param string $prefix The prefix to add to each dir (used for indentation)
     * @param bool $sort If true, will sort the dirs
     * @return array The dirs as a tree
     */
    public function getDirsTree(string $dir, bool $recursive = true, bool $full_path = false, array $exclude_dirs = [], string $prefix = '', bool $sort = true) : array
    {
        return new DirObj($dir)->getDirsTree($recursive, $full_path, $exclude_dirs, $prefix, $sort, null, 1);
    }

    /**
     * Returns the files from the specified folder, as a tree
     * @param string $dir The dir
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $extensions If specified, will return only the files matching the extensions
     * @param array $exclude_extensions If specified, will exclude the files matching the extensions
     * @param string $prefix The prefix to add to each file (used for indentation)
     * @param bool $sort If true, will sort the files
     * @return array The files as a tree
     */
    public function getFilesTree(string $dir, bool $recursive = true, bool $full_path = false, array $exclude_dirs = [], array $extensions = [], array $exclude_extensions = [], string $prefix = '', bool $sort = true) : array
    {
        return new DirObj($dir)->getFilesTree($recursive, $full_path, $exclude_dirs, $extensions, $exclude_extensions, $prefix, $sort, null, 1);
    }

    /**
     * Create a folder. Does nothing if the folder already exists
     * @param string $dir The dir
     * @param int|null $permissions The permissions to set for the folder. If null, no permissions will be set
     * @param bool $recursive If true, will create parent folders if they don't exist
     * @return \Mars\Dir The Dir instance
     * @throws Exception if the folder can't be created
     */
    public function create(string $dir, ?int $permissions = null, bool $recursive = false) : DirObj
    {
        return new DirObj($dir)->create($permissions, $recursive);
    }

    /**
     * Copies a dir
     * @param string $dir The dir
     * @param string $destination The destination dir
     * @return \Mars\Dir The new dir or null if $dir doesn't exist
     * @throws Exception If folders can't be created/files can't be copied
     */
    public function copy(string $dir, string $destination) : ?DirObj
    {
        return new DirObj($dir)->copy($destination);
    }

    /**
     * Moves a dir
     * @param string $dir The dir
     * @param string $destination The destination dir
     * @return \Mars\Dir The new dir or null if $dir doesn't exist
     * @throws Exception if the dir can't be moved
     */
    public function move(string $dir, string $destination_dir) : ?DirObj
    {
        return new DirObj($dir)->move($destination_dir);
    }

    /**
     * Deletes a dir
     * @param string $dir The dir
     * @param bool $delete_dir If true, will delete the dir itself; if false, will clean it
     * @return \Mars\Dir The Dir instance or null if $dir doesn't exist
     * @throws Exception if the dir can't be deleted
     */
    public function delete(string $dir, bool $delete_dir = true) : ?DirObj
    {
        return new DirObj($dir)->delete($delete_dir);
    }

    /**
     * Deletes all the files/subdirectories from a directory but does not delete the folder itself
     * @param string $dir The dir
     * @return \Mars\Dir The Dir instance
     * @throws Exception if the dir can't be cleaned
     */
    public function clean(string $dir) : DirObj
    {
        return new DirObj($dir)->clean();
    }

    /**
     * Deletes expired files from a dir
     * @param string $dir The name of the folder
     * @param int $expires The threshold timestamp
     * @return \Mars\Dir|null Will return null if the dir itself was deleted, the dir itself otherwise
     * @throws Exception If files can't be deleted
     */
    public function cleanExpired(string $dir, int $expires) : ?DirObj
    {
        return new DirObj($dir)->cleanExpired($expires);
    }
}
