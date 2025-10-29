-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 28, 2025 at 05:03 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bmn_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `bagian`
--

CREATE TABLE `bagian` (
  `id` int NOT NULL,
  `iddeputi` int NOT NULL,
  `idbiro` int NOT NULL,
  `uraianbagian` varchar(200) NOT NULL,
  `status` set('on','off') NOT NULL DEFAULT 'on',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bagian`
--

INSERT INTO `bagian` (`id`, `iddeputi`, `idbiro`, `uraianbagian`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 101, 'Bagian Umum', 'on', '2025-10-27 21:16:48', '2025-10-27 21:16:48'),
(2, 1, 102, 'Bagian Keuangan', 'on', '2025-10-27 21:16:48', '2025-10-27 21:16:48'),
(3, 1, 103, 'Bagian Teknis', 'on', '2025-10-27 21:16:48', '2025-10-27 21:16:48'),
(4, 1, 104, 'Bagian Administrasi', 'on', '2025-10-27 21:16:48', '2025-10-27 21:16:48'),
(5, 1, 105, 'Bagian Perencanaan', 'on', '2025-10-27 21:16:48', '2025-10-27 21:16:48'),
(6, 1, 1, 'Bagian Pelaksana 20', 'on', '2025-10-28 04:40:52', '2025-10-28 04:40:52'),
(7, 1, 1, 'Bagian Pelaksana 21', 'on', '2025-10-28 04:40:52', '2025-10-28 04:40:52'),
(8, 1, 1, 'Bagian Pelaksana 22', 'on', '2025-10-28 04:40:52', '2025-10-28 04:40:52'),
(20, 1, 1, 'Bagian Pelaksana 20', 'on', '2025-10-28 04:43:16', '2025-10-28 04:43:16'),
(21, 1, 1, 'Bagian Pelaksana 21', 'on', '2025-10-28 04:43:16', '2025-10-28 04:43:16'),
(22, 1, 1, 'Bagian Pelaksana 22', 'on', '2025-10-28 04:43:16', '2025-10-28 04:43:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bagian`
--
ALTER TABLE `bagian`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bagian`
--
ALTER TABLE `bagian`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
