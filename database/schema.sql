-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `wap-test`;

-- Use the database
USE `wap-test`;

-- Create users table with extended profile fields
CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `fullname` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `phone` varchar(50) DEFAULT NULL,
    `phone_secondary` varchar(50) DEFAULT NULL,
    `course` text DEFAULT NULL,
    `course_secondary` varchar(255) DEFAULT NULL,
    `course_tertiary` varchar(255) DEFAULT NULL,
    `profile_picture` varchar(500) DEFAULT NULL,
    `profile_picture_path` varchar(1000) DEFAULT NULL,
    `address` text DEFAULT NULL,
    `date_of_birth` date DEFAULT NULL,
    `gender` enum('Male','Female','Other') DEFAULT NULL,
    `status` enum('Active','Inactive','Suspended') DEFAULT 'Active',
    `email_verified` boolean DEFAULT FALSE,
    `last_login` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_email` (`email`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;