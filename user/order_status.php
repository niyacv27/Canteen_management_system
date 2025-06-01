<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

// Include database connection
include('../db.php');

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Updated query to fetch orders from both cart and checkout tables with sorting by created_at
$query = "
    SELECT o.order_id, o.quantity, o.price, o.delivery_date, o.status, o.order_type, o.created_at, 
           i.name AS item_name, i.image AS item_image, 'cart' AS source
    FROM cart o
    JOIN items i ON o.item_id = i.id
    WHERE o.user_id = :user_id
    UNION ALL
    SELECT c.order_id, c.quantity, c.price, c.delivery_date, c.order_status, c.order_type, c.created_at, 
           i.name AS item_name, i.image AS item_image, 'checkout' AS source
    FROM checkout c
    JOIN items i ON c.item_id = i.id
    WHERE c.user_id = :user_id
    ORDER BY created_at DESC
";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $user_id]);

$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total price (exclude paid orders)
$total_price = 0;
$approved_orders = []; // Array to hold approved orders

foreach ($order_items as $item) {
    // Add to the total price if status is not 'Paid' and is approved
    if (strtolower($item['status']) !== 'paid') {
        $total_price += $item['price'] * $item['quantity'];
    }
    if (strtolower($item['status']) === 'approved') {
        $approved_orders[] = $item; // Add the approved orders to the array
    }
}

// Handle actions (Cancel or Pay Now)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        // Cancel the order from the cart or checkout table
        $order_id = $_POST['order_id'];
        $source = $_POST['source']; // Get the source (cart or checkout)

        if ($source === 'cart') {
            // Step 1: Retrieve the reference_unique_key using the order_id from cart
            $query = "SELECT reference_unique_key FROM cart WHERE order_id = :order_id AND user_id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':order_id' => $order_id, ':user_id' => $user_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($order) {
                $reference_unique_key = $order['reference_unique_key'];

                // Step 2: Delete the order from the cart table
                $query = "DELETE FROM cart WHERE order_id = :order_id AND user_id = :user_id";
                $stmt = $pdo->prepare($query);
                $stmt->execute([':order_id' => $order_id, ':user_id' => $user_id]);

                // Step 3: Delete the order from the orders table using the reference_unique_key
                $query = "DELETE FROM orders WHERE reference_unique_key = :reference_unique_key";
                $stmt = $pdo->prepare($query);
                $stmt->execute([':reference_unique_key' => $reference_unique_key]);

                // Refresh the page to reflect changes
                header('Location: order_status.php');
                exit;
            }
        } elseif ($source === 'checkout') {
            // Step 1: Delete the order from the checkout table
            $query = "DELETE FROM checkout WHERE order_id = :order_id AND user_id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':order_id' => $order_id, ':user_id' => $user_id]);

            // Refresh the page to reflect changes
            header('Location: order_status.php');
            exit;
        }
    } elseif (isset($_POST['paynow'])) {
        // Update the status to Paid in the orders table (not affected by cancel button)
        $order_id = $_POST['order_id'];
        $query = "UPDATE orders SET status = 'Paid' WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':order_id' => $order_id, ':user_id' => $user_id]);
    }

    // Refresh the page to reflect changes
    header('Location: order_status.php');
    exit;
}

// Handle Proceed to Checkout
if (isset($_POST['proceed_to_checkout'])) {
    // Filter only approved orders
    $approved_orders = array_filter($order_items, function ($item) {
        return strtolower($item['status']) === 'approved';
    });

    // Calculate total price for approved orders
    $total_price = 0;
    foreach ($approved_orders as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // Store the approved orders and their total price in session
    $_SESSION['approved_orders'] = $approved_orders;
    $_SESSION['total_price'] = $total_price;
    
    // Redirect to the checkout page
    header('Location: checkout.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status - EasyEats</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/order_status.css"> <!-- Link to custom styles -->
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to custom styles -->
</head>
<body>
    <!-- Navbar (Home, Cart, etc.) -->
    <?php include('navbar.php'); ?>

    <div class="container my-5">
        <h1>Your Order Status</h1>

        <?php if (empty($order_items)): ?>
            <p>You don't have any orders. <a href="menu.php">Browse items</a> and place an order.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Order Type</th>
                        <th>Order Date</th>
                        <th>Delivery Date</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td>
                                <?php echo $item['item_name']; ?>
                                <img src="../uploads/<?php echo $item['item_image']; ?>" alt="<?php echo $item['item_name']; ?>" width="100">
                            </td>
                            <td>
                                <span class="form-control-plaintext"><?php echo ucfirst($item['order_type']); ?></span>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="<?php echo $item['created_at']; ?>" disabled>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="<?php echo $item['delivery_date']; ?>" disabled>
                            </td>
                            <td>
                                <span class="form-control-plaintext"><?php echo ucfirst($item['status']); ?></span>
                            </td>
                            <td>
                                $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </td>
                            <td>
                                <!-- Show action buttons based on the order status -->
                                <?php if ($item['status'] === 'Approved'): ?>
                                    <!-- Approved order: show both Cancel and Pay Now buttons -->
                                    <form action="order_status.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $item['order_id']; ?>">
                                        <input type="hidden" name="source" value="<?php echo $item['source']; ?>"> <!-- Cart or Checkout -->
                                        <button type="submit" name="cancel" class="btn btn-danger btn-sm">Cancel</button>
                                    </form>
                                    
                                    <!-- Pay Now form: redirect to checkout page with order_id -->
                                    <form action="checkout.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $item['order_id']; ?>">
                                        <button type="submit" class="btn btn-success btn-sm">Pay Now</button>
                                    </form>
                                <?php elseif ($item['status'] === 'Pending'): ?>
                                    <!-- Pending order: show only Cancel button -->
                                    <form action="order_status.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $item['order_id']; ?>">
                                        <input type="hidden" name="source" value="<?php echo $item['source']; ?>"> <!-- Cart or Checkout -->
                                        <button type="submit" name="cancel" class="btn btn-danger btn-sm">Cancel</button>
                                    </form>
                                <?php elseif ($item['status'] === 'rejected'): ?>
                                    <!-- Rejected order: no action buttons -->
                                    <span class="form-control-plaintext">No action available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-right">
                <h3>Total: $<?php echo number_format($total_price, 2); ?></h3>
                <!-- Add button to proceed to checkout with approved orders -->
                <form action="checkout.php" method="POST">
                    <button type="submit" name="proceed_to_checkout" class="btn btn-success">Proceed to Checkout</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
