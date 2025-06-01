<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the admin login page if not logged in or not an admin
    header('Location: admin-login.php');  
    exit;
}

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easyeats";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch only orders with status 'Pending'
$sql = "SELECT * FROM orders WHERE status = 'Pending'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to navbar styles -->
    <link rel="stylesheet" href="css/order-management.css"> <!-- Link to order management styles -->
    <title>Order Management - EasyEats</title>
</head>
<body>

    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <h1>Order Management</h1>
    
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Item Name</th>
                <th>Order Type</th>
                <th>Date</th>
                <th>Customization</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['item_name']; ?></td>
                <td><?php echo $row['order_type']; ?></td> <!-- Display order type -->
                <td><?php echo $row['order_datetime']; ?></td> <!-- Display date -->
                <td><?php echo $row['customization']; ?></td> <!-- Display customization -->
                <td><?php echo $row['status']; ?></td>
                <td>
                    <!-- Approve button (change status to "Approved") -->
                    <a href="update-order.php?id=<?php echo $row['id']; ?>&action=approve" class="approve-btn">Approve</a>
                    <!-- Reject button (change status to "Rejected") -->
                    <a href="update-order.php?id=<?php echo $row['id']; ?>&action=reject" class="reject-btn">Reject</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
