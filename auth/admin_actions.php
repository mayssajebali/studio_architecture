<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: ../accueil.php');
    exit;
}
require_once '../db.php';
/** @var PDO $pdo */
$action = $_POST['action'] ?? '';


function uploadImage($file, $oldPath = '') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return $oldPath;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allowed)) return $oldPath;

    $dir = __DIR__ . '/../images/styles/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $filename = uniqid('style_') . '.' . $ext;
    move_uploaded_file($file['tmp_name'], $dir . $filename);
    return 'images/styles/' . $filename;
}


if ($action === 'ajouter') {
    $imagePath = uploadImage($_FILES['image'] ?? null);
    $stmt = $pdo->prepare("
        INSERT INTO styles (titre, description, materiau, palette, mobilier, caracteristiques, image_path, ordre)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_POST['titre'], $_POST['description'], $_POST['materiau'],
        $_POST['palette'], $_POST['mobilier'], $_POST['caracteristiques'],
        $imagePath, (int)$_POST['ordre']
    ]);
    header('Location: dashboard_admin.php?section=styles&msg=ajout');
    exit;
}

if ($action === 'modifier') {
    $id = (int)$_POST['id'];
    $oldPath = $_POST['image_actuelle'] ?? '';
    $imagePath = uploadImage($_FILES['image'] ?? null, $oldPath);
    $stmt = $pdo->prepare("
        UPDATE styles SET titre=?, description=?, materiau=?, palette=?, mobilier=?, caracteristiques=?, image_path=?, ordre=?
        WHERE id=?
    ");
    $stmt->execute([
        $_POST['titre'], $_POST['description'], $_POST['materiau'],
        $_POST['palette'], $_POST['mobilier'], $_POST['caracteristiques'],
        $imagePath, (int)$_POST['ordre'], $id
    ]);
    header('Location: dashboard_admin.php?section=styles&msg=modif');
    exit;
}

if ($action === 'supprimer') {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("SELECT image_path FROM styles WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && !empty($row['image_path']) && strpos($row['image_path'], 'images/styles/') === 0) {
        $file = __DIR__ . '/../' . $row['image_path'];
        if (file_exists($file)) unlink($file);
    }
    $pdo->prepare("DELETE FROM styles WHERE id = ?")->execute([$id]);
    header('Location: dashboard_admin.php?section=styles&msg=suppression');
    exit;
}

if ($action === 'traiter_demande') {
    $id = (int)$_POST['id'];
    $statut = $_POST['statut'];
    $reponse = trim($_POST['reponse'] ?? '');
    $stmt = $pdo->prepare("UPDATE demandes_contact SET statut = ?, reponse_admin = ? WHERE id = ?");
    $stmt->execute([$statut, $reponse ?: null, $id]);
    header('Location: dashboard_admin.php?section=demandes');
    exit;
}

if ($action === 'moderer_avis') {
    $id = (int)$_POST['id'];
    $statut = in_array($_POST['statut'], ['approuve', 'masque', 'en_attente']) ? $_POST['statut'] : 'en_attente';
    $pdo->prepare("UPDATE temoignages SET statut = ? WHERE id = ?")->execute([$statut, $id]);
    header('Location: dashboard_admin.php?section=avis');
    exit;
}

if ($action === 'supprimer_avis') {
    $id = (int)$_POST['id'];
    $pdo->prepare("DELETE FROM temoignages WHERE id = ?")->execute([$id]);
    header('Location: dashboard_admin.php?section=avis');
    exit;
}


if ($action === 'ajouter_avis') {
    $client_name = trim($_POST['client_name']);
    $rating = (int)$_POST['rating'];
    $content = trim($_POST['content']);
    $date = $_POST['date'] ?? date('Y-m-d');
    $statut = 'approuve'; 

    $stmt = $pdo->prepare("
        INSERT INTO temoignages (client_name, content, rating, date, statut)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$client_name, $content, $rating, $date, $statut]);

    header('Location: dashboard_admin.php?section=avis&msg=avis_ajoute');
    exit;
}

header('Location: dashboard_admin.php');
exit;
?>