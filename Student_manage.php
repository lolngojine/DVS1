<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/db.php';

// Initialize variables
$totalStudents = 0;
$search = $_GET['search'] ?? '';
$students = [];

// Get dashboard stats
try {
    // Total students count
    $stmt = $conn->query("SELECT COUNT(*) FROM students");
    if ($stmt) {
        $totalStudents = $stmt->fetch_row()[0];
        $stmt->close();
    }
    
    // Search functionality
    $query = "SELECT * FROM students";
    if (!empty($search)) {
        $query .= " WHERE registration_number LIKE ? OR full_name LIKE ?";
        $searchTerm = "%$search%";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
    } else {
        $stmt = $conn->prepare($query);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

// Handle student deletion
if (isset($_GET['delete'])) {
    $studentId = $_GET['delete'];
    try {
        // First delete associated devices
        $stmt = $conn->prepare("DELETE FROM devices WHERE student_id = ?");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        
        // Then delete the student
        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        
        $_SESSION['success'] = "Student and associated devices deleted successfully!";
        header("Location: amangeStudent.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting student: " . $e->getMessage();
    }
}

// Display messages
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;

// Clear messages
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management | JKUAT Device Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .navbar {
            background-color: green;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(22, 223, 46, 0.91);
        }
        
        .logo {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin-left: 1.5rem;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--light-color);
        }
        
        .admin-dashboard {
            display: flex;
            min-height: calc(100vh - 120px);
        }
        
        .sidebar {
            width: 250px;
            background-color: rgb(0,0,0,0.05);
            padding: 1.5rem;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 0.75rem 1rem;
            color: var(--dark-color);
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .sidebar-menu i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            flex: 1;
            padding: 2rem;
            background-color: #f5f7fa;
        }
        
        h1, h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        
        .card h3 {
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .card-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--secondary-color);
            margin: 1rem 0;
        }
        
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color:rgb(79, 238, 21);
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        
        .alert.error {
            background-color: #fde8e8;
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        
        .alert.success {
            background-color: #e8fdf1;
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        
        .alert.info {
            background-color: #e8f4fd;
            color: var(--secondary-color);
            border-left: 4px solid var(--secondary-color);
        }
        
        .alert i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }
        
        .search-form {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .form-control {
            flex: 1;
            min-width: 250px;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }
        
        tr:hover {
            background-color: #f9f9f9;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-buttons .btn {
            padding: 0.5rem;
            font-size: 0.9rem;
        }
        
        .admin-footer {
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: auto;
        }
        
        .admin-footer p {
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .admin-dashboard {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .form-control {
                width: 100%;
            }
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
                <li><a href="Student_manage.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="admin.php"><i class="fas fa-laptop"></i> Register Device</a></li>
                <li><a href="Student_manage.php" class="active"><i class="fas fa-users"></i> Manage Students</a></li>
                <li><a href="amanage_devices.php"><i class="fas fa-laptop-medical"></i> Manage Devices</a></li>
                <li><a href="generate_report.php"><i class="fas fa-file-export"></i> Generate Reports</a></li>
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
            
            <!-- Dashboard Card -->
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Students</h3>
                    <div class="card-value"><?php echo number_format($totalStudents); ?></div>
                </div>
            </div>
            
            <!-- Search Form -->
            <form method="GET" class="search-form">
                <input type="text" name="search" class="form-control" 
                       placeholder="Search by registration number or name" 
                       value="<?php echo htmlspecialchars($search); ?>">



                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="Student_manage.php" class="btn btn-danger">
                    <i class="fas fa-sync"></i> Reset
                </a>
                <a href="admin.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Student
                </a>
            </form>

            <!-- Students Table -->
            <?php if (!empty($students)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Reg No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                        
                            <th>Actions</th>
                           
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['registration_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                <td class="action-buttons">
                                    <a href="aedit_student.php?id=<?php echo $student['student_id']; ?>" 
                                       class="btn btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="amangeStudent.php?delete=<?php echo $student['student_id']; ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this student and all their devices?')"
                                       title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a href="astudent_device.php?student_id=<?php echo $student['student_id']; ?>" 
                                       class="btn btn-success" title="View Devices">
                                        <i class="fas fa-laptop"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert info">
                    <i class="fas fa-info-circle"></i> 
                    No students found matching your criteria.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="admin-footer">
        <p>©2025 JKUAT Device Registration System. All rights reserved.</p>
        <p>
            <i class="fas fa-user"></i>
            Admin Dashboard | <?php echo htmlspecialchars($_SESSION['admin']['username'] ?? 'Admin'); ?>
        </p>
    </footer>
</body>
</html>