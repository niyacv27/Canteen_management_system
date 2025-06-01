<?php
// Start the session
session_start();

// // Check if user is logged in
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
//     header('Location: login.php');
//     exit;
// }
$approved_orders = $_SESSION['approved_orders'] ?? [];
$total_price = $_SESSION['total_price'] ?? 0;

// Include database connection
include('../db.php');

// Get the order_id from the GET request
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Retrieve the reference_unique_key from the cart table using order_id
    $query = "SELECT reference_unique_key FROM cart WHERE order_id = :order_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':order_id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "Order not found.";
        exit;
    }

    $reference_unique_key = $order['reference_unique_key'];

    // Retrieve data from both orders and cart using the reference_unique_key
    $query = "SELECT 
                o.id AS order_id, o.username, o.item_name, o.order_datetime, o.customization, 
                o.status AS order_status, o.order_type, 
                c.item_id, c.user_id, c.delivery_date, c.quantity, c.price, c.status AS cart_status, 
                c.created_at, c.updated_at,
                i.image AS item_image, i.name AS item_name
              FROM orders o
              JOIN cart c ON o.reference_unique_key = c.reference_unique_key
              JOIN items i ON c.item_id = i.id
              WHERE o.reference_unique_key = :reference_unique_key";

    $stmt = $pdo->prepare($query);
    $stmt->execute([':reference_unique_key' => $reference_unique_key]);
    $checkout_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$checkout_details) {
        echo "No checkout details found.";
        exit;
    }

    // Insert data into the checkout table if not already inserted
    $checkQuery = "SELECT COUNT(*) FROM checkout WHERE order_id = :order_id";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([':order_id' => $checkout_details['order_id']]);
    $exists = $stmt->fetchColumn();

    // If the record doesn't exist, insert it into the checkout table
    if ($exists == 0) {
        $insertQuery = "INSERT INTO checkout (
                            order_id, username, item_name, order_datetime, customization, 
                            order_status, order_type, item_id, user_id, delivery_date, quantity, 
                            price, cart_status, created_at, updated_at, item_image, reference_unique_key
                        ) VALUES (
                            :order_id, :username, :item_name, :order_datetime, :customization, 
                            :order_status, :order_type, :item_id, :user_id, :delivery_date, :quantity, 
                            :price, :cart_status, :created_at, :updated_at, :item_image, :reference_unique_key
                        )";

        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([
            ':order_id' => $checkout_details['order_id'],
            ':username' => $checkout_details['username'],
            ':item_name' => $checkout_details['item_name'],
            ':order_datetime' => $checkout_details['order_datetime'],
            ':customization' => $checkout_details['customization'],
            ':order_status' => $checkout_details['order_status'],
            ':order_type' => $checkout_details['order_type'],
            ':item_id' => $checkout_details['item_id'],
            ':user_id' => $checkout_details['user_id'],
            ':delivery_date' => $checkout_details['delivery_date'],
            ':quantity' => $checkout_details['quantity'],
            ':price' => $checkout_details['price'],
            ':cart_status' => $checkout_details['cart_status'],
            ':created_at' => $checkout_details['created_at'],
            ':updated_at' => $checkout_details['updated_at'],
            ':item_image' => $checkout_details['item_image'],
            ':reference_unique_key' => $reference_unique_key
        ]);
    }
} else {
    echo "No order selected.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - EasyEats</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/checkout.css"> <!-- Custom Styles -->
    <link rel="stylesheet" href="css/navbar.css"> <!-- Navbar Styles -->
</head>
<body>
    <!-- Navbar (Home, Cart, etc.) -->
    <?php include('navbar.php'); ?>

    <div class="container my-5">
        <h1 class="display-4 text-center mb-5">Checkout</h1>
        
        <div class="row">
            <!-- Order Summary Section -->
            <div class="col-md-8">
                <h3 class="h4 mb-4 text-uppercase font-weight-bold">Order Summary</h3>
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
                                        <img src="../uploads/<?php echo $checkout_details['item_image']; ?>" alt="<?php echo $checkout_details['item_name']; ?>" width="100" class="mr-3">
                                        <?php echo $checkout_details['item_name']; ?>
                                    </td>
                                    <td><?php echo $checkout_details['quantity']; ?></td>
                                    <td>$<?php echo number_format($checkout_details['price'], 2); ?></td>
                                    <td>$<?php echo number_format($checkout_details['price'] * $checkout_details['quantity'], 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <p><strong>Order ID:</strong> <?php echo $checkout_details['order_id']; ?></p>
                        <p><strong>Order Date:</strong> <?php echo $checkout_details['order_datetime']; ?></p>
                        <p><strong>Delivery Date:</strong> <?php echo $checkout_details['delivery_date']; ?></p>
                        <p><strong>Order Type:</strong> <?php echo ucfirst($checkout_details['order_type']); ?></p>
                        <p><strong>Customization:</strong> <?php echo $checkout_details['customization'] ? $checkout_details['customization'] : 'None'; ?></p>
                        <p><strong>Status:</strong> <?php echo ucfirst($checkout_details['order_status']); ?></p>
                    </div>
                </div>
            </div>

<!-- Payment Section -->
<div class="col-md-4">
    <h3 class="h4 mb-4 text-uppercase font-weight-bold">Payment</h3>
    <div class="card shadow-sm">
        <div class="card-body">
        <form action="process_payment.php" method="POST" id="payment-form">
    <input type="hidden" name="reference_unique_key" value="<?php echo $reference_unique_key; ?>"> <!-- Only reference_unique_key -->

    <!-- Payment Method Selection -->
    <div class="form-group">
        <label for="payment_method">Select Payment Method</label>
        <select class="form-control" id="payment_method" name="payment_method" required>
            <option value="credit_card">Credit/Debit Card</option>
            <option value="net_banking">Net Banking</option>
            <option value="paypal">PayPal</option>
            <option value="google_pay">Google Pay</option>
            <option value="cash_on_delivery">Cash on Delivery</option>
        </select>
    </div>

                <!-- Credit Card Payment Section -->
                <div id="credit_card_section" class="payment-section" style="display: none;">
                    <h5>Credit/Debit Card</h5>
                    <div class="form-group">
                        <label for="card_name">Name on Card</label>
                        <input type="text" class="form-control" id="card_name" name="card_name" placeholder="Enter your name" >
                    </div>
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" placeholder="Enter your card number" >
                    </div>
                    <div class="form-group">
                        <label for="expiration_date">Expiration Date</label>
                        <input type="text" class="form-control" id="expiration_date" name="expiration_date" placeholder="MM/YY" >
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" class="form-control" id="cvv" name="cvv" placeholder="Enter CVV">
                    </div>
                </div>

                <!-- Net Banking Section -->
                <div id="net_banking_section" class="payment-section" style="display: none;">
                    <h5>Net Banking</h5>
                    <div class="form-group">
                        <label for="bank_name">Select Your Bank</label>
                        <select class="form-control" name="bank_name">
                            <option value="bank1">SBI</option>
                            <option value="bank2">AXIS</option>
                            <option value="bank3">UNION BANK</option>
                            <option value="bank4">BanK OF BARODA</option>
                        </select>
                    </div>
                </div>

                <!-- PayPal Section -->
                <div id="paypal_section" class="payment-section" style="display: none;">
                    <h5>PayPal</h5>
                    <p>Pay securely using your PayPal account.</p>
                    <button type="button" class="btn btn-warning btn-block">Proceed to PayPal</button>
                </div>

                <!-- Google Pay Section -->
                <div id="google_pay_section" class="payment-section" style="display: none;">
                    <h5>Google Pay</h5>
                    <p>Pay with your Google Pay account.</p>
                    <button type="button" class="btn btn-danger btn-block">Pay with Google Pay</button>
                </div>

                <!-- Cash on Delivery Section -->
                <div id="cod_section" class="payment-section" style="display: none;">
                    <h5>Cash on Delivery</h5>
                    <p>Pay when you receive the delivery.</p>
                </div>

              
    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary btn-lg btn-block" id="submit-btn">Proceed to Payment</button>
</form>
        </div>
    </div>
</div>

<!-- JavaScript for Dynamic Payment Sections -->
<script>
    // Toggle payment sections based on selected payment method
    document.getElementById('payment_method').addEventListener('change', function() {
        // Hide all payment method sections
        var sections = document.querySelectorAll('.payment-section');
        sections.forEach(function(section) {
            section.style.display = 'none';
        });

        // Show the selected payment method section
        var selectedMethod = this.value;
        if (selectedMethod === 'credit_card') {
            document.getElementById('credit_card_section').style.display = 'block';
        } else if (selectedMethod === 'net_banking') {
            document.getElementById('net_banking_section').style.display = 'block';
        } else if (selectedMethod === 'paypal') {
            document.getElementById('paypal_section').style.display = 'block';
        } else if (selectedMethod === 'google_pay') {
            document.getElementById('google_pay_section').style.display = 'block';
        } else if (selectedMethod === 'cash_on_delivery') {
            document.getElementById('cod_section').style.display = 'block';
        }
    });


    document.getElementById('payment-form').addEventListener('submit', function(event) {
    var confirmation = confirm("Are you sure you want to proceed with the payment?");
    if (!confirmation) {
        event.preventDefault(); // Prevent form submission if not confirmed
    }
});

</script>


        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery (for Bootstrap components) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Show and hide credit card fields based on payment method selection
        document.getElementById('payment_method').addEventListener('change', function() {
            var paymentMethod = this.value;
            var creditCardSection = document.getElementById('credit_card_section');

            if (paymentMethod === 'credit_card') {
                creditCardSection.style.display = 'block';
            } else {
                creditCardSection.style.display = 'none';
            }
        });


        

    </script>
</body>
</html>
