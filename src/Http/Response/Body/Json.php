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
     * @see BodyInterface::send()
     * {@inheritDoc}
     */
    public function send(mixed $content) : string
    {
        header('Content-Type: application/json', true);

        $data = ['success' => $this->app->success()];

        if ($this->app->messages->count()) {
            $data['messages'] = $this->app->messages->get();
        }
        if ($this->app->warnings->count()) {
            $data['warnings'] = $this->app->warnings->get();
        }
        if ($this->app->info->count()) {
            $data['info'] = $this->app->info->get();
        }
        if ($this->app->errors->count()) {
            $data['errors'] = $this->app->errors->get();
        }

        if ($content) {
            $data['data'] = $content;
        }

        $content = $this->app->json->encode($data);

        echo $content;

        return $content;
    }

    /**
     * @see BodyInterface::redirect()
     * {@inheritDoc}
     */
    public function redirect(string $url)
    {
        header('Content-Type: application/json', true);

        $data = ['success' => $this->app->success(), 'redirect' => $url];
        
        $content = $this->app->json->encode($data);

        echo $content;

        die;
    }
}
