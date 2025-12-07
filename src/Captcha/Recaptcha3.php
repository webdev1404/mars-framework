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
            throw new \Exception('The recaptcha3 site and secret keys must be set');
        }

        $this->app->document->javascript->load('https://www.google.com/recaptcha/api.js?render=' . urlencode($this->app->config->captcha->recaptcha->site_key));
    }

    /**
     * @see CaptchaInterface::check()
     * {@inheritdoc}
     */
    public function check() : bool
    {
        $post_data = [
            'secret' => $this->app->config->captcha->recaptcha->secret_key,
            'response' => $this->app->request->post->get('recaptcha3-token'),
            'remoteip' => $this->app->ip
        ];

        $response = $this->app->web->request->post('https://www.google.com/recaptcha/api/siteverify', $post_data);

        $data = $response->getJson();

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
                grecaptcha.execute('<?php echo $this->app->config->captcha->recaptcha->site_key; ?>', {action: 'submit'}).then(function(token) {
                    document.getElementById('<?php echo $field_name; ?>').value = token;
                });
            });
        </script>
        <?php
        
    }
}
