<?php
// Start the session
session_start();

// Include database connection
include('../db.php');

// Check if the reference_unique_key is provided via GET
if (isset($_GET['reference_unique_key'])) {
    $reference_unique_key = $_GET['reference_unique_key'];

    // Retrieve the order details using reference_unique_key from the checkout table
    $query = "SELECT * FROM checkout WHERE reference_unique_key = :reference_unique_key";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':reference_unique_key' => $reference_unique_key]);

    // Check if the order exists in the checkout table
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "Order not found.";
        exit;
    }

    // After successfully retrieving order details, proceed to delete related records
    try {
        // Begin a transaction to ensure data consistency
        $pdo->beginTransaction();

        // Delete related rows from the 'cart' table
        $deleteCartQuery = "DELETE FROM cart WHERE reference_unique_key = :reference_unique_key";
        $stmt = $pdo->prepare($deleteCartQuery);
        $stmt->execute([':reference_unique_key' => $reference_unique_key]);

        // Delete related rows from the 'orders' table
        $deleteOrderQuery = "DELETE FROM orders WHERE reference_unique_key = :reference_unique_key";
        $stmt = $pdo->prepare($deleteOrderQuery);
        $stmt->execute([':reference_unique_key' => $reference_unique_key]);

        // Commit the transaction if everything is successful
        $pdo->commit();

    } catch (Exception $e) {
        // Rollback the transaction if anything goes wrong
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
        exit;
    }

} else {
    echo "No reference key provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - EasyEats</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css"> <!-- Navbar Styles -->
</head>
<body>

    <!-- Navbar (Home, Cart, etc.) -->
    <?php include('navbar.php'); ?>

    <div class="container my-5">
        <h1 class="display-4 text-center mb-5">Payment Successful</h1>
        
        <div class="row">
            <!-- Order Summary Section -->
            <div class="col-md-8">
                <h3 class="h4 mb-4 text-uppercase font-weight-bold">Order Details</h3>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <img src="../uploads/<?php echo $order['item_image']; ?>" alt="<?php echo $order['item_name']; ?>" width="100" class="mr-3">
                                        <?php echo $order['item_name']; ?>
                                    </td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td>$<?php echo number_format($order['price'], 2); ?></td>
                                    <td>$<?php echo number_format($order['price'] * $order['quantity'], 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
                        <p><strong>Order Date:</strong> <?php echo $order['order_datetime']; ?></p>
                        <p><strong>Delivery Date:</strong> <?php echo $order['delivery_date']; ?></p>
                        <p><strong>Order Type:</strong> <?php echo ucfirst($order['order_type']); ?></p>
                        <p><strong>Customization:</strong> <?php echo $order['customization'] ? $order['customization'] : 'None'; ?></p>
                        <p><strong>Status:</strong> <?php echo ucfirst($order['order_status']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Payment Confirmation Section -->
            <div class="col-md-4">
                <h3 class="h4 mb-4 text-uppercase font-weight-bold">Payment Confirmation</h3>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-success">Your payment has been successfully processed. Thank you for your purchase!</p>
                        <p><strong>Payment Method:</strong> <?php echo ucfirst($order['order_type']); ?></p>
                        <p><strong>Total Amount:</strong> $<?php echo number_format($order['price'] * $order['quantity'], 2); ?></p>
                        <p><strong>Order Status:</strong> <span class="text-success"><?php echo ucfirst($order['order_status']); ?></span></p>
                        <a href="home.php" class="btn btn-primary btn-block">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery (for Bootstrap components) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
