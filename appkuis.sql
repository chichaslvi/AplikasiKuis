-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2025 at 10:18 AM
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
-- Database: `appkuis`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nik` int(11) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('Admin','Reviewer','Agent') NOT NULL,
  `field_1` varchar(255) DEFAULT NULL,
  `field_2` varchar(255) DEFAULT NULL,
  `field_3` varchar(255) DEFAULT NULL,
  `field_4` varchar(255) DEFAULT NULL,
  `must_change_password` tinyint(1) DEFAULT 1,
  `last_password_change` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nik`, `password`, `role`, `field_1`, `field_2`, `field_3`, `field_4`, `must_change_password`, `last_password_change`, `created_at`, `updated_at`) VALUES
(1, 1, '$2y$10$CBNL6MtF1zqlyIdL.L1fFeHcX57L2uI0OdwHp51L3X1WvOHqjSwKS', 'Admin', NULL, NULL, NULL, NULL, 0, '2025-09-01 18:39:32', NULL, NULL),
(2, 2, '$2y$10$56Gf/fxSqVQMjb4Z2qPcHe.vje7bI2J3Z1fHU1v0wjJHjxLuMpCAG', 'Admin', NULL, NULL, NULL, NULL, 0, '2025-09-01 18:50:59', NULL, NULL),
(3, 3, '$2y$10$Bq5RdeWu.hTJEnRpRO.SPeikuUsNDdhwFhqJwix3WjtuwnFP.Nb4m', 'Reviewer', NULL, NULL, NULL, NULL, 0, '2025-09-01 20:58:51', NULL, NULL),
(4, 4, '$2y$10$fVQsL4uCtcTOI8ww0mHVieRC5tX87Zk7J8EEifAEDMqcL.vfmjUjG', 'Reviewer', NULL, NULL, NULL, NULL, 0, '2025-09-01 21:04:08', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_nik` (`nik`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
