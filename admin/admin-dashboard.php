<?php
// Start session and check if admin is logged in
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the admin login page if not logged in or not an admin
    header('Location: admin-login.php');  
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EasyEats</title>
    <link rel="stylesheet" href="css/admin-dashboard.css"> <!-- Your CSS file for styling -->
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to navbar styles -->
</head>
<body>

    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Admin Dashboard Content -->
    <section class="dashboard-content">
        <h1>Welcome to Admin Dashboard</h1>
        <p>Select an option from the menu to manage orders, announcements, items, and more.</p>
    </section>

</body>
</html>
