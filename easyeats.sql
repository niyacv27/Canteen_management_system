-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2024 at 09:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `easyeats`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `text`, `date_time`) VALUES
(1, 'you admins name is gokul', '2024-11-12 06:45:05'),
(2, 'hello all\\r\\n', '2024-11-12 06:47:11'),
(3, 'all', '2024-11-12 06:47:21'),
(4, 'hello', '2024-11-12 08:16:42');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(255) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `order_type` varchar(255) NOT NULL DEFAULT 'Pending',
  `reference_unique_key` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`order_id`, `user_id`, `item_id`, `delivery_date`, `quantity`, `price`, `status`, `created_at`, `updated_at`, `order_type`, `reference_unique_key`) VALUES
(80, 1, 5, '2024-11-21', 3, 123.00, 'Rejected', '2024-11-12 20:24:43', '2024-11-12 20:25:15', 'preorder', '241112-21-6733b98b44c1f'),
(81, 1, 6, NULL, 1, 170.00, 'Rejected', '2024-11-12 20:27:15', '2024-11-12 20:27:28', 'preorder', '241112-21-6733ba235e56b');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `text`, `time`) VALUES
(3, 'dragon', '2024-11-12 07:01:33'),
(4, 'rice', '2024-11-12 07:46:28');

-- --------------------------------------------------------

--
-- Table structure for table `checkout`
--

CREATE TABLE `checkout` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `order_datetime` datetime DEFAULT NULL,
  `customization` text DEFAULT NULL,
  `order_status` varchar(50) DEFAULT NULL,
  `order_type` varchar(50) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `cart_status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `item_image` varchar(255) DEFAULT NULL,
  `reference_unique_key` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkout`
--

INSERT INTO `checkout` (`id`, `username`, `item_name`, `order_datetime`, `customization`, `order_status`, `order_type`, `delivery_date`, `quantity`, `price`, `cart_status`, `created_at`, `updated_at`, `item_image`, `reference_unique_key`, `user_id`, `item_id`, `order_id`) VALUES
(9, 'gokul', 'biriyani', '2024-11-13 01:14:01', 'sdsd', 'paid', 'dayorder', '2024-11-13', 1, 170.00, 'paid', '2024-11-13 01:14:01', '2024-11-13 01:14:01', 'background.jpg', '241112-20-6733b0016fe5b', 1, 6, 93),
(10, 'gokul', 'biriyani', '2024-11-13 01:15:15', 'add', 'paid', 'dayorder', '2024-11-13', 1, 170.00, 'paid', '2024-11-13 01:15:15', '2024-11-13 01:15:15', 'background.jpg', '241112-20-6733b04b98cb4', 1, 6, 94),
(11, 'gokul', 'biriyani', '2024-11-13 01:16:20', 'sdfsd', 'paid', 'dayorder', '2024-11-13', 1, 170.00, 'paid', '2024-11-13 01:16:20', '2024-11-13 01:16:20', 'background.jpg', '241112-20-6733b08cb9b11', 1, 6, 95),
(12, 'gokul', 'biriyani', '2024-11-13 01:24:20', 'asas', 'paid', 'preorder', '2024-11-14', 3, 170.00, 'paid', '2024-11-13 01:24:20', '2024-11-13 01:24:56', 'background.jpg', '241112-20-6733b26c73853', 1, 6, 96),
(14, 'gokul', 'gokul', '2024-11-13 01:30:49', 'sdd', 'paid', 'dayorder', '2024-11-13', 1, 123.00, 'paid', '2024-11-13 01:30:49', '2024-11-13 01:30:49', 'flower.png', '241112-21-6733b3f166f22', 1, 5, 98),
(15, 'gokul', 'biriyani', '2024-11-13 01:30:36', 'wasa', 'paid', 'dayorder', '2024-11-13', 1, 170.00, 'paid', '2024-11-13 01:30:36', '2024-11-13 01:30:36', 'background.jpg', '241112-21-6733b3e431af6', 1, 6, 97),
(17, 'gokul', 'biriyani', '2024-11-13 01:58:10', 'dsfs', 'paid', 'dayorder', '2024-11-13', 1, 170.00, 'paid', '2024-11-13 01:58:10', '2024-11-13 01:58:10', 'background.jpg', '241112-21-6733ba5ab8ef1', 1, 6, 104),
(18, 'gokul', 'biriyani', '2024-11-13 01:58:54', 'sdsd', 'paid', 'dayorder', '2024-11-13', 1, 170.00, 'paid', '2024-11-13 01:58:54', '2024-11-13 01:58:54', 'background.jpg', '241112-21-6733ba860d55d', 1, 6, 105);

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `feedback_text` text NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `user_name`, `feedback_text`, `date_time`, `status`) VALUES
(12, 'gokul', 'hello', '2024-11-12 19:33:07', 'unread'),
(13, 'gokul', 'hello', '2024-11-12 20:23:45', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `price`, `image`, `created_at`, `category_id`) VALUES
(5, 'gokul', 123.00, 'flower.png', '2024-11-12 07:45:03', 3),
(6, 'biriyani', 170.00, 'background.jpg', '2024-11-12 08:04:05', 4);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `order_datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `customization` text DEFAULT NULL,
  `status` varchar(255) DEFAULT 'Pending',
  `order_type` varchar(255) DEFAULT NULL,
  `reference_unique_key` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `username`, `item_name`, `order_datetime`, `customization`, `status`, `order_type`, `reference_unique_key`) VALUES
(101, 'gokul', 'gokul', '2024-11-12 20:24:43', 'dfgdf', 'Rejected', 'preorder', '241112-21-6733b98b44c1f'),
(102, 'gokul', 'biriyani', '2024-11-12 20:27:15', 'sdsd', 'Rejected', 'preorder', '241112-21-6733ba235e56b');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`) VALUES
(1, 'gokul', '$2y$10$AdPoGuTazu38G9m6i2g8QuPfi/B1OUrylXwcM0r5jgk8ex2Bs3/Lm', 'gokul@gmail.com', 'user'),
(2, 'admin', '$2y$10$AdPoGuTazu38G9m6i2g8QuPfi/B1OUrylXwcM0r5jgk8ex2Bs3/Lm', 'admin@gmail.com', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `checkout`
--
ALTER TABLE `checkout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
