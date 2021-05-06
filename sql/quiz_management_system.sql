-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 07, 2020 at 05:02 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quiz_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `answer_id` int(11) NOT NULL,
  `answer` int(11) DEFAULT NULL,
  `FK_user_id_answers` int(11) NOT NULL,
  `FK_quiz_id_answers` int(11) NOT NULL,
  `FK_questions_id_answers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`answer_id`, `answer`, `FK_user_id_answers`, `FK_quiz_id_answers`, `FK_questions_id_answers`) VALUES
(6, 1, 2, 5, 3),
(7, 2, 2, 5, 4),
(8, 1, 2, 5, 5),
(9, 2, 2, 5, 6),
(10, 2, 2, 5, 7),
(11, 1, 3, 5, 3),
(12, 2, 3, 5, 4),
(13, 2, 3, 5, 5),
(14, 3, 3, 5, 6),
(15, 1, 3, 5, 7);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `question_title` varchar(320) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `first_answer` varchar(320) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `second_answer` varchar(320) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `third_answer` varchar(320) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `correct_answer` int(11) NOT NULL,
  `FK_quiz_id_questions` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `question_title`, `first_answer`, `second_answer`, `third_answer`, `correct_answer`, `FK_quiz_id_questions`) VALUES
(3, 'What is the color of the sky?', 'Blue', 'Red', 'Pink', 1, 5),
(4, 'How many hands do human have?', 'One hand', 'Two hands', 'Three hands', 2, 5),
(5, 'Why do we eat?', 'To make fun', 'To live', 'To dance', 2, 5),
(6, 'How many planets in the solar system?', '5', '7', '8', 3, 5),
(7, 'How many eys do fish have?', '1', '2', '3', 2, 5),
(11, 'How many natural satelite earth has?', '1', '2', '3', 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `quiz_id` int(11) NOT NULL,
  `title` varchar(320) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `course` varchar(32) DEFAULT NULL,
  `section` varchar(32) DEFAULT NULL,
  `number_of_question` varchar(32) DEFAULT NULL,
  `quiz_time` varchar(32) DEFAULT NULL,
  `FK_teacher_id_quiz` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`quiz_id`, `title`, `course`, `section`, `number_of_question`, `quiz_time`, `FK_teacher_id_quiz`) VALUES
(5, 'First quiz of QMS', '303', 'A', '5', '25', 1),
(7, 'another test', '23', 'a', '1', '10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `teacher_unique_id` varchar(16) DEFAULT NULL,
  `first_name` varchar(72) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(72) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email_addr` varchar(320) NOT NULL,
  `password_hash` varchar(320) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `teacher_unique_id`, `first_name`, `last_name`, `email_addr`, `password_hash`) VALUES
(1, '35f51077e6bef55f', 'MD.', 'Soyaib', 'soyaib@gmail.com', '$2y$12$GlbHHN7KAW6PAQAdHAjf8O6ba3Q2puyHP.HRcJMjxPIWxEK7jjxgq');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_unique_id` varchar(16) DEFAULT NULL,
  `first_name` varchar(72) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(72) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `email_addr` varchar(320) NOT NULL,
  `password_hash` varchar(320) NOT NULL,
  `FK_teacher_id_users` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_unique_id`, `first_name`, `last_name`, `student_id`, `email_addr`, `password_hash`, `FK_teacher_id_users`) VALUES
(2, '25f52699a497725f', 'Al', 'Shakib', '17103105', 'al.shakib.mail@gmail.com', '$2y$12$GlbHHN7KAW6PAQAdHAjf8O6ba3Q2puyHP.HRcJMjxPIWxEK7jjxgq', 1),
(3, '35f53a18a575775f', 'Sajia Afrin', 'Moon', '17103065', 'sajiaafrin237@gmail.com', '$2y$12$njpwMPBVZvcAcTFvDrccVe3/ZUT7raOHnf/3Oab1aa7JFfclTHv1y', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `FK_user_id_answers` (`FK_user_id_answers`),
  ADD KEY `FK_quiz_id_answers` (`FK_quiz_id_answers`),
  ADD KEY `answers_ibfk_3` (`FK_questions_id_answers`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `questions_ibfk_1` (`FK_quiz_id_questions`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`quiz_id`),
  ADD KEY `FK_teacher_id_quiz` (`FK_teacher_id_quiz`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `email_addr` (`email_addr`),
  ADD UNIQUE KEY `teacher_unique_id` (`teacher_unique_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email_addr` (`email_addr`),
  ADD UNIQUE KEY `user_unique_id` (`user_unique_id`),
  ADD KEY `FK_teacher_id_users` (`FK_teacher_id_users`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`FK_user_id_answers`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`FK_quiz_id_answers`) REFERENCES `quiz` (`quiz_id`),
  ADD CONSTRAINT `answers_ibfk_3` FOREIGN KEY (`FK_questions_id_answers`) REFERENCES `questions` (`question_id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`FK_quiz_id_questions`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`FK_teacher_id_quiz`) REFERENCES `teachers` (`teacher_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`FK_teacher_id_users`) REFERENCES `teachers` (`teacher_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
