<?php
/**
* The Mail Class
* @package Mars
*/

namespace Mars;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Mail\MailInterface;

/**
 * The Mail Class
 * The system's mailer object
 */
class Mail
{
    use Kernel;

    /**
     * @var array $supported_drivers The supported drivers
     */
    public protected(set) array $supported_drivers = [
        'phpmailer' => \Mars\Mail\PhpMailer::class
    ];

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, MailInterface::class, 'mail', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var MailInterface $driver The driver object
     */
    public protected(set) MailInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->mail_driver);

            return $this->driver;
        }
    }

    /**
     * Sends a mail
     * @param string|array $to The adress(es) where the mail will be sent
     * @param string $subject The subject of the mail
     * @param string $message The body of the mail
     * @param array $options The options of the mail, if any
     * @param array $attachments The attachments, if any, to the mail
     * @param string|array $bcc Bcc recipients, if any
     * @throws \Exception If the mail couldn't be sent
     */
    public function send(string|array $to, string $subject, string $message, array $options = [], array $attachments = [], string|array $bcc = [])
    {
        $this->app->plugins->run('mail_send', $to, $subject, $message, $options, $attachments, $bcc, $this);

        $is_html = $options['is_html'] ?? true;
        $from = $options['from'] ?? $this->app->config->mail_from;
        $from_name = $options['from_name'] ?? $this->app->config->mail_from_name;
        $reply_to = $options['reply_to'] ?? '';
        $reply_to_name = $options['reply_to_name'] ?? '';

        try {
            $this->driver->setRecipient($to);
            $this->driver->setSubject($subject);
            $this->driver->setBody($message, $is_html);
            $this->driver->setFrom($from, $from_name);

            if ($reply_to) {
                $this->driver->setSender($reply_to, $reply_to_name);
            }

            if ($attachments) {
                $this->driver->setAttachments($attachments);
            }
            if ($bcc) {
                $this->driver->setRecipientBcc($bcc);
            }

            $this->driver->send();

            $this->app->plugins->run('mail_sent', $to, $subject, $message, $options, $attachments, $bcc, $this);
        } catch (\Exception $e) {
            $this->app->plugins->run('mail_send_error', $e->getMessage(), $to, $subject, $message, $options, $attachments, $bcc, $this);

            throw new \Exception(App::__('mail_error', ['{ERROR}' => $e->getMessage()]));
        }
    }

    /**
     * Sends a mail using a template
     * @param string|array $to The adress(es) where the mail will be sent
     * @param string $subject The subject of the mail
     * @param string $template The template to use as the body of the mail
     * @param array $vars Vars to add as template vars
     * @param array $options The options of the mail, if any
     * @param array $attachments The attachments, if any, to the mail
     * @param string|array $bcc Bcc recipients, if any
     * @throws \Exception If the mail couldn't be sent
     */
    public function sendTemplate(string|array $to, string $subject, string $template, array $vars = [], array $options = [], array $attachments = [], string|array $bcc = [])
    {
        $message = $this->getTemplate($template, $vars);

        $this->send($to, $subject, $message, $options, $attachments, $bcc);
    }

    /**
     * Returns the content of a template to be used as the body of the email
     * @param string $filename The template's filename
     * @param array $vars Vars to add as template vars
     * @return string The template's content
     */
    public function getTemplate(string $filename, array $vars = []) : string
    {
        $content = '';
        if (is_file($filename)) {
            $content = $this->app->theme->getTemplateFromFilename($filename, $vars, 'mail');
        } else {
            $content = $this->app->theme->getTemplate($filename, $vars, 'mail');
        }

        return nl2br($content);
    }
}
