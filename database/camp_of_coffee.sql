-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2025 at 08:40 PM
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
-- Database: `camp_of_coffee`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 3, 'login', 'User logged in', '2025-09-16 18:16:43'),
(2, 3, 'login', 'User logged in', '2025-09-16 18:17:09'),
(3, 3, 'sale', 'Created sale #11 total ₱105.00', '2025-09-16 18:17:21'),
(4, 3, 'login', 'User logged in', '2025-09-16 18:31:32'),
(5, 3, 'login', 'User logged in', '2025-09-16 18:32:21'),
(6, 4, 'login', 'User logged in', '2025-09-16 18:33:52'),
(7, 3, 'login', 'User logged in', '2025-09-16 18:34:11');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `stock`, `created_at`, `updated_at`) VALUES
(1, 'Americano', 'Coffee', 25.00, 96, '2025-09-16 16:41:36', '2025-09-16 18:13:51'),
(2, 'Cappuccino', 'Coffee', 35.00, 96, '2025-09-16 16:41:36', '2025-09-16 17:08:16'),
(3, 'Latte', 'Coffee', 40.00, 99, '2025-09-16 16:41:36', '2025-09-16 17:32:14'),
(4, 'Espresso', 'Coffee', 30.00, 97, '2025-09-16 16:41:36', '2025-09-16 17:33:12'),
(5, 'Mocha', 'Coffee', 45.00, 100, '2025-09-16 16:41:36', '2025-09-16 16:41:36'),
(6, 'Matcha Latte', 'Non-Coffee', 45.00, 48, '2025-09-16 16:41:36', '2025-09-16 17:43:28'),
(7, 'Strawberry Smoothie', 'Non-Coffee', 50.00, 48, '2025-09-16 16:41:36', '2025-09-16 17:43:28'),
(8, 'Chocolate Cake', 'Dessert', 60.00, 28, '2025-09-16 16:41:36', '2025-09-16 18:13:51'),
(9, 'Cheesecake', 'Dessert', 65.00, 25, '2025-09-16 16:41:36', '2025-09-16 18:13:51'),
(10, 'Croissant', 'Pastry', 35.00, 36, '2025-09-16 16:41:36', '2025-09-16 18:17:21'),
(11, 'Camper\'s Cup', 'Coffee', 120.00, 18, '2025-09-16 17:44:15', '2025-09-16 18:13:51'),
(12, 'Hiker\'s Cup', 'Coffee', 120.00, 20, '2025-09-16 17:44:44', '2025-09-16 17:45:27'),
(13, 'Explorer\'s Cup', 'Coffee', 120.00, 21, '2025-09-16 17:45:21', '2025-09-16 17:45:21'),
(14, 'Caramel Macchiato', 'Coffee', 120.00, 27, '2025-09-16 17:46:14', '2025-09-16 17:46:14'),
(15, 'Caramilk', 'Coffee', 120.00, 76, '2025-09-16 18:18:16', '2025-09-16 18:18:16'),
(16, 'Vanilla Latte', 'Coffee', 110.00, 22, '2025-09-16 18:18:49', '2025-09-16 18:18:49');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `sale_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `cashier_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `sale_date`, `total_amount`, `cashier_id`, `created_at`) VALUES
(1, '2025-09-17 00:43:57', 135.00, NULL, '2025-09-16 16:43:57'),
(2, '2025-09-17 00:46:13', 125.00, NULL, '2025-09-16 16:46:13'),
(3, '2025-09-17 00:46:26', 65.00, NULL, '2025-09-16 16:46:26'),
(4, '2025-09-17 01:04:10', 85.00, NULL, '2025-09-16 17:04:10'),
(5, '2025-09-17 01:08:16', 130.00, NULL, '2025-09-16 17:08:16'),
(6, '2025-09-17 01:32:14', 165.00, NULL, '2025-09-16 17:32:14'),
(7, '2025-09-17 01:33:12', 110.00, NULL, '2025-09-16 17:33:12'),
(8, '2025-09-17 01:43:28', 145.00, NULL, '2025-09-16 17:43:28'),
(9, '2025-09-17 01:48:27', 25.00, NULL, '2025-09-16 17:48:27'),
(10, '2025-09-17 02:13:51', 390.00, 3, '2025-09-16 18:13:51'),
(11, '2025-09-17 02:17:21', 105.00, 3, '2025-09-16 18:17:21');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `subtotal`, `created_at`) VALUES
(1, 1, 9, 1, 65.00, 65.00, '2025-09-16 16:43:57'),
(2, 1, 2, 2, 35.00, 70.00, '2025-09-16 16:43:57'),
(3, 2, 9, 1, 65.00, 65.00, '2025-09-16 16:46:13'),
(4, 2, 2, 1, 35.00, 35.00, '2025-09-16 16:46:13'),
(5, 2, 1, 1, 25.00, 25.00, '2025-09-16 16:46:13'),
(6, 3, 4, 1, 30.00, 30.00, '2025-09-16 16:46:26'),
(7, 3, 10, 1, 35.00, 35.00, '2025-09-16 16:46:26'),
(8, 4, 1, 1, 25.00, 25.00, '2025-09-16 17:04:10'),
(9, 4, 8, 1, 60.00, 60.00, '2025-09-16 17:04:10'),
(10, 5, 9, 1, 65.00, 65.00, '2025-09-16 17:08:16'),
(11, 5, 2, 1, 35.00, 35.00, '2025-09-16 17:08:16'),
(12, 5, 4, 1, 30.00, 30.00, '2025-09-16 17:08:16'),
(13, 6, 9, 1, 65.00, 65.00, '2025-09-16 17:32:14'),
(14, 6, 8, 1, 60.00, 60.00, '2025-09-16 17:32:14'),
(15, 6, 3, 1, 40.00, 40.00, '2025-09-16 17:32:14'),
(16, 7, 10, 1, 35.00, 35.00, '2025-09-16 17:33:12'),
(17, 7, 4, 1, 30.00, 30.00, '2025-09-16 17:33:12'),
(18, 7, 6, 1, 45.00, 45.00, '2025-09-16 17:33:12'),
(19, 8, 7, 2, 50.00, 100.00, '2025-09-16 17:43:28'),
(20, 8, 6, 1, 45.00, 45.00, '2025-09-16 17:43:28'),
(21, 9, 1, 1, 25.00, 25.00, '2025-09-16 17:48:27'),
(22, 10, 11, 2, 120.00, 240.00, '2025-09-16 18:13:51'),
(23, 10, 9, 1, 65.00, 65.00, '2025-09-16 18:13:51'),
(24, 10, 1, 1, 25.00, 25.00, '2025-09-16 18:13:51'),
(25, 10, 8, 1, 60.00, 60.00, '2025-09-16 18:13:51'),
(26, 11, 10, 3, 35.00, 105.00, '2025-09-16 18:17:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','cashier') DEFAULT 'cashier',
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `full_name`, `email`, `phone`, `profile_image`, `created_at`, `updated_at`) VALUES
(3, 'escall', '$2y$10$lcX/w2rlrOO5o2Bwrrttwec2vVo9QozcOMV7b8lMWgTYAYC.1ZQmm', 'admin', '', '', '', NULL, '2025-09-16 17:58:13', '2025-09-16 18:03:44'),
(4, 'alex', '$2y$10$DeSoaIbiOyhkAVxhmwhVk.jYsZUc049SPBmypOE7vn4Wv/Tl8thDi', 'cashier', '', '', '', NULL, '2025-09-16 18:04:07', '2025-09-16 18:05:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`created_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cashier_id` (`cashier_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
