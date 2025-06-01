<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
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

// Fetch all announcements
$sql = "SELECT * FROM announcements ORDER BY date_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyEats - Announcements</title>
    <!-- Add your stylesheets here -->
    <link rel="stylesheet" href="css/navbar.css"> <!-- Link to navbar styles -->
    <link rel="stylesheet" href="css/announcement-user.css"> <!-- Link to custom user announcement styles -->
</head>
<body>
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Announcements</h1>

        <!-- Display Announcements -->
        <div class="announcements">
            <?php if ($result->num_rows > 0) { 
                while($row = $result->fetch_assoc()) { ?>
                    <div class="announcement">
                        <p class="announcement-text"><?php echo nl2br(htmlspecialchars($row['text'])); ?></p>
                        <small class="announcement-date"><?php echo $row['date_time']; ?></small>
                    </div>
                <?php } 
            } else { 
                echo "<p>No announcements available at the moment.</p>";
            } ?>
        </div>
    </div>

</body>
</html>
