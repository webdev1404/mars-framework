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

        if (!$this->app->config->captcha->recaptcha->site_key || !$this->app->config->captcha->recaptcha->secret_key) {
            throw new \Exception('The reCAPTCHA v2 configuration is incomplete. Please set both captcha.recaptcha.site_key and captcha.recaptcha.secret_key in your configuration.');
        }

        $this->app->document->js->load('https://www.google.com/recaptcha/api.js');
    }

    /**
     * @see CaptchaInterface::check()
     * {@inheritDoc}
     */
    public function check() : bool
    {
        $post_data = [
            'secret' => $this->app->config->captcha->recaptcha->secret_key,
            'response' => $this->app->request->post->get('g-recaptcha-response'),
            'remoteip' => $this->app->ip
        ];

        $response = $this->app->web->request->post('https://www.google.com/recaptcha/api/siteverify', $post_data);

        $data = $response->getJson();
        if ($data === null) {
            return false;
        }

        return $data['success'] ?? false;
    }

    /**
     * @see CaptchaInterface::output()
     * {@inheritDoc}
     */
    public function output()
    {
        echo '<div class="g-recaptcha" data-sitekey="' . $this->app->escape->html($this->app->config->captcha->recaptcha->site_key) . '"></div>';
    }
}
