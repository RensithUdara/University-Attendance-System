-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 08:06 AM
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
-- Database: `campus_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `lecture_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late') NOT NULL DEFAULT 'present',
  `marked_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `student_id`, `lecture_id`, `course_id`, `date`, `status`, `marked_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 1, '2026-02-02', 'present', '2026-02-02 03:05:00', 'Checked in by QR scan.', '2026-02-02 03:05:00', '2026-02-02 03:05:00'),
(2, 7, 1, 1, '2026-02-02', 'present', '2026-02-02 03:07:00', 'Checked in by QR scan.', '2026-02-02 03:07:00', '2026-02-02 03:07:00'),
(3, 8, 1, 1, '2026-02-02', 'late', '2026-02-02 03:18:00', 'Arrived after the grace period.', '2026-02-02 03:18:00', '2026-02-02 03:18:00'),
(4, 9, 2, 2, '2026-02-03', 'present', '2026-02-03 04:02:00', 'Checked in by QR scan.', '2026-02-03 04:02:00', '2026-02-03 04:02:00'),
(5, 10, 2, 2, '2026-02-03', 'present', '2026-02-03 04:04:00', 'Checked in by QR scan.', '2026-02-03 04:04:00', '2026-02-03 04:04:00'),
(6, 11, 3, 3, '2026-02-04', 'present', '2026-02-04 03:34:00', 'Checked in by QR scan.', '2026-02-04 03:34:00', '2026-02-04 03:34:00'),
(7, 12, 3, 3, '2026-02-04', 'absent', NULL, 'Absent for the lecture.', '2026-02-04 03:30:00', '2026-02-04 03:30:00'),
(8, 13, 4, 4, '2026-02-05', 'present', '2026-02-05 05:03:00', 'Checked in by QR scan.', '2026-02-05 05:03:00', '2026-02-05 05:03:00'),
(9, 14, 4, 4, '2026-02-05', 'late', '2026-02-05 05:17:00', 'Arrived after the grace period.', '2026-02-05 05:17:00', '2026-02-05 05:17:00'),
(10, 15, 5, 5, '2026-02-06', 'present', '2026-02-06 03:01:00', 'Checked in by QR scan.', '2026-02-06 03:01:00', '2026-02-06 03:01:00');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `code` varchar(255) NOT NULL,
  `credits` int(11) NOT NULL,
  `lecturer_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `semester` enum('1','2','3','4','5','6','7','8') NOT NULL DEFAULT '1',
  `duration` varchar(255) DEFAULT NULL,
  `max_students` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `description`, `code`, `credits`, `lecturer_id`, `created_at`, `updated_at`, `semester`, `duration`, `max_students`) VALUES
(1, 'Advanced Web Application Development', 'Laravel, REST APIs, authentication, deployment, and production-ready web patterns.', 'ICT401', 3, 2, '2026-01-10 08:00:00', '2026-01-10 08:00:00', '6', '15 weeks', 45),
(2, 'Client-Side Web Engineering', 'HTML, CSS, JavaScript, responsive UI, and browser-based application behavior.', 'ICT102', 3, 2, '2026-01-10 08:10:00', '2026-01-10 08:10:00', '1', '15 weeks', 60),
(3, 'Human Computer Interaction', 'User research, wireframing, usability testing, and accessible interface design.', 'ICT203', 2, 3, '2026-01-10 08:20:00', '2026-01-10 08:20:00', '3', '15 weeks', 40),
(4, 'Database Systems', 'Relational design, SQL, normalization, indexing, and transaction fundamentals.', 'ICT204', 3, 3, '2026-01-10 08:30:00', '2026-01-10 08:30:00', '3', '15 weeks', 50),
(5, 'Computer Networks', 'Network models, IP addressing, routing concepts, switching, and basic troubleshooting.', 'ICT305', 3, 4, '2026-01-10 08:40:00', '2026-01-10 08:40:00', '4', '15 weeks', 45),
(6, 'Software Engineering Practices', 'Agile planning, requirements, testing, version control, and maintainable delivery workflows.', 'ICT306', 3, 4, '2026-01-10 08:50:00', '2026-01-10 08:50:00', '4', '15 weeks', 45),
(7, 'Applied Mathematics for Computing', 'Discrete mathematics, logic, matrices, probability, and computational problem solving.', 'ICT101', 2, 5, '2026-01-10 09:00:00', '2026-01-10 09:00:00', '1', '15 weeks', 70),
(8, 'Mobile Application Development', 'Flutter basics, mobile UI patterns, local storage, APIs, and deployment preparation.', 'ICT402', 3, 5, '2026-01-10 09:10:00', '2026-01-10 09:10:00', '6', '15 weeks', 40);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `enrolled_at` date NOT NULL,
  `status` enum('active','completed','dropped') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `semester` enum('1','2','3','4','5','6','7','8') NOT NULL DEFAULT '1',
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `enrolled_at`, `status`, `created_at`, `updated_at`, `semester`, `completed_at`) VALUES
(1, 6, 1, '2026-01-15', 'active', '2026-01-15 04:00:00', '2026-01-15 04:00:00', '6', NULL),
(2, 6, 8, '2026-01-15', 'active', '2026-01-15 04:02:00', '2026-01-15 04:02:00', '6', NULL),
(3, 7, 1, '2026-01-15', 'active', '2026-01-15 04:05:00', '2026-01-15 04:05:00', '6', NULL),
(4, 7, 8, '2026-01-15', 'active', '2026-01-15 04:06:00', '2026-01-15 04:06:00', '6', NULL),
(5, 8, 2, '2026-01-15', 'active', '2026-01-15 04:10:00', '2026-01-15 04:10:00', '1', NULL),
(6, 8, 7, '2026-01-15', 'active', '2026-01-15 04:11:00', '2026-01-15 04:11:00', '1', NULL),
(7, 9, 2, '2026-01-15', 'active', '2026-01-15 04:15:00', '2026-01-15 04:15:00', '1', NULL),
(8, 9, 7, '2026-01-15', 'active', '2026-01-15 04:16:00', '2026-01-15 04:16:00', '1', NULL),
(9, 10, 3, '2026-01-16', 'active', '2026-01-16 04:00:00', '2026-01-16 04:00:00', '3', NULL),
(10, 10, 4, '2026-01-16', 'active', '2026-01-16 04:01:00', '2026-01-16 04:01:00', '3', NULL),
(11, 11, 3, '2026-01-16', 'active', '2026-01-16 04:05:00', '2026-01-16 04:05:00', '3', NULL),
(12, 11, 4, '2026-01-16', 'active', '2026-01-16 04:06:00', '2026-01-16 04:06:00', '3', NULL),
(13, 12, 5, '2026-01-16', 'active', '2026-01-16 04:10:00', '2026-01-16 04:10:00', '4', NULL),
(14, 12, 6, '2026-01-16', 'active', '2026-01-16 04:11:00', '2026-01-16 04:11:00', '4', NULL),
(15, 13, 5, '2026-01-16', 'active', '2026-01-16 04:15:00', '2026-01-16 04:15:00', '4', NULL),
(16, 13, 6, '2026-01-16', 'active', '2026-01-16 04:16:00', '2026-01-16 04:16:00', '4', NULL),
(17, 14, 1, '2026-01-17', 'completed', '2026-01-17 04:00:00', '2026-04-30 10:00:00', '6', '2026-04-30 10:00:00'),
(18, 15, 8, '2026-01-17', 'active', '2026-01-17 04:05:00', '2026-01-17 04:05:00', '6', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lectures`
--

