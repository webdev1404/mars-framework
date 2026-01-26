<?php
/**
* The Json Response Class
* @package Mars
*/

namespace Mars\Http\Response;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Json Response Class
 * Generates a json response
 */
class Json implements ResponseInterface
{
    use Kernel;

    /**
     * @see ResponseInterface::output()
     * {@inheritDoc}
     */
    public function output(mixed $content)
    {
        header('Content-Type: application/json', true);

        $data = [
            'success' => $this->app->success(),
            'message' => $this->app->messages->getFirst(),
            'error' => $this->app->errors->getFirst(),
            'data' => [...$this->app->json->data, ...$this->app->array->get($content)]
        ];

        echo $this->app->json->encode($data);
    }
}
