<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/db.php'; // Include your database configuration

// Initialize variables with default values
$totalStudents = 0;
$registeredDevices = 0;
$pendingApprovals = 0;
$recentRegistrations = []; // Initialize as empty array

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    // Total students count
    $stmt = $conn->query("SELECT COUNT(*) FROM students");
    if ($stmt) {
        $totalStudents = $stmt->fetch_row()[0];
        $stmt->close();
    }
    
    // Registered devices count
    $stmt = $conn->query("SELECT COUNT(*) FROM devices");
    if ($stmt) {
        $registeredDevices = $stmt->fetch_row()[0];
        $stmt->close();
    }
    
    // Pending approvals count
    $stmt = $conn->query("SELECT COUNT(*) FROM devices WHERE approved = 0");
    if ($stmt) {
        $pendingApprovals = $stmt->fetch_row()[0];
        $stmt->close();
    }
    
    // Get recent registrations
    $stmt = $conn->query("
        SELECT d.device_id, d.brand, d.serial_number, d.registration_date, 
               s.registration_number, s.full_name
        FROM devices d
        JOIN students s ON d.student_id = s.student_id
        ORDER BY d.registration_date DESC
        LIMIT 5
    ");
    
    if ($stmt) {
        $recentRegistrations = $stmt->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

// Display success/error messages
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;

// Clear messages after displaying them
if (isset($_SESSION['error'])) unset($_SESSION['error']);
if (isset($_SESSION['success'])) unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | JKUAT Device Registration</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Your existing styles remain unchanged */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert.error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .alert.success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="home.php" class="logo">JKUAT</a>
        <ul class="nav-links">
            <li><a href="home.php">Back to Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    
    <!-- Admin Dashboard -->
    <div class="admin-dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="admin.php"><i class="fas fa-laptop"></i> Register Device</a></li>
                <li><a href="amangeStudent.php"><i class="fas fa-users"></i> Manage Students</a></li>
                <li><a href="amanage_devices.php"><i class="fas fa-laptop-medical"></i> Manage Devices</a></li>
                <a href="generate_report.php" class="btn btn-primary">
    <i class="fas fa-file-export"></i> Generate Report
</a>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <h1>Admin Dashboard</h1>
            
            <!-- Error/Success Messages -->
            <?php if ($error): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> 
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Students</h3>
                    <div class="card-value"><?php echo number_format($totalStudents); ?></div>
                    <a href="amangeStudents.php" class="btn btn-primary">View All</a>
                </div>
                
                <div class="card">
                    <h3>Registered Devices</h3>
                    <div class="card-value"><?php echo number_format($registeredDevices); ?></div>
                    <a href="amanage_devices.php" class="btn btn-primary">View All</a>
                </div>
                
                <div class="card">
                    <h3>Pending Approvals</h3>
                    <div class="card-value"><?php echo number_format($pendingApprovals); ?></div>
                    <a href="amanage_devices.php?filter=pending" class="btn btn-primary">Review</a>
                </div>
            </div>
            
            <!-- Recent Registrations Table -->
            <h2>Recent Device Registrations</h2>
            <table>
                <thead>
                    <tr>
                        <th>Reg No.</th>
                        <th>Student Name</th>
                        <th>Device Brand</th>
                        <th>Serial No.</th>
                        <th>Date Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentRegistrations)): ?>
                        <?php foreach ($recentRegistrations as $registration): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($registration['registration_number']); ?></td>
                                <td><?php echo htmlspecialchars($registration['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($registration['brand']); ?></td>
                                <td><?php echo htmlspecialchars($registration['serial_number']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($registration['registration_date'])); ?></td>
                                <td class="action-buttons">
                                    <a href="adeviceEdit.php?id=<?php echo $registration['device_id']; ?>" class="btn btn-primary">Edit</a>
                                    <a href="delete_device.php?id=<?php echo $registration['device_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this device?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No recent registrations found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Quick Actions -->
            <h2>Quick Actions</h2>
            <div class="action-buttons" style="margin-bottom: 20px;">
                <a href="admin.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Register New Device
                </a>
                <a href="bulk_upload.php" class="btn btn-success">
                    <i class="fas fa-upload"></i> Bulk Upload
                </a>
                <a href="generate_report.php" class="btn btn-primary">
                    <i class="fas fa-file-export"></i> Generate Report
                </a>
            </div>
        </div>
    </div>

    <footer class="admin-footer">
        <p>©2025 JKUAT Device Registration System. All rights reserved.
            <br>    
            <br>
            <i class="fas fa-user"></i>
            Admin Dashboard | <?php echo htmlspecialchars($_SESSION['admin']['username'] ?? 'Admin'); ?>
        </p>
    </footer>
</body>
</html>