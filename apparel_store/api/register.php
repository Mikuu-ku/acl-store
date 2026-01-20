<?php
include "config/database.php";

$success = false; 
if (isset($_POST['register'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $contact = mysqli_real_escape_string($conn, $_POST['contact_no']);

    if ($pass !== $confirm_pass) {
        $error = "Passwords do not match!";
    } 
    else if (!preg_match("/^09\d{9}$/", $contact)) {
        $error = "Contact number must be 11 digits and start with 09";
    } 
    else {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users 
                (first_name, last_name, email, password, contact_no, role)
                VALUES ('$fname','$lname','$email','$hashed_pass','$contact', 'user')";

        if (mysqli_query($conn, $sql)) {
            $success = true;
            header("Refresh: 2; url=login.php?msg=registered");
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Apparel's Clothing Line</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-form-container">
        
        <?php if($success): ?>
            <div class="login-success-overlay">
                <div class="checkmark-circle">
                    <i class="fas fa-check"></i>
                </div>
                <h3 style="letter-spacing: 1px;">ACCOUNT CREATED</h3>
                <p style="font-size: 12px; color: #888;">Welcome to the Apparel family.</p>
                <p style="font-size: 11px; margin-top: 15px; color: #aaa; text-transform: uppercase;">Redirecting to login...</p>
            </div>
        <?php else: ?>
            <div class="auth-logo">
                <a href="index.php">
                    <img src="assets/images/logo.png" alt="Apparel's Logo">
                </a>
            </div>

            <h2>Create Account</h2>
            <p class="auth-subtext">Join our clothing line today</p>

            <?php if(isset($error)): ?>
                <div class="error-msg"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <input type="text" name="first_name" placeholder="FIRST NAME" required>
                <input type="text" name="last_name" placeholder="LAST NAME" required>
                <input type="email" name="email" placeholder="EMAIL ADDRESS" required>
                
                <input type="password" name="password" placeholder="PASSWORD" required>
                <input type="password" name="confirm_password" placeholder="CONFIRM PASSWORD" required>
                
                <input type="text" name="contact_no" placeholder="CONTACT NO. (09XXXXXXXXX)" required>
                <button name="register" type="submit">Register</button>
            </form>
            
            <div class="auth-footer">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>