CREATE TABLE `lectures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `schedule` datetime NOT NULL,
  `duration` int(11) NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `room` varchar(255) DEFAULT NULL,
  `lesson_type` enum('theory','practical','lab','workshop') NOT NULL DEFAULT 'theory',
  `qr_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `title`, `description`, `schedule`, `duration`, `course_id`, `created_at`, `updated_at`, `room`, `lesson_type`, `qr_code`) VALUES
(1, 'Laravel Routing and Controllers', 'Build route groups, controllers, validation, and clean request handling.', '2026-02-02 08:30:00', 120, 1, '2026-01-20 03:00:00', '2026-01-20 03:00:00', 'Lab A1', 'practical', NULL),
(2, 'Responsive Layout Foundations', 'Create responsive pages using semantic HTML, modern CSS, and Bootstrap utilities.', '2026-02-03 09:30:00', 90, 2, '2026-01-20 03:10:00', '2026-01-20 03:10:00', 'Studio B2', 'practical', NULL),
(3, 'Usability Testing Workshop', 'Plan and run a small usability test using a course registration prototype.', '2026-02-04 09:00:00', 120, 3, '2026-01-20 03:20:00', '2026-01-20 03:20:00', 'Design Lab', 'workshop', NULL),
(4, 'SQL Joins and Query Design', 'Practice joins, aggregates, filtering, and query readability for reporting screens.', '2026-02-05 10:30:00', 120, 4, '2026-01-20 03:30:00', '2026-01-20 03:30:00', 'Lab C3', 'lab', NULL),
(5, 'IP Addressing and Subnetting', 'Work through IPv4 addressing, subnet masks, and network planning examples.', '2026-02-06 08:30:00', 90, 5, '2026-01-20 03:40:00', '2026-01-20 03:40:00', 'Network Lab', 'theory', NULL),
(6, 'Agile Sprint Planning', 'Estimate tasks, define sprint goals, and assign responsibilities for a team project.', '2026-02-09 13:00:00', 90, 6, '2026-01-20 03:50:00', '2026-01-20 03:50:00', 'Room 204', 'workshop', NULL),
(7, 'Logic and Proof Techniques', 'Apply propositional logic, truth tables, and introductory proof strategies.', '2026-02-10 08:30:00', 90, 7, '2026-01-20 04:00:00', '2026-01-20 04:00:00', 'Lecture Hall 1', 'theory', NULL),
(8, 'Flutter Widgets and Navigation', 'Build screen layouts, compose widgets, and configure basic app navigation.', '2026-02-11 10:30:00', 120, 8, '2026-01-20 04:10:00', '2026-01-20 04:10:00', 'Mobile Lab', 'practical', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_19_182017_add_role_to_users_table', 2),
(5, '2025_11_19_182051_create_courses_table', 2),
(6, '2025_11_19_182059_create_lectures_table', 2),
(7, '2025_11_19_182105_create_enrollments_table', 2),
(8, '2025_11_19_182111_create_attendances_table', 2),
(9, '2025_11_19_195536_add_semester_to_courses_table', 3),
(10, '2025_11_19_201325_add_semester_to_enrollments_table', 4),
(11, '2025_11_21_021041_add_fields_to_lectures_table', 5),
(12, '2025_11_22_014844_add_student_id_to_users_table', 6),
(15, '2025_11_22_015915_add_course_id_to_attendances_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- No session data is imported. Laravel will create fresh sessions at runtime.
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `lecturer_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','lecturer','student') NOT NULL DEFAULT 'student',
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `student_id`, `lecturer_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `phone`, `address`, `department`, `bio`, `profile_picture`, `date_of_birth`) VALUES
(1, NULL, NULL, 'System Administrator', 'admin@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-01 08:00:00', '2026-01-01 08:00:00', 'admin', '0700000001', 'Administration Office, Main Campus', 'Administration', 'Main administrator account for the attendance system.', NULL, NULL),
(2, NULL, 'LEC-ICT-001', 'Lecturer One', 'lecturer.one@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-02 08:00:00', '2026-01-02 08:00:00', 'lecturer', '0711002001', 'Faculty of Computing, Main Campus', 'Software Engineering', 'Demo lecturer account for web development courses.', NULL, '1988-03-14'),
(3, NULL, 'LEC-ICT-002', 'Lecturer Two', 'lecturer.two@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-02 08:05:00', '2026-01-02 08:05:00', 'lecturer', '0711002002', 'Faculty of Computing, Main Campus', 'Information Systems', 'Demo lecturer account for HCI and database courses.', NULL, '1990-07-22'),
(4, NULL, 'LEC-ICT-003', 'Lecturer Three', 'lecturer.three@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-02 08:10:00', '2026-01-02 08:10:00', 'lecturer', '0711002003', 'Network Laboratory, Main Campus', 'Network Engineering', 'Demo lecturer account for networking and project practice.', NULL, '1987-11-04'),
(5, NULL, 'LEC-ICT-004', 'Lecturer Four', 'lecturer.four@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-02 08:15:00', '2026-01-02 08:15:00', 'lecturer', '0711002004', 'Mathematics Unit, Main Campus', 'Computing Mathematics', 'Demo lecturer account for mathematics and mobile development.', NULL, '1985-05-30'),
(6, 'STU-ICT-2026-001', NULL, 'Student One', 'student.one@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-03 08:00:00', '2026-01-03 08:00:00', 'student', '0722001001', 'Student Residence A, Main Campus', 'Information Technology', 'Demo student account for semester six courses.', NULL, '2002-01-02'),
(7, 'STU-ICT-2026-002', NULL, 'Student Two', 'student.two@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-03 08:05:00', '2026-01-03 08:05:00', 'student', '0722001002', 'Student Residence A, Main Campus', 'Information Technology', 'Demo student account for semester six courses.', NULL, '2001-07-26'),
(8, 'STU-ICT-2026-003', NULL, 'Student Three', 'student.three@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-03 08:10:00', '2026-01-03 08:10:00', 'student', '0722001003', 'Student Residence B, Main Campus', 'Information Technology', 'Demo student account for first-year courses.', NULL, '2003-02-18'),
(9, 'STU-ICT-2026-004', NULL, 'Student Four', 'student.four@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-03 08:15:00', '2026-01-03 08:15:00', 'student', '0722001004', 'Student Residence B, Main Campus', 'Information Technology', 'Demo student account for first-year courses.', NULL, '2003-09-08'),
(10, 'STU-ICT-2026-005', NULL, 'Student Five', 'student.five@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-03 08:20:00', '2026-01-03 08:20:00', 'student', '0722001005', 'Student Residence C, Main Campus', 'Information Systems', 'Demo student account for second-year courses.', NULL, '2002-12-12'),
(11, 'STU-ICT-2026-006', NULL, 'Student Six', 'student.six@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-03 08:25:00', '2026-01-03 08:25:00', 'student', '0722001006', 'Student Residence C, Main Campus', 'Information Systems', 'Demo student account for second-year courses.', NULL, '2002-05-19'),
(12, 'STU-ICT-2026-007', NULL, 'Student Seven', 'student.seven@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-03 08:30:00', '2026-01-03 08:30:00', 'student', '0722001007', 'Student Residence D, Main Campus', 'Network Engineering', 'Demo student account for networking courses.', NULL, '2001-10-03'),
(13, 'STU-ICT-2026-008', NULL, 'Student Eight', 'student.eight@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-03 08:35:00', '2026-01-03 08:35:00', 'student', '0722001008', 'Student Residence D, Main Campus', 'Network Engineering', 'Demo student account for networking courses.', NULL, '2001-04-25'),
(14, 'STU-ICT-2026-009', NULL, 'Student Nine', 'student.nine@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-03 08:40:00', '2026-01-03 08:40:00', 'student', '0722001009', 'Student Residence E, Main Campus', 'Software Engineering', 'Demo student account for final-year courses.', NULL, '2000-11-11'),
(15, 'STU-ICT-2026-010', NULL, 'Student Ten', 'student.ten@mycampus.test', '2026-01-01 08:00:00', '$2y$10$AGUT8VlvUTPpfEwqKGxuquNQL1C9G9O/ksYs.D/giHCX25/URlBRW', NULL, '2026-01-03 08:45:00', '2026-01-03 08:45:00', 'student', '0722001010', 'Student Residence E, Main Campus', 'Software Engineering', 'Demo student account for final-year courses.', NULL, '2000-06-17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_student_id_foreign` (`student_id`),
  ADD KEY `attendances_lecture_id_foreign` (`lecture_id`),
  ADD KEY `attendances_course_id_foreign` (`course_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_code_unique` (`code`),
  ADD KEY `courses_lecturer_id_foreign` (`lecturer_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollments_student_id_foreign` (`student_id`),
  ADD KEY `enrollments_course_id_foreign` (`course_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lectures`
--
ALTER TABLE `lectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lectures_course_id_foreign` (`course_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_student_id_unique` (`student_id`),
  ADD UNIQUE KEY `users_lecturer_id_unique` (`lecturer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_lecture_id_foreign` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`),
  ADD CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_lecturer_id_foreign` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `lectures`
--
ALTER TABLE `lectures`
  ADD CONSTRAINT `lectures_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
