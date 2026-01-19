<?php
/**
* The Picture Class
* @package Mars
*/

namespace Mars\Html;

/**
 * The Picture Class
 * Renders a picture
 */
class Picture extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected static string $tag = 'picture';

    /**
     * {@inheritdoc}
     */
    protected static array $properties = ['images'];

    /**
     * @see TagInterface::html()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        $img = new Img($this->app);

        $html = $this->open();
        $html.= $this->getImages($attributes['images']);
        $html.= $img->html('', $this->getAttributes($attributes)) . "\n";
        $html.= $this->close();

        return $html;
    }

    /**
     * Returns the html code of the source images
     * @param array $images The images
     * @return string
     */
    protected function getImages(array $images) : string
    {
        $html = '';
        foreach ($images as $image) {
            $media_array = [];
            if (isset($image['min'])) {
                $media_array[] = "(min-width:{$image['min']}px)";
            }
            if (isset($image['max'])) {
                $media_array[] = "(max-width:{$image['max']}px)";
            }

            $media = implode(' and ', $media_array);

            $html.= '<source media="' . $media . '" srcset="' . $this->app->escape->html($image['url']) . '">' . "\n";
        }

        return $html;
    }
}
