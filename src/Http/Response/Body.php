<?php
/**
* The Response Body Class
* @package Mars
*/

namespace Mars\Http\Response;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\Lazyload;
use Mars\App\LazyLoadProperty;
use Mars\Http\Response\Body\Json;
use Mars\Http\Response\Body\Html;
use Mars\Http\Response\Body\Data\Data;
use Mars\Http\Response\Body\Data\Json as JsonData;
use Mars\Http\Response\Body\Data\Html as HtmlData;

/**
 * The Response Body Class
 * Handles the response body
 */
class Body
{
    use Kernel;
    use Lazyload;

    /**
     * @var Json $json The Json body handler
     */
    #[LazyLoadProperty]
    public protected(set) Json $json;

    /**
     * @var Html $html The Html body handler
     */
    #[LazyLoadProperty]
    public protected(set) Html $html;
    
    /**
     * @var mixed $content The content of the body
     */
    public protected(set) mixed $content = '';

    /**
     * Builds the Response Body object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->lazyLoad($app);
    }

    /**
     * Creates and returns a new Data object based on the passed data type
      * @return Data The created Data object
     */
    public function create(mixed $return_value, string $content = '') : Data
    {
          if ($return_value !== null) {
            if ($return_value instanceof Data) {
                return $return_value;
            }
            elseif (is_array ($return_value) || is_object($return_value)) {
                return new JsonData($return_value);
            } elseif (is_string($return_value)) {
                return new HtmlData($return_value);
            }
        }

        return new HtmlData($content);
    }

    /**
     * Sends the response body
     * @return string The sent content
     */
    public function send(Data $data) : string
    {
        return $this->{$data->type}->send($data->content);
    }
}

