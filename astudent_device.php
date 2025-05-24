<?php
require_once 'config/db.php';
session_start();



// Get student ID from URL
$studentId = $_GET['student_id'] ?? null;

if (!$studentId) {
    $_SESSION['error'] = "No student specified";
    header("Location: amangeStudent.php");
    exit();
}

// Fetch student and their devices
$student = [];
$devices = [];

try {
    // Get student details
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Student not found";
        header("Location: amangeStudent.php");
        exit();
    }
    
    $student = $result->fetch_assoc();
    
    // Get student's devices - now using registration_date instead of registration_number
    $stmt = $conn->prepare("SELECT * FROM devices WHERE student_id = ? ORDER BY registration_date DESC");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $devices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: amangeStudent.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Devices | JKUAT Device Registration</title>
    <link rel="stylesheet" href="dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="admin_dashboard.php" class="logo">JKUAT</a>
        <ul class="nav-links">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="home.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="form-container">
        <h2 class="form-title">Student Devices</h2>
        
        <!-- Error/Success Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i> 
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="student-info" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="margin-top: 0;">Student Information</h3>
            <p><strong>Registration Number:</strong> <?php echo htmlspecialchars($student['registration_number']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
        </div>

        <h3>Registered Devices</h3>
        
        <?php if (!empty($devices)): ?>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Serial No.</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($devices as $device): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($device['brand']); ?></td>
                            <td><?php echo htmlspecialchars($device['model']); ?></td>
                            <td><?php echo htmlspecialchars($device['serial_number']); ?></td>
                            <td>
                                <?php if ($device['approved']): ?>
                                    <span class="badge" style="background: #2ecc71; color: white; padding: 3px 8px; border-radius: 4px;">Approved</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #e74c3c; color: white; padding: 3px 8px; border-radius: 4px;">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <a href="adeviceEdit.php?id=<?php echo $device['device_id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="admin.php?delete=<?php echo $device['device_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this device?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert info">
                <i class="fas fa-info-circle"></i> This student has no registered devices.
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 20px;">
            <a href="amangeStudent.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back to Students</a>
            <a href="admin.php?reg_number=<?php echo urlencode($student['registration_number']); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Add New Device</a>
        </div>
    </div>

    <footer class="admin-footer">
        <p>©2025 JKUAT Device Registration System. All rights reserved.
          
        </p>
    </footer>
</body>
</html>