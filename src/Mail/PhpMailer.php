<?php
/**
* The PhpMailer Class
* @package Mars
*/

namespace Mars\Mail;

use Mars\App;
use Mars\App\Kernel;
use PHPMailer\PHPMailer\PHPMailer as PHPMailerHandle;

/**
 * The PhpMailer Class
 * Mail driver which uses PhpMailer
 */
class PhpMailer implements MailInterface
{
    use Kernel;

    /**
     * @var PHPMailerHandle $handle The driver's handle
     */
    protected PHPMailerHandle $handle {
        get {
            if (isset($this->handle)) {
                return $this->handle;
            }

            $this->handle = new PHPMailerHandle;
            $this->handle->setLanguage('en', $this->app->vendor_path . '/phpmailer/phpmailer/language/');
            $this->handle->CharSet = 'UTF-8';

            if ($this->app->config->mail->smtp->enable) {
                $this->handle->isSMTP();
                $this->handle->Host = $this->app->config->mail->smtp->host;
                $this->handle->Port = $this->app->config->mail->smtp->port;
                $this->handle->SMTPSecure = $this->app->config->mail->smtp->secure;
                if ($this->app->config->mail->smtp->username && $this->app->config->mail->smtp->password) {
                    $this->handle->SMTPAuth = true;
                    $this->handle->Username = $this->app->config->mail->smtp->username;
                    $this->handle->Password = $this->app->config->mail->smtp->password;
                }
            }

            return $this->handle;
        }
    }

    /**
     * @see MailInterface::setRecipient()
     * {@inheritdoc}
     */
    public function setRecipient(string|array $to)
    {
        $to = (array)$to;

        foreach ($to as $address) {
            $this->handle->addAddress($address);
        }
    }

    /**
     * @see MailInterface::setRecipientBcc()
     * {@inheritdoc}
     */
    public function setRecipientBcc(string|array $to)
    {
        $to = (array)$to;

        foreach ($to as $address) {
            $this->handle->addBCC($address);
        }
    }

    /**
     * @see MailInterface::setSubject()
     * {@inheritdoc}
     */
    public function setSubject(string $subject)
    {
        $this->handle->Subject = $subject;
    }

    /**
     * @see MailInterface::setBody()
     * {@inheritdoc}
     */
    public function setBody(string $body, bool $is_html = true)
    {
        $this->handle->Body = $body;
        $this->handle->isHTML($is_html);
    }

    /**
     * @see MailInterface::setFrom()
     * {@inheritdoc}
     */
    public function setFrom(string $from, string $from_name = '')
    {
        $this->handle->From = $from;
        $this->handle->FromName = $from_name;
    }

    /**
     * @see MailInterface::setSender()
     * {@inheritdoc}
     */
    public function setSender(string $reply_to, string $reply_to_name = '')
    {
        $this->handle->addReplyTo($reply_to, $reply_to_name);
    }

    /**
     * @see MailInterface::setAttachments()
     * {@inheritdoc}
     */
    public function setAttachments(array $attachments)
    {
        foreach ($attachments as $attachment) {
            $this->handle->addAttachment($attachment);
        }
    }

    /**
     * @see MailInterface::send()
     * {@inheritdoc}
     */
    public function send()
    {
        if (!$this->handle->send()) {
            throw new \Exception($this->handle->ErrorInfo);
        }
    }
}
