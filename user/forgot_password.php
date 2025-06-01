<?php
session_start();

// Display error messages if there are any
if (isset($_GET['error'])) {
    echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
}

if (isset($_GET['success'])) {
    echo '<p class="success">' . htmlspecialchars($_GET['success']) . '</p>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - EasyEats</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>

    <div class="login-container">
        <h2>Forgot Password</h2>
        
        <form action="forgot_password_process.php" method="POST">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" required>

            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required>

            <button type="submit">Reset Password</button>
        </form>

        <p>Remember your password? <a href="login.php">Login here</a></p>
    </div>

</body>
</html>
