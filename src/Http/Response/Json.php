<?php
/**
* The Json Response Class
* @package Mars
*/

namespace Mars\Http\Response;

/**
 * The Json Response Class
 * Generates a json response
 */
class Json extends Response implements ResponseInterface
{
    /**
     * @see ResponseInterface::output()
     * {@inheritDoc}
     */
    public function output(mixed $content)
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

        $data = [...$this->app->json->data, ...$this->app->array->get($content)];
        if ($data) {
            $data_array['data'] = $data;
        }

        $content = $this->app->json->encode($data_array);

        $this->outputContent($content);
    }
}
