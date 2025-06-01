<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

// Include database connection
include('../db.php');

// Check if the necessary POST data is provided (just reference_unique_key)
if (isset($_POST['reference_unique_key'])) {
    $reference_unique_key = $_POST['reference_unique_key'];

    // Update the checkout table status to 'paid' (or 'delivered' based on your needs)
    $updateQuery = "UPDATE checkout SET cart_status = 'paid' , order_status= 'paid' WHERE reference_unique_key = :reference_unique_key";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute([':reference_unique_key' => $reference_unique_key]);

    // If the status was successfully updated
    if ($stmt->rowCount() > 0) {
        // Optionally, you can delete related records from the cart table, if needed
        // $deleteCartQuery = "DELETE FROM cart WHERE reference_unique_key = :reference_unique_key";
        // $stmt = $pdo->prepare($deleteCartQuery);
        // $stmt->execute([':reference_unique_key' => $reference_unique_key]);

        // Redirect to a success page
        header("Location: payment_success.php?reference_unique_key=$reference_unique_key");
        exit;
    } else {
        echo "Failed to update checkout status.";
    }
} else {
    echo "Invalid request.";
    exit;
}
?>
