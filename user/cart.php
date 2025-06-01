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

// Updated query to exclude rejected orders (add WHERE status != 'rejected')
$query = "SELECT c.order_id, c.quantity, c.price, c.delivery_date, c.status, c.order_type, c.created_at, i.name AS item_name, i.image AS item_image
          FROM cart c
          JOIN items i ON c.item_id = i.id
          WHERE c.user_id = :user_id AND c.status != 'rejected'";  // Exclude rejected status orders

$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $user_id]);

$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total price
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Handle quantity update or item removal (if POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Get form data
        $order_id = $_POST['order_id'];
        $new_quantity = $_POST['quantity'];

        // Get current order type (don't overwrite it)
        $new_order_type = isset($_POST['order_type']) ? $_POST['order_type'] : null;  // Don't set a default here

        // If order_type is not set (in case the form didn't update it), retain the current order type
        $current_order_type = null;
        foreach ($cart_items as $item) {
            if ($item['order_id'] == $order_id) {
                $current_order_type = $item['order_type']; // Get the existing order_type
                break;
            }
        }

        // If the form has not provided a new order_type, retain the current order_type
        $new_order_type = $new_order_type ?: $current_order_type;

        // Handle delivery date based on order type
        if ($new_order_type === 'preorder') {
            $new_delivery_date = isset($_POST['delivery_date']) ? $_POST['delivery_date'] : null; // Ensure delivery_date is set
        } else {
            // For 'dayorder', set the delivery date to the current date
            $new_delivery_date = date('Y-m-d');
        }

        // Update item quantity and order details (order_type and delivery_date)
        $query = "UPDATE cart 
                  SET quantity = :quantity, order_type = :order_type, delivery_date = :delivery_date 
                  WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':quantity' => $new_quantity,
            ':order_type' => $new_order_type,
            ':delivery_date' => $new_delivery_date,
            ':order_id' => $order_id,
            ':user_id' => $user_id
        ]);
    } elseif (isset($_POST['remove'])) {
        // Remove item from cart
        $order_id = $_POST['order_id'];

        // Step 1: Retrieve the reference_unique_key using the order_id
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
        }
    }

    // Refresh the page to reflect changes
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - EasyEats</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/cart.css"> <!-- Link to custom styles -->
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to custom styles -->
</head>
<body>
    <!-- Navbar (Home, Cart, etc.) -->
    <?php include('navbar.php'); ?>

    <div class="container my-5">
        <h1>Your Cart</h1>

        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty. <a href="menu.php">Browse items</a> and add to your cart.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Order Type</th>
                        <th>Delivery Date</th>
                        <th>Status</th> <!-- Add the Status column -->
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    <?php foreach ($cart_items as $item): ?>
        <tr>
            <td>
                <?php echo $item['item_name']; ?>
                <img src="../uploads/<?php echo $item['item_image']; ?>" alt="<?php echo $item['item_name']; ?>" width="100">
            </td>
            <td>$<?php echo number_format($item['price'], 2); ?></td>
            <td>
                <form action="cart.php" method="POST" style="display:inline;">
                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
                    <input type="hidden" name="order_id" value="<?php echo $item['order_id']; ?>">
            </td>
            <td>
                <!-- Display order type as plain text -->
                <span class="form-control-plaintext"><?php echo ucfirst($item['order_type']); ?></span>
            </td>
            <td>
                <!-- If order type is dayorder, show current date, else show preorder date -->
                <?php if ($item['order_type'] === 'dayorder'): ?>
                    <input type="text" class="form-control" value="<?php echo $item['created_at']; ?>" disabled>
                <?php else: ?>
                    <input type="date" name="delivery_date" value="<?php echo $item['delivery_date']; ?>" class="form-control" required>
                <?php endif; ?>
            </td>
            <td>
                <!-- Display the status -->
                <span class="form-control-plaintext"><?php echo ucfirst($item['status']); ?></span>
            </td>
            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            <td>
                <!-- Update button in Actions column -->
                <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
                </form> <!-- Ensure this form is properly closed here -->

                <!-- Remove button form -->
                <form action="cart.php" method="POST" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $item['order_id']; ?>">
                    <button type="submit" name="remove" class="btn btn-danger btn-sm">Remove</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

            </table>
            <div class="text-right">
                <h3>Total: $<?php echo number_format($total_price, 2); ?></h3>
                <a href="order_status.php" class="btn btn-success">Check status</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
