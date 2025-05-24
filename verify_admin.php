<?php
session_start();
if(!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['admin_pin']) && $_POST['admin_pin'] === 'Simba1234') {
        $_SESSION['admin_verified'] = true;
        header("Location: Student_manage.php");
        exit();
    } else {
        $error = "Invalid PIN. Contact Dantebrave.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image:rgb(190, 148, 148);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .verification-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        .verification-box h2 {
            color:rgb(11, 88, 11);
            margin-bottom: 20px;
        }
        .verification-box input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .verification-box button {
            background:rgb(28, 131, 28);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
        }
        .verification-box button:hover {
            background:rgb(48, 14, 242);
        }
        .error {
            color:rgb(241, 24, 16);
            margin-bottom: 15px;
        }
    </style>
</head>
<body>


    <div class="verification-box">
        <h2>Admin Verification</h2>
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>



        <form method="POST">
            
            <input type="password" name="admin_pin" placeholder="Enter Admin Password to proceed" required>
            <button type="submit">Verify</button>
            <br>
            <br>
            <br>
            <li><a href="home.php"><i class="fa-solid fa-backward"></i>Go Back</a></li >
        </form>
    </div>
</body>
</html>