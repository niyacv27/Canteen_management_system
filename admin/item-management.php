<?php
session_start();

// Check if admin is logged in
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

// Fetch categories for the dropdown
$categories_result = mysqli_query($conn, "SELECT * FROM category");

// Add item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category'];  // Category ID from the dropdown
    $image = $_FILES['image']['name'];
    $image_temp = $_FILES['image']['tmp_name'];

    // Move the image to the upload directory
    move_uploaded_file($image_temp, '../uploads/' . $image);

    // Insert new item into the database (without description)
    $query = "INSERT INTO items (name, price, category_id, image) 
              VALUES ('$name', '$price', '$category_id', '$image')";
    mysqli_query($conn, $query);
    header("Location: item-management.php");
}

// Delete item
if (isset($_GET['delete'])) {
    $item_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM items WHERE id = $item_id");
    header("Location: item-management.php");
}

// Fetch items along with their category names
$items_result = mysqli_query($conn, "SELECT items.*, category.text as category_name FROM items JOIN category ON items.category_id = category.id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Management</title>
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to navbar styles -->
    <link rel="stylesheet" href="css/item-management.css"> <!-- Link to admin panel styles -->
</head>
<body>

    <!-- Navbar -->
    <?php include('navbar.php'); ?>

    <div class="container">
        <!-- Add Item Form Section -->
        <div class="form-section">
            <h2>Add New Item</h2>
            <form action="item-management.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Item Name" required><br>
                <input type="number" name="price" placeholder="Price" required><br>

                <select name="category" required>
                    <option value="">Select Category</option>
                    <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                        <option value="<?= $category['id'] ?>"><?= $category['text'] ?></option>
                    <?php endwhile; ?>
                </select><br>

                <input type="file" name="image" accept="image/*" required><br>

                <button type="submit" name="add_item">Add Item</button>
            </form>
        </div>

        <!-- Manage Existing Items Section -->
        <div class="items-section">
            <h2>Manage Existing Items</h2>
            <table>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>

                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['category_name']) ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td><img src="../uploads/<?= htmlspecialchars($item['image']) ?>" alt="Item Image"></td>
                    <td class="actions">
                        <a href="item-management.php?delete=<?= $item['id'] ?>" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>

            </table>
        </div>
    </div>

</body>
</html>
