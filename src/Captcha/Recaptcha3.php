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
     * @var array $csp_directives The CSP directives to be added for the captcha to work
     */
    protected array $csp_directives = [
        'script-src' => 'https://www.gstatic.com/recaptcha/',
        'frame-src' => 'https://www.google.com/recaptcha/',
        'connect-src' => 'https://www.google.com/recaptcha/',
    ];

    /**
     * @var bool $initialized Whether the captcha has been initialized
     */
    protected bool $initialized = false;

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
    }

    /**
     * Initializes the captcha
     */
    protected function init()
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        $this->app->document->js->add($this->app->assets_url . '/framework/js/captcha/recaptcha3.min.js', attributes: ['defer' => true]);
        $this->app->document->js->add('https://www.google.com/recaptcha/api.js?render=' . urlencode($this->app->config->captcha->recaptcha->site_key), attributes: ['defer' => true]);

        $this->app->response->headers->csp->add($this->csp_directives);
    }

    /**
     * @see CaptchaInterface::verify()
     * {@inheritDoc}
     */
    public function verify() : bool
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
     * @see CaptchaInterface::render()
     * {@inheritDoc}
     */
    public function render()
    {
        $this->init();

        $field_name = 'recaptcha3-token-' . $this->app->random->getString(32);

        echo $this->app->html->hidden('recaptcha3-token', '', ['id' => $field_name]);
        ?>
        <script<?php echo $this->app->document->js->getNonce() ?>>
            document.addEventListener('DOMContentLoaded', () => {
                onloadRecaptcha3Callback('<?php echo $this->app->escape->jsString($field_name); ?>', '<?php echo $this->app->escape->jsString($this->app->config->captcha->recaptcha->site_key); ?>');
            });
        </script>
        <?php
        
    }
}
