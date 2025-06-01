<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyEats - Landing Page</title>
    <link rel="stylesheet" href="css/index.css"> <!-- Link to your CSS -->
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <a href="#">EasyEats</a>
        </div>
        
        <!-- Hamburger Menu -->
        <div class="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <ul class="nav-links">
            <!-- Login Dropdown -->
            <li class="dropdown">
                <a href="#">Login</a>
                <div class="dropdown-content">
                    
                    <a href="admin/login.php">Admin </a>
                    <a href="user/login.php">User </a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Section 1: Background Image Section -->
    <section class="background-section">
        <div class="background-text">
            <h1>Welcome to EasyEats</h1>
            <p>Your one-stop destination for delicious food.</p>
        </div>
    </section>

    <!-- Section 2: EasyEats Description Section -->
    <section class="eazy-eats-section">
        <h2>What is EasyEats</h2>
        <p>EasyEats is a web-based canteen management system designed to streamline the dining experience for students and staff at our college. By providing real-time menu updates and a user-friendly ordering platform, EasyEats makes it simple to browse available food options and pre-book meals, reducing wait times and ensuring food availability. The platform is built to accommodate the unique scheduling needs of various departments, addressing the common challenges of food accessibility and availability on campus.</p>
    </section>

    <!-- Section 3: Food Images Section -->
    <section class="food-gallery">
        <h2>Our Menu</h2>
        <div class="food-images">
            <div class="food-item">
                <img src="images/porotta.png" alt="Porotta">
            </div>
            <div class="food-item">
                <img src="images/biriyani.png" alt="Biriyani">
            </div>
            <div class="food-item">
                <img src="images/meals.png" alt="Meals">
            </div>
            <div class="food-item">
                <img src="images/sprite.png" alt="Sprite">
            </div>
            <div class="food-item">
                <img src="images/samosa.png" alt="Samosa">
            </div>
            <div class="food-item">
                <img src="images/puffs.png" alt="Puffs">
            </div>
        </div>
    </section>

    <!-- Section 4: About Us Section -->
    <section class="about-section">
        <h2>About Us</h2>
        <p>To be the most trusted platform for food ordering on campus. EasyEats bridges the gap between students and the canteen by offering an efficient, real-time ordering experience. It solves the food availability challenge by enabling pre-booking, minimizing food waste, and enhancing satisfaction for all users.</p>
    </section>

    <script>
        // Toggle the menu on mobile
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    </script>

</body>  
</html>
