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
    public function check(string $path) : static
    {
        new DirObj($path)->check();

        return $this;
    }

    /**
     * Checks if a dir exists
     * @param string $path The folder path
     * @return bool True if the dir exists
     */
    public function exists(string $path) : bool
    {
        return new DirObj($path)->exists;
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
     * @param string $path The folder path
     * @param string $filename The filename to check
     * @param bool $check_exists If true, will check if the file actually exists
     * @return bool True if $filename is inside the dir
     */
    public function contains(string $path, string $filename, bool $check_exists = true) : bool
    {
        return new DirObj($path)->contains($filename, $check_exists);
    }

    /**
     * Returns the dirs and files from the specified folder
     * @param string $path The folder path
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will return the file's full path
     * @param array $extensions If specified, will return only the files matching the extensions
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $exclude_extensions If specified, will exclude the files matching the extensions
     * @return array The files and dirs
     */
    public function get(string $path, bool $recursive = true, bool $full_path = true, array $extensions = [], array $exclude_dirs = [], array $exclude_extensions = []) : array
    {
        return new DirObj($path)->get($recursive, $full_path, $extensions, $exclude_dirs, $exclude_extensions);
    }

    /**
     * Returns the dirs from the specified folder
     * @param string $path The folder path
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @return array The dirs
     */
    public function getDirs(string $path, bool $recursive = true, bool $full_path = true, array $exclude_dirs = []) : array
    {
        return new DirObj($path)->getDirs($recursive, $full_path, $exclude_dirs);
    }

    /**
     * Returns the dirs from the specified folder, sorted
     * @see Dir::getDirs()
     */
    public function getDirsSorted(string $path, bool $recursive = true, bool $full_path = true, array $exclude_dirs = []) : array
    {
        return new DirObj($path)->getDirsSorted($recursive, $full_path, $exclude_dirs);
    }

    /**
     * Returns the files from the specified folder
     * @param string $path The folder path
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will return the file's full path
     * @param array $extensions If specified, will return only the files matching the extensions
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $exclude_extensions If specified, will exclude the files matching the extensions
     * @param bool $include_dirs If true, will include the directories in the result
     * @return array The files
     */
    public function getFiles(string $path, bool $recursive = true, bool $full_path = true, array $extensions = [], array $exclude_dirs = [], array $exclude_extensions = [], bool $include_dirs = false) : array
    {
        return new DirObj($path)->getFiles($recursive, $full_path, $extensions, $exclude_dirs, $exclude_extensions, $include_dirs);
    }

    /**
     * Returns the files from the specified folder, sorted
     * @see Dir::getFiles()
     */
    public function getFilesSorted(string $path, bool $recursive = true, bool $full_path = true, array $extensions = [], array $exclude_dirs = [], array $exclude_extensions = [], bool $include_dirs = false) : array
    {
        return new DirObj($path)->getFilesSorted($recursive, $full_path, $extensions, $exclude_dirs, $exclude_extensions, $include_dirs);
    }

    /**
     * Returns the directory tree from the specified folder
     * @param string $path The folder path
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param string $prefix The prefix to add to each dir (used for indentation)
     * @param bool $sort If true, will sort the dirs
     * @return array The dirs as a tree
     */
    public function getDirsTree(string $path, bool $recursive = true, bool $full_path = false, array $exclude_dirs = [], string $prefix = '', bool $sort = true) : array
    {
        return new DirObj($path)->getDirsTree($recursive, $full_path, $exclude_dirs, $prefix, $sort, null, 1);
    }

    /**
     * Returns the files from the specified folder, as a tree
     * @param string $path The folder path
     * @param bool $recursive If true will enum. recursive
     * @param bool $full_path If true it will return the file's full path
     * @param array $exclude_dirs Array of dirs to exclude, if any
     * @param array $extensions If specified, will return only the files matching the extensions
     * @param array $exclude_extensions If specified, will exclude the files matching the extensions
     * @param string $prefix The prefix to add to each file (used for indentation)
     * @param bool $sort If true, will sort the files
     * @return array The files as a tree
     */
    public function getFilesTree(string $path, bool $recursive = true, bool $full_path = false, array $exclude_dirs = [], array $extensions = [], array $exclude_extensions = [], string $prefix = '', bool $sort = true) : array
    {
        return new DirObj($path)->getFilesTree($recursive, $full_path, $exclude_dirs, $extensions, $exclude_extensions, $prefix, $sort, null, 1);
    }

    /**
     * Create a folder. Does nothing if the folder already exists
     * @param string $path The folder path
     * @param int|null $permissions The permissions to set for the folder. If null, no permissions will be set
     * @param bool $recursive If true, will create parent folders if they don't exist
     * @return \Mars\Dir The Dir instance
     * @throws Exception if the folder can't be created
     */
    public function create(string $path, ?int $permissions = null, bool $recursive = false) : DirObj
    {
        return new DirObj($path)->create($permissions, $recursive);
    }

    /**
     * Copies a dir
     * @param string $path The folder path
     * @param string $destination_path The destination path
     * @return \Mars\Dir The new dir or null if $path doesn't exist
     * @throws Exception If folders can't be created/files can't be copied
     */
    public function copy(string $path, string $destination_path) : ?DirObj
    {
        return new DirObj($path)->copy($destination_path);
    }

    /**
     * Moves a dir
     * @param string $path The folder path
     * @param string $destination_path The destination folder path
     * @return \Mars\Dir The new dir or null if $path doesn't exist
     * @throws Exception if the dir can't be moved
     */
    public function move(string $path, string $destination_path) : ?DirObj
    {
        return new DirObj($path)->move($destination_path);
    }

    /**
     * Deletes a dir
     * @param string $path The folder path
     * @param bool $delete_dir If true, will delete the dir itself; if false, will clean it
     * @return \Mars\Dir The Dir instance or null if $path doesn't exist
     * @throws Exception if the dir can't be deleted
     */
    public function delete(string $path, bool $delete_dir = true) : ?DirObj
    {
        return new DirObj($path)->delete($delete_dir);
    }

    /**
     * Deletes all the files/subdirectories from a directory but does not delete the folder itself
     * @param string $path The folder path
     * @return \Mars\Dir The Dir instance
     * @throws Exception if the dir can't be cleaned
     */
    public function clean(string $path) : DirObj
    {
        return new DirObj($path)->clean();
    }

    /**
     * Deletes expired files from a dir
     * @param string $path The name of the folder
     * @param int $expires The threshold timestamp
     * @return \Mars\Dir|null Will return null if the dir itself was deleted, the dir itself otherwise
     * @throws Exception If files can't be deleted
     */
    public function cleanExpired(string $path, int $expires) : ?DirObj
    {
        return new DirObj($path)->cleanExpired($expires);
    }
}
