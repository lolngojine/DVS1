<?php
require_once 'config/db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regNumber = $_POST['registration_number'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $course = $_POST['course'];
    
    try {
        // Check if student already exists
        $stmt = $conn->prepare("SELECT student_id FROM students WHERE registration_number = ?");
        $stmt->bind_param("s", $regNumber);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['error'] = "A student with this registration number already exists";
        } else {
            // Insert new student
            $stmt = $conn->prepare("
                INSERT INTO students (registration_number, first_name, last_name, email, phone, course)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssssss", $regNumber, $firstName, $lastName, $email, $phone, $course);
            $stmt->execute();
            
            $_SESSION['success'] = "Student added successfully!";
            header("Location: manage_students.php");
            exit();
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error adding student: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student | JKUAT Device Registration</title>
    <link rel="stylesheet" href="admins.css">
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
        <h2 class="form-title">Add New Student</h2>
        
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
                <input type="text" id="registration_number" name="registration_number" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="course">Course</label>
                <input type="text" id="course" name="course" class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Student</button>
            <a href="manage_students.php" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
        </form>
    </div>

    <footer class="admin-footer">
        <p>©2025 JKUAT Device Registration System. All rights reserved.
            <br><br>
            <i class="fas fa-user"></i> <?php echo $_SESSION['admin']['username']; ?>
        </p>
    </footer>
</body>
</html>