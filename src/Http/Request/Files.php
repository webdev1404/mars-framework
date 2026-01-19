<?php
/**
* The FILES Request Class
* @package Mars
*/

namespace Mars\Http\Request;

use Mars\App;

/**
 * The FILES Request Class
 * Handles the $_FILES interactions
 */
class Files extends Input
{
    /**
     * @var array $disallowed_extensions The extensions of the files which are disallowed at upload
     */
    protected array $disallowed_extensions = [
        'php', 'inc', 'cgi', 'pl', 'py', 'exe', 'com', 'bat', 'dll', 'sh', 'bin', 'svg',
        'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'pht', 'phps', 'phar',
        'asp', 'aspx', 'asax', 'asa', 'jsp', 'htaccess', 'htpasswd', 'js', 'vbs', 'scr', 'ps1', 'jar'
    ];

    /**
     * Builds the Files Request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->data = &$_FILES;
    }

    /**
     * Uploads a file/files
     * @param string $name The name of the file
     * @param string $upload_dir Destination folder
     * @param string|array $allowed_extensions Array containing the extensions of the file that are allowed to be uploaded. If '*' is passed all types of files are allowed [minus those deemed unsafe]
     * @param bool $append_suffix If true, will always generate a random suffix for the uploaded filename
     * @param bool $append_suffix_if_file_exists If true, will generate a random suffix if the file already exists
     * @return array Returns the list of uploaded files
     * @throws Exception if the upload failed
     */
    public function upload(string $name, string $upload_dir, string|array $allowed_extensions = [], bool $append_suffix = false, bool $append_suffix_if_file_exists = true) : array
    {
        $this->app->plugins->run('http.request.files.upload', $name, $upload_dir, $allowed_extensions, $append_suffix, $append_suffix_if_file_exists, $this);

        if (!$this->has($name)) {
            return [];
        }

        $uploaded_files = [];
        $file = $this->data[$name];
        $tmp_array = (array)$file['tmp_name'];
        $errors_array = (array)$file['error'];
        $name_array = (array)$file['name'];

        foreach ($tmp_array as $i => $tmp_filename) {
            if (!$tmp_filename) {
                continue;
            }

            $name = $name_array[$i];
            $extension = $this->app->file->getExtension($name);

            if (!is_uploaded_file($tmp_filename)) {
                throw new \Exception($this->getUploadError($errors_array[$i], $name));
            }

            if (in_array($extension, $this->disallowed_extensions)) {
                throw new \Exception(App::__('error.upload.invalid_type', ['{FILE}' => $name]));
            }
            if ($allowed_extensions && $allowed_extensions != '*') {
                if (!in_array($extension, (array)$allowed_extensions)) {
                    throw new \Exception(App::__('error.upload.invalid_type', ['{FILE}' => $name]));
                }
            }

            $filename = $this->getFilename($name, $upload_dir, $append_suffix, $append_suffix_if_file_exists);
            
            if (move_uploaded_file($tmp_filename, $filename)) {
                $this->app->plugins->run('http.request.files.upload.success', $filename, $this);

                $uploaded_files[$name] = $filename;
            } else {
                $this->app->plugins->run('http.request.files.upload.error', $filename, $tmp_filename, $upload_dir, $this);
                
                throw new \Exception($this->getUploadError($errors_array[$i], $name));
            }
        }

        return $uploaded_files;
    }

    /**
     * Returns the filename where the file will be uploaded
     * @param string $name The name of the file
     * @param string $upload_dir Destination folder
     * @param bool $append_suffix If true, will always generate a random suffix for the uploaded filename
     * @param bool $append_suffix_if_file_exists If true, will generate a random suffix if the file already exists
     * @return string
     */
    protected function getFilename(string $name, string $upload_dir, bool $append_suffix = false, bool $append_suffix_if_file_exists = true) : string
    {
        $filename = $upload_dir . '/' . $this->app->filter->filename($name);

        if (!$append_suffix && $append_suffix_if_file_exists) {
            if (is_file($filename)) {
                $append_suffix = true;
            }
        }

        if ($append_suffix) {
            $suffix = $this->app->random->getString(20);
            $filename = $this->app->file->append($filename, '-' . $suffix);
        }

        return $filename;
    }

    /**
     * Returns the upload error
     * @param int $code The error code
     * @param string $file The file
     * @return string The error message
     */
    protected function getUploadError(int $code, string $file) : string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return App::__('error.upload.size', ['{SIZE}' => ini_get('upload_max_filesize')]);
            case UPLOAD_ERR_PARTIAL:
                return App::__('error.upload.partial');
            case UPLOAD_ERR_NO_FILE:
                return App::__('error.upload.nofile');
            case UPLOAD_ERR_NO_TMP_DIR:
                return App::__('error.upload.tmp');
        }

        return App::__('error.upload.generic', ['{FILE}' => $file]);
    }
}
