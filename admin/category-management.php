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

// Insert new category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_name'])) {
    $category_name = $conn->real_escape_string($_POST['category_name']); // Escape special characters

    $sql = "INSERT INTO category  (text) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_name); // Bind the parameter to avoid SQL injection

    if ($stmt->execute()) {
        $message = "Category added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch all categories
$sql = "SELECT * FROM category ORDER BY time DESC";
$result = $conn->query($sql);

// Delete category
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the category from the database
    $sql_delete = "DELETE FROM category WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $delete_id);
    
    if ($stmt_delete->execute()) {
        header("Location: category-management.php"); // Redirect to refresh the page
        exit();
    } else {
        $message = "Error deleting category: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to navbar styles -->
    <link rel="stylesheet" href="css/category-management.css"> <!-- Link to custom styles -->
    <title>Category Management - EasyEats</title>
</head>
<body>

    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Category Management</h1>

        <!-- Display status message -->
        <?php if (isset($message)) { echo "<p class='status-message'>$message</p>"; } ?>

        <!-- Category Form -->
        <form action="category-management.php" method="POST" class="category-form">
            <input type="text" name="category_name" placeholder="Enter category name" required>
            <button type="submit">Add Category</button>
        </form>

        <!-- Categories Table -->
        <h2>All Categories</h2>
        <table>
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { 
                    while($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['text']); ?></td>
                            <td><?php echo $row['time']; ?></td>
                            <td>
                                <a href="category-management.php?delete_id=<?php echo $row['id']; ?>" class="remove-btn">Remove</a>
                            </td>
                        </tr>
                    <?php } 
                } else { 
                    echo "<tr><td colspan='4'>No categories found.</td></tr>";
                } ?>
            </tbody>
        </table>
    </div>

</body>
</html>
