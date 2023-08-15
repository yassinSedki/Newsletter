<?php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(
        string $to = null,
        string $subject = 'Votre inscription à la newsletter',
        string $content = null,
        string $htmlTemplate = null,
        array  $context=[]
    ): void {
        $email = (new TemplatedEmail())
            ->from('sedkiyassin@gmail.com')
            ->to($to)
            ->subject($subject)
            //->html($content)
            ->htmlTemplate($htmlTemplate)
            ->context($context);


        $this->mailer->send($email);
    }
}
