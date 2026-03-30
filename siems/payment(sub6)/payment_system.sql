-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 06:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `payment_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `student_id`, `action`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, NULL, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:15:44'),
(2, NULL, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:15:55'),
(3, NULL, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:18:55'),
(4, NULL, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:19:03'),
(5, NULL, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:19:16'),
(6, NULL, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:19:19'),
(7, NULL, '2024005', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:20:37'),
(8, NULL, '2024005', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:20:38'),
(9, NULL, '2024005', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:20:45'),
(10, NULL, '2024005', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:20:54'),
(11, NULL, '2024003', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:24:52'),
(12, NULL, '2024004', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:25:14'),
(13, NULL, '2024004', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:25:23'),
(14, NULL, '2024004', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:33:51'),
(15, NULL, '2024004', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:33:53'),
(16, NULL, '2024004', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:34:08'),
(17, NULL, '2024004', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:34:49'),
(18, NULL, '2024004', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:34:58'),
(19, NULL, '2024005', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:35:23'),
(20, NULL, '2024005', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:35:23'),
(21, NULL, '2024005', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:35:58'),
(22, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:48:52'),
(23, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:51:25'),
(24, NULL, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:51:25'),
(25, NULL, '2024003', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:51:25'),
(26, NULL, '2024004', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:51:25'),
(27, NULL, '2024005', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:51:25'),
(28, NULL, '2024006', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:51:25'),
(29, NULL, '2024007', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:51:25'),
(30, NULL, '2024008', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:51:25'),
(31, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:52:09'),
(32, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:52:10'),
(33, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:53:08'),
(34, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:53:08'),
(35, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 18:53:18'),
(36, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:01:11'),
(37, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:01:22'),
(38, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:01:38'),
(39, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:02:53'),
(40, NULL, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:02:53'),
(41, NULL, '2024003', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:02:53'),
(42, NULL, '2024004', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:02:53'),
(43, NULL, '2024005', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:02:53'),
(44, NULL, '2024006', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:02:53'),
(45, NULL, '2024007', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:02:53'),
(46, NULL, '2024008', 'Assessment Recalculated', NULL, 'None (0%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:02:53'),
(47, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:03:20'),
(48, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:03:20'),
(49, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:03:26'),
(50, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:07:24'),
(51, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:09:25'),
(52, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:10:42'),
(53, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:10:49'),
(54, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:13:31'),
(55, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:13:42'),
(56, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:13:47'),
(57, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:18:39'),
(58, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:19:13'),
(59, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:19:33'),
(60, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:20:24'),
(61, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:22:50'),
(62, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:26:25'),
(63, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:26:47'),
(64, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:26:57'),
(65, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:27:10'),
(66, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:27:14'),
(67, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:27:30'),
(68, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:28:05'),
(69, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:28:11'),
(70, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:32:03'),
(71, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:34:49'),
(72, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:35:15'),
(73, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:35:17'),
(74, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:40:39'),
(75, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:40:49'),
(76, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:46:23'),
(77, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:46:32'),
(78, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:47:44'),
(79, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:47:53'),
(80, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:49:53'),
(81, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:53:14'),
(82, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:56:39'),
(83, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:56:50'),
(84, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:56:59'),
(85, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:57:02'),
(86, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:57:03'),
(87, NULL, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% (100%)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Sa', '2026-03-20 19:59:28'),
(88, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:03'),
(89, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:03'),
(90, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:03'),
(91, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:03'),
(92, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:03'),
(93, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:03'),
(94, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:37'),
(95, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:50'),
(96, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:50'),
(97, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:50'),
(98, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:50'),
(99, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:16:50'),
(100, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:17:00'),
(101, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:17:00'),
(102, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:17:00'),
(103, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:17:00'),
(104, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:17:00'),
(105, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:23:40'),
(106, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:23:40'),
(107, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:23:40'),
(108, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:26:25'),
(109, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:26:25'),
(110, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:26:25'),
(111, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:26:25'),
(112, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:26:25'),
(113, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:26:30'),
(114, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:30:27'),
(115, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:32:40'),
(116, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:35:19'),
(117, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:35:28'),
(118, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:38:07'),
(119, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:38:16'),
(120, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:38:17'),
(121, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:42:29'),
(122, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 14:47:17'),
(123, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:10'),
(124, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:12'),
(125, 3, '2024001', 'Payment Received', NULL, 'Amount: ₱7,675.00 (Online Payment via GCash)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:12'),
(126, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:22'),
(127, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:22'),
(128, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:22'),
(129, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:22'),
(130, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:22'),
(131, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:22'),
(132, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:28'),
(133, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:28'),
(134, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:09:28'),
(135, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:16:38'),
(136, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:16:38'),
(137, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:16:38'),
(138, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:21:19'),
(139, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:21:19'),
(140, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:21:19'),
(141, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:07'),
(142, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:07'),
(143, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:07'),
(144, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:07'),
(145, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:07'),
(146, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:07'),
(147, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:11'),
(148, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:11'),
(149, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:11'),
(150, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:32'),
(151, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:32'),
(152, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:32'),
(153, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:32'),
(154, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:22:32'),
(155, 1, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:25:22'),
(156, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:25:41'),
(157, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:25:41'),
(158, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:25:41'),
(159, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:25:41'),
(160, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:25:41'),
(161, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:25:41'),
(162, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:25:48'),
(163, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:25:48'),
(164, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:25:48'),
(165, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:26:14'),
(166, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:26:14'),
(167, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:26:14'),
(168, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:26:14'),
(169, 8, '2024006', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,150.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:26:14'),
(170, 1, '2024006', 'Assigned Scholarship', NULL, 'Sibling (GPA: 1.50, Stackable: 0)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:11'),
(171, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:11'),
(172, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:11'),
(173, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:36'),
(174, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:36'),
(175, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:36'),
(176, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:36'),
(177, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:36'),
(178, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:36'),
(179, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:42'),
(180, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:42'),
(181, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:27:42'),
(182, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:42'),
(183, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:42'),
(184, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:42'),
(185, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:42'),
(186, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:42'),
(187, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:42'),
(188, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:42'),
(189, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:42'),
(190, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:49'),
(191, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:49'),
(192, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:49'),
(193, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:49'),
(194, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:49'),
(195, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:49'),
(196, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:49'),
(197, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:32:49'),
(198, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:33:53'),
(199, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:33:53'),
(200, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:33:53'),
(201, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:33:53'),
(202, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:33:53'),
(203, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:33:53'),
(204, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:33:53'),
(205, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:33:53'),
(206, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:31'),
(207, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:31'),
(208, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:31'),
(209, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:31'),
(210, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:31'),
(211, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:31'),
(212, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:31'),
(213, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:31'),
(214, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:59'),
(215, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:59'),
(216, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:59'),
(217, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:59'),
(218, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:59'),
(219, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:59'),
(220, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:59');
INSERT INTO `audit_log` (`id`, `user_id`, `student_id`, `action`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(221, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:48:59'),
(222, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:18'),
(223, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:18'),
(224, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:18'),
(225, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:18'),
(226, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:19'),
(227, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:19'),
(228, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:19'),
(229, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:19'),
(230, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:26'),
(231, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:26'),
(232, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:26'),
(233, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:26'),
(234, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:26'),
(235, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:26'),
(236, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:26'),
(237, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:49:26'),
(238, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:03'),
(239, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:03'),
(240, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:03'),
(241, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:03'),
(242, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:03'),
(243, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:03'),
(244, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:03'),
(245, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:03'),
(246, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:04'),
(247, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:20'),
(248, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:20'),
(249, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:20'),
(250, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:20'),
(251, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:20'),
(252, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:20'),
(253, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:20'),
(254, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:20'),
(255, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,350.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:48'),
(256, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:48'),
(257, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:48'),
(258, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:48'),
(259, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:48'),
(260, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:48'),
(261, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:48'),
(262, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:48'),
(263, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:48'),
(264, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,350.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:50:48'),
(265, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:56:56'),
(266, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:56:56'),
(267, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:56:56'),
(268, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:56:56'),
(269, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:56:57'),
(270, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:56:57'),
(271, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:57:34'),
(272, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:57:34'),
(273, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:57:34'),
(274, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:59:36'),
(275, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:59:36'),
(276, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:59:36'),
(277, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:59:36'),
(278, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:59:36'),
(279, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 15:59:43'),
(280, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:00:27'),
(281, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:00:27'),
(282, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:00:27'),
(283, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:00:28'),
(284, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:00:28'),
(285, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:00:38'),
(286, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:00:44'),
(287, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:00:44'),
(288, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:00:44'),
(289, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:00:44'),
(290, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:00:44'),
(291, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:01:05'),
(292, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:01:07'),
(293, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:01:07'),
(294, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:01:07'),
(295, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:01:07'),
(296, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:01:07'),
(297, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:01:16'),
(298, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:01:16'),
(299, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:01:16'),
(300, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:02:48'),
(301, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:02:48'),
(302, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:02:48'),
(303, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:02:48'),
(304, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:02:48'),
(305, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:02:53'),
(306, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:11'),
(307, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:13'),
(308, 5, '2024003', 'Payment Received', NULL, 'Amount: ₱2,000.00 (Online Payment via GCash)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:13'),
(309, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:21'),
(310, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:21'),
(311, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:21'),
(312, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:21'),
(313, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:21'),
(314, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:21'),
(315, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:29'),
(316, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:29'),
(317, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:03:29'),
(318, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:06:12'),
(319, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:06:12'),
(320, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:06:12'),
(321, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:06:12'),
(322, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:06:12'),
(323, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:26'),
(324, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:26'),
(325, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:26'),
(326, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:26'),
(327, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:27'),
(328, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:27'),
(329, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:27'),
(330, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:28'),
(331, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,350.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:28'),
(332, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:36'),
(333, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:36'),
(334, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:36'),
(335, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:36'),
(336, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:36'),
(337, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:37'),
(338, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:37'),
(339, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:37'),
(340, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,350.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:07:37'),
(341, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:16:17'),
(342, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:16:17'),
(343, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:16:17'),
(344, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:16:17'),
(345, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:16:17'),
(346, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:16:17'),
(347, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:16:23'),
(348, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:17:00'),
(349, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:17:00'),
(350, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:17:00'),
(351, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:17:01'),
(352, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 16:17:01'),
(353, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:15:47'),
(354, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:15:47'),
(355, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:15:47'),
(356, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:15:47'),
(357, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:15:47'),
(358, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:15:48'),
(359, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:15:48'),
(360, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:15:48'),
(361, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,350.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:15:48'),
(362, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:19:06'),
(363, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:19:06'),
(364, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:19:06'),
(365, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:19:06'),
(366, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:19:06'),
(367, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:19:06'),
(368, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:19:06'),
(369, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:19:06'),
(370, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,350.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 17:19:06'),
(371, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:00:27'),
(372, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:00:27'),
(373, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:00:27'),
(374, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:00:27'),
(375, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:00:27'),
(376, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:00:27'),
(377, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:00:27'),
(378, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:00:27'),
(379, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,350.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:00:27'),
(380, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:04:47'),
(381, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:04:47'),
(382, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:04:47'),
(383, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:04:48'),
(384, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:04:48'),
(385, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:04:48'),
(386, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:04:48'),
(387, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:04:48'),
(388, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,350.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:04:48'),
(389, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:12:51'),
(390, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:12:51'),
(391, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:12:52'),
(392, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:12:52'),
(393, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:12:52'),
(394, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:12:52'),
(395, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:12:52'),
(396, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:12:52'),
(397, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,350.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:12:52'),
(398, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:13:38'),
(399, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:13:39'),
(400, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:13:39'),
(401, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:13:39'),
(402, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:14:10'),
(403, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:14:11'),
(404, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:14:11'),
(405, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:14:13'),
(406, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:14:17'),
(407, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:14:17'),
(408, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:14:17'),
(409, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:14:20'),
(410, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:14:26'),
(411, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:14:26'),
(412, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:14:26'),
(413, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:18:07'),
(414, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:18:07'),
(415, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:18:07'),
(416, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:18:18'),
(417, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:18:18'),
(418, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:18:18'),
(419, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:18:27'),
(420, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:18:27'),
(421, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:18:27'),
(422, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:10'),
(423, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:10'),
(424, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:10'),
(425, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:18'),
(426, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:23'),
(427, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:23'),
(428, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:23'),
(429, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:26');
INSERT INTO `audit_log` (`id`, `user_id`, `student_id`, `action`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(430, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:26'),
(431, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:26'),
(432, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:29'),
(433, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:29'),
(434, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:22:29'),
(435, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:43:44'),
(436, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:43:44'),
(437, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:43:44'),
(438, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:43:53'),
(439, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:43:53'),
(440, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:43:53'),
(441, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:44:06'),
(442, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:44:06'),
(443, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:44:06'),
(444, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:44:11'),
(445, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:44:11'),
(446, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:44:11'),
(447, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:44:15'),
(448, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:44:18'),
(449, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:44:18'),
(450, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:44:18'),
(451, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:44:21'),
(452, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:56:44'),
(453, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:56:44'),
(454, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:56:44'),
(455, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:56:44'),
(456, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 21:57:06'),
(457, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:02:28'),
(458, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:02:46'),
(459, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:02:49'),
(460, 5, '2024003', 'Payment Received', NULL, 'Amount: ₱5,675.00 (Online Payment via GCash)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:02:49'),
(461, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:00'),
(462, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:00'),
(463, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:00'),
(464, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:28'),
(465, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:29'),
(466, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:29'),
(467, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:29'),
(468, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:33'),
(469, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:33'),
(470, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:33'),
(471, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:51'),
(472, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:51'),
(473, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:03:51'),
(474, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:00'),
(475, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:00'),
(476, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:00'),
(477, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:03'),
(478, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:13'),
(479, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:13'),
(480, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:13'),
(481, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:55'),
(482, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:55'),
(483, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱10,675.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:55'),
(484, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,012.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:55'),
(485, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:55'),
(486, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:55'),
(487, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,950.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:55'),
(488, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱22,550.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:55'),
(489, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱21,350.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:04:55'),
(490, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:05:34'),
(491, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:05:34'),
(492, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:05:34'),
(493, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:05:34'),
(494, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:05:34'),
(495, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:09:43'),
(496, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:09:43'),
(497, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:09:43'),
(498, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:09:43'),
(499, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱16,612.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-21 22:09:43'),
(500, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:03:54'),
(501, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:03:54'),
(502, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:03:54'),
(503, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:03:54'),
(504, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:04:39'),
(505, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:04:39'),
(506, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:04:39'),
(507, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:04:42'),
(508, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:04:46'),
(509, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:04:46'),
(510, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:04:46'),
(511, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:04:59'),
(512, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:04:59'),
(513, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:04:59'),
(514, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:05:21'),
(515, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:05:22'),
(516, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱862.50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:05:22'),
(517, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:36'),
(518, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:36'),
(519, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:36'),
(520, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:39'),
(521, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:39'),
(522, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:39'),
(523, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:43'),
(524, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:43'),
(525, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:43'),
(526, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:48'),
(527, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:48'),
(528, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:48'),
(529, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:51'),
(530, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:55'),
(531, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:55'),
(532, 8, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:06:55'),
(533, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:07:39'),
(534, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:07:39'),
(535, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:07:39'),
(536, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:07:39'),
(537, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:07:52'),
(538, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:07:52'),
(539, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:07:52'),
(540, 4, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:07:52'),
(541, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:06'),
(542, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:06'),
(543, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:06'),
(544, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:06'),
(545, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:20'),
(546, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:20'),
(547, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:20'),
(548, 6, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:20'),
(549, 7, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:35'),
(550, 7, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:35'),
(551, 7, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:35'),
(552, 7, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:35'),
(553, 9, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:54'),
(554, 9, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:54'),
(555, 9, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:54'),
(556, 9, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:08:54'),
(557, 10, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:12'),
(558, 10, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:12'),
(559, 10, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:12'),
(560, 10, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:12'),
(561, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:41'),
(562, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:41'),
(563, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:41'),
(564, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:41'),
(565, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:41'),
(566, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:41'),
(567, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:41'),
(568, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:41'),
(569, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:09:41'),
(570, 9, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:03'),
(571, 9, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:03'),
(572, 9, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:03'),
(573, 9, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:03'),
(574, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:25'),
(575, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:25'),
(576, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:25'),
(577, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:25'),
(578, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:25'),
(579, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:25'),
(580, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:25'),
(581, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:25'),
(582, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:25'),
(583, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:29'),
(584, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:29'),
(585, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:29'),
(586, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:29'),
(587, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:29'),
(588, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:29'),
(589, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:29'),
(590, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:29'),
(591, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:29'),
(592, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:36'),
(593, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:36'),
(594, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:36'),
(595, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:36'),
(596, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:36'),
(597, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:36'),
(598, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:36'),
(599, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:36'),
(600, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:36'),
(601, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:10:36'),
(602, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:11:14'),
(603, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:11:14'),
(604, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:11:14'),
(605, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:11:14'),
(606, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:11:14'),
(607, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:11:14'),
(608, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:11:14'),
(609, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:11:14'),
(610, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:11:14'),
(611, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:15:38'),
(612, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:15:38'),
(613, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:15:38'),
(614, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:15:38'),
(615, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:15:39'),
(616, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:15:39'),
(617, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:15:39'),
(618, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:15:39'),
(619, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:15:39'),
(620, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:16:43'),
(621, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:16:43'),
(622, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:16:43'),
(623, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:16:43'),
(624, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:16:44'),
(625, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:16:44'),
(626, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:16:44'),
(627, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:16:44'),
(628, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:16:44'),
(629, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:17'),
(630, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:17'),
(631, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:17'),
(632, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:17'),
(633, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:17'),
(634, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:17'),
(635, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:17'),
(636, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:17'),
(637, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:17'),
(638, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:56'),
(639, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:56'),
(640, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:56'),
(641, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:56');
INSERT INTO `audit_log` (`id`, `user_id`, `student_id`, `action`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(642, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:56'),
(643, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:56'),
(644, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:56'),
(645, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:56'),
(646, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:17:56'),
(647, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:18:27'),
(648, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:18:27'),
(649, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:18:27'),
(650, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:18:27'),
(651, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:18:27'),
(652, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:18:27'),
(653, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:18:27'),
(654, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:18:27'),
(655, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:18:27'),
(656, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:03'),
(657, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:03'),
(658, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:03'),
(659, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:03'),
(660, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:46'),
(661, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:46'),
(662, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:46'),
(663, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:46'),
(664, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:46'),
(665, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:46'),
(666, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:46'),
(667, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:46'),
(668, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:46'),
(669, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:53'),
(670, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:53'),
(671, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:53'),
(672, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:53'),
(673, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:53'),
(674, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:53'),
(675, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:53'),
(676, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:53'),
(677, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:19:53'),
(678, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:00'),
(679, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:00'),
(680, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:00'),
(681, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:00'),
(682, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:00'),
(683, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:00'),
(684, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:00'),
(685, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:00'),
(686, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:00'),
(687, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:00'),
(688, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:14'),
(689, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:14'),
(690, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:14'),
(691, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:14'),
(692, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:14'),
(693, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:14'),
(694, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:14'),
(695, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:14'),
(696, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:14'),
(697, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:33'),
(698, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:48'),
(699, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:48'),
(700, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:48'),
(701, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:48'),
(702, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:48'),
(703, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:48'),
(704, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:48'),
(705, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:48'),
(706, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:20:48'),
(707, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:21:09'),
(708, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:21:09'),
(709, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:21:09'),
(710, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:21:09'),
(711, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:21:15'),
(712, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:21:15'),
(713, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:21:15'),
(714, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:02'),
(715, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:02'),
(716, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:02'),
(717, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:34'),
(718, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:34'),
(719, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:34'),
(720, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:37'),
(721, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:37'),
(722, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:37'),
(723, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:42'),
(724, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:42'),
(725, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:42'),
(726, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:22:44'),
(727, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:23:09'),
(728, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:23:09'),
(729, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 09:23:09'),
(730, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:13:44'),
(731, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:13:44'),
(732, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:13:44'),
(733, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:13:44'),
(734, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:16:58'),
(735, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:16:58'),
(736, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:16:58'),
(737, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:22:52'),
(738, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:22:52'),
(739, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:22:52'),
(740, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:14'),
(741, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:14'),
(742, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:14'),
(743, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:14'),
(744, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:28'),
(745, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:28'),
(746, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:28'),
(747, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:28'),
(748, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:47'),
(749, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:47'),
(750, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:47'),
(751, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:53'),
(752, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:53'),
(753, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:53'),
(754, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:58'),
(755, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:58'),
(756, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:23:58'),
(757, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:24:03'),
(758, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:30:21'),
(759, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:30:43'),
(760, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:30:43'),
(761, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:30:43'),
(762, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:30:54'),
(763, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:33:57'),
(764, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:35:00'),
(765, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:35:01'),
(766, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:35:01'),
(767, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:35:05'),
(768, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:35:58'),
(769, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:35:58'),
(770, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:35:58'),
(771, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 10:35:58'),
(772, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:11:13'),
(773, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:11:13'),
(774, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:11:13'),
(775, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:11:13'),
(776, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:11:26'),
(777, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:11:26'),
(778, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:11:26'),
(779, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:11:32'),
(780, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:11:39'),
(781, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:11:39'),
(782, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:11:39'),
(783, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:17:26'),
(784, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:17:26'),
(785, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:17:26'),
(786, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:17:43'),
(787, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:22:33'),
(788, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:22:33'),
(789, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:22:33'),
(790, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:22:33'),
(791, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:22:33'),
(792, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:22:33'),
(793, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:22:33'),
(794, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:22:33'),
(795, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:22:33'),
(796, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:34:42'),
(797, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:34:42'),
(798, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,750.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:34:42'),
(799, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:34:42'),
(800, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:34:42'),
(801, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:34:42'),
(802, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:34:42'),
(803, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:34:42'),
(804, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:34:42'),
(805, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:35:05'),
(806, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:35:05'),
(807, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:35:05'),
(808, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:35:05'),
(809, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:35:05'),
(810, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:35:05'),
(811, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:35:06'),
(812, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:35:06'),
(813, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱350.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:35:06'),
(814, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:38:54'),
(815, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:38:55'),
(816, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:38:55'),
(817, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:38:55'),
(818, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:39:09'),
(819, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:39:09'),
(820, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:39:09'),
(821, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:40:30'),
(822, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:40:30'),
(823, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:40:30'),
(824, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:40:30'),
(825, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:40:44'),
(826, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:40:44'),
(827, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:40:44'),
(828, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:40:50'),
(829, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:40:50'),
(830, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:40:50'),
(831, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:41:55'),
(832, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:41:55'),
(833, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:41:56'),
(834, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:41:56'),
(835, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:42:02'),
(836, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:42:02'),
(837, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:42:02'),
(838, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:43:01'),
(839, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:43:01'),
(840, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:43:01'),
(841, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:43:01'),
(842, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:43:06'),
(843, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:43:06'),
(844, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:43:06'),
(845, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:43:19'),
(846, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:43:19'),
(847, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:43:19'),
(848, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:43:19'),
(849, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:48:49'),
(850, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:48:49'),
(851, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:48:49');
INSERT INTO `audit_log` (`id`, `user_id`, `student_id`, `action`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(852, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:49:17'),
(853, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:49:17'),
(854, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:49:17'),
(855, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:49:17'),
(856, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:49:17'),
(857, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:50:09'),
(858, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:50:09'),
(859, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:50:09'),
(860, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:50:09'),
(861, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:50:09'),
(862, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:51:12'),
(863, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:51:12'),
(864, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:51:12'),
(865, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:51:12'),
(866, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:51:15'),
(867, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:51:28'),
(868, 5, '2024003', 'Payment Submitted (Pending)', NULL, 'Amount: ₱1,000.00 via GCash (Online Payment via GCash)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:51:28'),
(869, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:55:46'),
(870, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:55:46'),
(871, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:55:46'),
(872, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:58:42'),
(873, 1, '2024003', 'Payment Verified', 'Pending', 'Verified (Receipt: OPN-69C27AC01E3D2)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:58:42'),
(874, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:05'),
(875, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:05'),
(876, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:05'),
(877, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:05'),
(878, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:11'),
(879, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:11'),
(880, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:11'),
(881, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:41'),
(882, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:41'),
(883, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:41'),
(884, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:41'),
(885, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 11:59:41'),
(886, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:00:25'),
(887, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:00:25'),
(888, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:00:25'),
(889, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:00:25'),
(890, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:00:53'),
(891, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:00:53'),
(892, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:00:53'),
(893, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:00:53'),
(894, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:00:53'),
(895, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:08:43'),
(896, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:08:43'),
(897, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:08:43'),
(898, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:08:43'),
(899, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:08:47'),
(900, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:08:47'),
(901, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 12:08:47'),
(902, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:10:38'),
(903, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:10:38'),
(904, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:10:38'),
(905, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:10:44'),
(906, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:10:44'),
(907, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:10:44'),
(908, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:16:23'),
(909, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:16:23'),
(910, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:16:23'),
(911, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:16:23'),
(912, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:16:23'),
(913, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:19'),
(914, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:19'),
(915, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:19'),
(916, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:19'),
(917, 1, '2024003', 'Payment Received', NULL, 'Amount: ₱1,000.00 via Cash (Cash Payment)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:19'),
(918, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:19'),
(919, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:19'),
(920, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:19'),
(921, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:19'),
(922, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:19'),
(923, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:45'),
(924, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:46'),
(925, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:46'),
(926, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:17:46'),
(927, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:18:30'),
(928, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:18:30'),
(929, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:18:30'),
(930, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:18:30'),
(931, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:18:30'),
(932, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:20:39'),
(933, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:20:39'),
(934, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:20:39'),
(935, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:20:39'),
(936, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:20:40'),
(937, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:32'),
(938, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:32'),
(939, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:32'),
(940, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:32'),
(941, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:32'),
(942, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:32'),
(943, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:33'),
(944, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:33'),
(945, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:33'),
(946, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:42'),
(947, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:42'),
(948, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:42'),
(949, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:42'),
(950, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:42'),
(951, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:43'),
(952, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:43'),
(953, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:43'),
(954, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:43'),
(955, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:53'),
(956, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:53'),
(957, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:53'),
(958, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:53'),
(959, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:54'),
(960, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:54'),
(961, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:54'),
(962, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:54'),
(963, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:54'),
(964, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:33:54'),
(965, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:35:05'),
(966, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:35:05'),
(967, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:35:05'),
(968, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:35:05'),
(969, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:35:10'),
(970, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:35:10'),
(971, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:35:10'),
(972, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:35:19'),
(973, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:35:19'),
(974, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:35:19'),
(975, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:36:54'),
(976, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:36:54'),
(977, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:36:54'),
(978, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:36:54'),
(979, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:36:54'),
(980, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:36:54'),
(981, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:36:54'),
(982, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:36:54'),
(983, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:36:54'),
(984, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱175.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:37:01'),
(985, 1, '2024002', 'Assessment Recalculated', NULL, 'QC Foundation 100% - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:37:01'),
(986, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:37:02'),
(987, 1, '2024004', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:37:02'),
(988, 1, '2024005', 'Assessment Recalculated', NULL, 'Valedictorian - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:37:02'),
(989, 1, '2024006', 'Assessment Recalculated', NULL, 'Sibling - Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:37:02'),
(990, 1, '2024007', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:37:02'),
(991, 1, '2024008', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:37:02'),
(992, 1, '2024009', 'Assessment Recalculated', NULL, 'Net Assessment: ₱0.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:37:02'),
(993, 1, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 13:37:36'),
(994, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:30:25'),
(995, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:30:25'),
(996, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:30:25'),
(997, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:30:25'),
(998, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:30:58'),
(999, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:30:58'),
(1000, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:30:58'),
(1001, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:31:22'),
(1002, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:31:22'),
(1003, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:31:22'),
(1004, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:31:30'),
(1005, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:10'),
(1006, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:10'),
(1007, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:23'),
(1008, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:23'),
(1009, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:23'),
(1010, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:30'),
(1011, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:30'),
(1012, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:31'),
(1013, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:33'),
(1014, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:33'),
(1015, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:33'),
(1016, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:55'),
(1017, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:55'),
(1018, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:37:55'),
(1019, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:50:05'),
(1020, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:50:05'),
(1021, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:50:05'),
(1022, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:50:05'),
(1023, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:50:24'),
(1024, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:50:33'),
(1025, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:50:37'),
(1026, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:50:37'),
(1027, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:50:37'),
(1028, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:51:00'),
(1029, 1, '2024003', 'Document Fee Approved', 'Pending', 'Paid (Certificate of Enrollment)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:51:00'),
(1030, 1, '2024003', 'Document Ready', 'Paid', 'Generated (Certificate of Enrollment)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:51:26'),
(1031, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:52:29'),
(1032, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:52:29'),
(1033, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:52:29'),
(1034, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:52:29'),
(1035, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:52:38'),
(1036, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:52:39'),
(1037, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:52:39'),
(1038, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:52:46'),
(1039, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:52:47'),
(1040, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:52:47'),
(1041, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:52:50'),
(1042, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:53:09'),
(1043, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:53:09'),
(1044, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,975.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 15:53:09'),
(1045, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:03:19'),
(1046, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:03:19'),
(1047, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:03:19'),
(1048, 3, '2024001', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:03:19'),
(1049, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:04:07'),
(1050, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:04:07'),
(1051, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:04:07'),
(1052, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:04:07'),
(1053, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:45:40'),
(1054, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:45:40'),
(1055, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:45:40'),
(1056, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 16:45:40'),
(1057, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:14:11'),
(1058, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:14:11');
INSERT INTO `audit_log` (`id`, `user_id`, `student_id`, `action`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1059, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:14:11'),
(1060, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:14:11'),
(1061, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:16:45'),
(1062, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱13,025.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:16:53'),
(1063, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱13,025.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:16:53'),
(1064, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱13,025.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:16:57'),
(1065, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱13,025.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:16:57'),
(1066, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱13,025.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:16:57'),
(1067, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱13,025.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:18:54'),
(1068, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱13,025.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:18:54'),
(1069, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱13,025.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:18:54'),
(1070, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱13,025.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:19:38'),
(1071, 1, '2024003', 'Document Payment Validated', 'Pending', 'Paid (Certificate of Enrollment)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:19:38'),
(1072, 1, '2024003', 'Document Released', 'Paid', 'Generated (Certificate of Enrollment)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:19:42'),
(1073, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:23:08'),
(1074, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:23:08'),
(1075, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:23:08'),
(1076, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:23:08'),
(1077, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:23:13'),
(1078, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:35:47'),
(1079, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:38:01'),
(1080, 5, '2024003', 'Payment Submitted (Pending)', NULL, 'Amount: ₱100.00 via GCash (Document Request Payment [DOCREQ:4] - Certificate of Enrollment)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:38:19'),
(1081, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:38:19'),
(1082, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:38:22'),
(1083, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:38:22'),
(1084, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:38:22'),
(1085, 1, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:39:05'),
(1086, 1, '2024003', 'Payment Verified', 'Pending', 'Verified (Receipt: OPN-69C2CC0B62FDD)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:39:05'),
(1087, 1, '2024003', 'Document Payment Validated', 'Pending', 'Paid (Request #4)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:39:05'),
(1088, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:39:28'),
(1089, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:39:28'),
(1090, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:39:28'),
(1091, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:39:28'),
(1092, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:39:36'),
(1093, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:39:36'),
(1094, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:39:36'),
(1095, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:39:38'),
(1096, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:53:22'),
(1097, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:57:09'),
(1098, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:57:54'),
(1099, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:57:54'),
(1100, 5, '2024003', 'Assessment Recalculated', NULL, 'Academic 50% - Net Assessment: ₱12,925.00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 17:57:54');

-- --------------------------------------------------------

--
-- Table structure for table `document_requests`
--

CREATE TABLE `document_requests` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `status` enum('Pending','Paid','Generated') DEFAULT 'Pending',
  `fee_amount` decimal(10,2) DEFAULT 0.00,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_requests`
