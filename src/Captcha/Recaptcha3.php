<?php
/**
* The Recaptcha3 Captcha Driver Class
* @package Mars
*/

namespace Mars\Captcha;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Recaptcha3 Captcha Driver Class
 * Captcha driver which uses Recaptcha3
 */
class Recaptcha3 implements CaptchaInterface
{
    use Kernel;

    /**
     * Builds the recaptcha3 object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        if (!$this->app->config->captcha->recaptcha->site_key || !$this->app->config->captcha->recaptcha->secret_key) {
            throw new \Exception('The reCAPTCHA v3 configuration is incomplete. Please set both captcha.recaptcha.site_key and captcha.recaptcha.secret_key in your configuration.');
        }

        $this->app->document->js->load('https://www.google.com/recaptcha/api.js?render=' . urlencode($this->app->config->captcha->recaptcha->site_key));
    }

    /**
     * @see CaptchaInterface::check()
     * {@inheritdoc}
     */
    public function check() : bool
    {
        $token = $this->app->request->post->get('recaptcha3-token');
        if (!$token) {
            return false;
        }

        $post_data = [
            'secret' => $this->app->config->captcha->recaptcha->secret_key,
            'response' => $token,
            'remoteip' => $this->app->ip
        ];

        $response = $this->app->web->request->post('https://www.google.com/recaptcha/api/siteverify', $post_data);

        $data = $response->getJson();
        if ($data === null) {
            return false;
        }

        $success = $data['success'] ?? false;
        if (!$success) {
            return false;
        }

        $score = $data['score'] ?? 0;
        if ($score >= $this->app->config->captcha->recaptcha->min_score) {
            return true;
        }

        return false;
    }

    /**
     * @see CaptchaInterface::output()
     * {@inheritdoc}
     */
    public function output()
    {
        $field_name = 'recaptcha3-token-' . $this->app->random->getString(32);

        echo $this->app->html->hidden('recaptcha3-token', '', ['id' => $field_name]);
        ?>
        <script>
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo $this->app->escape->jsString($this->app->config->captcha->recaptcha->site_key); ?>', {action: 'submit'}).then(function(token) {
                    document.getElementById('<?php echo $this->app->escape->jsString($field_name); ?>').value = token;
                });
            });
        </script>
        <?php
        
    }
}
