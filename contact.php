
    <style>
        /* Contact Us Page CSS */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #333;
            background-image
        }
        
        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .contact-header {
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
            margin-bottom: 40px;
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

    <link rel="stylesheet" href="homme.css">
       <nav class="navbar">
        <a href="home.php" class="logo">
      JKUAT
        </a>
       
        <ul class="nav-links">

            <li><a href="home.php">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="about.php">Contact</a></li>
            <li><a href="#verify">Verify Device</a></li>
            <li><a href="verify_admin.php">admin</a></li>
            <li><a href="logout.php">logout</a></li>

            <ul class="nav-links">

    </nav>
</head>
<body>
    <div class="contact-container">
        <header class="contact-header">
            <h1>Contact us</h1>
            <p>We're here to help with any questions or issues regarding device verification</p>
        </header>
        
        <div class="contact-content">
            <div class="contact-form">
                <h2>Send Us a Message</h2>
                <form action="#" method="POST">
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
</html>