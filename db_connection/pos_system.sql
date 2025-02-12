-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2025 at 08:36 AM
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
-- Database: `pos_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `total`, `created_at`) VALUES
(1, 3600.00, '2025-02-07 03:19:02'),
(2, 2400.00, '2025-02-07 04:05:58'),
(3, 1200.00, '2025-02-07 05:04:40'),
(4, 0.00, '2025-02-07 11:54:29'),
(5, 0.00, '2025-02-07 11:54:33'),
(6, 0.00, '2025-02-07 12:01:19'),
(7, 500.00, '2025-02-07 12:08:15'),
(8, 600.00, '2025-02-07 12:12:24'),
(9, 1000.00, '2025-02-07 12:13:51'),
(10, 1200.00, '2025-02-08 04:24:43'),
(11, 17000.00, '2025-02-09 08:24:19'),
(12, 120000.00, '2025-02-09 14:21:18'),
(13, 28500.00, '2025-02-12 03:41:26');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 3, 1200.00),
(2, 2, 2, 3, 800.00),
(3, 3, 1, 1, 1200.00),
(4, 7, 3, 1, 500.00),
(5, 8, 3, 1, 500.00),
(6, 8, 6, 1, 100.00),
(7, 9, 2, 1, 800.00),
(8, 9, 5, 1, 200.00),
(9, 10, 1, 1, 1200.00),
(10, 11, 4, 1, 2000.00),
(11, 11, 3, 1, 15000.00),
(12, 12, 1, 4, 30000.00),
(13, 13, 6, 1, 1500.00),
(14, 13, 3, 1, 15000.00),
(15, 13, 2, 1, 12000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`) VALUES
(1, 'Laptop', 'High-performance laptop for work and gaming', 30000.00, 1),
(2, 'Smartphone', 'Latest model with advanced features', 12000.00, 10),
(3, 'Tablet', 'Lightweight and powerful tablet', 15000.00, 16),
(4, 'Wireless Headphones', 'Noise-canceling over-ear headphones', 2000.00, 24),
(5, 'Smartwatch', 'Fitness tracker with heart rate monitor', 5000.00, 29),
(6, 'Mechanical Keyboard', 'RGB backlit mechanical keyboard', 1500.00, 16),
(7, 'Gaming Mouse', 'Ergonomic mouse with programmable buttons', 1000.00, 22),
(8, 'External Hard Drive', '1TB portable external HDD', 900.00, 12),
(9, 'USB-C Charger', 'Fast charging USB-C adapter', 500.00, 35),
(10, 'Bluetooth Speaker', 'Portable speaker with deep bass', 1300.00, 10),
(11, 'Smart TV', '4K Ultra HD Smart TV with HDR', 25000.00, 8),
(12, 'Wireless Earbuds', 'True wireless earbuds with noise cancellation', 3500.00, 20),
(13, 'Gaming Chair', 'Ergonomic gaming chair with lumbar support', 10000.00, 5),
(14, 'Mechanical Pencil', 'High-precision mechanical pencil for writing', 300.00, 50),
(15, 'Portable SSD', '500GB high-speed portable SSD', 5000.00, 15),
(16, 'Bluetooth Headset', 'Wireless Bluetooth headset with mic', 1800.00, 12),
(17, 'Smart Light Bulb', 'WiFi-enabled smart light bulb with voice control', 1200.00, 25),
(18, 'Power Bank', '10,000mAh fast-charging power bank', 2000.00, 30),
(19, 'Electric Kettle', '1.5L stainless steel electric kettle', 2500.00, 10),
(20, 'Graphic Tablet', 'Digital drawing tablet with stylus', 8000.00, 7);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_d` int(10) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_d`, `email`, `password`) VALUES
(1, 'admin@gmail.com', 'admin123'),
(2, 'admin', 'admin123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_d`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_d` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
