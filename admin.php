<?php

session_start();

// Display success/error messages
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
   

   <?php
// Start session and check login
//session_start();

// Display success/error messages
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <link rel="stylesheet" href="admins.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="home.php" class="logo">JKUAT</a>
        <ul class="nav-links">
            <li><a href="home.php">Back to Home</a></li>
            <li><a href="Student_manage.php">Dashnoard</a></li>
            <li><a href="verify_admin.php">Logout</a></li>
        </ul>
    </nav>
    
    <!-- Admin Content -->
    <section id="admin-section" class="section bg-light">
        <main class="admin-container">

            <!-- Error/Success Messages -->
            <?php if (isset($_SESSION['error'])): ?>

                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php echo $_SESSION['error']; ?>
                </div>


                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> 
                    <?php echo $_SESSION['success']; ?>
                </div>

                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <h1>Welcome Admin</h1>
            <h1>To</h1>
            <h1>Device Registration Form</h1>
            <p class="admin-subtitle">Proceed to register new devices in the system</p>


            
            <form class="registration-form" action="process_registration.php" method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <h2>Device Owner Information</h2>
                    
                    <div class="form-group">
                    <i class="fas fa-user"></i>
                        <label for="full-name">Full Name:</label>
                        <input type="text" id="full-name" name="full_name" placeholder="Enter student's full name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="regNo">Registration Number:</label>
                        <input type="text" id="regNo" name="reg_number" required placeholder="e.g., CI/00001/2021">
                    </div>
                    
                    <div class="form-group">
                        <label for="passport-photo">Passport Photo:</label>
                        <input type="file" id="passport-photo" name="passport_photo" accept="image/*" required>
                    </div>
                    
                    <div class="form-group">
                    <i class="fas fa-envelope"></i> 
                     <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="student@jkuat.ac.ke" required>
                    </div>

                    <div class="form-group">
                    <i class="fas fa-phone"></i>
                        <label for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" placeholder="Valid phone number" required>
                    </div>
                </div>
                
                <div class="form-section">
               
                    <h2>Device Information</h2>
                    
                    <div class="form-group">
                        <label for="laptop-brand">Laptop Brand:</label>
                        <input type="text" id="laptop-brand" name="laptop_brand" required placeholder="e.g., HP, Dell, Lenovo">
                    </div>
                    
                    <div class="form-group">
                        <label for="model">Model:</label>
                        <input type="text" id="model" name="model" required placeholder="e.g., EliteBook 840">
                    </div>

                    <div class="form-group">
                        <label for="lap-photo">Laptop Photo:</label>
                        <input type="file" id="lap-photo" name="lap_photo" accept="image/*" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="receipt-photo">Laptop Receipt:</label>
                        <input type="file" id="receipt-photo" name="receipt_photo" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label for="serial-number">Serial Number:</label>
                        <input type="text" id="serial-number" name="serial_number" required placeholder="Device serial number">
                    </div>
                    
                    <div class="form-group">
                    
                        <label for="unique-number">Unique Identifier:</label>
                        <input type="text" id="unique-number" name="unique_number" placeholder="JKUAT-XXXX-XXXX" required>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Register Device</button>
                    <button type="reset" class="reset-btn">Clear Form</button>
                </div>
            </form>
        </main>
    </section>

    <footer class="admin-footer">
        <p>©2025 JKUAT Device Registration System. All rights reserved.
            <br>    
            <br>
            <i class="fas fa-user"></i>
            DanteBrave
        </p>
    </footer>