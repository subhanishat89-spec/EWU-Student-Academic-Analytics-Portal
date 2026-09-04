-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2026 at 10:02 PM
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
-- Database: `ewu_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_records`
--

CREATE TABLE `academic_records` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `dept` varchar(50) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `year` year(4) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(120) NOT NULL,
  `marks` int(11) NOT NULL,
  `credits` float NOT NULL,
  `grade_point` float NOT NULL,
  `letter_grade` varchar(3) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_records`
--

INSERT INTO `academic_records` (`id`, `name`, `student_id`, `dept`, `semester`, `year`, `course_code`, `course_name`, `marks`, `credits`, `grade_point`, `letter_grade`, `created_at`) VALUES
(12, 'Nishat Subha', '2023-1-60-248', 'CSE', 'Spring', '2026', 'CSE 487', 'Cyber Security , Ethics and law', 70, 3, 3.5, 'A-', '2026-04-21 19:52:04'),
(13, 'Nishat Subha', '2023-1-60-248', 'CSE', 'Spring', '2026', 'CSE 479', 'Web Programming', 65, 4, 3.25, 'B+', '2026-04-21 19:52:04'),
(14, 'Nishat Subha', '2023-1-60-248', 'CSE', 'Spring', '2026', 'CSE 400A', 'Capstone', 75, 1, 3.75, 'A', '2026-04-21 19:52:04'),
(15, 'Nishat Subha', '2023-1-60-248', 'CSE', 'Spring', '2026', 'CSE 366', 'AI', 70, 4, 3.5, 'A-', '2026-04-21 19:52:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_records`
--
ALTER TABLE `academic_records`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_records`
--
ALTER TABLE `academic_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
