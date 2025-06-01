<?php
session_start();
require_once '../db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $newPassword = trim($_POST['new_password']);

    // Input validation
    if (empty($username) || empty($newPassword)) {
        header("Location: forgot_password.php?error=All fields are required");
        exit;
    }

    // Check if the username is an email or just a username
    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        // The username is an email
        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = :email");
        $stmt->bindParam(':email', $username);
    } else {
        // The username is not an email, it's a regular username
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
    }

    // Execute the query
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists
    if ($user) {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the new password

        // Update the password in the database
        $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
        $updateStmt->bindParam(':password', $newPassword);
        $updateStmt->bindParam(':user_id', $user['id']);
        $updateStmt->execute();

        header("Location: forgot_password.php?success=Your password has been updated. You can now login.");
    } else {
        header("Location: forgot_password.php?error=No account found with that username or email.");
    }
} else {
    // If the form is not submitted, redirect to forgot password page
    header("Location: forgot_password.php");
    exit;
}
?>
