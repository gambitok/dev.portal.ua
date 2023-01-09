<?php

define('RDD', __DIR__);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
@ini_set('display_errors', true);
date_default_timezone_set("Europe/Kiev");
header('Content-Type: text/html; charset=windows-1251');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$mail = new PHPMailer(true);

$host   = "smtp.office365.com";
$name   = "toko.robot@outlook.com";
$pass   = "Qwerty456852z";
$email  = "gambitokgd@gmail.com";
$date   = date("Y-m-d H:i:s");

$cname  = "TOKO GROUP";
$title  = "TOKO GROUP - PRICE";
$html   = "<p>Hello!</p><p>Your price list on $date</p><p>Sincerely, TOKO GROUP</p><small>TOKO GROUP LTD, TIN:403029222256, USREOU:40302920</small>";
$htmla  = "Hello! Your price list on $date. Sincerely, TOKO GROUPS. TOKO GROUP LTD, TIN:403029222256, USREOU:40302920";

$fname  = "price.csv";
$path   = RDD . "/PHPMailer/test.csv";

try {
    $mail->SMTPDebug  = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host       = $host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $name;
    $mail->Password   = $pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom($name, $cname);
    $mail->addAddress($email);
    $mail->addReplyTo($name, $cname);

    $mail->isHTML(true);
    $mail->Subject = $title;
    $mail->Body    = $html;
    $mail->AltBody = $htmla;
    $mail->addAttachment($path, $fname);

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

$content = "";