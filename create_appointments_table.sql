-- Create appointments table for managing client appointments
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `service` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `meeting_type` enum('presentiel','visio','domicile') DEFAULT 'presentiel',
  `budget_range` varchar(10) DEFAULT NULL,
  `elements` text DEFAULT NULL,
  `newsletter` tinyint(1) DEFAULT 0,
  `reminder` tinyint(1) DEFAULT 0,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(10) NOT NULL,
  `status` enum('en_attente','confirme','termine','annule') DEFAULT 'en_attente',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `appointment_date` (`appointment_date`),
  KEY `status` (`status`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