--

INSERT INTO `document_requests` (`id`, `student_id`, `document_type`, `status`, `fee_amount`, `requested_at`) VALUES
(4, '2024003', 'Certificate of Enrollment', 'Paid', 100.00, '2026-03-24 17:38:19');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `semester` enum('1st','2nd','Summer') NOT NULL DEFAULT '1st'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `subject_id`, `academic_year`, `semester`) VALUES
(12, '2024001', 1, '2025-2026', '1st'),
(7, '2024001', 2, '2025-2026', '1st'),
(11, '2024001', 3, '2025-2026', '1st'),
(10, '2024001', 4, '2025-2026', '1st'),
(8, '2024001', 5, '2025-2026', '1st'),
(9, '2024001', 6, '2025-2026', '1st'),
(6, '2024003', 1, '2025-2026', '1st'),
(1, '2024003', 2, '2025-2026', '1st'),
(5, '2024003', 3, '2025-2026', '1st'),
(4, '2024003', 4, '2025-2026', '1st'),
(2, '2024003', 5, '2025-2026', '1st'),
(3, '2024003', 6, '2025-2026', '1st');

-- --------------------------------------------------------

--
-- Table structure for table `fee_configs`
--

CREATE TABLE `fee_configs` (
  `id` int(11) NOT NULL,
  `fee_name` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `program` enum('BSIT','BSHM','BSBA','BSED','BSCRIM','BSPsych','BSTM','BSComEng','BSOA','BSAIS','BSEntreP','All') NOT NULL,
  `type` enum('Tuition','Misc','Registration','Document') NOT NULL,
  `unit_count` int(11) DEFAULT 21 COMMENT 'For tuition rate calculation',
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fee_configs`
--

INSERT INTO `fee_configs` (`id`, `fee_name`, `amount`, `program`, `type`, `unit_count`, `active`, `created_at`) VALUES
(7, 'Tuition Fee', 1500.00, 'BSIT', 'Tuition', 17, 1, '2026-03-24 11:34:42'),
(8, 'Cultural Fee', 350.00, 'BSIT', 'Misc', NULL, 1, '2026-03-24 11:35:05');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `type` enum('Receipt','Balance_Reminder') NOT NULL,
  `message` text NOT NULL,
  `sent_status` enum('Pending','Sent','Failed') DEFAULT 'Pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `student_id`, `type`, `message`, `sent_status`, `sent_at`, `created_at`) VALUES
(1, '2024003', '', 'Your payment of ₱1,000.00 via GCash has been Verified.', 'Pending', NULL, '2026-03-24 11:58:42'),
(6, '2024003', '', 'Your payment of ₱100.00 via GCash has been verified.', 'Pending', NULL, '2026-03-24 17:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `receipt_no` varchar(50) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL,
  `payment_method` enum('Cash','GCash','Maya','Online Banking','Credit/Debit Card') DEFAULT 'Cash',
  `proof_image` varchar(255) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `verification_status` enum('Verified','Pending','Rejected') DEFAULT 'Verified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `student_id`, `amount_paid`, `receipt_no`, `payment_date`, `remarks`, `payment_method`, `proof_image`, `reference_number`, `qr_code`, `verification_status`) VALUES
(34, '2024003', 1000.00, 'OPN-69C27AC01E3D2', '2026-03-24 11:51:28', 'Online Payment via GCash', 'GCash', 'assets/uploads/receipts/receipt_69c27bb7c0b74.png', 'OPN-69C27AC01E3D2', NULL, 'Verified'),
(35, '2024003', 1000.00, 'OPN-69C28EDF49A18', '2026-03-24 13:17:19', 'Cash Payment', 'Cash', NULL, NULL, NULL, 'Verified'),
(36, '2024003', 100.00, 'OPN-69C2CC0B62FDD', '2026-03-24 17:38:19', 'Document Request Payment [DOCREQ:4] - Certificate of Enrollment', 'GCash', NULL, NULL, NULL, 'Verified');

-- --------------------------------------------------------

--
-- Table structure for table `scholarships`
--

CREATE TABLE `scholarships` (
  `student_id` varchar(20) NOT NULL,
  `discount_type` varchar(50) NOT NULL DEFAULT 'None',
  `gpa` decimal(3,2) DEFAULT NULL,
  `stackable` tinyint(1) DEFAULT 1,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `assigned_by` int(11) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `validated_by` int(11) DEFAULT NULL,
  `validated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarships`
--

INSERT INTO `scholarships` (`student_id`, `discount_type`, `gpa`, `stackable`, `status`, `assigned_by`, `assigned_at`, `validated_by`, `validated_at`) VALUES
('2024001', 'Academic 50%', 1.45, 1, 'Approved', NULL, '2026-03-20 18:15:39', NULL, NULL),
('2024002', 'QC Foundation 100%', 1.10, 0, 'Approved', NULL, '2026-03-20 18:15:39', NULL, NULL),
('2024003', 'Academic 50%', NULL, 1, 'Approved', NULL, '2026-03-20 22:12:12', NULL, NULL),
('2024004', 'Sibling', NULL, 1, 'Approved', NULL, '2026-03-20 22:12:12', NULL, NULL),
('2024005', 'Valedictorian', NULL, 1, 'Approved', NULL, '2026-03-20 22:12:12', NULL, NULL),
('2024006', 'Sibling', 1.50, 0, 'Approved', NULL, '2026-03-21 15:27:10', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_assessments`
--

CREATE TABLE `student_assessments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `total_tuition` decimal(10,2) DEFAULT 0.00,
  `total_misc` decimal(10,2) DEFAULT 0.00,
  `total_registration` decimal(10,2) DEFAULT 0.00,
  `gross_total` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) DEFAULT 0.00,
  `balance` decimal(10,2) DEFAULT 0.00,
  `discount_applied` decimal(5,4) DEFAULT 0.0000,
  `semester` varchar(20) DEFAULT '2024-1',
  `assessed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_assessments`
--

INSERT INTO `student_assessments` (`id`, `student_id`, `total_tuition`, `total_misc`, `total_registration`, `gross_total`, `discount_amount`, `grand_total`, `balance`, `discount_applied`, `semester`, `assessed_at`) VALUES
(1339, '2024002', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0000, '2024-1', '2026-03-24 13:37:01'),
(1341, '2024004', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0000, '2024-1', '2026-03-24 13:37:02'),
(1342, '2024005', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0000, '2024-1', '2026-03-24 13:37:02'),
(1343, '2024006', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0000, '2024-1', '2026-03-24 13:37:02'),
(1344, '2024007', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0000, '2024-1', '2026-03-24 13:37:02'),
(1345, '2024008', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0000, '2024-1', '2026-03-24 13:37:02'),
(1346, '2024009', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0000, '2024-1', '2026-03-24 13:37:02'),
(1401, '2024001', 25500.00, 350.00, 0.00, 25850.00, 12925.00, 12925.00, 12925.00, 0.0000, '2024-1', '2026-03-24 16:03:19'),
(1448, '2024003', 25500.00, 350.00, 0.00, 25850.00, 12925.00, 12925.00, 10925.00, 0.0000, '2024-1', '2026-03-24 17:57:54');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `units` int(11) NOT NULL DEFAULT 3,
  `program` enum('BSIT','BSHM','BSBA','BSED','BSCRIM','BSPsych','BSTM','BSComEng','BSOA','BSAIS','BSEntreP','All') NOT NULL,
  `semester` enum('1st','2nd','Summer') NOT NULL DEFAULT '1st',
  `year_level` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `code`, `description`, `units`, `program`, `semester`, `year_level`) VALUES
(1, 'UTS', 'Understanding the Self', 3, 'BSIT', '1st', 1),
(2, 'COMPROG', 'Introduction to Computer Programming', 3, 'BSIT', '1st', 1),
(3, 'RPH', 'Readings in Philippine History', 3, 'BSIT', '1st', 1),
(4, 'PURCOM', 'Purposive Communication', 3, 'BSIT', '1st', 1),
(5, 'NET101', 'Networking 1', 3, 'BSIT', '1st', 1),
(6, 'PE3', 'Path Fit', 2, 'BSIT', '1st', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `program` enum('BSIT','BSHM','BSBA','BSED','BSCRIM','BSPsych','BSTM','BSComEng','BSOA','BSAIS','BSEntreP','All') NOT NULL,
  `enrollment_status` enum('Auto','Enrolled','Pending') DEFAULT 'Auto',
  `year_level` int(11) NOT NULL,
  `discount_type` enum('None','Academic (50%)','QC Foundation (100%)') DEFAULT 'None',
  `role` enum('admin','student','enrollment') NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `student_id`, `full_name`, `program`, `enrollment_status`, `year_level`, `discount_type`, `role`, `password_hash`, `created_at`) VALUES
(1, 'ADMIN001', 'Cashier Admin', 'All', 'Auto', 0, 'None', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-03-20 15:56:34'),
(2, 'ADMIN002', 'Super Admin', 'All', 'Auto', 0, 'None', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-03-20 15:56:34'),
(3, '2024001', 'John Doe', 'BSIT', 'Enrolled', 1, 'Academic (50%)', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-03-20 15:56:34'),
(4, '2024002', 'Jane Smith', 'BSHM', 'Auto', 2, 'QC Foundation (100%)', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-03-20 15:56:34'),
(5, '2024003', 'Bob Johnson', 'BSIT', 'Enrolled', 3, 'Academic (50%)', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-03-20 15:56:34'),
(6, '2024004', 'Alice Brown', 'BSHM', 'Auto', 1, 'None', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-03-20 15:56:34'),
(7, '2024005', 'Charlie Wilson', 'BSBA', 'Auto', 4, 'None', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-03-20 15:56:34'),
(8, '2024006', 'David Crim', 'BSCRIM', 'Auto', 2, 'None', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-03-20 16:39:50'),
(9, '2024007', 'Emma Psych', 'BSPsych', 'Pending', 1, 'None', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-03-20 16:39:50'),
(10, '2024008', 'Frank TM', 'BSTM', 'Auto', 3, 'None', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-03-20 16:39:50'),
(11, '2024009', 'Jane Doe', 'BSIT', 'Auto', 3, 'None', 'student', '$2y$10$qr5HVdOnZ3m4G3D1HlRx2uTa.DN.sCj.EfPEaYrwjx1IW.O8.Vt0y', '2026-03-21 15:50:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `document_requests`
--
ALTER TABLE `document_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_subject` (`student_id`,`subject_id`,`academic_year`,`semester`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `fee_configs`
--
ALTER TABLE `fee_configs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fee_configs_program_type` (`program`,`type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_no` (`receipt_no`),
  ADD KEY `idx_payments_student_date` (`student_id`,`payment_date`);

--
-- Indexes for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `assigned_by` (`assigned_by`),
  ADD KEY `validated_by` (`validated_by`);

--
-- Indexes for table `student_assessments`
--
ALTER TABLE `student_assessments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_semester` (`student_id`,`semester`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `idx_users_program` (`program`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1101;

--
-- AUTO_INCREMENT for table `document_requests`
--
ALTER TABLE `document_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `fee_configs`
--
ALTER TABLE `fee_configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `student_assessments`
--
ALTER TABLE `student_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1449;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `document_requests`
--
ALTER TABLE `document_requests`
  ADD CONSTRAINT `document_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD CONSTRAINT `scholarships_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scholarships_ibfk_2` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `scholarships_ibfk_3` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `student_assessments`
--
ALTER TABLE `student_assessments`
  ADD CONSTRAINT `student_assessments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
