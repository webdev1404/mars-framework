<?php
/**
* The File Class
* @package Mars
*/

namespace Mars\Filesystem;

use Mars\App;
use Mars\App\Kernel;
use Mars\Dir as DirObj;
use Mars\File as FileObj;

/**
 * The File Class
 * Filesystem functionality
 */
class File
{
    use Kernel;

    /**
     * Returns a File object
     * @param string $filename The filename
     * @return \Mars\File The File object
     */
    protected function getObj(string $filename) : FileObj
    {
        return new FileObj($filename);
    }

    /**
     * Check that the filename [file/folder] doesn't contain invalid chars. and is located in the right path. Throws a fatal error for an invalid filename
     * @param string $filename The filename
     * @return static
     * @throws Exception if the filename is not valid
     */
    public function check(string $filename) : static
    {
        $this->getObj($filename)->check();

        return $this;
    }

    /**
     * Builds a filename from its parts
     * @param array $parts The parts to build the filename from
     * @return \Mars\File The new File instance
     */
    public function build(array $parts) : FileObj
    {
        $parts = array_filter($parts);

        $filename = '/' . rtrim(implode('/', $parts), '/');

        return new FileObj($filename);
    }

    /**
     * Returns a File instance
     * @param string $filename The filename
     * @return \Mars\File The File instance
     */
    public function get(string $filename) : FileObj
    {
        return $this->getObj($filename);
    }

    /**
     * The directory of the file. Will be an empty string if the file is in the root directory
     * @param string $filename The filename
     * @return \Mars\Dir|null The directory of the file. Will be null if the file has no parent dir
     */
    public function getDir(string $filename) : ?DirObj
    {
        return $this->getObj($filename)->dir;
    }

    /**
     * Alias of getDir
     */
    public function getPath(string $filename) : ?DirObj
    {
        return $this->getObj($filename)->path;
    }

    /**
     * Returns the filename without the extension
     * @param string $filename The filename
     * @return string The filename, without the extension
     */
    public function getStem(string $filename) : string
    {
        return $this->getObj($filename)->stem;
    }

    /**
     * Returns the full path of a file without the extension
     * @param string $filename The filename
     * @return string The full path of the file without the extension
     */
    public function getFullStem(string $filename) : string
    {
        return $this->getObj($filename)->full_stem;
    }

    /**
     * Returns the name of a file
     * @param string $filename The filename
     * @return string The name of the file (basename)
     */
    public function getName(string $filename) : string
    {
        return $this->getObj($filename)->name;
    }

    /**
     * Returns the mime type of a file
     * @param string $filename The filename
     * @return bool|string The mime type of the file
     */
    public function getType(string $filename) : bool|string
    {
        return $this->getObj($filename)->type;
    }

    /**
     * Returns the size of a file
     * @param string $filename The filename
     * @return bool|int
     */
    public function getSize(string $filename) : bool|int
    {
        return $this->getObj($filename)->size;
    }

    /**
     * Checks if a file exists
     * @param string $filename The filename
     * @return bool True if the file exists, false otherwise
     */
    public function exists(string $filename) : bool
    {
        return $this->getObj($filename)->exists;
    }

    /**
     * Returns the extension of a file in lowercase. Eg: jpg
     * @param string $filename The filename
     * @param bool $add_dot If true, will also return the dot of the extension
     * @return string The extension
     */
    public function getExtension(string $filename, bool $add_dot = false) : string
    {
        return $this->getObj($filename)->getExtension($add_dot);
    }

    /**
     * Adds extension to filename
     * @param string $filename The filename to append the extension to
     * @param string $extension The extension
     * @return string The filename + extension
     */
    public function addExtension(string $filename, string $extension) : string
    {
        return $this->getObj($filename)->addExtension($extension);
    }

    /**
     * Returns the relative path of a filename. Eg: /var/www/mars/dir/some_file.txt => dir/some_file.txt
     * @param string $filename The filename
     * @param string $path The path to test against. If empty $this->app->base_path is used
     * @return string The relative path
     */
    public function getRel(string $filename, string $path = '') : string
    {
        return $this->getObj($filename)->getRel($path);
    }

