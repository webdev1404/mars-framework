<?php
/**
* The Recaptcha2 Captcha Driver Class
* @package Mars
*/

namespace Mars\Captcha;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Recaptcha2 Captcha Driver Class
 * Captcha driver which uses Recaptcha2
 */
class Recaptcha2 implements CaptchaInterface
{
    use Kernel;

    /**
     * Builds the recaptcha2 object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        if (!$this->app->config->captcha_recaptcha_public_key || !$this->app->config->captcha_recaptcha_private_key) {
            throw new \Exception('The recaptcha2 public and private keys must be set');
        }

        $this->app->document->javascript->load('https://www.google.com/recaptcha/api.js');
    }

    /**
     * @see CaptchaInterface::check()
     * {@inheritdoc}
     */
    public function check() : bool
    {
        $post_data = [
            'secret' => $this->app->config->captcha_recaptcha_private_key,
            'response' => $this->app->request->post('g-recaptcha-response'),
            'remoteip' => $this->app->ip
        ];

        $response = $this->app->web->request->post('https://www.google.com/recaptcha/api/siteverify', $post_data);

        $data = $response->getJson();

        return $data->success;
    }

    /**
     * @see CaptchaInterface::output()
     * {@inheritdoc}
     */
    public function output()
    {
        echo '<div class="g-recaptcha" data-sitekey="' . $this->app->config->captcha_recaptcha_public_key . '"></div>';
    }
}
