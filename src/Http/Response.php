<?php
/**
* The Response Class
* @package Mars
*/

namespace Mars\Http;

use Mars\App;
use Mars\LazyLoadProperty;
use Mars\App\Kernel;
use Mars\App\Lazyload;
use Mars\App\Handlers;
use Mars\Http\Response\ResponseInterface;
use Mars\Http\Response\Data\Cookies;
use Mars\Http\Response\Data\Headers;

/**
 * The Response Class
 * Outputs the system's html/ajax response
 */
class Response
{
    use Kernel;
    use Lazyload;

    /**
     * @var array $supported_responses The supported responses types
     */
    protected array $supported_responses = [
        'json' => \Mars\Http\Response\Json::class,
        'html' => \Mars\Http\Response\Html::class
    ];

    /**
     * @var Handlers $responses The $responses object
     */
    public protected(set) Handlers $responses {
        get {
            if (isset($this->responses)) {
                return $this->responses;
            }

            $this->responses = new Handlers($this->supported_responses, ResponseInterface::class, $this->app);

            return $this->responses;
        }
    }

    /**
     * @var Cookies $cookies The cookies object
     */
    #[LazyLoadProperty]
    public Cookies $cookies;

    /**
     * @var Headers $headers The headers object
     */
    #[LazyLoadProperty]
    public Headers $headers;
    
    /**
     * @var string $type The response type
     */
    public string $type = 'html' {
        set(string $type) {
            switch ($type) {
                case 'json':
                case 'ajax':
                    $this->type = 'json';
                    break;
                default:
                    $this->type = 'html';
            }
        }
    }

    /**
     * Builds the Response object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->lazyLoad($app);
    }

    /**
     * Outputs the $content
     * @param mixed $content The content to output
     */
    public function output($content)
    {
        $this->headers->output();

        $this->responses->get($this->type)->output($content);
    }
}