    /**
     * Returns the prefix of a file
     * @param string $filename The name of the file
     * @param int $chars The number of chars of the returned prefix
     * @return string
     */
    public function getPrefix(string $filename, int $chars = 4) : string
    {
        return $this->getObj($filename)->getPrefix($chars);
    }

    /**
     * Appends $append to $filename (before the extension)
     * @param string $filename The filename
     * @param string $append The text to append
     * @return \Mars\File The new filename
     */
    public function append(string $filename, string $append) : FileObj
    {
        return $this->getObj($filename)->append($append);
    }

    /**
     * Generates a random file name
     * @param string $extension The extension of the file, if any
     * @return string A random filename
     */
    public function getRandom(string $extension = '') : string
    {
        $filename = $this->app->random->getString();
        
        return $this->getObj($filename)->addExtension($extension);
    }

    /**
     * Returns a temporary filename
     * @param string $name The name of the file, if any
     * @param string $dir The dir of the temp. filename. If empty $this->app->tmp_path is used
     * @return FileObj The temporary filename
     */
    public function getTmp(string $name = '', string $dir = '') : FileObj
    {
        if (!$dir) {
            $dir = $this->app->tmp_path;
        }

        $tmp_filename = $dir . '/';
        if ($name) {
            $tmp_filename.= basename($name) . '-';
        }
        $tmp_filename .= $this->app->random->getString() . time();
        

        return new FileObj($tmp_filename);
    }

    /**
     * Reads the content of a file
     * @param string $filename The filename
     * @return string The contents of the file
     * @throws Exception if the file can't be read
     */
    public function read(string $filename) : string
    {
        return $this->getObj($filename)->read();
    }

    /**
     * Writes to a file
     * @param string $filename The filename
     * @param string $content The content to write
     * @param bool $append If true will append the data to the file rather than create the file
     * @return int Returns the number of written bytes
     * @throws Exception if the file can't be written
     */
    public function write(string $filename, string $content, bool $append = false) : int
    {
        return $this->getObj($filename)->write($content, $append);
    }

    /**
     * Deletes a file
     * @param string $filename The filename to delete
     * @return static
     * @throws Exception if the file can't be deleted
     */
    public function delete(string $filename) : static
    {
        $this->getObj($filename)->delete();

        return $this;
    }

    /**
     * Copies a file
     * @param string $source_filename The source file
     * @param string $destination_filename The destination file
     * @return \Mars\File The new file
     * @throws Exception if the file can't be copied
     */
    public function copy(string $source_filename, string $destination_filename) : FileObj
    {
        return $this->getObj($source_filename)->copy($destination_filename);
    }

    /**
     * Moves a file
     * @param string $source_filename The source file
     * @param string $destination_filename The destination file
     * @return FileObj The moved file
     * @throws Exception if the file can't be moved
     */
    public function move(string $source_filename, string $destination_filename) : FileObj
    {
        return $this->getObj($source_filename)->move($destination_filename);
    }

    /**
     * Outputs a file for download. Notice: It doesn't call die after it outputs the content,it is the caller's job to do it
     * @param string $filename The filename to output
     * @param string $output_name The name under which the user will be prompted to save the file
     * @throws Exception if the file can't be opened
     */
    public function promptForDownload(string $filename, string $output_name = '')
    {
        $this->app->plugins->run('file.prompt.for.download', $filename, $output_name, $this);

        $f = fopen($filename, 'r');
        if ($f === false) {
            throw new \Exception(App::__('error.file.read', ['{FILE}' => $filename]));
        }

        $size = filesize($filename);
        if (!$output_name) {
            $output_name = basename($filename);
        }

        header('Content-Type: ' . $this->getType($filename));
        header('Content-Length: ' . $size);
        header('Content-Disposition: attachment; filename="' . $this->app->filter->filename($output_name) . '"');

        while ($data = fread($f, 65536)) {
            echo $data;
        }

        fclose($f);
    }
}
