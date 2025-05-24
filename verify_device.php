

<?php
require_once 'config/db.php';
session_start();




if (isset($_GET['reg_number'])) {
    $regNumber = trim($_GET['reg_number']);
    
    // Fetch student details
    $studentQuery = "SELECT * FROM students WHERE registration_number = ?";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("s", $regNumber);
    $stmt->execute();
    $studentResult = $stmt->get_result();
    
    if ($studentResult->num_rows > 0) {
        $student = $studentResult->fetch_assoc();
        
        // Fetch device details
        $deviceQuery = "SELECT * FROM devices WHERE student_id = ?";
        $stmt = $conn->prepare($deviceQuery);
        $stmt->bind_param("i", $student['student_id']);
        $stmt->execute();
        $deviceResult = $stmt->get_result();
        
        if ($deviceResult->num_rows > 0) {
            $device = $deviceResult->fetch_assoc();
            
            // Store results in session to display on admin.php
            $_SESSION['verify_results'] = [
                'student' => $student,
                'device' => $device
            ];
        } else {
            $_SESSION['verify_error'] = "No device registered for this student";
        }
    } else {
        $_SESSION['verify_error'] = "Student not found in our records";
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: home.php");
    exit();
}
?>


