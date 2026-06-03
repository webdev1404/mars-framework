<?php
/**
* The Json Response Class
* @package Mars
*/

namespace Mars\Http\Response\Body;

use Mars\App\Kernel;

/**
 * The Json Response Class
 * Generates a json response
 */
class Json implements BodyInterface
{
    use Kernel;

    /**
     * @see ResponseInterface::send()
     * {@inheritDoc}
     */
    public function send(mixed $content) : string
    {
        header('Content-Type: application/json', true);

        $data_array = ['success' => $this->app->success()];

        if ($this->app->messages->count()) {
            $data_array['messages'] = $this->app->messages->get();
        }
        if ($this->app->warnings->count()) {
            $data_array['warnings'] = $this->app->warnings->get();
        }
        if ($this->app->info->count()) {
            $data_array['info'] = $this->app->info->get();
        }
        if ($this->app->errors->count()) {
            $data_array['errors'] = $this->app->errors->get();
        }

        $data = $this->app->array->get($content);
        if ($data) {
            $data_array['data'] = $data;
        }

        $content = $this->app->json->encode($data_array);

        echo $content;

        return $content;
    }
}
