-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 08:00 AM
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
-- Database: `assignment`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `CommentID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL,
  `Comment` varchar(255) NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`CommentID`, `UserID`, `TaskID`, `Comment`, `CreatedAt`) VALUES
(18, 7, 1, 'Hello bro', '2025-09-06 16:13:02'),
(19, 7, 1, 'Bye bye', '2025-09-06 16:13:21'),
(20, 7, 1, 'test textContext', '2025-09-06 16:15:29'),
(21, 7, 1, 'testing', '2025-09-06 16:19:49'),
(22, 7, 1, 'test1', '2025-09-06 16:21:56'),
(23, 7, 1, 'a', '2025-09-06 16:29:14'),
(24, 7, 1, 'b', '2025-09-06 16:30:06'),
(25, 7, 1, 'c', '2025-09-06 16:32:32'),
(26, 7, 1, 'a', '2025-09-06 16:33:08'),
(27, 7, 1, 'd', '2025-09-06 16:34:12'),
(28, 7, 1, 'qq', '2025-09-06 16:38:34'),
(29, 7, 1, 'aa', '2025-09-06 16:39:05'),
(30, 7, 1, 'cc', '2025-09-06 17:01:02'),
(31, 7, 1, 'bb', '2025-09-06 19:52:49'),
(32, 7, 1, 'qq', '2025-09-06 19:52:55'),
(33, 7, 1, 'qwerty', '2025-09-06 20:09:45'),
(34, 7, 1, 'test2', '2025-09-06 20:14:18'),
(35, 7, 1, 'test3', '2025-09-06 20:14:42'),
(36, 7, 1, 'test4', '2025-09-08 20:04:46'),
(37, 7, 1, 'test5', '2025-09-08 20:04:56'),
(38, 7, 1, 'test6', '2025-09-08 20:08:05'),
(39, 7, 1, 'test7', '2025-09-08 20:26:55'),
(40, 7, 1, 'test2', '2025-09-10 13:57:47'),
(41, 7, 1, 'hhh', '2025-09-10 13:57:54');

-- --------------------------------------------------------

--
-- Table structure for table `fileshared`
--

CREATE TABLE `fileshared` (
  `FileID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL,
  `FileName` varchar(255) NOT NULL,
  `Extension` varchar(255) NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fileshared`
--

INSERT INTO `fileshared` (`FileID`, `UserID`, `TaskID`, `FileName`, `Extension`, `CreatedAt`) VALUES
(4, 7, 1, 'test', 'txt', '2025-09-08 19:51:10'),
(6, 7, 1, 'superstore', 'csv', '2025-09-09 12:41:40'),
(7, 7, 1, 'test', 'html', '2025-09-09 20:42:26'),
(8, 7, 1, 'd', 'docx', '2025-09-10 13:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `goal`
--

CREATE TABLE `goal` (
  `GoalID` int(11) NOT NULL,
  `WorkSpaceID` int(11) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Type` enum('Short','Long','','') NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL,
  `Deadline` datetime NOT NULL,
  `Progress` enum('Pending','InProgress','Completed','') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `NotificationID` int(11) NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `RelatedID` int(11) NOT NULL,
  `RelatedTable` varchar(255) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receiver`
--

CREATE TABLE `receiver` (
  `NotificationID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `TaskID` int(11) NOT NULL,
  `WorkSpaceID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL,
  `Deadline` datetime NOT NULL,
  `Priority` enum('High','Medium','Low','') NOT NULL,
  `Status` enum('Pending','InProgress','Completed','') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`TaskID`, `WorkSpaceID`, `Title`, `Description`, `StartTime`, `EndTime`, `Deadline`, `Priority`, `Status`) VALUES
(1, 1, 'PHP', 'Making backend of a website', '2025-09-06 09:48:25', '0000-00-00 00:00:00', '2025-09-09 09:48:25', 'High', 'Pending'),
(2, 1, 'PHP', 'Making backend of a website', '2025-09-06 09:48:25', '2025-09-06 09:48:25', '2025-09-06 09:48:25', 'High', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `taskaccess`
--

CREATE TABLE `taskaccess` (
  `UserID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taskaccess`
--

INSERT INTO `taskaccess` (`UserID`, `TaskID`) VALUES
(7, 1),
(7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `HasedPassword` varchar(255) NOT NULL,
  `PictureName` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Username`, `Email`, `HasedPassword`, `PictureName`) VALUES
(2, 'b', 'b@g.co', '$2y$10$c4W2oP9pk7YVHRgRRizd0O5GZJz4Rw0zgaJfbO25v49UXgon1UWSW', NULL),
(3, 'c', 'c@g.co', '$2y$10$DhvDKrKe/P6Yrp3WEHbH4.EAtcw431tJ1OD1PG6oyFFsTMgOzDyEK', NULL),
(5, 'e', 'e!!!@g.co', '$2y$10$VVEUM2rdx/qEo50c8rJwAezajqOiW889k453QDEcSDe8XMOco0HjW', NULL),
(6, '&#60;script&#62;alert(&#34;You have virus&#34;)d&#60;/script&#62;', 'd@g.co', '$2y$10$ZEiq5Tul2NtBB0xzbZBF..rMMMXNQVFgIOm08Vpk67nVrZOwDarPO', NULL),
(7, 'w', 'w@g.co', '$2y$10$mwSvl9XRsOaS3l09FC8R.O5Vs0t4AIzVCRkn63KuLLX5I68RANK4S', '7.png'),
(8, 'f', 'f@g.co', '$2y$10$.Lrmri6kNTJD3A1S8UPqY.ie5H8ktLGC0tte3YJOeX1IaX8ixaozm', NULL),
(10, 'a', 'a@g.co', '$2y$10$PojV.2f1SFLNVuhfBrytquWuYhuJ96FVuCD3Sa.gMICsnCaEbpMVi', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `workspace`
--

CREATE TABLE `workspace` (
  `WorkSpaceID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workspace`
--

INSERT INTO `workspace` (`WorkSpaceID`, `Name`, `UserID`) VALUES
(1, 'Web Development', 7);

-- --------------------------------------------------------

--
-- Table structure for table `workspacemember`
--

CREATE TABLE `workspacemember` (
  `WorkSpaceID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `UserRole` enum('Employee','Manager','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`CommentID`);

--
-- Indexes for table `fileshared`
--
ALTER TABLE `fileshared`
  ADD PRIMARY KEY (`FileID`);

--
-- Indexes for table `goal`
--
ALTER TABLE `goal`
  ADD PRIMARY KEY (`GoalID`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`NotificationID`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`TaskID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `workspace`
--
ALTER TABLE `workspace`
  ADD PRIMARY KEY (`WorkSpaceID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `CommentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `fileshared`
--
ALTER TABLE `fileshared`
  MODIFY `FileID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `goal`
--
ALTER TABLE `goal`
  MODIFY `GoalID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `workspace`
--
ALTER TABLE `workspace`
  MODIFY `WorkSpaceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
