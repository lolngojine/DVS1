<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Display success/error messages
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;

// Clear messages after displaying them
unset($_SESSION['error']);
unset($_SESSION['success']);

// Include database connection
require_once 'db_connect.php';

// Function to get email settings
function getEmailSetting($setting) {
    global $conn;
    $query = "SELECT value FROM email_settings WHERE setting = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $setting);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['value'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <link rel="stylesheet" href="admins.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .device-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .device-table th, .device-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .device-table th {
            background-color: #f2f2f2;
        }
        .device-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .action-btn {
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        .download-btn {
            background-color: #2196F3;
            color: white;
        }
        .email-btn {
            background-color: #9C27B0;
            color: white;
        }
        .tab-container {
            margin-bottom: 20px;
        }
        .tab-button {
            padding: 10px 20px;
            background-color: #f1f1f1;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .tab-button.active {
            background-color: #4CAF50;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .email-status {
            font-size: 0.9em;
            margin-top: 5px;
            color: #666;
        }
        .email-sent {
            color: #4CAF50;
        }
        .email-pending {
            color: #FFC107;
        }
        .email-failed {
            color: #F44336;
        }
        #email_template {
            width: 100%;
            min-height: 200px;
            font-family: monospace;
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
    
    <!-- Admin Content -->
    <section id="admin-section" class="section bg-light">
        <main class="admin-container">
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

            <h1>Welcome Admin</h1>
            <p class="admin-subtitle">Manage device registrations and notifications</p>

            <!-- Tab Navigation -->
            <div class="tab-container">
                <button class="tab-button active" onclick="openTab('register-tab')">Register Device</button>
                <button class="tab-button" onclick="openTab('manage-tab')">Manage Devices</button>
                <button class="tab-button" onclick="openTab('email-tab')">Email Settings</button>
            </div>

            <!-- Register Device Tab -->
            <div id="register-tab" class="tab-content active">
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
                
                <div class="email-notice">
                    <p><i class="fas fa-info-circle"></i> Upon successful registration, the system will automatically send a confirmation email to the student.</p>
                </div>
            </div>

            <!-- Manage Devices Tab -->
            <div id="manage-tab" class="tab-content">
                <h2>Registered Devices</h2>
                <div class="search-container">
                    <input type="text" id="search-input" placeholder="Search by name, reg number, or serial..." onkeyup="searchDevices()">
                    <button class="download-btn" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Export to Excel</button>
                    <button class="download-btn" onclick="resendAllEmails()"><i class="fas fa-paper-plane"></i> Resend All Emails</button>
                </div>
                
                <table class="device-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Reg Number</th>
                            <th>Device Brand</th>
                            <th>Email Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all devices with email status
                        $query = "SELECT d.*, e.status as email_status, e.sent_at 
                                 FROM devices d
                                 LEFT JOIN email_logs e ON d.id = e.device_id
                                 ORDER BY d.registration_date DESC";
                        $result = mysqli_query($conn, $query);
                        
                        if ($result && mysqli_num_rows($result) > 0) {
                            $count = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $emailStatus = $row['email_status'] ?? 'pending';
                                $sentAt = $row['sent_at'] ?? 'Not sent';
                                
                                echo "<tr>
                                    <td>{$count}</td>
                                    <td>".htmlspecialchars($row['full_name'])."</td>
                                    <td>".htmlspecialchars($row['reg_number'])."</td>
                                    <td>".htmlspecialchars($row['laptop_brand'])."</td>
                                    <td>
                                        <span class='email-status email-{$emailStatus}'>
                                            <i class='fas fa-envelope'></i> " . ucfirst($emailStatus) . "
                                            <br><small>{$sentAt}</small>
                                        </span>
                                    </td>
                                    <td>
                                        <button class='action-btn edit-btn' onclick='editDevice({$row['id']})'><i class='fas fa-edit'></i></button>
                                        <button class='action-btn delete-btn' onclick='confirmDelete({$row['id']})'><i class='fas fa-trash'></i></button>
                                        <button class='action-btn download-btn' onclick='downloadDetails({$row['id']})'><i class='fas fa-download'></i></button>
                                        <button class='action-btn email-btn' onclick='resendEmail({$row['id']})' title='Resend Email'><i class='fas fa-paper-plane'></i></button>
                                    </td>
                                </tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='6'>No devices registered yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Email Settings Tab -->
            <div id="email-tab" class="tab-content">
                <h2>Email Configuration</h2>
                <form action="update_email_settings.php" method="POST">
                    <div class="form-group">
                        <label for="email_subject">Email Subject:</label>
                        <input type="text" id="email_subject" name="email_subject" value="<?php echo htmlspecialchars(getEmailSetting('subject')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email_template">Email Template:</label>
                        <textarea id="email_template" name="email_template" rows="10" required><?php echo htmlspecialchars(getEmailSetting('template')); ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="submit-btn">Save Settings</button>
                        <button type="button" class="reset-btn" onclick="resetEmailTemplate()">Reset Template</button>
                    </div>
                </form>
                
                <div class="email-test">
                    <h3>Test Email Configuration</h3>
                    <div class="form-group">
                        <label for="test_email">Send test email to:</label>
                        <input type="email" id="test_email" placeholder="admin@example.com">
                        <button class="submit-btn" onclick="sendTestEmail()">Send Test Email</button>
                    </div>
                </div>
            </div>
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

    <script>
        // Tab functionality
        function openTab(tabId) {
            // Hide all tab contents
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Remove active class from all tab buttons
            const tabButtons = document.getElementsByClassName('tab-button');
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }
            
            // Show the selected tab content and mark button as active
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        
        // Search functionality
        function searchDevices() {
            const input = document.getElementById('search-input');
            const filter = input.value.toUpperCase();
            const table = document.querySelector('.device-table');
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 1; i < tr.length; i++) {
                let found = false;
                const td = tr[i].getElementsByTagName('td');
                
                for (let j = 1; j < td.length - 1; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = found ? '' : 'none';
            }
        }
        
        // Device management functions
        function editDevice(deviceId) {
            window.location.href = `edit_device.php?id=${deviceId}`;
        }
        
        function confirmDelete(deviceId) {
            if (confirm('Are you sure you want to delete this device registration?')) {
                window.location.href = `delete_device.php?id=${deviceId}`;
            }
        }
        
        function downloadDetails(deviceId) {
            window.location.href = `download_details.php?id=${deviceId}`;
        }
        
        function exportToExcel() {
            window.location.href = 'export_devices.php';
        }
        
        // Email functions
        function resendEmail(deviceId) {
            if (confirm('Resend confirmation email for this device?')) {
                window.location.href = `resend_email.php?id=${deviceId}`;
            }
        }
        
        function resendAllEmails() {
            if (confirm('Resend emails for ALL registered devices? This might take a while.')) {
                window.location.href = 'resend_all_emails.php';
            }
        }
        
        function sendTestEmail() {
            const email = document.getElementById('test_email').value;
            if (email && confirm(`Send test email to ${email}?`)) {
                window.location.href = `send_test_email.php?email=${encodeURIComponent(email)}`;
            }
        }
        
        function resetEmailTemplate() {
            if (confirm('Reset email template to default? This cannot be undone.')) {
                document.getElementById('email_template').value = `Dear {student_name},\\n\\nYour {device_brand} device (Serial: {serial_number}) has been successfully registered with JKUAT.\\n\\nRegistration Number: {reg_number}\\nUnique ID: {unique_number}\\n\\nThank you,\\nJKUAT Device Registration Team`;
                document.getElementById('email_subject').value = 'JKUAT Device Registration Confirmation';
            }
        }
    </script>
</body>
</html>