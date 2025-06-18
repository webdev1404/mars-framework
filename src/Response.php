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

/**
 * The Response Class
 * Outputs the system's html/ajax response
 */
class Response
{
    use InstanceTrait;
    use GhostTrait;

    /**
     * @var array $supported_$responses The supported $responses types
     */
    protected array $supported_responses = [
        'json' => \Mars\Response\Types\Json::class,
        'html' => \Mars\Response\Types\Html::class
    ];

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
        $this->lazyLoad($app);

        $this->app = $app;
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
