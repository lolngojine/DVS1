

<?php
session_start();
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JKUAT Registration</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="reg.css">
    <link rel="stylesheet" href="regist.css">

    <style>
        /* Added styles to fix alignment issues */
        .social-icons-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 15px 0;
        }
        
        .social-icons-container a {
            color: #1226c0;
            font-size: 20px;
        }
        
        .links-container {
            text-align: center;
            margin-top: 15px;
        }
        
        .or-divider {
            text-align: center;
            margin: 15px 0;
            color: #555;
            position: relative;
        }
        
        .or-divider::before,
        .or-divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: #ddd;
        }
        
        .or-divider::before {
            left: 0;
        }
        
        .or-divider::after {
            right: 0;
        }
    </style>
</head>
<body>
    <div class="left-section">
        <!-- Left side with background image -->
    </div>
    
    <div class="right-section">
        <div class="form-container">
            <div class="form-header">
                <img src="photos/jkuat-logo.webp" alt="JKUAT Logo" class="logo">
                <h1>Create Account</h1>

                <?php
                if (isset($errors['user_exist'])) {
                    echo '<div class="error-main">
                            <p>' . $errors['user_exist'] . '</p>
                          </div>';
                    unset($errors['user_exist']);
                }
                ?>



        
                <!----send msg-- has not work for now--->
                <?php
                if(!empty($_POST["signup"])){
                    $name = $_POST["name"];
                    $email = $_POST["email"];
                
                    $toEmail = "simbadan7@gmail.com";
                
                    $mailHeader="Name:" . $name .
                    "\r\n Email:" .$email . "\r\n";
                
                    if(mail($toEmail, $name, $mailHeader)){
                       $message = "You have successfully registered to DVS JKUAT";
                    }
                }
                //end of sending msg code ?>
            </div>



            
            <form method="POST" action="user-account.php">
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" id="name" placeholder="Name" required>
                    <?php
                    if (isset($errors['name'])){
                        echo '<div class="error">
                                <p>' . $errors['name'] . '</p>
                              </div>';
                    }
                    ?>
                </div>

                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="Email" required>
                    <?php
                    if (isset($errors['email'])) {
                        echo '<div class="error">
                                <p>' . $errors['email'] . '</p>
                              </div>';
                        unset($errors['email']);
                    }
                    ?>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Password">
                    <i id="eye" class="fa fa-eye"></i>
                    <?php
                    if (isset($errors['password'])) {
                        echo '<div class="error">
                                <p>' . $errors['password'] . '</p>
                              </div>';
                        unset($errors['password']);
                    }
                    ?>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <?php
                    if (isset($errors['confirm_password'])) {
                        echo '<div class="error">
                                <p>' . $errors['confirm_password'] . '</p>
                              </div>';
                        unset($errors['confirm_password']);
                    }
                    ?>
                </div>

                <input type="submit" class="btn" value="Sign Up" name="signup">

                <?php
                if(!empty($message)){
                ?>
                <div class="success">
                    <strong><?php echo $message; ?></strong>
                </div>
                <?php } ?>
            </form>
            
            <div class="or-divider">----------or--------</div>
            
            <div class="social-icons-container">
                <a href="https://support.google.com/accounts/answer/27441?hl=en&co=GENIE.Platform%3DAndroid"><i class="fab fa-google"></i></a>
                <a href="#"><i class="fab fa-facebook"></i></a>
            </div>
            
            <div class="links-container">
                <p>Already Have Account? <a href="index.php">Sign In</a></p>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>

<?php
if(isset($_SESSION['errors'])){
    unset($_SESSION['errors']);
}
?>