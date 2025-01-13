<?php
/**
* The Video Class
* @package Mars
*/

namespace Mars\Html;

/**
 * The Video Class
 * Renders a video
 */
class Video extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected static string $tag = 'video';

    /**
     * @var array $types The supported video types
     */
    protected static array $types = [
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
        'ogg' => 'video/ogg'
    ];

    /**
     * {@inheritdoc}
     */
    protected static array $properties = ['urls'];

    /**
     * @see \Mars\Html\TagInterface::html()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        $html = $this->open($this->getAttributes($attributes));
        $html.= $this->getSource($attributes['urls']);
        $html.= $this->close();

        return $html;
    }

    /**
     * Returns the html code of the source urls
     * @param array $urls The urls
     * @return string
     */
    protected function getSource(array $urls) : string
    {
        $html = '';
        foreach ($urls as $url) {
            $type = $this->getType($url);
            if ($type) {
                $type = ' type="' . $type . '"';
            }

            $html.= '<source src="' . $this->app->escape->html($url) . '"'. $type .'>' . "\n";
        }

        return $html;
    }

    /**
     * Returns the type of the url
     * @param string $url The url
     * @return string
     */
    protected function getType(string $url) : string
    {
        $ext = $this->app->file->getExtension($url);
        if (isset(static::$types[$ext])) {
            return static::$types[$ext];
        }

        return '';
    }
}
