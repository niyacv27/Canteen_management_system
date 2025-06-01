<nav class="navbar">
<div class="logo">
        <a href="#">EasyEats</a>
    </div>
    <ul class="nav-links">
        <li><a href="home.php">Home</a></li>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="order_status.php">Order Status</a></li>
        
        <li><a href="announcement-user.php">Announcements</a></li>
        <li><a href="profile.php">Profile</a></li>
        <?php if (isset($_SESSION['user_id'])) { ?>
            <li><a href="../logout.php">Logout</a></li>
        <?php } else { ?>
            <li><a href="../login.php">Login</a></li>
        <?php } ?>
    </ul>
</nav>
