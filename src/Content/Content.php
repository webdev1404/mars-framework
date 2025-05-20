<?php
/**
* The Base Content Class
* @package Mars
*/

namespace Mars\Content;

use Mars\App;
use Mars\App\InstanceTrait;

/**
 * The Base Content Class
 * Base class for content classes
 */
abstract class Content
{
    use InstanceTrait;
    
    /**
     * @var string $name The template's name
     */
    protected string $name = '';
    
    /**
     * @var string $title The title tag of the page
     */
    protected string $title = '';
    
    /**
     * @var array $meta Meta data of the page
     */
    protected array $meta = [];
    
    /**
     * Builds the Content object
     * @param string $name The name of the page/template etc..
     * @param string $title The title tag of the page
     * @param array $meta Meta data of the page
     * @param App $app The app object
     */
    public function __construct(string $name, string $title = '', array $meta = [], ?App $app = null)
    {
        $this->app = $app ?? $this->getApp();
        $this->name = $name;
        $this->title = $title;
        $this->meta = $meta;        
    }
    
    /**
     * Outputs the title and meta tags
     */
    protected function outputTitleAndMeta()
    {
        if ($this->title) {
            $this->app->document->title->set($this->title);
        }
        
        if ($this->meta) {
            foreach ($this->meta as $name => $val) {
                $this->app->document->meta->set($name, $val);
            }
        }        
    }
}
