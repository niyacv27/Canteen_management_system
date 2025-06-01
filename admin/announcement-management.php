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

// Insert announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['announcement'])) {
    $announcement = $conn->real_escape_string($_POST['announcement']); // Escape special characters

    $sql = "INSERT INTO announcements (text) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $announcement); // Bind the parameter to avoid SQL injection

    if ($stmt->execute()) {
        $message = "New announcement posted!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch all announcements
$sql = "SELECT * FROM announcements ORDER BY date_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to navbar styles -->
    <link rel="stylesheet" href="css/announcement-management.css"> <!-- Link to custom styles -->
    <title>Announcement Management - EasyEats</title>
</head>
<body>
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Announcement Management</h1>

        <!-- Display status message -->
        <?php if (isset($message)) { echo "<p class='status-message'>$message</p>"; } ?>

        <!-- Announcement Form -->
        <form action="announcement-management.php" method="POST" class="announcement-form">
            <textarea name="announcement" rows="4" cols="50" placeholder="Enter announcement here..." required></textarea>
            <br>
            <button type="submit">Post Announcement</button>
        </form>

        <!-- Existing Announcements -->
        <h2>Recent Announcements</h2>
        <div class="announcements">
            <?php if ($result->num_rows > 0) { 
                while($row = $result->fetch_assoc()) { ?>
                    <div class="announcement">
                        <p class="announcement-text"><?php echo nl2br(htmlspecialchars($row['text'])); ?></p>
                        <small class="announcement-date"><?php echo $row['date_time']; ?></small>
                    </div>
                <?php } 
            } else { 
                echo "<p>No announcements yet.</p>";
            } ?>
        </div>
    </div>

</body>
</html>
