<?php

$name = $_PST["name"];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

require "vendor/autoload.php"

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

email= new PHPMailer(true);

$email->isSMTP();
$mail-> SMTPAuth = true;


?>