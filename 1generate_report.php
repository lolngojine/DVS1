<?php
session_start();
require_once 'config/db.php';



// Set default timezone
date_default_timezone_set('Africa/Nairobi');

// Function to generate CSV report
function generateReport($conn, $type) {
    $filename = "JKUAT_Device_Report_" . date('Y-m-d') . ".csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    switch($type) {
        case 'students':
            $query = "SELECT student_id, registration_number, full_name, email, phone FROM students";
            $result = $conn->query($query);
            
            // Write headers
            fputcsv($output, array('ID', 'Registration Number', 'Full Name', 'Email', 'Phone'));
            
            // Write data
            while($row = $result->fetch_assoc()) {
                fputcsv($output, $row);
            }
            break;
            
        case 'devices':
            $query = "SELECT d.device_id, d.brand, d.model, d.serial_number, 
                             s.registration_number, s.full_name, 
                             d.registration_date, d.approved
                      FROM devices d
                      JOIN students s ON d.student_id = s.student_id";
            $result = $conn->query($query);
            
            // Write headers
            fputcsv($output, array('Device ID', 'Brand', 'Model', 'Serial Number', 
                                  'Student Reg No', 'Student Name', 
                                  'Registration Date', 'Approval Status'));
            
            // Write data
            while($row = $result->fetch_assoc()) {
                $row['approved'] = $row['approved'] ? 'Approved' : 'Pending';
                fputcsv($output, $row);
            }
            break;
            
        case 'pending':
            $query = "SELECT d.device_id, d.brand, d.model, d.serial_number, 
                             s.registration_number, s.full_name, d.registration_date
                      FROM devices d
                      JOIN students s ON d.student_id = s.student_id
                      WHERE d.approved = 0";
            $result = $conn->query($query);
            
            // Write headers
            fputcsv($output, array('Device ID', 'Brand', 'Model', 'Serial Number', 
                                  'Student Reg No', 'Student Name', 'Registration Date'));
            
            // Write data
            while($row = $result->fetch_assoc()) {
                fputcsv($output, $row);
            }
            break;
    }
    
    fclose($output);
    exit();
}

// Handle report generation request
if (isset($_GET['download'])) {
    $reportType = $_GET['download'];
    generateReport($conn, $reportType);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports | JKUAT Device Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="dash.css">

    <style>
        /* Use the same CSS as in your student_management.php for consistency */
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
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .navbar{
            color:green;
        }
        .report-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 8px rgba(163, 82, 82, 0.79);
        }
       
        
        .report-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .report-card {
            background: white;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 1.5rem;
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }
        
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.61);
        }
        
        .report-card i {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }
        
        .report-card h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .report-card p {
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .btn-download {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--success-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-download:hover {
            background-color:rgb(12, 28, 210);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar --->
    <nav class="navbar">
        <a href="home.php" class="logo">JKUAT</a>
        <ul class="nav-links">
            <li><a href="student_manage.php">Dashboard</a></li>
            <li><a href="home.php">Logout</a></li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <div class="report-container">
        <h1><i class="fas fa-file-export"></i> Generate Reports</h1>
        <p>Select the type of report you want to download:</p>
        
        <div class="report-options">
            <!-- Students Report -->
            <div class="report-card">
                <i class="fas fa-users"></i>
                <h3>Students Report</h3>
                <p>Download complete list of all registered students with their contact information.</p>
                <a href="generate_report.php?download=students" class="btn-download">
                    <i class="fas fa-download"></i> Download Students Report
                </a>
            </div>
            
            <!-- Devices Report -->
            <div class="report-card">
                <i class="fas fa-laptop"></i>
                <h3>Devices Report</h3>
                <p>Complete list of all registered devices with student information.</p>
                <a href="generate_report.php?download=devices" class="btn-download">
                    <i class="fas fa-download"></i> Download Devices Report
                </a>
            </div>
            
            <!-- Pending Approvals Report --
            <div class="report-card">
                <i class="fas fa-clock"></i>
                <h3>Pending Approvals</h3>
                <p>List of all devices pending administrative approval.</p>
                <a href="generate_report.php?download=pending" class="btn-download">
                    <i class="fas fa-download"></i> Download Pending Devices
                </a>
            </div>
    -->
        </div>
    </div>
    
   
</body>
</html>