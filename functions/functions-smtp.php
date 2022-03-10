<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../phpmailer/Exception.php';
require_once __DIR__.'/../phpmailer/PHPMailer.php';
require_once __DIR__.'/../phpmailer/SMTP.php';

$mail = new PHPMailer();

$mail->CharSet = 'UTF-8';

//$mail->SMTPDebug = 2;
//$mail->Debugoutput = 'html';
$mail->Host = $smtp_host;
$mail->Port = $smtp_port;
$mail->SMTPSecure = $smtp_encryption;
$mail->SMTPAuth = $smtp_auth;
$mail->SMTPAutoTLS = $smtp_autoTLS;
$mail->Username = $smtp_username;
$mail->Password = $smtp_password;
$mail->setFrom($header, $chat);

// Absender Adresse setzen
$mail->From = $header;

// Absender Alias setzen
$mail->FromName = $chat;

// Empfänger Adresse und Alias hinzufügen
$mail->addAddress($mailempfaenger);
//$mail->addAddress($mailempfaenger, $mailempfaengername);

// Betreff
$mail->Subject = $mailbetreff;

// HTML aktivieren
$mail->isHtml(true);

// Der Nachrichteninhalt als HTML
$mail->Body = $inhalt;

// Alternativer Nachrichteninhalt für Clients, die kein HTML darstellen
$mail->AltBody = strip_tags( $mail->Body );

$mail->send()
?>