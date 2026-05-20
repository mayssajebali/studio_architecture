<?php
/**
 * Setup script to create the appointments table
 * Run this file once to create the table in your database
 */

require_once 'db.php';
/** @var PDO $pdo */

try {
    // Create appointments table
    $sql = "CREATE TABLE IF NOT EXISTS `appointments` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    
    echo "✅ SUCCESS! The 'appointments' table has been created successfully.<br><br>";
    echo "You can now:<br>";
    echo "1. Delete this file (setup_appointments.php) for security<br>";
    echo "2. Go to your admin dashboard to manage appointments<br>";
    echo "3. Clients can book appointments via calendrier.php<br><br>";
    echo "<a href='auth/dashboard_admin.php?section=rendez-vous'>Go to Appointments Management</a>";
    
} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "<br><br>";
    echo "Please check your database connection in db.php";
}
?>
