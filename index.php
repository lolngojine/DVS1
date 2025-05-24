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
  <title>JKUAT Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="index.css">
</head>

<body>
  <div class="container">
    <div class="form-title">
      <img src="photos/jkuat-logo.webp" alt="JKUAT Logo" class="logo" style="width:80px; height:auto; margin-bottom:10px;">
      <h1>Sign In</h1>

      <?php if (isset($errors['login'])): ?>
        <div class="error-main">
          <p><?= $errors['login'] ?></p>
        </div>
        <?php unset($errors['login']); ?>
      <?php endif; ?>
    </div>

    <form method="POST" action="user-account.php">
      <div class="input-group">
        <label for="email" style="display:none;">Email</label>
        <i class="fas fa-envelope" style="position:absolute; top:50%; left:10px; transform:translateY(-50%); color:#004B87;"></i>
        <input type="email" name="email" id="email" placeholder="Email" required style="padding-left:40px;">
        <?php if (isset($errors['email'])): ?>
          <div class="error">
            <p><?= $errors['email'] ?></p>
          </div>
        <?php endif; ?>
      </div>

      <div class="input-group password">
        <label for="password" style="display:none;">Password</label>
        <i class="fas fa-lock" style="position:absolute; top:50%; left:10px; transform:translateY(-50%); color:#004B87;"></i>
        <input type="password" name="password" id="password" placeholder="Password" required style="padding-left:40px;">
        <i id="eye" class="fa fa-eye"></i>
        <?php if (isset($errors['password'])): ?>
          <div class="error">
            <p><?= $errors['password'] ?></p>
          </div>
        <?php endif; ?>
      </div>

      <div class="recover">
        <a href="#">Recover Password</a>
      </div>

      <input type="submit" class="btn" value="Sign In" name="signin">

      <p class="or">---------- or ----------</p>

      <div class="icons">
        <a href="#"><i class="fab fa-google"></i></a>
        <a href="#"><i class="fab fa-facebook"></i></a>
      </div>

      <div class="links">
        <p>Don't have an account yet? <a href="register.php">Sign Up</a></p>
      </div>
    </form>
  </div>

  <script src="script.js"></script>
</body>
</html>

<?php
if (isset($_SESSION['errors'])) {
  unset($_SESSION['errors']);
}
?>
