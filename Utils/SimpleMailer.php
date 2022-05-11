<?php
namespace Utils;


use Configs\Config;
use Nish\Utils\Mailer\IMailer;

class SimpleMailer
{
    /**
     * @param $host
     * @param $username
     * @param $password
     * @param array|null $to
     * @param array|null $bcc
     * @param array|null $cc
     * @param null $fromAddr
     * @param string $subject
     * @param string $htmlBody
     * @param string $textBody
     * @param int $port
     * @param string $smtpSecure
     * @param null $replyTo
     * @param array|null $attachments
     * @throws \Nish\Exceptions\MailerException
     */
    public static function sendSMTPMail($host, $username, $password, ?array $to = null, ?array $bcc = null, ?array $cc = null, $fromAddr = null, $subject = '', $htmlBody = '', $textBody = '', $port = 587, $smtpSecure = 'tls', $replyTo = null, ?array $attachments = null)
    {
        /* @var IMailer $mailerClass */
        $mailerClass = Config::getMailerClass();
        $mailerClass::sendSMTPMail($host, $username, $password, $to, $bcc, $cc, $fromAddr, $subject, $htmlBody, $textBody, $port, $smtpSecure, $replyTo, $attachments);
    }

    /**
     * @param $subject
     * @param $htmlBody
     * @param array $bcc
     * @param array|null $attachments
     * @param string $textBody
     * @return bool
     * @throws \Nish\Exceptions\MailerException
     */
    public static function sendBCCMail($subject, $htmlBody, array $bcc, ?array $attachments = null, $textBody = '')
    {
        $sender = Config::getMailSender();

        self::sendSMTPMail(
            $sender['host'],
            $sender['username'],
            $sender['password'],
            null,
            $bcc,
            null,
            $sender['from'],
            $subject,
            $htmlBody,
            $textBody,
            $sender['port'],
            $sender['smtpSecure'],
            $sender['replyTo'],
            $attachments
        );

        return true;
    }
}