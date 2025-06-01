<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Redirect to login page if not logged in as a user
    header('Location: login.php');  
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
    $user_name = $_SESSION['username']; // Get the user's name from session

    $sql = "INSERT INTO feedbacks (user_name, feedback_text, status) VALUES (?, ?, 'unread')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_name, $feedback); // Bind the parameters

    if ($stmt->execute()) {
        $message = "Feedback posted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch all feedbacks for admin view
$sql = "SELECT * FROM feedbacks ORDER BY date_time DESC";
$result = $conn->query($sql);

// Delete feedback (only admin can delete)
if (isset($_GET['delete']) && $_SESSION['role'] === 'user') {
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
        <h1>User Feedback</h1>
        
        <!-- User feedback submission form -->
        <div class="feedback-form">
            <h3>Submit Your Feedback</h3>
            <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
            <form action="feedback-management.php" method="POST">
                <div class="form-group">
                    <textarea name="feedback" rows="5" class="form-control" required placeholder="Write your feedback here..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
            </form>
        </div>
    </div>
</body>
</html>
