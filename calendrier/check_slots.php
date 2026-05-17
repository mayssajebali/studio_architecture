<?php
require_once '../db.php';
/** @var PDO $pdo */
header('Content-Type: application/json');
$date = $_GET['date'] ?? '';
if (empty($date)) {
    echo json_encode([]);
    exit;
}
$stmt = $pdo->prepare("SELECT appointment_time FROM appointments WHERE appointment_date = ?");
$stmt->execute([$date]);
$taken = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($taken);
?>