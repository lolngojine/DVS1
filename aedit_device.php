<?php
require_once 'config/db.php';
session_start();



// Get device ID from URL
$deviceId = $_GET['id'] ?? null;

if (!$deviceId) {
    $_SESSION['error'] = "No device specified";
    header("Location: manage_devices.php");
    exit();
}

// Fetch device details
$device = [];
$student = [];

try {
    $stmt = $conn->prepare("
        SELECT d.*, s.* 
        FROM devices d
        JOIN students s ON d.student_id = s.student_id
        WHERE d.device_id = ?
    ");
    $stmt->bind_param("i", $deviceId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Device not found";
        header("Location: manage_devices.php");
        exit();
    }
    
    $device = $result->fetch_assoc();
    $student = [
        'registration_number' => $device['registration_number'],
        'first_name' => $device['first_name'],
        'last_name' => $device['last_name']
    ];
    
} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: manage_devices.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = $_POST['brand'];
    $serialNumber = $_POST['serial_number'];
    $model = $_POST['model'];
    $approved = isset($_POST['approved']) ? 1 : 0;
    
    try {
        $stmt = $conn->prepare("
            UPDATE devices 
            SET brand = ?, serial_number = ?, model = ?, approved = ?
            WHERE device_id = ?
        ");
        $stmt->bind_param("sssii", $brand, $serialNumber, $model, $approved, $deviceId);
        $stmt->execute();
        
        $_SESSION['success'] = "Device updated successfully!";
        header("Location: manage_devices.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating device: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Device | JKUAT Device Registration</title>
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
        <h2 class="form-title">Edit Device</h2>
        
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
            <p><strong>Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="brand">Device Brand</label>
                <input type="text" id="brand" name="brand" class="form-control" value="<?php echo htmlspecialchars($device['brand']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="model">Device Model</label>
                <input type="text" id="model" name="model" class="form-control" value="<?php echo htmlspecialchars($device['model']); ?>">
            </div>
            
            <div class="form-group">
                <label for="serial_number">Serial Number</label>
                <input type="text" id="serial_number" name="serial_number" class="form-control" value="<?php echo htmlspecialchars($device['serial_number']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="approved" <?php echo $device['approved'] ? 'checked' : ''; ?>> Approved
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            <a href="manage_devices.php" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
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