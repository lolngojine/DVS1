<?php
require_once 'config/db.php';
session_start();



$search = $_GET['search'] ?? '';
$students = [];

try {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students | JKUAT Device Registration</title>
    <link rel="stylesheet" href="dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="home.php" class="logo">JKUAT</a>
        <ul class="nav-links">
            <li><a href="Student_manage.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="form-container">
        <h2 class="form-title">Manage Students</h2>
        
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

        <!-- Search Form -->
        <form method="GET" class="search-form">
            <input type="text" name="search" class="form-control" placeholder="Search by reg number or name" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
            <a href="amangeStudent.php" class="btn btn-danger"><i class="fas fa-sync"></i> Reset</a>
            <a href="admin.php" class="btn btn-success"><i class="fas fa-plus"></i> Add Student</a>
        </form>

        <!-- Students Table -->
        <?php if (!empty($students)): ?>
            <table class="results-table">
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
                                <a href="aedit_student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="amangeStudent.php?delete=<?php echo $student['student_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student and all their devices?')"><i class="fas fa-trash"></i></a>
                                <a href="astudent_device.php?student_id=<?php echo $student['student_id']; ?>" class="btn btn-success"><i class="fas fa-laptop"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert info">
                <i class="fas fa-info-circle"></i> No students found matching your criteria.
            </div>
        <?php endif; ?>
    </div>

    <footer class="admin-footer">
    <p>©2025 JKUAT Device Registration System. All rights reserved.
        <br><br>
        <i class="fas fa-user"></i> <?php echo $_SESSION['admin']['username'] ?? 'Admin'; ?>
    </p>
</footer>
</body>
</html>