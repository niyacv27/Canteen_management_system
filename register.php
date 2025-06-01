<?php
session_start(); // Start the session

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
    <title>Sign Up - EasyEats</title>
    <link rel="stylesheet" href="css/registration.css"> <!-- Optional: link to your CSS file -->
</head>
<body>

    <div class="register-container">
        <h2>Create Your Account</h2>
        
        <form action="register_process.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Sign Up</button>
        </form>

        <p>Already have an account? <a href="user/login.php">Login here</a></p>
    </div>

</body>
</html>
