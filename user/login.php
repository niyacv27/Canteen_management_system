<?php
session_start(); // Start the session to track user login status

// Display error messages if there are any
if (isset($_GET['error'])) {
    echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EasyEats</title>
    <link rel="stylesheet" href="../css/login.css"> <!-- Link to the login-specific CSS file -->
</head>
<body>

    <div class="login-container">
        <h2>Login to EasyEats</h2>
        
        <form action="login_process.php" method="POST">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="../register.php">Sign up here</a></p>
        <p><a href="forgot_password.php">Forgot Password?</a></p> <!-- Forgot Password Link -->
    </div>

</body>
</html>
