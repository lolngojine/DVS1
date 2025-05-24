<?php
require_once 'config/db.php';
session_start();



// Get student ID from URL
$studentId = $_GET['id'] ?? null;

if (!$studentId) {
    $_SESSION['error'] = "No student specified";
    header("Location: amangeStudents.php");
    exit();
}

// Fetch student details
$student = [];

try {
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Student not found";
        header("Location: amangeStudents.php");
        exit();
    }
    
    $student = $result->fetch_assoc();
    
} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: amanageStudents.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regNumber = $_POST['registration_number'];
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
   
    
    // Assuming your table has these columns:
// student_id, registration_number, first_name, last_name, email, phone, passport_photo

try {
    $stmt = $conn->prepare("
        UPDATE students 
        SET registration_number = ?, 
            full_name = ?, 
             
            email = ?, 
            phone = ?, 
            passport_photo = ? 
        WHERE student_id = ?
    ");
    
    $stmt->bind_param(
        "ssssssi",  // Notice 7 parameters (6 strings + 1 integer)
        $_POST['registration_number'],
        $_POST['full_name'],
        //$_POST['last_name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['passport_photo'],
        $_POST['student_id']
    );
    
    $stmt->execute();
    $_SESSION['success'] = "Student updated successfully!";
} catch (Exception $e) {
    $_SESSION['error'] = "Error updating student: " . $e->getMessage();
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student | JKUAT Device Registration</title>
    <link rel="stylesheet" href="dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="admin_dashboard.php" class="logo">JKUAT</a>
        <ul class="nav-links">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="form-container">
        <h2 class="form-title">Edit Student</h2>
        
        <!-- Error/Success Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i> 
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="registration_number">Registration Number</label>
                <input type="text" id="registration_number" name="registration_number" class="form-control" 
                       value="<?php echo htmlspecialchars($student['registration_number']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="full_name">Student full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" 
                       value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            
            
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($student['email']); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       value="<?php echo htmlspecialchars($student['phone']); ?>">
            </div>
            
            
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            <a href="Student_manage.php" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
        </form>
    </div>

    <footer class="admin-footer">
        <p>©2025 JKUAT Device Registration System. All rights reserved.
           
        </p>
    </footer>
</body>
</html>