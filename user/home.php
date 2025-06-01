<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Redirect to login page if not logged in or not a user
    header('Location: login.php');  
    exit;
}

// Include the database connection
include('../db.php');

// Query to fetch food items
$query = "SELECT * FROM items";
$stmt = $pdo->prepare($query);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all items as an associative array
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyEats - Home</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to custom navbar styles -->
    <link rel="stylesheet" href="css/home.css"> 
</head>
<body>
    <!-- Navbar (Home, Cart, etc.) -->
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Welcome to EasyEats!</h1>

        <!-- Display food items -->
        <div class="row">
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                            <!-- Image -->
                            <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top" alt="Food Image">
                            
                            <!-- Card Body -->
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                <p class="card-text text-muted">$<?php echo number_format($item['price'], 2); ?></p>
                            </div>

                            <!-- Order Button -->
                            <div class="card-footer bg-light text-center border-0">
                                <button type="button" class="btn btn-success btn-lg w-100" onclick="toggleDropdown(<?php echo $item['id']; ?>)">Order</button>

                                <div id="order-dropdown-<?php echo $item['id']; ?>" class="order-dropdown d-none mt-3">
                                    <form action="add_order.php" method="POST" id="order-form-<?php echo $item['id']; ?>">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($item['name']); ?>">
                                        <input type="hidden" name="order_type" value="preorder">
                                        
                                        <select name="order_type" class="form-control mb-3">
                                            <option value="preorder">Preorder</option>
                                            <option value="dayorder">Day Order</option>
                                        </select>

                                        <button type="button" class="btn btn-primary btn-lg w-100" id="place-order-btn-<?php echo $item['id']; ?>">Place Order</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">No food items available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Customization Modal -->
    <div class="modal fade" id="customizationModal" tabindex="-1" role="dialog" aria-labelledby="customizationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customizationModalLabel">Add Customization</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="customization-form">
                        <div class="form-group">
                            <label for="customization-text">Enter your custom request</label>
                            <textarea class="form-control" id="customization-text" rows="3" placeholder="Add your notes here..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitCustomizationBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Custom Script for Dropdown Toggle -->
    <script>
        // Function to toggle the visibility of the dropdown
        function toggleDropdown(itemId) {
            var dropdown = document.getElementById('order-dropdown-' + itemId);
            dropdown.classList.toggle('d-none');
        }
    </script>

    <script>
        // Function to open the customization modal and handle form submission
function handlePlaceOrder(itemId) {
    // Get the selected order type
    var orderType = $('select[name="order_type"]').val();

    // If the order type is "Day Order," skip the customization modal and submit the form
    if (orderType === 'dayorder') {
        var orderForm = $('#order-form-' + itemId);
        orderForm.submit(); // Submit the form directly
        alert("Thank you for your order! Your order has been placed successfully.");
        return; // Exit the function, so the modal doesn't open
    }

    // Otherwise, show the customization modal for "Preorder" or other types
    $('#customizationModal').modal('show');

    // When the "OK" button in the modal is clicked
    $('#submitCustomizationBtn').click(function() {
        // Get the custom text
        var customizationText = $('#customization-text').val();
        
        // Check if the user entered any customization text
        if (customizationText.trim() === '') {
            alert('Please enter some customization text');
            return; // Prevent submission if empty
        }

        // Attach the customization text to the form
        var orderForm = $('#order-form-' + itemId);
        $('<input>').attr({
            type: 'hidden',
            name: 'customization',
            value: customizationText
        }).appendTo(orderForm);

        // Submit the form
        orderForm.submit();
        
        // Close the modal
        $('#customizationModal').modal('hide');

        // Display the order confirmation alert
        alert("Thank you for your order! Your order has been placed successfully.");
    });
}

// Bind the Place Order button click to open the modal or submit the form
<?php foreach ($items as $item): ?>
    $('#place-order-btn-<?php echo $item['id']; ?>').click(function() {
        handlePlaceOrder(<?php echo $item['id']; ?>);
    });
<?php endforeach; ?>

    </script>

</body>
</html>
