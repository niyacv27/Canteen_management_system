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

// Insert feedback
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback'])) {
    $feedback = $conn->real_escape_string($_POST['feedback']); // Escape special characters
    $user_name = $_SESSION['user_name']; // Assuming user_name is stored in session

    $sql = "INSERT INTO feedbacks (user_name, feedback_text, status) VALUES (?, ?, 'unread')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_name, $feedback); // Bind the parameters

    if ($stmt->execute()) {
        $message = "Feedback posted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch all feedbacks
$sql = "SELECT * FROM feedbacks ORDER BY date_time DESC";
$result = $conn->query($sql);

// Delete feedback
if (isset($_GET['delete'])) {
    $feedback_id = $_GET['delete'];
    $delete_sql = "DELETE FROM feedbacks WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $feedback_id);
    
    if ($delete_stmt->execute()) {
        header("Location: feedback-management.php"); // Refresh page after deletion
    } else {
        $message = "Error deleting feedback: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to navbar styles -->
    <link rel="stylesheet" href="css/feedback-management.css"> <!-- Link to custom styles -->
    <title>Feedback Management - EasyEats</title>
</head>
<body>
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Feedback Management</h1>
        <div class="feedbacks">
            <?php if ($result->num_rows > 0) { 
                while($row = $result->fetch_assoc()) { ?>
                    <div class="feedback">
                        <p class="feedback-user"><?php echo htmlspecialchars($row['user_name']); ?> says:</p>
                        <p class="feedback-text"><?php echo nl2br(htmlspecialchars($row['feedback_text'])); ?></p>
                        <small class="feedback-date"><?php echo $row['date_time']; ?></small>
                        <div class="actions">
                            <a href="feedback-management.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this feedback?')">Delete</a>
                            <?php if ($row['status'] == 'unread') { ?>
                                
                            <?php } ?>
                        </div>
                    </div>
                <?php } 
            } else { 
                echo "<p>No feedback available.</p>";
            } ?>
        </div>
    </div>

</body>
</html>

