<?php
session_start();
require_once 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Input validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: register.php?error=All fields are required");
        exit;
    }

    // Check if the password and confirm password match
    if ($password !== $confirm_password) {
        header("Location: register.php?error=Passwords do not match");
        exit;
    }

    // Password validation (basic rules)
    // 1. Minimum 8 characters
    // 2. At least one uppercase letter
    // 3. At least one lowercase letter
    // 4. At least one number
    // 5. (Optional) At least one special character

    $password_regex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    if (!preg_match($password_regex, $password)) {
        header("Location: register.php?error=Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character.");
        exit;
    }

    // Check if the username already exists in the database
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        // Username already taken
        header("Location: register.php?error=Username already taken");
        exit;
    }

    // Check if the email already exists in the database
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        // Email already taken
        header("Location: register.php?error=Email already taken");
        exit;
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);

    if ($stmt->execute()) {
        // Registration successful
        $_SESSION['user_id'] = $pdo->lastInsertId(); // Store user ID in the session
        $_SESSION['username'] = $username; // Store username in the session

        // Redirect to the login page or dashboard
        header("Location: user/login.php");
        exit;
    } else {
        // If there was an error inserting the data
        header("Location: register.php?error=Something went wrong, please try again");
        exit;
    }
} else {
    // If the request is not POST, redirect back to register page
    header("Location: register.php");
    exit;
}
