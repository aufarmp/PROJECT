-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 08:10 AM
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
-- Table structure for table `tb_bookmarks`
--

CREATE TABLE `tb_bookmarks` (
  `user_id` int(11) NOT NULL,
  `komik_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 1, 1, 'Why do you game?', '2020-07-15'),
(2, 2, 1, 'Arisu in Borderland (1)', '2011-04-18'),
(5, 8, 1, 'Test', '0000-00-00');

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
(10, 'Drama'),
(1, 'Fantasy'),
(14, 'Isekai'),
(5, 'Psychological'),
(9, 'Romance'),
(3, 'Sci-fi'),
(11, 'Slice of Life'),
(4, 'Thriller');

-- --------------------------------------------------------

--
-- Table structure for table `tb_histori_bacaan`
--

CREATE TABLE `tb_histori_bacaan` (
  `history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `last_read_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_histori_bacaan`
--

INSERT INTO `tb_histori_bacaan` (`history_id`, `user_id`, `chapter_id`, `last_read_at`) VALUES
(12, 2, 1, '2025-11-14 04:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `tb_komik`
--

CREATE TABLE `tb_komik` (
  `komik_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(64) NOT NULL,
  `cover_image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('ongoing','completed') NOT NULL,
  `first_chapter_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_komik`
--

INSERT INTO `tb_komik` (`komik_id`, `title`, `author`, `cover_image`, `description`, `status`, `first_chapter_at`) VALUES
(1, 'Shangri-La Frontier', 'Ryosuke Fuji', '/comic_project/comic-web/assets/comic_cover/Shangri-La-Frontier.jpg\r\n', 'Shangri-La Frontier follows the journey of high school student Rakurou Hizutome, known in the gaming world as Sunraku, whose peculiar hobby is playing poorly made games. However, when he delves into the popular virtual reality game Shangri-La Frontier, he finds himself unexpectedly challenged by its mainstream appeal. Armed with nothing but his wit and eccentric playstyle, Rakurou navigates the game’s challenges, drawing on his vast gaming experience to carve his path to success. With a unique blend of humor, strategy, and adventure, Shangri-La Frontier explores the exhilarating world of virtual gaming and the unexpected triumphs that come with it.', 'ongoing', '2020-07-15'),
(2, 'Alice in Borderland', 'Haro Aso', '/comic_project/comic-web/assets/comic_cover/Alice-In-Borderland.jpg\r\n', 'Feeling unsettled about the future, high school student Ryouhei Arisu often escapes the reality of life. After hanging out at a bar, Arisu and his best friends, Daikichi Karube and Chouta Segawa, wait for the first train to arrive in the morning. Suddenly, a colorful array of fireworks set off in the sky, and an enormous blinding firework renders them unconscious.\\\\r\\\\n\\\\r\\\\nThe trio finds themselves back at the bar covered in dust, discovering that the city has become a barren wasteland. But instead of being worried, Arisu feels alive for the very first time in his life and relishes the freedom of this lifeless city. However, his bliss is cut short when the group rashly enters a festival venue. Seeing its delicacies and lively ambiance, they think the place is a dream; unbeknownst to them, it will be the setting for their first deadly game.', 'completed', '2011-04-18'),
(8, 'test', 'test', '/comic_project/comic-web/assets/comic_cover/1760928172_Kaorurin.jpg', 'test', 'ongoing', '0000-00-00');

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
(2, 7),
(8, 9),
(8, 10);

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
(1, 1, 1, '/comic_project/comic-web/assets/comic_pages/shangri-la-frontier/slf-chapter-1/shangri-la-pg1.jpg'),
(2, 1, 2, '/comic_project/comic-web/assets/comic_pages/shangri-la-frontier/slf-chapter-1/shangri-la-pg2.jpg'),
(3, 1, 3, '/comic_project/comic-web/assets/comic_pages/shangri-la-frontier/slf-chapter-1/shangri-la-pg3.jpg'),
(4, 1, 4, '/comic_project/comic-web/assets/comic_pages/shangri-la-frontier/slf-chapter-1/shangri-la-pg4.jpg'),
(5, 2, 1, '/comic_project/comic-web/assets/comic_pages/alice-borderland/aib-chapter-1/alice-borderland-01.jpg'),
(6, 2, 2, '/comic_project/comic-web/assets/comic_pages/alice-borderland/aib-chapter-1/alice-borderland-02.jpg'),
(7, 2, 3, '/comic_project/comic-web/assets/comic_pages/alice-borderland/aib-chapter-1/alice-borderland-03.jpg'),
(8, 2, 4, '/comic_project/comic-web/assets/comic_pages/alice-borderland/aib-chapter-1/alice-borderland-04.jpg'),
(13, 5, 1, '/comic_project/comic-web/assets/comic_pages/8/1/kaorurin-ch1-02.jpg'),
(14, 5, 2, '/comic_project/comic-web/assets/comic_pages/8/1/kaorurin-ch1-01.jpg');

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
  `profile_picture_url` varchar(255) DEFAULT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`user_id`, `username`, `email`, `password`, `role`, `profile_picture_url`, `created_at`) VALUES
(1, 'Admin', 'admin@gmail.com', 'Admin1234', 'admin', NULL, '2025-10-15'),
(2, 'User', 'User@gmail.com', 'User1234', 'user', '/comic_project/comic-web/assets/profile_pictures/user_2_1763093591.png', '2025-10-15'),
(7, 'User233', 'usernew@gmail.com', 'User1234', 'user', NULL, '0000-00-00'),
(8, 'Usernew', 'Usernow@gmail.com', 'User1234', 'user', NULL, '0000-00-00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_bookmarks`
--
ALTER TABLE `tb_bookmarks`
  ADD PRIMARY KEY (`user_id`,`komik_id`),
  ADD KEY `komik_id` (`komik_id`);

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
-- Indexes for table `tb_histori_bacaan`
--
ALTER TABLE `tb_histori_bacaan`
  ADD PRIMARY KEY (`history_id`),
  ADD UNIQUE KEY `unique_user_chapter` (`user_id`,`chapter_id`),
  ADD KEY `chapter_id` (`chapter_id`);

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
  MODIFY `chapter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_genre`
--
ALTER TABLE `tb_genre`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_histori_bacaan`
--
ALTER TABLE `tb_histori_bacaan`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tb_komik`
--
ALTER TABLE `tb_komik`
  MODIFY `komik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_pages`
--
ALTER TABLE `tb_pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `user_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_bookmarks`
--
ALTER TABLE `tb_bookmarks`
  ADD CONSTRAINT `tb_bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_bookmarks_ibfk_2` FOREIGN KEY (`komik_id`) REFERENCES `tb_komik` (`komik_id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_chapter`
--
ALTER TABLE `tb_chapter`
  ADD CONSTRAINT `tb_chapter_ibfk_1` FOREIGN KEY (`komik_id`) REFERENCES `tb_komik` (`komik_id`);

--
-- Constraints for table `tb_histori_bacaan`
--
ALTER TABLE `tb_histori_bacaan`
  ADD CONSTRAINT `tb_histori_bacaan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_histori_bacaan_ibfk_2` FOREIGN KEY (`chapter_id`) REFERENCES `tb_chapter` (`chapter_id`) ON DELETE CASCADE;

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
