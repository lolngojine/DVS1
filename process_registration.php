<?php
session_start();

// Enable proper error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once 'config/db.php';

// Load Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

// Use proper PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unset($_SESSION['verify_results']);
    unset($_SESSION['verify_error']);
}

// Test DB connection
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
} else {
  //  echo "DB connected successfully!<br>";
}

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    
    try {
        // Check for duplicate registration number
        $checkRegNo = $conn->prepare("SELECT student_id FROM students WHERE registration_number = ?");
        $checkRegNo->bind_param("s", $_POST['reg_number']);
        $checkRegNo->execute();
        $checkRegNo->store_result();
        
        if ($checkRegNo->num_rows > 0) {
            throw new Exception("The registration number <b>{$_POST['reg_number']}</b> already exists!");
        }
        $checkRegNo->close();

        // Check for duplicate serial number
        $checkSerial = $conn->prepare("SELECT device_id FROM devices WHERE serial_number = ?");
        $checkSerial->bind_param("s", $_POST['serial_number']);
        $checkSerial->execute();
        $checkSerial->store_result();
        
        if ($checkSerial->num_rows > 0) {
            throw new Exception("The device with serial <b>{$_POST['serial_number']}</b> is already registered!");
        }
        $checkSerial->close();

        // Process file uploads
        $passportPhoto = uploadFile('passport_photo', 'uploads/passports/');
        $laptopPhoto = uploadFile('lap_photo', 'uploads/laptops/');
        $receiptPhoto = uploadFile('receipt_photo', 'uploads/receipts/');

        // Insert student data
        $studentSql = "INSERT INTO students (registration_number, full_name, email, phone, passport_photo_path) 
                       VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($studentSql);
        $stmt->bind_param("sssss", 
            $_POST['reg_number'],
            $_POST['full_name'], 
            $_POST['email'],
            $_POST['phone'],
            $passportPhoto
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to save student information. Please try again.");
        }
        
        $studentId = $stmt->insert_id;
        
        // Insert device data
        $deviceSql = "INSERT INTO devices (student_id, brand, model, serial_number, unique_identifier, laptop_photo_path, receipt_photo_path)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($deviceSql);
        $stmt->bind_param("issssss",
            $studentId,
            $_POST['laptop_brand'],
            $_POST['model'],
            $_POST['serial_number'],
            $_POST['unique_number'],
            $laptopPhoto,
            $receiptPhoto
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to register the device. Please verify all details.");
        }
        
        // If all successful
        $conn->commit();
        $_SESSION['success'] = "Device is now registered successfully!";







        // Send confirmation email using PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->SMTPDebug = 2; // Enable verbose debug output
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'danlolngojine@gmail.com'; // Your actual Gmail
            $mail->Password = 'kljs jlum gosb capr'; // Generated App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->Timeout = 40; // Increase timeout
        
            // Recipients
            $mail->setFrom('danlolngojine@gmail.com', 'DVS Administrator JKUAT ');
            $mail->addAddress($_POST['email'], $_POST['full_name']); // Student's email and name
        
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Device Registration Confirmation Alert';
            $mail->Body = "Dear <b>{$_POST['full_name']}</b>,<br><br>
            Your Device has been successfully registered in the JKUAT Device Verification System.
            <br>
            <br>
        
            Confirm your <b>details</b> below:<br>

            <br>
            Registration number:{$_POST['reg_number']}<br>
            Labtop Brand:{$_POST['laptop_brand']}
            <br>
            Model:{$_POST['model']}
            <br>
            Serial Number:{$_POST['serial_number']}
            <br>
            <br>
            <br>
            Incase you any error  contact ADMIN <br>
            <b>0769996589<b>
            <br>
            Lolngojine Dantebrave

            <br>
            Thanks You
            <br>
            <br>
            
             We assure condefidentiality and intergrity of data provided. @<b>SIMBA</b>
             <br>
            ";
        
            if(!$mail->send()) {
                throw new Exception("Mailer Error: " . $mail->ErrorInfo);
            }
            
            $_SESSION['success'] .= " Confirmation email sent!";
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Email sending failed: " . $e->getMessage();
            error_log("Email Error: " . $e->getMessage()); // Log to server error log
        }

        // Redirect
       header("Location: admin.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
        
        // Clean up uploaded files if transaction failed
        if (isset($passportPhoto) && file_exists($passportPhoto)) unlink($passportPhoto);
        if (isset($laptopPhoto) && file_exists($laptopPhoto)) unlink($laptopPhoto);
        if (isset($receiptPhoto) && file_exists($receiptPhoto)) unlink($receiptPhoto);
    }
    
    // Close connections and redirect
    if (isset($stmt)) $stmt->close();
    $conn->close();
    header("Location:admin.php");
    exit();
}

// File upload function
function uploadFile($fieldName, $targetDir) {
    if (!isset($_FILES[$fieldName])) return null;
    
    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $targetPath = $targetDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    }
    
    return null;
}
?>