<?php
session_start();
if(isset($_SESSION['user'])){
    $user = $_SESSION['user'];

}else{
    header("Location: index.php");
    exit();
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure web based. Dan</title>

    <!-----links of styles----->
    <link rel="stylesheet" href="homme.css">
    <link rel="stylesheet" href="card.css">
    <link rel="stylesheet" href="verification.css">

    <!-----link of icons-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


    <script src="response.js"></script> 

<!--Hii nimeweka page yake ya javascript ---->

<!---------
  <script>
        function showAdmin() {
            const password = prompt("Enter admin password:");
            if (password === "Daniel1234") {
                document.getElementById("admin-section").style.display = "block";
            } else {
                alert("Unauthorized access!");
            }
        }
    </script>
   ---->

</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <a href="https://www.jkuat.ac.ke" class="logo">
            
      JKUAT
        </a>
       
        <ul class="nav-links">

            <li><a href="home.php"><i class="fas fa-home" style="color:Red; size:50px;"></i>Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact"> <i class="fas fa-phone"style="color:Red; size:50px"></i>Contact</a></li>
            <li><a href="#verify">Verify Device</a></li>
            <li><a href="verify_admin.php"><i class="fas fa-user" style="color:Red; size:20px;"></i>admin</a></li>
            <li><a href="register.php"> <i class="fas fa-sign-out-alt"style="color:Red; size:50px"></i>Logout</a></li>
           

            <ul class="nav-links">

    </nav>
    
    <!-- page Section -->
    <header class="page">
        <div class="page-content">
            <h1>Secure Device Verification Portal</h1>
            <p>Ensure your laptop is registered for seamless through the admin &copy; Simba.</p>
            <a href="#verify" class="button">Verify Now</a>
        </div>
    </header>

 

    <!-- Verification Section -->
  

    <section id="verify" class="section bg-light">
    <div class="verify-container">
        <h2>Verify Your Device</h2>
        <form class="verification-form" method="POST" action="">
        <i class="fa-solid fa-laptop" styles="color:red; font-size:20px;"></i>
            <label>Enter Student Registration Number:</label>
            <input type="text" name="reg_number" placeholder="e.g., SCP01-1028/2020" required>
            <button type="submit" name="verify"><i class="fa-solid fa-square-check" styles="color:blue;"></i>Verify</button>
        </form>


    <?php
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
        require_once 'config/db.php';
        $regNumber = trim($_POST['reg_number']);
        
        // Fetch student details
        $studentQuery = "SELECT * FROM students WHERE registration_number = ?";
        $stmt = $conn->prepare($studentQuery);
        $stmt->bind_param("s", $regNumber);
        $stmt->execute();
        $studentResult = $stmt->get_result();
        
        if ($studentResult->num_rows > 0) {
            $student = $studentResult->fetch_assoc();
            
            // Fetch device details
            $deviceQuery = "SELECT * FROM devices WHERE student_id = ?";
            $stmt = $conn->prepare($deviceQuery);
            $stmt->bind_param("i", $student['student_id']);
            $stmt->execute();
            $deviceResult = $stmt->get_result();
            
            if ($deviceResult->num_rows > 0) {
                $device = $deviceResult->fetch_assoc();
                ?>
                <div class="verification-results">
                    <h3>Verification Results</h3>
                    
                    <div class="student-info">
                        <h4>Student Information</h4>
                        <div class="info-grid">
                            <div>
                                <p><strong>Registration Number:</strong></p>
                                <p><?= htmlspecialchars($student['registration_number']) ?></p>
                            </div>
                            <div>
                                <p><strong>Full Name:</strong></p>
                                <p><?= htmlspecialchars($student['full_name']) ?></p>
                            </div>

                            <div>
                                <p><strong>Tel:</strong></p>
                                <p><?= htmlspecialchars($student['phone']) ?></p>
                            </div>

                            <div>
                                <p><strong>Passport Photo:</strong></p>

                                <?php 
                                    if (!empty($student['passport_photo_path'])): ?>
                                    <img src="<?= $student['passport_photo_path'] ?>" 
                                         alt="Passport Photo" style="max-width: 150px;">
                                <?php else: 
                                    ?>
                                    <p>No photo available</p>
                                <?php endif; ?>
                                </div>
                                <section>
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="device-info">
                        <h4>Device Information</h4>
                        <div class="info-grid">
                            <div>
                                <p><strong>Serial Number:</strong></p>
                                <p><?= htmlspecialchars($device['serial_number']) ?></p>
                            </div>
                            <div>
                                <p><strong>Model:</strong></p>
                                <p><?= htmlspecialchars($device['model']) ?></p>
                            </div>
                            <div>
                                <p><strong>Unique Identifier:</strong></p>
                                <p><?= htmlspecialchars($device['unique_identifier']) ?></p>
                            </div>
                            <div>
                                <p><strong>Device Photo:</strong></p>

                                <?php 
                                if (!empty($device['laptop_photo_path'])):
                                 ?>
                                    <img src="<?= $device['laptop_photo_path'] ?>" 
                                         alt="Device Photo" style="max-width: 150px;">
                                <?php
                             else: ?>
                                    <p>No photo available</p>
                                <?php
                             endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                echo '<div class="alert error"><i class="fas fa-exclamation-circle"></i> No device registered for this student</div>';
            }
        } else {
            echo '<div class="alert error"><i class="fas fa-exclamation-circle"></i> Student Hana laptop Huyo</div>';
        }
        
        $stmt->close();
        $conn->close();
    }
    ?>





