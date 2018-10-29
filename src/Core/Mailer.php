<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Mailer{

    private $mail;

    public function __construct()
    {

        $this->mail = new PHPMailer(true);
        try {

            $this->mail->isSMTP();
            $this->mail->SMTPDebug  = App::env("smtpDebug");
            $this->mail->Host       = App::env("smtpHost");
            $this->mail->SMTPAuth   = App::env("smtpAuth");
            $this->mail->Username   = App::env("smtpUser");
            $this->mail->Password   = App::env("smtpPass");
            $this->mail->SMTPSecure = App::env("smtpSecure");
            $this->mail->Port       = App::env("smtpPort");

        }catch (Exception $e){

            echo 'Message could not be sent. Mailer Error: ', $this->mail->ErrorInfo;

        }

    }

    public function send_mail($from, $replyTo, $targets, $subject, $body, $altBody){

        try {

            $this->mail->setFrom($from["mailAddress"], $from["name"]);

            $this->mail->addReplyTo($replyTo["mailAddress"], $replyTo["name"]);

            foreach($targets as $target)
                $this->mail->addAddress($target["mailAddress"], $target["name"]);

            $this->mail->isHTML();
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = $altBody;

            $this->mail->send();

        }catch (Exception $e){

            echo 'Message could not be sent. Mailer Error: ', $this->mail->ErrorInfo;

        }

    }

}