-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2025 at 06:42 AM
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
-- Table structure for table `tb_chapter`
--

CREATE TABLE `tb_chapter` (
  `chapter_id` int(11) NOT NULL,
  `komik_id` int(11) NOT NULL,
  `chapter_number` decimal(65,0) NOT NULL,
  `title` varchar(255) NOT NULL,
  `release_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_chapter`
--

INSERT INTO `tb_chapter` (`chapter_id`, `komik_id`, `chapter_number`, `title`, `release_date`) VALUES
(1, 1, 1, 'Shangri-La Frontier', '2020-07-15'),
(2, 2, 1, 'Alice in Borderland', '2011-04-18');

-- --------------------------------------------------------

--
-- Table structure for table `tb_genre`
--

CREATE TABLE `tb_genre` (
  `genre_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_genre`
--

INSERT INTO `tb_genre` (`genre_id`, `name`) VALUES
(2, 'Action'),
(8, 'Adventure'),
(6, 'Comedy'),
(7, 'Crime'),
(1, 'Fantasy'),
(5, 'Psychological'),
(3, 'Sci-fi'),
(4, 'Thriller');

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

-- --------------------------------------------------------

--
-- Table structure for table `tb_komik_genre`
--

CREATE TABLE `tb_komik_genre` (
  `komik_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_komik_genre`
--

INSERT INTO `tb_komik_genre` (`komik_id`, `genre_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 6),
(1, 8),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 7);

-- --------------------------------------------------------

--
-- Table structure for table `tb_pages`
--

CREATE TABLE `tb_pages` (
  `page_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `page_number` int(64) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pages`
--

INSERT INTO `tb_pages` (`page_id`, `chapter_id`, `page_number`, `image_url`) VALUES
(1, 1, 1, '/comic_project/comic-web/assets/comic_pages/shangri-la-frontier/chapter-1/shangri-la-pg1.jpg'),
(2, 1, 2, '/comic_project/comic-web/assets/comic_pages/shangri-la-frontier/chapter-1/shangri-la-pg2.jpg\r\n'),
(3, 1, 3, '/comic_project/comic-web/assets/comic_pages/shangri-la-frontier/chapter-1/shangri-la-pg3.jpg'),
(4, 1, 4, '/comic_project/comic-web/assets/comic_pages/shangri-la-frontier/chapter-1/shangri-la-pg4.jpg'),
(5, 2, 1, '/comic_project/comic-web/assets/comic_pages/alice-borderland/aib-chapter-1/alice-borderland-01.jpg'),
(6, 2, 2, '/comic_project/comic-web/assets/comic_pages/alice-borderland/aib-chapter-1/alice-borderland-02.jpg'),
(7, 2, 3, '/comic_project/comic-web/assets/comic_pages/alice-borderland/aib-chapter-1/alice-borderland-03.jpg'),
(8, 2, 4, '/comic_project/comic-web/assets/comic_pages/alice-borderland/aib-chapter-1/alice-borderland-04.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `user_id` int(5) NOT NULL,
  `username` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`user_id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@gmail.com', 'Admin1234', 'admin', '2025-10-15'),
(2, 'User', 'User@gmail.com', 'User1234', 'user', '2025-10-15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_chapter`
--
ALTER TABLE `tb_chapter`
  ADD PRIMARY KEY (`chapter_id`),
  ADD KEY `komik_id` (`komik_id`);

--
-- Indexes for table `tb_genre`
--
ALTER TABLE `tb_genre`
  ADD PRIMARY KEY (`genre_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `tb_komik`
--
ALTER TABLE `tb_komik`
  ADD PRIMARY KEY (`komik_id`);

--
-- Indexes for table `tb_komik_genre`
--
ALTER TABLE `tb_komik_genre`
  ADD KEY `komik_id` (`komik_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `tb_pages`
--
ALTER TABLE `tb_pages`
  ADD PRIMARY KEY (`page_id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`(64));

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_chapter`
--
ALTER TABLE `tb_chapter`
  MODIFY `chapter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_genre`
--
ALTER TABLE `tb_genre`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_komik`
--
ALTER TABLE `tb_komik`
  MODIFY `komik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_pages`
--
ALTER TABLE `tb_pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `user_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_chapter`
--
ALTER TABLE `tb_chapter`
  ADD CONSTRAINT `tb_chapter_ibfk_1` FOREIGN KEY (`komik_id`) REFERENCES `tb_komik` (`komik_id`);

--
-- Constraints for table `tb_komik_genre`
--
ALTER TABLE `tb_komik_genre`
  ADD CONSTRAINT `tb_komik_genre_ibfk_1` FOREIGN KEY (`komik_id`) REFERENCES `tb_komik` (`komik_id`),
  ADD CONSTRAINT `tb_komik_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `tb_genre` (`genre_id`);

--
-- Constraints for table `tb_pages`
--
ALTER TABLE `tb_pages`
  ADD CONSTRAINT `tb_pages_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `tb_chapter` (`chapter_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
