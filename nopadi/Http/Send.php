<?php

namespace Nopadi\Http;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Send
{
  /*Envia um e-mail*/
  public static function email($options)
  {
    /*carrega as variáveis*/
    $email =  $options['email'];
    $name =  isset($options['name']) ? $options['name'] : null;
    $text = isset($options['message']) ? $options['message'] : $options['text'];
    $html =  isset($options['html']) ? $options['html'] : $text;
    $subject =  isset($options['subject']) ? $options['subject'] : $options['title'];
    $from = isset($options['from']) ? $options['from'] : NP_EMAIL_NAME;
	
	$subject = utf8_decode($subject);
	$html = utf8_decode($html);
	$text = utf8_decode($text);
	$name = utf8_decode($name);
	$from = utf8_decode($from);

    $attachment = isset($options['attachment']) ? $options['attachment'] : false;

    //instanciando a classe (true habilita as exceções)
    $mail = new PHPMailer();

    // configura para envio via SMTP
    $mail->isSMTP();
    // servidor SMTP local
    $mail->Host = NP_EMAIL_HOST;
    // localhost não precisa de autenticação SMTP
    $mail->SMTPAuth = NP_EMAIL_AUTH;
    // também não precisa de criptografia
    $mail->SMTPSecure = false;
    // Nome de usuário do serviço SMTP
    $mail->Username = NP_EMAIL_USER;
    // Senha de usuário
    $mail->Password = NP_EMAIL_PASS;
    //porta do serviço SMTP
    $mail->Port = NP_EMAIL_PORT;

    //remetente
    $mail->setFrom(NP_EMAIL, $from);
    // destinatário
    $mail->addAddress($email, $name);

    // anexo (opcional)
    if ($attachment) $mail->addAttachment($attachment);

    // e-mail no formato HTML
    $mail->isHTML(true);

    // assunto
    $mail->Subject = $subject;
    // corpo da mensagem em HTML
    $mail->Body = $html;
    // corpo da mensagem em texto comum, para clientes de e-mail sem suporte a HTML
    $mail->AltBody = $text;

    return $mail->send();
  }
}
