-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 09:35 AM
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
(1, 1, 1, 'wwwwwwwwwwwwwwwwwwwwwwwwwwwww', '2025-09-10 14:39:56');

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
(1, 1, 1, 'q', '', '2025-09-10 14:40:36');

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

--
-- Dumping data for table `goal`
--

INSERT INTO `goal` (`GoalID`, `WorkSpaceID`, `Description`, `Type`, `StartTime`, `EndTime`, `Deadline`, `Progress`) VALUES
(1, 1, 'rrrrr', 'Short', '2025-09-10 08:40:52', '2025-09-10 08:40:52', '2025-09-10 08:40:52', 'Pending');

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

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`NotificationID`, `CreatedAt`, `RelatedID`, `RelatedTable`, `Title`, `Description`) VALUES
(1, '2025-09-10 14:42:02', 1, 'goal', 'eeeeeeeee', 'wwwwwwwwwww');

-- --------------------------------------------------------

--
-- Table structure for table `receiver`
--

CREATE TABLE `receiver` (
  `NotificationID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receiver`
--

INSERT INTO `receiver` (`NotificationID`, `UserID`) VALUES
(1, 2);

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
  `Deadline` datetime,
  `Priority` enum('High','Medium','Low','') NOT NULL,
  `Status` enum('Pending','InProgress','Completed','') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`TaskID`, `WorkSpaceID`, `Title`, `Description`, `StartTime`, `EndTime`, `Deadline`, `Priority`, `Status`) VALUES
(1, 1, 'hhhhhhh', 'hgfertghgfrdfghgr', '2025-09-10 08:10:00', '2025-09-10 08:10:00', '2025-09-10 08:10:00', '', 'Pending');

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
(1, 1),
(2, 1);

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
(1, 'eee', 'jj@g.co', 'jhtrdvbjytrdcvbhtfvff5654ewsdvbrdccdcdd', NULL),
(2, 'b', 'b@g.co', '$2y$10$c4W2oP9pk7YVHRgRRizd0O5GZJz4Rw0zgaJfbO25v49UXgon1UWSW', NULL),
(3, 'c', 'c@g.co', '$2y$10$DhvDKrKe/P6Yrp3WEHbH4.EAtcw431tJ1OD1PG6oyFFsTMgOzDyEK', NULL),
(5, 'e', 'e!!!@g.co', '$2y$10$VVEUM2rdx/qEo50c8rJwAezajqOiW889k453QDEcSDe8XMOco0HjW', NULL),
(6, '&#60;script&#62;alert(&#34;You have virus&#34;)d&#60;/script&#62;', 'd@g.co', '$2y$10$ZEiq5Tul2NtBB0xzbZBF..rMMMXNQVFgIOm08Vpk67nVrZOwDarPO', NULL),
(7, 'w', 'w@g.co', '$2y$10$mwSvl9XRsOaS3l09FC8R.O5Vs0t4AIzVCRkn63KuLLX5I68RANK4S', NULL),
(8, 'f', 'f@g.co', '$2y$10$.Lrmri6kNTJD3A1S8UPqY.ie5H8ktLGC0tte3YJOeX1IaX8ixaozm', NULL),
(10, 'a', 'a@g.co', '$2y$10$PojV.2f1SFLNVuhfBrytquWuYhuJ96FVuCD3Sa.gMICsnCaEbpMVi', NULL),
(11, '3p', 'p@g.co', '$2y$10$27I3yzd8m6n8NKtCdnXir.RvNP/GQRDIElhm/WoNqpXNvCWoN1pve', NULL);

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
(1, 'wwwwwwwwww', 1);

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
-- Dumping data for table `workspacemember`
--

INSERT INTO `workspacemember` (`WorkSpaceID`, `UserID`, `UserRole`) VALUES
(1, 1, 'Manager'),
(1, 2, 'Employee');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`CommentID`),
  ADD KEY `have` (`UserID`),
  ADD KEY `for` (`TaskID`);

--
-- Indexes for table `fileshared`
--
ALTER TABLE `fileshared`
  ADD PRIMARY KEY (`FileID`),
  ADD KEY `TaskID` (`TaskID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `goal`
--
ALTER TABLE `goal`
  ADD PRIMARY KEY (`GoalID`),
  ADD KEY `WorkSpaceID` (`WorkSpaceID`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`NotificationID`);

--
-- Indexes for table `receiver`
--
ALTER TABLE `receiver`
  ADD KEY `NotificationID` (`NotificationID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`TaskID`),
  ADD KEY `WorkSpaceID` (`WorkSpaceID`);

--
-- Indexes for table `taskaccess`
--
ALTER TABLE `taskaccess`
  ADD KEY `TaskID` (`TaskID`),
  ADD KEY `UserID` (`UserID`);

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
  ADD PRIMARY KEY (`WorkSpaceID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `workspacemember`
--
ALTER TABLE `workspacemember`
  ADD KEY `UserID` (`UserID`),
  ADD KEY `WorkSpaceID` (`WorkSpaceID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `CommentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fileshared`
--
ALTER TABLE `fileshared`
  MODIFY `FileID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `goal`
--
ALTER TABLE `goal`
  MODIFY `GoalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `workspace`
--
ALTER TABLE `workspace`
  MODIFY `WorkSpaceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `for` FOREIGN KEY (`TaskID`) REFERENCES `task` (`TaskID`),
  ADD CONSTRAINT `have` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `fileshared`
--
ALTER TABLE `fileshared`
  ADD CONSTRAINT `fileshared_ibfk_1` FOREIGN KEY (`TaskID`) REFERENCES `task` (`TaskID`),
  ADD CONSTRAINT `fileshared_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `goal`
--
ALTER TABLE `goal`
  ADD CONSTRAINT `goal_ibfk_1` FOREIGN KEY (`WorkSpaceID`) REFERENCES `workspace` (`WorkSpaceID`);

--
-- Constraints for table `receiver`
--
ALTER TABLE `receiver`
  ADD CONSTRAINT `receiver_ibfk_1` FOREIGN KEY (`NotificationID`) REFERENCES `notification` (`NotificationID`),
  ADD CONSTRAINT `receiver_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `task_ibfk_1` FOREIGN KEY (`WorkSpaceID`) REFERENCES `workspace` (`WorkSpaceID`);

--
-- Constraints for table `taskaccess`
--
ALTER TABLE `taskaccess`
  ADD CONSTRAINT `taskaccess_ibfk_1` FOREIGN KEY (`TaskID`) REFERENCES `task` (`TaskID`),
  ADD CONSTRAINT `taskaccess_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `workspace`
--
ALTER TABLE `workspace`
  ADD CONSTRAINT `workspace_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `workspacemember`
--
ALTER TABLE `workspacemember`
  ADD CONSTRAINT `workspacemember_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `workspacemember_ibfk_2` FOREIGN KEY (`WorkSpaceID`) REFERENCES `workspace` (`WorkSpaceID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
