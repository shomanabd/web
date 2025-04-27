-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 16, 2025 at 02:11 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web1223166_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `Documents`
--

CREATE TABLE `Documents` (
  `document_id` int NOT NULL,
  `project_id` char(10) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Projects`
--

CREATE TABLE `Projects` (
  `project_id` char(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `total_budget` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Projects`
--

INSERT INTO `Projects` (`project_id`, `title`, `description`, `customer_name`, `total_budget`, `start_date`, `end_date`) VALUES
('PROJ-00001', 'test task manager', 'just test task manager', 'ahmad abdalaziz', 4000.00, '2024-10-10', '2025-10-10'),
('PROJ-00002', 'proj2', 'this is project 2', 'abod', 1000.00, '2025-10-10', '2025-10-12');

-- --------------------------------------------------------

--
-- Table structure for table `ProjectTeamLeaders`
--

CREATE TABLE `ProjectTeamLeaders` (
  `project_id` char(10) NOT NULL,
  `team_leader_id` int NOT NULL,
  `assignment_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ProjectTeamLeaders`
--

INSERT INTO `ProjectTeamLeaders` (`project_id`, `team_leader_id`, `assignment_id`) VALUES
('PROJ-00001', 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `TaskProgress`
--

CREATE TABLE `TaskProgress` (
  `progress_id` int NOT NULL,
  `task_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `progress_percentage` decimal(5,2) NOT NULL,
  `status` enum('Pending','In Progress','Completed') NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `TaskProgress`
--

INSERT INTO `TaskProgress` (`progress_id`, `task_id`, `user_id`, `progress_percentage`, `status`, `updated_at`) VALUES
(1, 1, 5, 4.00, 'In Progress', '2025-01-16 03:45:57'),
(2, 1, 5, 100.00, 'In Progress', '2025-01-16 03:46:26'),
(3, 1, 5, 56.00, 'Completed', '2025-01-16 03:48:03'),
(4, 1, 5, 21.00, 'Completed', '2025-01-16 03:48:06');

-- --------------------------------------------------------

--
-- Table structure for table `Tasks`
--

CREATE TABLE `Tasks` (
  `task_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `project_id` char(10) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `effort` decimal(5,2) NOT NULL,
  `status` enum('Pending','In Progress','Completed') DEFAULT 'Pending',
  `priority` enum('Low','Medium','High') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Tasks`
--

INSERT INTO `Tasks` (`task_id`, `name`, `description`, `project_id`, `start_date`, `end_date`, `effort`, `status`, `priority`) VALUES
(1, 'task 1', 'just test the create task', 'PROJ-00001', '2025-01-01', '2024-11-12', 2.00, 'Completed', 'Low');

-- --------------------------------------------------------

--
-- Table structure for table `TeamAssignments`
--

CREATE TABLE `TeamAssignments` (
  `assignment_id` int NOT NULL,
  `task_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `role` enum('Developer','Designer','Tester','Analyst','Support','Project Leader') NOT NULL,
  `contribution_percentage` decimal(5,2) NOT NULL,
  `start_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `TeamAssignments`
--

INSERT INTO `TeamAssignments` (`assignment_id`, `task_id`, `user_id`, `role`, `contribution_percentage`, `start_date`) VALUES
(3, NULL, 2, 'Project Leader', 100.00, '2025-01-16'),
(8, 1, 6, 'Tester', 40.00, '2025-01-16'),
(9, 1, 5, 'Developer', 60.00, '2025-01-16');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `date_of_birth` date NOT NULL,
  `id_number` char(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `role` enum('Manager','Project Leader','Team Member') NOT NULL,
  `qualification` varchar(100) NOT NULL,
  `skills` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `system_user_id` char(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `name`, `address`, `date_of_birth`, `id_number`, `email`, `telephone`, `role`, `qualification`, `skills`, `username`, `password_hash`, `system_user_id`) VALUES
(1, 'Abd', '11,main street,Jenin,Palestine', '2003-05-01', '4082893790', 'abosalameabd@gmail.com', '0598467002', 'Manager', 'student', 'HTML , CSS , php', 'Abd123', '$2y$10$5nTnFM4yd1SMwpg4QbsCR.jkSeVbBGrBOyKd0Y0WsRykUmIplWYn2', NULL),
(2, 'Ali', '2,main,Nablus,palestine', '2002-10-10', '4088123456', 'test@gmail.com', '0909090909', 'Project Leader', 'Master in computer science', 'HTML , CSS , PHP', 'Ali333', '$2y$10$AoocA6H/ibvPP.ZnIIsOIua7PSwYzYEQf4O.oduTL4eSn2hrzR.hi', NULL),
(4, 'Member 1', '11,main,Ramallah,Palestime', '2000-01-18', '1023456787', 'test1@gmail.com', '102345678', 'Team Member', 'CS', 'HTML , CSS , PHP', 'User11', '$2y$10$PSZOqacVCR9oYQfTZJp3m.LCa8jhyU5388I0TKualD58WbAKwPhfa', NULL),
(5, 'User 2', '2,main,Ramallah,Palestine', '2000-03-01', '4034567890', 'test2@gmail.com', '0123456789', 'Team Member', 'CS', 'HTML , CSS , PHP', 'User22', '$2y$10$e4gj8fvIOEc/9i6M7F5GG.pEhVxwxydPn0DQ/EQ.3KMk/IpVj3Hba', NULL),
(6, 'User 3', '3,main,Ramallah,Palestine', '2001-11-11', '4098765432', 'test3@gmail.com', '0597190000', 'Team Member', 'CS', 'HTML , CSS, PHP', 'User33', '$2y$10$ay/qmMHeDB4KPP7QTgWgFOtoEytzEM/iJnbADtW0Cdd9bhH5gN7Wu', NULL),
(11, 'User 4', '4,main,Ramallah,Palestine', '2001-01-01', '1020304050', 'test4@gmail.com', '1020304050', 'Team Member', 'CS', 'HTML , CSS ,PHP', 'User44', '$2y$10$eqUqSaX982sLEWc56ow0cesTNVtQ8RBJLSTXbAHrRG.62ttyMwASu', '0000000006'),
(17, 'Ahmad', '5,main,Ramallah,Palestine', '2000-01-20', '4440001111', 'test6@gmail.com', '1020304050', 'Project Leader', 'CS', 'html', 'Ahmad1', '$2y$10$6f8owLX48/kAqn2QhACMYu/KI2nrP7S35yROcMVLRgE8Kw5l0ftsS', '0000000007'),
(19, 'User6', '5,main,Ramallah,Palestine', '2000-01-01', '1223344566', 'test65@gmail.com', '1234569999', 'Team Member', 'cs', 'html', 'User66', '$2y$10$ucCZaNCNTuT8v3/Evx/JHOLFWTk1wic8.egupnlZUfnawDrB/F6HG', '0000000008'),
(34, 'user 7', '7,main,Ramallah,Palestine', '2002-10-10', '4443336667', 'test77@gmail.com', '1020304050', 'Team Member', 'CS', 'html', 'User77', '$2y$10$P/AURz8K0pc8ZUsQ.DVTMOJ8F7Leb5DRMB2eMQv4idlHx2QhKchlm', '0000000009'),
(36, 'User8', '4,main,Ramallah,Palestine', '1980-02-02', '1020304059', 'test47@gmail.com', '1234569909', 'Team Member', 'cs', 'html', 'User99', '$2y$10$983s7.878mrDlMAp.6r0K.BjT3nBbFPNbebT/.iT4kCoQTjJL5llO', '0000000010'),
(38, 'User9', '4,main,Ramallah,Palestine', '1980-02-02', '1020399059', 'test40@gmail.com', '1234569909', 'Team Member', 'cs', 'html', 'User08', '$2y$10$DQ0R2JeCi77zcsMVpP8z5uAJthP0WZLQcjKSdeWSd.E7OArnn1z3u', '0000000011');

--
-- Triggers `Users`
--
DELIMITER $$
CREATE TRIGGER `before_insert_users` BEFORE INSERT ON `Users` FOR EACH ROW BEGIN
    SET NEW.system_user_id = LPAD((SELECT COUNT(*) FROM Users) + 1 , 10, '0');
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Documents`
--
ALTER TABLE `Documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `Projects`
--
ALTER TABLE `Projects`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `ProjectTeamLeaders`
--
ALTER TABLE `ProjectTeamLeaders`
  ADD PRIMARY KEY (`project_id`,`team_leader_id`),
  ADD KEY `team_leader_id` (`team_leader_id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indexes for table `TaskProgress`
--
ALTER TABLE `TaskProgress`
  ADD PRIMARY KEY (`progress_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Tasks`
--
ALTER TABLE `Tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `TeamAssignments`
--
ALTER TABLE `TeamAssignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `system_user_id` (`system_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Documents`
--
ALTER TABLE `Documents`
  MODIFY `document_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `TaskProgress`
--
ALTER TABLE `TaskProgress`
  MODIFY `progress_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Tasks`
--
ALTER TABLE `Tasks`
  MODIFY `task_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `TeamAssignments`
--
ALTER TABLE `TeamAssignments`
  MODIFY `assignment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Documents`
--
ALTER TABLE `Documents`
  ADD CONSTRAINT `Documents_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `ProjectTeamLeaders`
--
ALTER TABLE `ProjectTeamLeaders`
  ADD CONSTRAINT `ProjectTeamLeaders_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ProjectTeamLeaders_ibfk_2` FOREIGN KEY (`team_leader_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ProjectTeamLeaders_ibfk_3` FOREIGN KEY (`assignment_id`) REFERENCES `TeamAssignments` (`assignment_id`) ON DELETE CASCADE;

--
-- Constraints for table `TaskProgress`
--
ALTER TABLE `TaskProgress`
  ADD CONSTRAINT `TaskProgress_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `TaskProgress_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `Tasks`
--
ALTER TABLE `Tasks`
  ADD CONSTRAINT `Tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `TeamAssignments`
--
ALTER TABLE `TeamAssignments`
  ADD CONSTRAINT `TeamAssignments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `TeamAssignments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
