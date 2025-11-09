-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2025 at 04:06 AM
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
(1, 1, 1, 'wwwwwwwwwwwwwwwwwwwwwwwwwwwww', '2025-09-10 14:39:56'),
(2, 7, 1, 'test1', '2025-09-10 15:38:57'),
(3, 7, 1, 'test2', '2025-09-10 15:39:28'),
(4, 7, 1, 'test3', '2025-09-10 15:39:33'),
(5, 7, 1, 'test4', '2025-09-10 15:39:36'),
(6, 7, 1, 'test5', '2025-09-10 15:40:00'),
(7, 7, 1, 'test6', '2025-09-10 15:40:05'),
(8, 7, 1, 'test7', '2025-09-11 13:41:18'),
(9, 7, 1, 'wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww', '2025-09-11 16:30:27'),
(10, 12, 1, '11', '2025-09-29 20:30:20'),
(11, 7, 1, '111', '2025-09-29 20:31:11'),
(12, 7, 1, '123', '2025-09-30 10:51:00'),
(13, 7, 1, 'hi', '2025-10-03 10:48:54'),
(14, 7, 1, '11', '2025-10-03 16:29:12'),
(15, 7, 1, '121', '2025-10-08 16:19:24'),
(16, 7, 1, '12', '2025-10-15 19:33:15'),
(19, 16, 33, 'qweqweeqwe', '2025-10-26 10:30:01'),
(20, 18, 34, '111', '2025-10-26 10:36:53');

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
(2, 7, 1, 't', 'txt', '2025-09-10 15:40:49'),
(3, 7, 1, 'd', 'docx', '2025-09-10 15:40:56'),
(4, 7, 1, 'testErrorNotInServer', 'html', '2025-09-10 15:41:00'),
(5, 7, 1, 'logo', 'png', '2025-09-11 12:41:09'),
(6, 7, 1, 'New 文本文档', 'txt', '2025-09-11 12:57:25'),
(7, 7, 1, 'd', 'docx', '2025-09-11 13:47:51'),
(8, 7, 1, 'chatbot back up', 'docx', '2025-09-30 10:50:10'),
(9, 7, 1, '', 'png', '2025-10-08 16:19:05'),
(10, 7, 1, 'FEP Assignment Criteria UCDF2405', 'pptx', '2025-10-15 19:43:52'),
(12, 16, 33, 'headshotmaster_image_1760010435583', 'png', '2025-10-26 10:30:10'),
(13, 16, 33, 'headshotmaster_image_1760010435583', 'png', '2025-10-26 10:30:27'),
(14, 18, 34, 'headshotmaster_image_1760010435583', 'png', '2025-10-26 10:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `goal`
--

CREATE TABLE `goal` (
  `GoalID` int(11) NOT NULL,
  `WorkSpaceID` int(11) NOT NULL,
  `GoalTitle` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Type` enum('Short','Long','','') NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime DEFAULT NULL,
  `Deadline` datetime NOT NULL,
  `Progress` enum('Pending','In Progress','Completed') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goal`
--

INSERT INTO `goal` (`GoalID`, `WorkSpaceID`, `GoalTitle`, `Description`, `Type`, `StartTime`, `EndTime`, `Deadline`, `Progress`) VALUES
(10, 13, 'aa', 'No description provided', 'Long', '2025-10-24 20:35:00', '2025-10-25 10:21:36', '2025-10-31 20:35:00', 'Completed'),
(11, 13, 'test', 'aa', 'Long', '2025-10-25 10:17:00', '2025-10-27 16:17:24', '2025-10-29 10:17:00', 'Completed'),
(12, 13, 'test1', 'No description provided', 'Long', '2025-10-02 10:21:00', '2025-10-27 16:16:37', '2025-10-25 10:21:00', 'Completed'),
(13, 13, 'qqq', 'qqq', 'Short', '2025-10-10 10:21:00', NULL, '2025-10-30 10:21:00', 'Pending'),
(17, 1, 'test by manager', 'No description provided', 'Long', '2025-10-24 19:50:00', NULL, '2025-10-25 19:50:00', 'In Progress'),
(18, 13, 'goal', 'No description provided', 'Long', '2025-10-27 16:27:00', NULL, '2025-10-28 16:27:00', 'In Progress');

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
(1, '2025-09-10 14:42:02', 1, 'goal', 'eeeeeeeee', 'wwwwwwwwwww'),
(3, '2025-10-16 08:55:42', 2, 'workspace', 'Added to workspace', 'You have been added to a new workspace: Second Workspace'),
(4, '2025-10-16 08:55:42', 3, 'task', 'Added to task', 'You have been added to a new task in workspace: Second Workspace'),
(5, '2025-10-16 09:07:43', 11, 'task', 'Added to task', 'You have been added to a new task in RWDD ASSIGNMENT: test2'),
(6, '2025-10-16 09:12:25', 3, 'task', 'Removed from task', 'You have been removed from the task: Second task'),
(7, '2025-10-16 09:12:40', 3, 'task', 'Removed from task', 'You have been removed from the task: Second task'),
(8, '2025-10-16 09:13:26', 3, 'task', 'Added to task', 'You have been added to a new task in Second Workspace: Second task'),
(9, '2025-10-16 09:13:30', 3, 'task', 'Removed from task', 'You have been removed from the task: Second task'),
(10, '2025-10-16 09:14:17', 3, 'task', 'Added to task', 'You have been added to a new task in Second Workspace: Second task'),
(11, '2025-10-16 09:14:26', 3, 'task', 'Removed from task', 'You have been removed from the task: Second task'),
(12, '2025-10-16 09:15:52', 8, 'task', 'Added to task', 'You have been added to a new task in Second Workspace: aa'),
(13, '2025-10-16 09:15:56', 8, 'task', 'Removed from task', 'You have been removed from the task: aa'),
(14, '2025-10-16 09:18:42', 8, 'task', 'Removed from task', 'You have been removed from the task: aa'),
(15, '2025-10-16 09:20:38', 3, 'task', 'Removed from task', 'You have been removed from the task: Second task'),
(16, '2025-10-16 09:21:58', 13, 'task', 'Removed from task', 'You have been removed from the task: second task'),
(17, '2025-10-16 09:23:10', 13, 'task', 'Added to task', 'You have been added to a new task in Second Workspace: second task'),
(18, '2025-10-16 09:23:15', 13, 'task', 'Removed from task', 'You have been removed from the task: second task'),
(19, '2025-10-16 09:24:02', 13, 'task', 'Added to task', 'You have been added to a new task in Second Workspace: second task'),
(20, '2025-10-16 09:25:27', 4, 'workspace', 'Added to workspace', 'You have been added to a new workspace: Second Workspace'),
(21, '2025-10-16 09:25:31', 4, 'workspace', 'Removed from workspace', 'You have been removed from the workspace: Second Workspace'),
(23, '2025-10-16 10:20:21', 1, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: RWDD_ASSIGNMENT'),
(25, '2025-10-16 11:32:18', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD_ASSIGNMENT has been renamed to: RWDD ASSIGNMENT'),
(26, '2025-10-16 11:32:42', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD ASSIGNMENT has been renamed to: RWDD_ASSIGNMENT'),
(27, '2025-10-16 11:35:22', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD_ASSIGNMENT has been renamed to: RWDD ASSIGNMENT'),
(28, '2025-10-16 11:35:39', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD ASSIGNMENT has been renamed to: RWDD_ASSIGNMENT'),
(29, '2025-10-16 11:36:31', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD_ASSIGNMENT has been renamed to: wwwwwwwwww'),
(30, '2025-10-16 12:03:08', 1, 'workspace', 'Workspace renamed', 'The workspace: wwwwwwwwww has been renamed to: RWDD_ASSIGNMENT'),
(31, '2025-10-16 12:04:23', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD_ASSIGNMENT has been renamed to: RWDD ASSIGNMENT'),
(32, '2025-10-16 12:04:30', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD ASSIGNMENT has been renamed to: RWDD_ASSIGNMENT'),
(33, '2025-10-16 12:07:09', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD_ASSIGNMENT has been renamed to: RWDD ASSIGNMENT'),
(34, '2025-10-16 12:08:21', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD ASSIGNMENT has been renamed to: RWDD_ASSIGNMENT'),
(35, '2025-10-16 12:09:48', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD_ASSIGNMENT has been renamed to: RWDD ASSIGNMENT'),
(36, '2025-10-16 12:10:36', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD ASSIGNMENT has been renamed to: RWDD_ASSIGNMENT'),
(37, '2025-10-16 12:11:55', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD_ASSIGNMENT has been renamed to: RWDD ASSIGNMENT'),
(38, '2025-10-16 12:12:22', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD ASSIGNMENT has been renamed to: RWDD_ASSIGNMENT'),
(39, '2025-10-16 12:14:10', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD_ASSIGNMENT has been renamed to: RWDD ASSIGNMENT'),
(40, '2025-10-16 12:14:40', 1, 'task', 'Task Updated', 'A task in RWDD ASSIGNMENT has been updated: Do the Task'),
(41, '2025-10-16 12:15:50', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD ASSIGNMENT has been renamed to: RWDD_ASSIGNMENT'),
(42, '2025-10-16 12:23:55', 4, 'workspace', 'Added to workspace', 'You have been added to a new workspace: Second Workspace'),
(43, '2025-10-16 12:23:55', 13, 'task', 'Added to task', 'You have been added to a new task in Second Workspace: second task'),
(44, '2025-10-16 15:49:54', 1, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: RWDD_ASSIGNMENT'),
(45, '2025-10-16 16:32:42', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD_ASSIGNMENT has been renamed to: RWDD ASSIGNMENT'),
(46, '2025-10-16 16:34:28', 6, 'workspace', 'Added to workspace', 'You have been added to a new workspace: second workspace'),
(47, '2025-10-16 16:34:28', 14, 'task', 'Added to task', 'You have been added to a new task in second workspace: second task'),
(48, '2025-10-16 16:34:31', 6, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: second workspace'),
(49, '2025-10-16 16:34:34', 6, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: second workspace'),
(50, '2025-10-16 16:35:06', 14, 'task', 'Task deleted', 'The task: second task has been deleted.'),
(51, '2025-10-16 16:36:45', 6, 'workspace', 'Workspace deleted', 'The workspace: second workspace has been deleted.'),
(52, '2025-10-16 16:40:06', 15, 'task', 'Task deleted', 'The task: Second has been deleted.'),
(53, '2025-10-16 16:40:42', 7, 'workspace', 'Added to workspace', 'You have been added to a new workspace: Second'),
(54, '2025-10-16 16:40:47', 7, 'workspace', 'Workspace deleted', 'The workspace: Second has been deleted.'),
(55, '2025-10-16 16:41:55', 8, 'workspace', 'Added to workspace', 'You have been added to a new workspace: Second'),
(56, '2025-10-16 16:41:55', 16, 'task', 'Added to task', 'You have been added to a new task in Second: second task'),
(57, '2025-10-16 16:42:17', 16, 'task', 'Task deleted', 'The task: second task has been deleted.'),
(58, '2025-10-16 16:43:06', 8, 'workspace', 'Workspace deleted', 'The workspace: Second has been deleted.'),
(59, '2025-10-17 08:21:26', 9, 'workspace', 'Added to workspace', 'You have been added to a new workspace: New Workspace'),
(60, '2025-10-17 08:21:30', 9, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: New Workspace'),
(61, '2025-10-17 08:21:39', 9, 'workspace', 'Workspace deleted', 'The workspace: New Workspace has been deleted.'),
(62, '2025-10-17 08:22:42', 19, 'task', 'Task Updated', 'A task in New Workspace has been updated: aaab'),
(63, '2025-10-17 08:23:00', 10, 'workspace', 'Added to workspace', 'You have been added to a new workspace: New Workspace'),
(64, '2025-10-17 08:23:00', 19, 'task', 'Added to task', 'You have been added to a new task in New Workspace: aaab'),
(65, '2025-10-17 08:23:28', 10, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: New Workspace'),
(66, '2025-10-17 08:24:31', 19, 'task', 'Task deleted', 'The task: aaab has been deleted.'),
(67, '2025-10-17 08:24:46', 10, 'workspace', 'Workspace deleted', 'The workspace: New Workspace has been deleted.'),
(68, '2025-10-17 10:08:47', 1, 'task', 'Task Updated', 'A task in RWDD ASSIGNMENT has been updated: Do the Task'),
(69, '2025-10-17 10:08:56', 1, 'task', 'Task Updated', 'A task in RWDD ASSIGNMENT has been updated: Do the Task'),
(71, '2025-10-21 19:47:17', 11, 'workspace', 'Workspace renamed', 'The workspace \'New Workspace\' has been renamed to \'Workspace 2\'.'),
(74, '2025-10-21 19:51:40', 21, 'task', 'Task created', 'A new task \'22\' has been created in workspace \'Workspace 2\'.'),
(75, '2025-10-21 19:52:48', 22, 'task', 'Task created', 'A new task \'22\' has been created in workspace \'Workspace 2\'.'),
(77, '2025-10-21 19:53:47', 22, 'task', 'Task deleted', 'The task: 22 has been deleted.'),
(80, '2025-10-21 19:55:05', 1, 'workspace', 'Workspace renamed', 'The workspace: RWDD ASSIGNMENT has been renamed to: RWDD_ASSIGNMENT'),
(81, '2025-10-21 19:55:34', 12, 'workspace', 'Workspace renamed', 'The workspace \'New Workspace\' has been renamed to \'workspace 2\'.'),
(82, '2025-10-21 19:55:34', 12, 'workspace', 'Workspace renamed', 'The workspace \'workspace 2\' has been renamed to \'workspace 2\'.'),
(83, '2025-10-21 19:55:51', 11, 'workspace', 'Workspace renamed', 'The workspace: Workspace 2 has been renamed to: workspace 3'),
(84, '2025-10-21 19:57:21', 1, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: RWDD_ASSIGNMENT'),
(85, '2025-10-21 19:57:31', 1, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: RWDD_ASSIGNMENT'),
(86, '2025-10-21 19:58:45', 1, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: RWDD_ASSIGNMENT'),
(87, '2025-10-21 19:58:48', 1, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: RWDD_ASSIGNMENT'),
(89, '2025-10-23 08:33:14', 24, 'task', 'Task created', 'A new task \'test endtime\' has been created in workspace \'New Workspace\'.'),
(93, '2025-10-23 08:35:11', 26, 'task', 'Task created', 'A new task \'testing\' has been created in workspace \'New Workspace\'.'),
(95, '2025-10-23 10:08:29', 28, 'task', 'Task created', 'A new task \'aa\' has been created in workspace \'New Workspace\'.'),
(96, '2025-10-23 10:09:44', 29, 'task', 'Task created', 'A new task \'aa\' has been created in workspace \'New Workspace\'.'),
(97, '2025-10-24 20:02:10', 30, 'task', 'Task created', 'A new task \'employee create\' has been created in workspace \'RWDD_ASSIGNMENT\'.'),
(98, '2025-10-24 20:22:40', 1, 'goal', 'Goal deleted', 'The goal:  has been deleted from workspace \'RWDD_ASSIGNMENT\'.'),
(99, '2025-10-24 20:35:52', 10, 'goal', 'Goal created', 'A new goal \'Goal created\' has been created in workspace \'New Workspace\'.'),
(100, '2025-10-24 20:36:40', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(101, '2025-10-25 10:17:47', 11, 'goal', 'Goal created', 'A new goal \'Goal created\' has been created in workspace \'New Workspace\'.'),
(102, '2025-10-25 10:19:20', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(103, '2025-10-25 10:19:27', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(104, '2025-10-25 10:19:31', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(105, '2025-10-25 10:19:36', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(106, '2025-10-25 10:19:42', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(107, '2025-10-25 10:19:48', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(108, '2025-10-25 10:20:44', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(109, '2025-10-25 10:20:52', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(110, '2025-10-25 10:20:56', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(111, '2025-10-25 10:20:59', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(112, '2025-10-25 10:21:16', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(113, '2025-10-25 10:21:20', 11, 'goal', 'Goal updated', 'The goal: \'test\' has been updated in workspace \'New Workspace\'.'),
(114, '2025-10-25 10:21:31', 12, 'goal', 'Goal created', 'A new goal \'Goal created\' has been created in workspace \'New Workspace\'.'),
(115, '2025-10-25 10:21:34', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(116, '2025-10-25 10:21:36', 10, 'goal', 'Goal updated', 'The goal: \'aa\' has been updated in workspace \'New Workspace\'.'),
(117, '2025-10-25 10:21:51', 13, 'goal', 'Goal created', 'A new goal \'Goal created\' has been created in workspace \'New Workspace\'.'),
(118, '2025-10-25 10:23:35', 14, 'goal', 'Goal created', 'A new goal \'Goal created\' has been created in workspace \'RWDD_ASSIGNMENT\'.'),
(119, '2025-10-25 10:23:50', 14, 'goal', 'Goal updated', 'The goal: \'test emp create\' has been updated in workspace \'RWDD_ASSIGNMENT\'.'),
(120, '2025-10-25 10:23:52', 14, 'goal', 'Goal updated', 'The goal: \'test emp create\' has been updated in workspace \'RWDD_ASSIGNMENT\'.'),
(121, '2025-10-25 10:24:31', 14, 'goal', 'Goal deleted', 'The goal: test emp create has been deleted from workspace \'RWDD_ASSIGNMENT\'.'),
(122, '2025-10-25 10:25:54', 15, 'goal', 'Goal created', 'A new goal \'Goal created\' has been created in workspace \'RWDD_ASSIGNMENT\'.'),
(123, '2025-10-25 16:29:47', 31, 'task', 'Task created', 'A new task \'test manager by manager\' has been created in workspace \'RWDD_ASSIGNMENT\'.'),
(124, '2025-10-25 16:30:48', 16, 'goal', 'Goal created', 'A new goal \'Goal created\' has been created in workspace \'RWDD_ASSIGNMENT\'.'),
(125, '2025-10-25 16:36:08', 31, 'task', 'Task Updated', 'A task in RWDD_ASSIGNMENT has been updated: test manager by manager'),
(126, '2025-10-25 19:50:22', 15, 'goal', 'Goal deleted', 'The goal: test emp create has been deleted from workspace \'RWDD_ASSIGNMENT\'.'),
(127, '2025-10-25 19:50:26', 16, 'goal', 'Goal deleted', 'The goal: test by manager has been deleted from workspace \'RWDD_ASSIGNMENT\'.'),
(128, '2025-10-25 19:50:37', 17, 'goal', 'Goal created', 'A new goal \'Goal created\' has been created in workspace \'RWDD_ASSIGNMENT\'.'),
(129, '2025-10-25 19:52:27', 32, 'task', 'Task created', 'A new task \'test by manager\' has been created in workspace \'RWDD_ASSIGNMENT\'.'),
(132, '2025-10-26 10:23:48', 15, 'workspace', 'Workspace renamed', 'The workspace: New Workspace has been renamed to: Rwdd_assignment'),
(133, '2025-10-26 10:27:55', 16, 'workspace', 'Workspace renamed', 'The workspace: New Workspace has been renamed to: RWDD_ASSIGNMENT'),
(134, '2025-10-26 10:28:34', 33, 'task', 'Task created', 'A new task \'www\' has been created in workspace \'RWDD_ASSIGNMENT\'.'),
(135, '2025-10-26 10:28:49', 16, 'workspace', 'Added to workspace', 'You have been added to a new workspace: RWDD_ASSIGNMENT'),
(136, '2025-10-26 10:28:59', 16, 'workspace', 'Removed from workspace', 'You have been removed from the workspace: RWDD_ASSIGNMENT'),
(137, '2025-10-26 10:29:05', 16, 'workspace', 'Added to workspace', 'You have been added to a new workspace: RWDD_ASSIGNMENT'),
(138, '2025-10-26 10:29:09', 16, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: RWDD_ASSIGNMENT'),
(139, '2025-10-26 10:29:53', 33, 'task', 'Added to task', 'You have been added to a new task in RWDD_ASSIGNMENT: www'),
(140, '2025-10-26 10:31:09', 33, 'task', 'Task Updated', 'A task in RWDD_ASSIGNMENT has been updated: www'),
(141, '2025-10-26 10:36:37', 34, 'task', 'Task created', 'A new task \'qwe\' has been created in workspace \'New Workspace\'.'),
(142, '2025-10-26 12:27:13', 18, 'workspace', 'Workspace renamed', 'The workspace: New Workspace has been renamed to: RWDD_ASSIGNMENT'),
(143, '2025-10-26 12:27:25', 35, 'task', 'Task created', 'A new task \'pou\' has been created in workspace \'RWDD_ASSIGNMENT\'.'),
(144, '2025-10-26 12:27:36', 18, 'workspace', 'Added to workspace', 'You have been added to a new workspace: RWDD_ASSIGNMENT'),
(145, '2025-10-26 12:27:41', 18, 'workspace', 'Granted Manager Access', 'You have been granted manager access in a workspace: RWDD_ASSIGNMENT'),
(146, '2025-10-26 12:27:49', 18, 'workspace', 'Removed from workspace', 'You have been removed from the workspace: RWDD_ASSIGNMENT'),
(147, '2025-10-26 12:27:58', 18, 'workspace', 'Workspace deleted', 'The workspace: RWDD_ASSIGNMENT has been deleted.'),
(148, '2025-10-26 12:28:12', 36, 'task', 'Task created', 'A new task \'123\' has been created in workspace \'New Workspace\'.'),
(149, '2025-10-26 12:28:31', 36, 'task', 'Task Updated', 'A task in New Workspace has been updated: 123'),
(150, '2025-10-26 12:28:58', 19, 'workspace', 'Added to workspace', 'You have been added to a new workspace: New Workspace'),
(151, '2025-10-26 12:28:58', 36, 'task', 'Added to task', 'You have been added to a new task in New Workspace: 123'),
(152, '2025-10-27 16:16:37', 12, 'goal', 'Goal updated', 'The goal: \'test1\' has been updated in workspace \'New Workspace\'.'),
(153, '2025-10-27 16:17:24', 11, 'goal', 'Goal updated', 'The goal: \'test\' has been updated in workspace \'New Workspace\'.'),
(154, '2025-10-27 16:17:36', 19, 'workspace', 'Workspace deleted', 'The workspace: New Workspace has been deleted.'),
(155, '2025-10-27 16:18:20', 17, 'goal', 'Goal updated', 'The goal: \'test by manager\' has been updated in workspace \'RWDD_ASSIGNMENT\'.'),
(156, '2025-10-27 16:21:03', 24, 'task', 'Task Updated', 'A task in New Workspace has been updated: test endtime'),
(157, '2025-10-27 16:27:47', 18, 'goal', 'Goal created', 'A new goal \'goal\' has been created in workspace \'New Workspace\'.'),
(158, '2025-11-02 12:11:13', 13, 'workspace', 'Workspace renamed', 'The workspace: New Workspace has been renamed to: new new');

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
(1, 2),
(3, 10),
(4, 10),
(5, 10),
(8, 10),
(10, 10),
(11, 10),
(12, 10),
(13, 10),
(14, 7),
(15, 7),
(16, 7),
(17, 7),
(18, 7),
(19, 7),
(20, 10),
(21, 10),
(23, 7),
(25, 1),
(25, 7),
(25, 2),
(25, 3),
(25, 5),
(25, 8),
(25, 10),
(25, 11),
(25, 12),
(25, 6),
(26, 1),
(26, 7),
(26, 2),
(26, 3),
(26, 5),
(26, 8),
(26, 10),
(26, 11),
(26, 12),
(26, 6),
(27, 1),
(27, 1),
(27, 7),
(27, 1),
(27, 7),
(27, 2),
(27, 1),
(27, 7),
(27, 2),
(27, 3),
(27, 1),
(27, 7),
(27, 2),
(27, 3),
(27, 5),
(27, 1),
(27, 7),
(27, 2),
(27, 3),
(27, 5),
(27, 8),
(27, 1),
(27, 7),
(27, 2),
(27, 3),
(27, 5),
(27, 8),
(27, 10),
(27, 1),
(27, 7),
(27, 2),
(27, 3),
(27, 5),
(27, 8),
(27, 10),
(27, 11),
(27, 1),
(27, 7),
(27, 2),
(27, 3),
(27, 5),
(27, 8),
(27, 10),
(27, 11),
(27, 12),
(27, 1),
(27, 7),
(27, 2),
(27, 3),
(27, 5),
(27, 8),
(27, 10),
(27, 11),
(27, 12),
(27, 6),
(28, 1),
(28, 1),
(28, 7),
(28, 1),
(28, 7),
(28, 2),
(28, 1),
(28, 7),
(28, 2),
(28, 3),
(28, 1),
(28, 7),
(28, 2),
(28, 3),
(28, 5),
(28, 1),
(28, 7),
(28, 2),
(28, 3),
(28, 5),
(28, 8),
(28, 1),
(28, 7),
(28, 2),
(28, 3),
(28, 5),
(28, 8),
(28, 10),
(28, 1),
(28, 7),
(28, 2),
(28, 3),
(28, 5),
(28, 8),
(28, 10),
(28, 11),
(28, 1),
(28, 7),
(28, 2),
(28, 3),
(28, 5),
(28, 8),
(28, 10),
(28, 11),
(28, 12),
(28, 1),
(28, 7),
(28, 2),
(28, 3),
(28, 5),
(28, 8),
(28, 10),
(28, 11),
(28, 12),
(28, 6),
(29, 1),
(29, 1),
(29, 7),
(29, 1),
(29, 7),
(29, 2),
(29, 1),
(29, 7),
(29, 2),
(29, 3),
(29, 1),
(29, 7),
(29, 2),
(29, 3),
(29, 5),
(29, 1),
(29, 7),
(29, 2),
(29, 3),
(29, 5),
(29, 8),
(29, 1),
(29, 7),
(29, 2),
(29, 3),
(29, 5),
(29, 8),
(29, 10),
(29, 1),
(29, 7),
(29, 2),
(29, 3),
(29, 5),
(29, 8),
(29, 10),
(29, 11),
(29, 1),
(29, 7),
(29, 2),
(29, 3),
(29, 5),
(29, 8),
(29, 10),
(29, 11),
(29, 12),
(29, 1),
(29, 7),
(29, 2),
(29, 3),
(29, 5),
(29, 8),
(29, 10),
(29, 11),
(29, 12),
(29, 6),
(30, 1),
(30, 1),
(30, 7),
(30, 1),
(30, 7),
(30, 2),
(30, 1),
(30, 7),
(30, 2),
(30, 3),
(30, 1),
(30, 7),
(30, 2),
(30, 3),
(30, 5),
(30, 1),
(30, 7),
(30, 2),
(30, 3),
(30, 5),
(30, 8),
(30, 1),
(30, 7),
(30, 2),
(30, 3),
(30, 5),
(30, 8),
(30, 10),
(30, 1),
(30, 7),
(30, 2),
(30, 3),
(30, 5),
(30, 8),
(30, 10),
(30, 11),
(30, 1),
(30, 7),
(30, 2),
(30, 3),
(30, 5),
(30, 8),
(30, 10),
(30, 11),
(30, 12),
(30, 1),
(30, 7),
(30, 2),
(30, 3),
(30, 5),
(30, 8),
(30, 10),
(30, 11),
(30, 12),
(30, 6),
(31, 1),
(31, 1),
(31, 7),
(31, 1),
(31, 7),
(31, 2),
(31, 1),
(31, 7),
(31, 2),
(31, 3),
(31, 1),
(31, 7),
(31, 2),
(31, 3),
(31, 5),
(31, 1),
(31, 7),
(31, 2),
(31, 3),
(31, 5),
(31, 8),
(31, 1),
(31, 7),
(31, 2),
(31, 3),
(31, 5),
(31, 8),
(31, 10),
(31, 1),
(31, 7),
(31, 2),
(31, 3),
(31, 5),
(31, 8),
(31, 10),
(31, 11),
(31, 1),
(31, 7),
(31, 2),
(31, 3),
(31, 5),
(31, 8),
(31, 10),
(31, 11),
(31, 12),
(31, 1),
(31, 7),
(31, 2),
(31, 3),
(31, 5),
(31, 8),
(31, 10),
(31, 11),
(31, 12),
(31, 6),
(32, 1),
(32, 1),
(32, 7),
(32, 1),
(32, 7),
(32, 2),
(32, 1),
(32, 7),
(32, 2),
(32, 3),
(32, 1),
(32, 7),
(32, 2),
(32, 3),
(32, 5),
(32, 1),
(32, 7),
(32, 2),
(32, 3),
(32, 5),
(32, 8),
(32, 1),
(32, 7),
(32, 2),
(32, 3),
(32, 5),
(32, 8),
(32, 10),
(32, 1),
(32, 7),
(32, 2),
(32, 3),
(32, 5),
(32, 8),
(32, 10),
(32, 11),
(32, 1),
(32, 7),
(32, 2),
(32, 3),
(32, 5),
(32, 8),
(32, 10),
(32, 11),
(32, 12),
(32, 1),
(32, 7),
(32, 2),
(32, 3),
(32, 5),
(32, 8),
(32, 10),
(32, 11),
(32, 12),
(32, 6),
(33, 1),
(33, 1),
(33, 7),
(33, 1),
(33, 7),
(33, 2),
(33, 1),
(33, 7),
(33, 2),
(33, 3),
(33, 1),
(33, 7),
(33, 2),
(33, 3),
(33, 5),
(33, 1),
(33, 7),
(33, 2),
(33, 3),
(33, 5),
(33, 8),
(33, 1),
(33, 7),
(33, 2),
(33, 3),
(33, 5),
(33, 8),
(33, 10),
(33, 1),
(33, 7),
(33, 2),
(33, 3),
(33, 5),
(33, 8),
(33, 10),
(33, 11),
(33, 1),
(33, 7),
(33, 2),
(33, 3),
(33, 5),
(33, 8),
(33, 10),
(33, 11),
(33, 12),
(33, 1),
(33, 7),
(33, 2),
(33, 3),
(33, 5),
(33, 8),
(33, 10),
(33, 11),
(33, 12),
(33, 6),
(34, 1),
(34, 1),
(34, 7),
(34, 1),
(34, 7),
(34, 2),
(34, 1),
(34, 7),
(34, 2),
(34, 3),
(34, 1),
(34, 7),
(34, 2),
(34, 3),
(34, 5),
(34, 1),
(34, 7),
(34, 2),
(34, 3),
(34, 5),
(34, 8),
(34, 1),
(34, 7),
(34, 2),
(34, 3),
(34, 5),
(34, 8),
(34, 10),
(34, 1),
(34, 7),
(34, 2),
(34, 3),
(34, 5),
(34, 8),
(34, 10),
(34, 11),
(34, 1),
(34, 7),
(34, 2),
(34, 3),
(34, 5),
(34, 8),
(34, 10),
(34, 11),
(34, 12),
(34, 1),
(34, 7),
(34, 2),
(34, 3),
(34, 5),
(34, 8),
(34, 10),
(34, 11),
(34, 12),
(34, 6),
(36, 1),
(36, 1),
(36, 7),
(36, 1),
(36, 7),
(36, 2),
(36, 1),
(36, 7),
(36, 2),
(36, 3),
(36, 1),
(36, 7),
(36, 2),
(36, 3),
(36, 5),
(36, 1),
(36, 7),
(36, 2),
(36, 3),
(36, 5),
(36, 8),
(36, 1),
(36, 7),
(36, 2),
(36, 3),
(36, 5),
(36, 8),
(36, 10),
(36, 1),
(36, 7),
(36, 2),
(36, 3),
(36, 5),
(36, 8),
(36, 10),
(36, 11),
(36, 1),
(36, 7),
(36, 2),
(36, 3),
(36, 5),
(36, 8),
(36, 10),
(36, 11),
(36, 12),
(36, 1),
(36, 7),
(36, 2),
(36, 3),
(36, 5),
(36, 8),
(36, 10),
(36, 11),
(36, 12),
(36, 6),
(37, 1),
(37, 7),
(37, 2),
(37, 3),
(37, 5),
(37, 8),
(37, 10),
(37, 11),
(37, 12),
(37, 6),
(38, 1),
(38, 7),
(38, 2),
(38, 3),
(38, 5),
(38, 8),
(38, 10),
(38, 11),
(38, 12),
(38, 6),
(39, 1),
(39, 7),
(39, 2),
(39, 3),
(39, 5),
(39, 8),
(39, 10),
(39, 11),
(39, 12),
(39, 6),
(40, 1),
(40, 7),
(40, 3),
(40, 5),
(40, 8),
(40, 10),
(40, 11),
(40, 2),
(41, 1),
(41, 7),
(41, 2),
(41, 3),
(41, 5),
(41, 8),
(41, 10),
(41, 11),
(41, 12),
(41, 6),
(42, 10),
(43, 10),
(44, 5),
(45, 1),
(45, 7),
(45, 2),
(45, 3),
(45, 5),
(45, 8),
(45, 10),
(45, 11),
(45, 12),
(45, 6),
(46, 10),
(47, 10),
(48, 7),
(49, 10),
(52, 7),
(53, 10),
(54, 7),
(54, 10),
(55, 10),
(56, 10),
(57, 7),
(57, 10),
(58, 7),
(58, 10),
(59, 10),
(60, 10),
(61, 7),
(61, 10),
(62, 7),
(63, 10),
(64, 10),
(65, 10),
(66, 7),
(66, 10),
(67, 7),
(67, 10),
(68, 1),
(68, 7),
(68, 3),
(68, 5),
(68, 8),
(68, 10),
(68, 11),
(68, 2),
(69, 1),
(69, 7),
(69, 3),
(69, 5),
(69, 8),
(69, 10),
(69, 11),
(69, 2),
(71, 7),
(74, 7),
(75, 7),
(77, 7),
(80, 1),
(80, 7),
(80, 2),
(80, 3),
(80, 5),
(80, 8),
(80, 10),
(80, 11),
(80, 12),
(80, 6),
(81, 7),
(82, 7),
(83, 7),
(84, 7),
(85, 7),
(86, 6),
(87, 2),
(89, 7),
(93, 7),
(95, 7),
(96, 7),
(97, 10),
(98, 1),
(98, 7),
(98, 2),
(98, 3),
(98, 5),
(98, 8),
(98, 10),
(98, 11),
(98, 12),
(98, 6),
(99, 7),
(100, 7),
(101, 7),
(102, 7),
(103, 7),
(104, 7),
(105, 7),
(106, 7),
(107, 7),
(108, 7),
(109, 7),
(110, 7),
(111, 7),
(112, 7),
(113, 7),
(114, 7),
(115, 7),
(116, 7),
(117, 7),
(118, 1),
(118, 7),
(118, 2),
(118, 3),
(118, 5),
(118, 8),
(118, 10),
(118, 11),
(118, 12),
(118, 6),
(119, 1),
(119, 7),
(119, 2),
(119, 3),
(119, 5),
(119, 8),
(119, 10),
(119, 11),
(119, 12),
(119, 6),
(120, 1),
(120, 7),
(120, 2),
(120, 3),
(120, 5),
(120, 8),
(120, 10),
(120, 11),
(120, 12),
(120, 6),
(121, 1),
(121, 7),
(121, 2),
(121, 3),
(121, 5),
(121, 8),
(121, 10),
(121, 11),
(121, 12),
(121, 6),
(122, 1),
(122, 7),
(122, 2),
(122, 3),
(122, 5),
(122, 8),
(122, 10),
(122, 11),
(122, 12),
(122, 6),
(123, 7),
(124, 1),
(124, 7),
(124, 2),
(124, 3),
(124, 5),
(124, 8),
(124, 10),
(124, 11),
(124, 12),
(124, 6),
(125, 7),
(126, 1),
(126, 7),
(126, 2),
(126, 3),
(126, 5),
(126, 8),
(126, 10),
(126, 11),
(126, 12),
(126, 6),
(127, 1),
(127, 7),
(127, 2),
(127, 3),
(127, 5),
(127, 8),
(127, 10),
(127, 11),
(127, 12),
(127, 6),
(128, 1),
(128, 7),
(128, 2),
(128, 3),
(128, 5),
(128, 8),
(128, 10),
(128, 11),
(128, 12),
(128, 6),
(129, 7),
(132, 15),
(133, 16),
(134, 16),
(135, 7),
(136, 7),
(137, 7),
(138, 7),
(139, 7),
(140, 16),
(140, 7),
(141, 18),
(142, 19),
(143, 19),
(144, 7),
(145, 7),
(146, 7),
(147, 19),
(148, 19),
(149, 19),
(150, 7),
(151, 7),
(152, 7),
(153, 7),
(154, 19),
(154, 7),
(155, 1),
(155, 7),
(155, 2),
(155, 3),
(155, 5),
(155, 8),
(155, 10),
(155, 11),
(155, 12),
(155, 6),
(156, 7),
(157, 7),
(158, 7);

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
  `EndTime` datetime DEFAULT NULL,
  `Deadline` datetime NOT NULL,
  `Priority` enum('High','Medium','Low') NOT NULL DEFAULT 'High',
  `Status` enum('Pending','In Progress','Completed') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`TaskID`, `WorkSpaceID`, `Title`, `Description`, `StartTime`, `EndTime`, `Deadline`, `Priority`, `Status`) VALUES
(1, 1, 'Do the Task', 'Do the task page', '2025-09-10 08:10:00', '2025-10-17 10:08:56', '2025-10-01 08:10:00', 'Low', 'Completed'),
(2, 1, 'DO the home', 'do the home page', '2025-09-30 09:00:00', NULL, '2025-10-30 09:00:00', 'High', 'Pending'),
(12, 1, 'test datetime-local', 'New task description', '2025-10-15 15:38:00', NULL, '2025-10-15 15:39:00', '', 'Pending'),
(17, 1, 'test endtime, status inprogress', 'ss', '2025-10-09 19:34:00', '2025-10-09 19:34:00', '2025-10-24 19:34:00', '', 'Pending'),
(18, 1, 'try inprogress', 'aa', '2025-10-09 19:36:00', '2025-10-09 19:36:00', '2025-10-23 19:36:00', '', ''),
(20, 1, 'test endtime with same start date', 'aa', '2025-10-17 10:16:00', NULL, '2025-10-18 10:16:00', '', 'Completed'),
(21, 11, '22', '22', '2025-10-09 19:51:00', NULL, '2025-10-21 19:51:00', 'Medium', 'In Progress'),
(24, 13, 'test endtime', 'ss', '2025-10-24 08:33:00', '2025-10-27 16:21:03', '2025-10-25 08:33:00', 'Low', 'Completed'),
(26, 13, 'testing', 'New task description', '2025-10-23 08:34:00', NULL, '2025-10-31 08:35:00', '', 'Pending'),
(29, 13, 'aa', 'New task description', '2025-10-23 10:09:00', NULL, '2025-10-31 10:09:00', '', 'Pending'),
(30, 1, 'employee create', 'aa', '2025-10-24 20:02:00', NULL, '2025-10-25 20:02:00', 'Low', 'Pending'),
(31, 1, 'test manager by manager', 'sa', '2025-10-25 16:29:00', NULL, '2025-11-29 16:29:00', 'Low', 'In Progress'),
(33, 16, 'www', 'www', '2025-10-26 10:28:00', NULL, '2025-10-28 10:28:00', 'Low', 'In Progress'),
(34, 17, 'qwe', 'qwe', '2025-10-26 10:36:00', NULL, '2025-10-30 10:36:00', 'Low', 'Pending');

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
(2, 2),
(7, 1),
(3, 1),
(5, 1),
(8, 1),
(10, 1),
(11, 1),
(6, 2),
(2, 1),
(7, 2),
(7, 12),
(7, 17),
(7, 18),
(7, 20),
(7, 21),
(7, 24),
(7, 26),
(7, 29),
(10, 30),
(7, 31),
(16, 33),
(7, 33),
(18, 34);

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
(7, 'Iuno', 'w@g.co', '$2y$10$TWL9at4gaZTPORv5WWHQ/Omgx/C2F0VFHqog6//ZXglRlcOj2RCXC', '7.png'),
(8, 'f', 'f@g.co', '$2y$10$.Lrmri6kNTJD3A1S8UPqY.ie5H8ktLGC0tte3YJOeX1IaX8ixaozm', NULL),
(10, 'a', 'a@g.co', '$2y$10$PojV.2f1SFLNVuhfBrytquWuYhuJ96FVuCD3Sa.gMICsnCaEbpMVi', '10.png'),
(11, '3p', 'p@g.co', '$2y$10$27I3yzd8m6n8NKtCdnXir.RvNP/GQRDIElhm/WoNqpXNvCWoN1pve', NULL),
(12, 'q', 'q@g.co', '$2y$10$kTnzFz9MkLUuDsxqjaxOQ.0SS7yOXxppvMsYf2VTDsH181lpUOQlO', NULL),
(13, 'aaaa', 'aaaa@g.co', '$2y$10$I5ZiyAtIDfnG2CfzT5TZFeqU2FWujyXX3hBfmvKcOP842Nnoy.c8.', NULL),
(14, 'w', 'wwwww@g.com', '$2y$10$Po44L57Ewninp5Vy7NrEmeGFRyqp.Wscz5JgcLj4Tmv1AEWHgKQUy', NULL),
(15, 'ww', 'w@g.com', '$2y$10$6nxZnb70ijay7o8R4s1ZpecIqSPgTIFIVE/f6ULUZQfA.0b5atpyO', '15.png'),
(16, 'qqqq', 'wong@g.com', '$2y$10$cWRTnURB7sBZOrUnXJeLcet.qDqb/C4lDxyD/90IiXmdymutQNTJi', '16.png'),
(17, '213', 'qwert@g.co', '$2y$10$w/4XHEIH8vIz3L7tjYMTOeU/Y92./qvEdXnwN54ZOLoD1B0CLnP4q', '17.png'),
(18, 'qw', 'qw@g.co', '$2y$10$H3g.ub.k93ajWnaJimHyC.g3VId/Dew8iqCNVJ63gfw8SgKxmqZha', '18.png'),
(19, 'po', 'poi@gmail.com', '$2y$10$Fb1PuFkG2IIPwOcQe5K3CuxbVAa5loV24.CbwYUIVGyYa6YaRwjaa', '19.png');

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
(1, 'RWDD_ASSIGNMENT', 1),
(11, 'workspace 3', 7),
(12, 'workspace 2', 7),
(13, 'new new', 7),
(14, 'New Workspace', 10),
(15, 'Rwdd_assignment', 15),
(16, 'RWDD_ASSIGNMENT', 16),
(17, 'New Workspace', 18);

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
(1, 7, 'Manager'),
(1, 2, 'Manager'),
(1, 3, 'Manager'),
(1, 5, 'Manager'),
(1, 8, 'Employee'),
(1, 10, 'Employee'),
(1, 11, 'Employee'),
(1, 12, 'Manager'),
(1, 6, 'Manager'),
(11, 7, 'Manager'),
(12, 7, 'Manager'),
(13, 7, 'Manager'),
(14, 10, 'Manager'),
(15, 15, 'Manager'),
(16, 16, 'Manager'),
(16, 7, 'Manager'),
(17, 18, 'Manager');

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
  MODIFY `CommentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `fileshared`
--
ALTER TABLE `fileshared`
  MODIFY `FileID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `goal`
--
ALTER TABLE `goal`
  MODIFY `GoalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `workspace`
--
ALTER TABLE `workspace`
  MODIFY `WorkSpaceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
