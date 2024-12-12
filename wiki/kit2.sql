-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 12, 2024 at 02:43 AM
-- Server version: 8.0.39
-- PHP Version: 8.2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kit2`
--

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int NOT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `friend_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `user_id`, `friend_id`, `created_at`) VALUES
(4, 'hakan', 'admin', '2024-11-21 08:41:07'),
(6, 'hakan', 'kdurant', '2024-11-21 17:24:09'),
(7, 'hakan', 'admin2', '2024-12-05 04:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `user` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `pass` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'default.png',
  `first_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `about_me` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`user`, `pass`, `profile_picture`, `first_name`, `last_name`, `title`, `about_me`) VALUES
('admin', '$2y$10$BKctie/lRYAgt/o2vksiJeMZwLupuLTybpspda8nb2X1s8uGDclfS', 'default.png', 'Site', 'Admin', 'Administrator', NULL),
('admin2', '$2y$10$bO0mFW2W2I03jfLTL7DbIeBt7JaxbalgXE8pEuScxD/4t6V3oE9CC', 'default.png', 'Site', 'Admin2', 'Administrator', NULL),
('hakan', '$2y$10$sXlN2aAgDb1ljePcFAjKXeyDsZPoUwSUG1yx4oNCz.ILRUPfdjqim', '673c70fecf03b-4775486.png', 'Hakan', 'Gursel', 'Developer', 'Enjoy working on creative development projects and exploring new technologies in my free time.'),
('kdurant', '$2y$10$CUZMGiZqGYb7cWJeuxjbW.eXSKWFnneFP0iPaWR7lRf6GaJD1X7OW', 'kdurant.jpg', 'Kevin', 'Durant', 'Guest User', 'As a professional basketball player, I strive for excellence on the court, pushing my limits and inspiring others through dedication, teamwork, and passion for the game. Off the court, I focus on using my platform to make a positive impact and encourage others to pursue their dreams.');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `sender` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `receiver` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender`, `receiver`, `message`, `created_at`) VALUES
(1, 'hakan', 'kdurant', 'Test', '2024-11-20 00:21:05'),
(2, 'kdurant', 'hakan', 'Hi', '2024-11-20 00:21:46'),
(3, 'hakan', 'kdurant', 'Test', '2024-11-20 00:21:58'),
(4, 'hakan', 'kdurant', 'Hi', '2024-11-20 00:22:16'),
(5, 'hakan', 'admin', 'Hi', '2024-11-20 01:00:01'),
(6, 'hakan', 'kdurant', 'Hi Bro', '2024-12-05 04:07:21'),
(7, 'hakan', 'kdurant', 'Hi Bro', '2024-12-05 04:07:24');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int NOT NULL,
  `user` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `text` text COLLATE utf8mb4_general_ci,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `likes` int DEFAULT '0',
  `dislikes` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user`, `text`, `image`, `created_at`, `likes`, `dislikes`) VALUES
(3, 'hakan', 'testing with a picture', NULL, '2024-11-19 05:44:05', 0, 0),
(5, 'admin', 'Admin test', '673c66d20251c-maxresdefault.jpg', '2024-11-19 10:22:10', 0, 0),
(6, 'hakan', 'Experience the serene beauty of Echo Park Lake, a hidden gem amidst the bustling skyline of Los Angeles. A perfect blend of nature and city life!', '673da4927a2c0-VC_PlacesToVisit_LosAngelesCounty_RF_1170794243.jpg', '2024-11-20 08:57:54', 0, 0),
(7, 'hakan', 'Hello', NULL, '2024-12-05 04:06:58', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `user` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `text` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`user`, `text`) VALUES
('hakan', 'Lorem Ipsum');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_friend` (`user_id`,`friend_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`user`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender` (`sender`),
  ADD KEY `receiver` (`receiver`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD KEY `user` (`user`(6));

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender`) REFERENCES `members` (`user`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver`) REFERENCES `members` (`user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
