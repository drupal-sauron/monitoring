<?php

namespace Sauron\Core\Transport;

use Sauron\Core\ConfigAware;
use Sauron\Core\ConfigLoader;
use Swift_Mailer;
use Swift_Message;
use Swift_SendmailTransport;
use Swift_SmtpTransport;

/**
 * Class Email
 * @package Sauron\Core\Transport
 */
class Email implements ConfigAware
{
    protected $config = NULL;

    /**
     * Inject config to the class
     *
     * @param ConfigLoader $config
     */
    public function setConfig(ConfigLoader $config)
    {
        $this->config = $config;
    }

    /**
     * Send email according to given params
     *
     * @param $to array recipients
     * @param $subject string email subject
     * @param $content string html content
     */
    public function send($to, $subject, $content)
    {
        $smtp = $this->config->getParam('administrator.mail_smtp.host');
        $transport = NULL;
        if ($smtp) {
            $port = $this->config->getParam('administrator.mail_smtp.port');
            if (!$port) {
                $port = 25;
            }
            $transport = Swift_SmtpTransport::newInstance($smtp, $port);
        }
        else {
            $sendmail = $this->config->getParam('administrator.mail_smtp.sendmail');
            if (!$sendmail) {
                $sendmail = '/usr/sbin/sendmail -bs';
            }

            $transport = Swift_SendmailTransport::newInstance($sendmail);
        }

        $from = $port = $this->config->getParam('administrator.mail');
        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($content, 'text/html');

        $mailer = Swift_Mailer::newInstance($transport);
        $mailer->send($message);
    }
}