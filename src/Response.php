<?php
/**
* The Response Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Lazyload\GhostTrait;
use Mars\Response\Types\DriverInterface;
use Mars\Response\Cookies;
use Mars\Response\Headers;
use Mars\Response\Push;

/**
 * The Response Class
 * Outputs the system's html/ajax response
 */
class Response
{
    use InstanceTrait;
    use GhostTrait;

    /**
     * @var Handlers $responses The $responses object
     */
    public protected(set) Handlers $responses {
        get {
            if (isset($this->responses)) {
                return $this->responses;
            }

            $this->responses = new Handlers($this->supported_responses, DriverInterface::class, $this->app);

            return $this->responses;
        }
    }

    /**
     * @var Cookies $cookies The cookies object
     */
    #[LazyLoad]
    public Cookies $cookies;

    /**
     * @var Headers $headers The headers object
     */
    #[LazyLoad]
    public Headers $headers;
    
    /**
     * @var string $type The response type
     */
    public string $type = 'html' {
        set(string $type) {
            switch ($type) {
                case 'ajax':
                case 'json':
                    $this->type = 'ajax';
                    break;
                default:
                    $this->type = 'html';
            }
        }
    }

    /**
     * @var array $supported_$responses The supported $responses types
     */
    protected array $supported_responses = [
        'ajax' => \Mars\Response\Types\Ajax::class,
        'html' => \Mars\Response\Types\Html::class
    ];

    /**
     * Builds the Response object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);

        $this->app = $app;
    }

    /**
     * Returns the converted content to $type
     * @param mixed $content The content
     * @return mixed
     */
    public function get($content)
    {
        return $this->responses->get($this->type)->get($content);
    }

    /**
     * Outputs the $content
     * @param string string The content to output
     */
    public function output(string $content)
    {
        $this->headers->output();

        $this->responses->get($this->type)->output($content);
    }
}
