<?php
/**
* The Base Image Class
* @package Mars
*/

namespace Mars\Images;

use GdImage;
use Mars\App;
use Mars\App\Kernel;

/**
 * The Base Image Class
 */
abstract class Image
{
    use Kernel;

    /**
     * @var string $filename The image's filename
     */
    protected string $filename = '';

    /**
     * @var string $mime_type The image's mime type
     */
    protected $mime_type = '';

    /**
     * @var int $quality The image quality of the resulting images
     */
    protected int $quality = 80;

    /**
     * @var bool $optimize If true, the images will be optimized when processed/uploaded
     */
    protected bool $optimize = false;

    /**
     * @var string $optimize_command The optimization command
     */
    protected string $optimize_command = '';

    /**
     * @see ImageInterface::__construct()
     * {@inheritdoc}
     */
    public function __construct(string $filename, App $app)
    {
        $this->app = $app;
        $this->filename = $filename;
        $this->optimize = $this->app->config->image_optimize;
    }

    /**
     * @see ImageInterface::isValid()
     * {@inheritdoc}
     */
    public function isValid() : bool
    {
        if (!is_file($this->filename)) {
            throw new \Exception("Image does not exist: {$this->filename}");
        }

        $finfo = \finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = \finfo_file($finfo, $this->filename);
        finfo_close($finfo);

        return $mime_type == $this->mime_type;
    }

    /**
     * @see ImageInterface::getSize()
     * {@inheritdoc}
     */
    public function getSize() : array
    {
        $info = getimagesize($this->filename);
        if (!$info) {
            throw new \Exception("Unable to get the size of image: {$this->filename}");
        }

        return [$info[0], $info[1]];
    }

    /**
     * @see ImageInterface::getWidth()
     * {@inheritdoc}
     */
    public function getWidth(): int
    {
        $info = getimagesize($this->filename);
        if (!$info) {
            throw new \Exception("Unable to get the size of image: {$this->filename}");
        }

        return $info[0];
    }

    /**
     * @see ImageInterface::getHeight()
     * {@inheritdoc}
     */
    public function getHeight(): int
    {
        $info = getimagesize($this->filename);
        if (!$info) {
            throw new \Exception("Unable to get the size of image: {$this->filename}");
        }

        return $info[1];
    }

    /**
     * @see ImageInterface::getRatio()
     * {@inheritdoc}
     */
    public function getRatio() : float
    {
        [$width, $height] = $this->getSize();

        return $width / $height;
    }

    /**
     * @see ImageInterface::create()
     * {@inheritdoc}
     */
    public function create(int $width, int $height, GdImage $source) : GdImage
    {
        return imagecreatetruecolor($width, $height);
    }

    /**
     * @see ImageInterface::optimize()
     * {@inheritdoc}
     */
    public function optimize() : bool
    {
        if (!$this->optimize || !$this->optimize_command) {
            return false;
        }

        $filename = escapeshellarg($this->filename);
        $command = str_replace('{FILENAME}', $filename, $this->optimize_command);

        exec($command, $output, $result_code);

        if ($result_code == 0) {
            return true;
        }

        return false;
    }
}