<section id="about" class="section">
    <div class="section-header">
        <h2>About Our Laptop Verification System</h2>
        <p class="subtitle">A robust solution for secure device management within JKUAT</p>
    </div>

    <div class="card-container">
        <!-- Card 1 -->
        <div class="feature-card">
            <div class="card-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="card-content">
                <h3>Secure Verification</h3>
                <p>Our advanced validation system ensures only authorized laptops can access JKUAT's network resources.</p>
                <div class="card-hover-content">
                    <p>Real-time verification prevents unauthorized access while maintaining user convenience.</p>
                    <div class="card-underline"></div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="feature-card">
            <div class="card-icon">
                <i class="fas fa-laptop"></i>
            </div>
            <div class="card-content">
                <h3>Seamless Registration</h3>
                <p>Quick and intuitive device registration process for administrators and users alike.</p>
                <div class="card-hover-content">
                    <p>Our streamlined interface reduces registration time while maintaining strict security protocols.</p>
                    <div class="card-underline"></div>
                </div>
            </div>
        </div>

        <!-- Card 3 (Additional card for better layout) -->
        <div class="feature-card">
            <div class="card-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-content">
                <h3>Real-time Monitoring</h3>
                <p>Continuous tracking of all registered devices across campus networks.</p>
                <div class="card-hover-content">
                    <p>Instant alerts for suspicious activity help maintain network integrity and security.</p>
                    <div class="card-underline"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .section {
        padding: 4rem 2rem;
        background: #f9f9ff;
        text-align: center;
    }

    .section-header {
        max-width: 800px;
        margin: 0 auto 3rem;
    }

    .section-header h2 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .subtitle {
        color: #7f8c8d;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .card-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .feature-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        padding: 2.5rem 2rem;
        width: 320px;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .card-icon {
        font-size: 2.5rem;
        color: #3498db;
        margin-bottom: 1.5rem;
    }

    .feature-card h3 {
        color: #2c3e50;
        font-size: 1.4rem;
        margin-bottom: 1rem;
    }

    .feature-card p {
        color: #7f8c8d;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .card-hover-content {
        position: absolute;
        bottom: -100%;
        left: 0;
        right: 0;
        background: #3498db;
        color: white;
        padding: 2rem;
        transition: all 0.4s ease;
        border-radius: 0 0 12px 12px;
    }

    .feature-card:hover .card-hover-content {
        bottom: 0;
    }

    .card-hover-content p {
        color: white;
        margin-bottom: 1.5rem;
    }

    .card-underline {
        width: 50px;
        height: 3px;
        background: rgba(255, 255, 255, 0.5);
        margin: 0 auto;
    }
</style>


<style>
    
     
        
        .contact-header {
            padding: 20px;
            background-color:rgb(16, 204, 47);
            color: white;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .contact-header h1 {
            margin: 0;
            font-size: 2.5em;
            color:black;
        }
        
        .contact-content {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            
        }
        
        .contact-form {
            flex: 2;
            min-width: 300px;
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .contact-info {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            color:navy;
        }
        
        .contact-form h2, .contact-info h2 {
            color:rgb(14, 14, 15);
            border-bottom: 2px solid #1a5276;
            padding-bottom: 10px;
            margin-top: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color:navy;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }
        
        .form-group textarea {
            height: 150px;
        }
        
        .submit-btn {
            background-color:rgb(20, 146, 14);
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .submit-btn:hover {
            background-color:rgb(40, 5, 238);
        }
        
        .contact-details {
            margin-top: 20px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .contact-icon {
            margin-right: 15px;
            color:rgb(12, 18, 200);
            font-size: 20px;
        }
        
        .opening-hours {
            margin-top: 30px;
        }
        
        .hours-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .hours-table tr:nth-child(even) {
            background-color:rgb(227, 174, 113);
        }
        
        .hours-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            color:green;    
        }
        
   
      
    </style>

    
    
       
</head>
<body>
    <div class="contact-container" id="contact">
        <header class="contact-header">
            <h1>Contact us</h1>
            <p>We're here to help with any questions or issues regarding device verification</p>
        </header>
        
        <div class="contact-content">
            <div class="contact-form">
                <h2>Send Us a Message</h2>
                <form action="send-email.php" method="POST">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                 
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject">
                            <option value="verification">Device Verification Issue</option>
                            <option value="access">System down issue</option>
                            <option value="registration">New Device Registration</option>
                            <option value="other">Other Inquiry</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
            
            <div class="contact-info">
                <h2>Contact Information</h2>
                
                <div class="contact-details">
                    <div class="contact-item">
                        <div class="contact-icon">📍</div>
                        <div>
                            <strong>Physical Address:</strong><br>
                            JKUAT Main Campus,<br>
                            IT Centre Building, Room 104 A,<br>
                            Juja, Kenya
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">📞</div>
                        <div>
                            <strong>Phone:</strong><br>
                            +254 742527442<br>
                            +254 769996589 (Emergency)
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">✉️</div>
                        <div>
                            <strong>Email:</strong><br>
                            verification@jkuat.ac.ke<br>
                            support-dvs@jkuat.ac.ke
                        </div>
                    </div>
                </div>
                
                <div class="opening-hours">
                    <h3>Operating Hours</h3>
                    <table class="hours-table">
                        <tr>
                            <td>Monday - Friday</td>
                            <td>5:00 AM - 5:00 PM</td>
                        </tr>
                        <tr>
                            <td>Saturday</td>
                            <td>5:00 AM - 1:00 PM</td>
                        </tr>
                        <tr>
                            <td>Sunday</td>
                            <td>6:00 AM - 4:00 PM</td>
                        </tr>
                    </table>
                </div>
                
              
        
       
    </div>
</body>

<!-------contact---->




    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email:
                    <br>
                   <a href="mailto:simbadan7@gmail.com">simba</a></p>
                <p>Phone: +254 742527442</p>
                <p>Address: <br>P.O.BOX 200600 JKUAT, Main Campus, Nairobi</p>
            </div>

            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="#"><i class="fas fa-home" style="color:yellow; size:50px;"></i>&nbsp;&nbsp;Home</a></p>
                <p><a href="#about"><i class="fas fa-address-card" style="color:yellow; size:50px;">&nbsp;&nbsp;</i>About Us</a></p>
                <p><a href="#"><i class="fa fa-shield" aria-hidden="true" style="color:yellow"></i>&nbsp;&nbsp;Services</a></p>
                <p><a href="#contact"><i class="fas fa-phone"style="color:yellow; size:50px"></i>&nbsp;&nbsp;Contact</a></p>
                <p><a href="https://www.jkuat.ac.ke/student-resources/"><i class="fas fa-resource" style="color:Red;">&nbsp;&nbsp;</i>Resouces</a></p>
            </div>

            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="icons1">
                    <a href="mailto:security@vc.jkuat.ac.ke"><i class="fa-solid fa-envelope"></i></a>
                    <a href="https://www.facebook.com/DiscoverJKUAT"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.bing.com/ck/a?!&&p=469f9bd69352e63dfc3987e8bb292937dfff6df03c0d11d5297d9f8c0b971834JmltdHM9MTc0NTI4MDAwMA&ptn=3&ver=2&hsh=4&fclid=2f8358d6-0dca-646d-254d-4d9f0c5c653b&psq=jkuat+official++twiter+page+link&u=a1aHR0cHM6Ly90d2l0dGVyLmNvbS9kaXNjb3ZlcmprdWF0L3dpdGhfcmVwbGllcw&ntb=1"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.bing.com/ck/a?!&&p=869fd996a9622b5479786f3aa0f2e745dd1635f48c6237aa43f781826984f57fJmltdHM9MTc0NTI4MDAwMA&ptn=3&ver=2&hsh=4&fclid=2f8358d6-0dca-646d-254d-4d9f0c5c653b&psq=jkuat+instergram+link&u=a1aHR0cHM6Ly93d3cuaW5zdGFncmFtLmNvbS9kaXNjb3ZlckprdWF0Lw&ntb=1"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.bing.com/ck/a?!&&p=81f9639deeb3f6f4770af9f2040fb2dd455159477e56f208c8a09822285e073bJmltdHM9MTc0NTI4MDAwMA&ptn=3&ver=2&hsh=4&fclid=2f8358d6-0dca-646d-254d-4d9f0c5c653b&psq=jkuat+lin+linkedin&u=a1aHR0cHM6Ly93d3cubGlua2VkaW4uY29tL3NjaG9vbC9qa3VhdC8&ntb=1"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
      <center>
      <i class="fas fa-user"></i>
      DanteBrave



            <h4 class="quote">
        
        Let Us Build Smarter And Save JKUAT

              </h4>

        <p>©2025 JKUAT. All Rights Reserved. </p>
        </center>
      </h2>

    </footer>

</body>
</html>