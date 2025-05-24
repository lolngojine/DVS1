<?php
// 1. Load Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

// 2. Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 3. Create PHPMailer instance
$mail = new PHPMailer(true);

try {
    // 4. Configure SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'danlolngojine@gmail.com'; // Your actual Gmail
    $mail->Password = 'kljs jlum gosb capr';    // Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    
    // 5. Enable debugging (optional but helpful)
    $mail->SMTPDebug = 2; // Shows connection details
    
    // 6. Test connection
    $mail->smtpConnect();
    echo "SMTP connection successful!";
    
    // 7. Close connection
    $mail->smtpClose();
    
} catch (Exception $e) {
    echo "SMTP connection failed: " . $e->getMessage();
    error_log("SMTP Error: " . $e->getMessage()); // Log to server error log
}
?>