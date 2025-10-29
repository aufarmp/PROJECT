-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2025 at 06:47 AM
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
-- Database: `db_komik`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_komik`
--

CREATE TABLE `tb_komik` (
  `komik_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(64) NOT NULL,
  `cover_image` varchar(255) NOT NULL,
  `status` enum('ongoing','completed') NOT NULL,
  `first_chapter_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_komik`
--

INSERT INTO `tb_komik` (`komik_id`, `title`, `author`, `cover_image`, `status`, `first_chapter_at`) VALUES
(1, 'Shangri-La Frontier', 'Ryosuke Fuji', '/comic_project/comic-web/assets/comic_cover/Shangri-La-Frontier.jpg\r\n', 'ongoing', '2020-07-15'),
(2, 'Alice in Borderland', 'Haro Aso', '/comic_project/comic-web/assets/comic_cover/Alice-In-Borderland.jpg\r\n', 'completed', '2011-04-18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_komik`
--
ALTER TABLE `tb_komik`
  ADD PRIMARY KEY (`komik_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_komik`
--
ALTER TABLE `tb_komik`
  MODIFY `komik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
