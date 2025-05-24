<?php
require_once 'config/db.php';
session_start();



$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all';
$devices = [];

try {
    $query = "SELECT d.*, s.registration_number, s.full_name
              FROM devices d 
              JOIN students s ON d.student_id = s.student_id";
    
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $query .= " WHERE (s.registration_number LIKE ? OR d.serial_number LIKE ? OR d.brand LIKE ?)";
        $searchTerm = "%$search%";
        $params = array_fill(0, 3, $searchTerm);
        $types = 'sss';
    }
    
    if ($filter === 'pending') {
        $query .= empty($search) ? " WHERE " : " AND ";
        $query .= "d.approved = 0";
    }
    
    $query .= " ORDER BY d.registration_number DESC";
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $devices = $result->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

// Handle device approval
if (isset($_POST['approve_device'])) {
    $deviceId = $_POST['device_id'];
    try {
        $stmt = $conn->prepare("UPDATE devices SET approved = 1 WHERE device_id = ?");
        $stmt->bind_param("i", $deviceId);
        $stmt->execute();
        $_SESSION['success'] = "Device approved successfully!";
        header("Location: amangedevices.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error approving device: " . $e->getMessage();
    }
}

// Handle device deletion
if (isset($_GET['delete'])) {
    $deviceId = $_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM devices WHERE device_id = ?");
        $stmt->bind_param("i", $deviceId);
        $stmt->execute();
        $_SESSION['success'] = "Device deleted successfully!";
        header("Location: amangedevices.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting device: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Devices | JKUAT Device Registration</title>
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
        <h2 class="form-title">Manage Devices</h2>
        
        <!-- Error/Success Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i> 
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i> 
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Search and Filter Form -->
        <form method="GET" class="search-form">
            <input type="text" name="search" class="form-control" placeholder="Search by reg number, serial or brand" value="<?php echo htmlspecialchars($search); ?>">
            <select name="filter" class="form-control" style="width: 150px;">
                <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Devices</option>
                <option value="pending" <?php echo $filter === 'pending' ? 'selected' : ''; ?>>Pending Approval</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
            <a href="manage_devices.php" class="btn btn-danger"><i class="fas fa-sync"></i> Reset</a>
        </form>

        <!-- Devices Table -->
        <?php if (!empty($devices)): ?>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Reg No.</th>
                        <th>Student Name</th>
                        <th>Device Brand</th>
                        <th>Serial No.</th>
                        <th>Status</th>
                        <th>Date Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($devices as $device): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($device['registration_number']); ?></td>
                            <td><?php echo htmlspecialchars($device['first_name'] . ' ' . $device['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($device['brand']); ?></td>
                            <td><?php echo htmlspecialchars($device['serial_number']); ?></td>
                            <td>
                                <?php if ($device['approved']): ?>
                                    <span class="badge" style="background: #2ecc71; color: white; padding: 3px 8px; border-radius: 4px;">Approved</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #e74c3c; color: white; padding: 3px 8px; border-radius: 4px;">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($device['registration_date'])); ?></td>
                            <td class="action-buttons">
                                <a href="edit_device.php?id=<?php echo $device['device_id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="manage_devices.php?delete=<?php echo $device['device_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this device?')"><i class="fas fa-trash"></i></a>
                                <?php if (!$device['approved']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="device_id" value="<?php echo $device['device_id']; ?>">
                                        <button type="submit" name="approve_device" class="btn btn-success"><i class="fas fa-check"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert info">
                <i class="fas fa-info-circle"></i> No devices found matching your criteria.
            </div>
        <?php endif; ?>
    </div>

    <footer class="admin-footer">
        <p>©2025 JKUAT Device Registration System. All rights reserved.
            <br><br>
            <i class="fas fa-user"></i> <?php echo $_SESSION['admin']['username']; ?>
        </p>
    </footer>
</body>
</html>