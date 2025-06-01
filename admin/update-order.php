<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the admin login page if not logged in or not an admin
    header('Location: admin-login.php');
    exit;
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    // Get the order ID and action
    $order_id = $_GET['id'];
    $action = $_GET['action'];

    // Validate the action (either "approve" or "reject")
    if ($action === 'approve' || $action === 'reject') {
        // Set the new status based on the action
        $status = ($action === 'approve') ? 'Approved' : 'Rejected';

        // Connect to the database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "easyeats";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Step 1: Retrieve the reference_unique_key using the order_id
        $query = "SELECT reference_unique_key FROM orders WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id); // "i" means integer
        $stmt->execute();
        $stmt->bind_result($reference_unique_key);
        $stmt->fetch();
        
        // Close the statement after fetching the result
        $stmt->close();

        if ($reference_unique_key) {
            // Step 2: Update the status in the cart table based on reference_unique_key
            $query = "UPDATE cart SET status = ? WHERE reference_unique_key = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $status, $reference_unique_key); // "ss" means string
            $stmt->execute();

            // Step 3: Update the status in the orders table
            $query = "UPDATE orders SET status = ? WHERE reference_unique_key = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $status, $reference_unique_key);
            $stmt->execute();

            // Check if the update was successful
            if ($stmt->affected_rows > 0) {
                // Redirect back to the order management page after updating the status
                header('Location: order-management.php');
                exit;
            } else {
                echo "Error updating order status.";
            }

            // Close the statement after all updates
            $stmt->close();
        } else {
            echo "Order not found.";
        }

        // Close the connection
        $conn->close();
    } else {
        echo "Invalid action.";
    }
} else {
    echo "Invalid request.";
}

?>
