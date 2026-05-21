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

// ═══════════════════════════════════════════════════════════
// GESTION DES ADMINISTRATEURS
// ═══════════════════════════════════════════════════════════

if ($action === 'ajouter_admin') {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: gestion_admins.php?msg=email_existe');
        exit;
    }

    // Hasher le mot de passe
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Insérer le nouvel admin
    $stmt = $pdo->prepare("
        INSERT INTO users (nom, prenom, email, telephone, mot_de_passe, role, created_at)
        VALUES (?, ?, ?, ?, ?, 'admin', NOW())
    ");
    $stmt->execute([$nom, $prenom, $email, $telephone, $mot_de_passe_hash]);

    header('Location: gestion_admins.php?msg=ajout');
    exit;
}

if ($action === 'modifier_admin') {
    $id = (int)$_POST['id'];
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone'] ?? '');

    // Vérifier si l'email existe déjà (sauf pour cet admin)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        header('Location: modifier_admin.php?id=' . $id . '&msg=email_existe');
        exit;
    }

    // Mettre à jour l'admin
    $stmt = $pdo->prepare("
        UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ?
        WHERE id = ? AND role = 'admin'
    ");
    $stmt->execute([$nom, $prenom, $email, $telephone, $id]);

    header('Location: gestion_admins.php?msg=modif');
    exit;
}

if ($action === 'changer_mdp_admin') {
    $id = (int)$_POST['id'];
    $nouveau_mdp = trim($_POST['nouveau_mdp'] ?? '');
    $confirmer_mdp = trim($_POST['confirmer_mdp'] ?? '');

    if (empty($nouveau_mdp)) {
        header('Location: modifier_admin.php?id=' . $id . '&msg=erreur');
        exit;
    }

    if ($nouveau_mdp !== $confirmer_mdp) {
        header('Location: modifier_admin.php?id=' . $id . '&msg=mdp_different');
        exit;
    }

    $mot_de_passe_hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET mot_de_passe = ? WHERE id = ? AND role = 'admin'");
    $stmt->execute([$mot_de_passe_hash, $id]);

    header('Location: modifier_admin.php?id=' . $id . '&msg=mdp_modifie');
    exit;
}

if ($action === 'supprimer_admin') {
    $id = (int)$_POST['id'];

    // Vérifier qu'il reste au moins un admin
    $count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    if ($count <= 1) {
        header('Location: gestion_admins.php?msg=dernier_admin');
        exit;
    }

    // Supprimer l'admin
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
    $stmt->execute([$id]);

    header('Location: gestion_admins.php?msg=suppression');
    exit;
}

// ═══════════════════════════════════════════════════════════
// PROFIL ADMIN (MOI-MÊME)
// ═══════════════════════════════════════════════════════════

if ($action === 'modifier_profil_admin') {
    $admin_id = $_SESSION['admin_id'];
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone'] ?? '');

    // Vérifier si l'email existe déjà (sauf pour cet admin)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $admin_id]);
    if ($stmt->fetch()) {
        header('Location: profil_admin.php?msg=email_existe');
        exit;
    }

    // Mettre à jour le profil
    $stmt = $pdo->prepare("
        UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ?
        WHERE id = ? AND role = 'admin'
    ");
    $stmt->execute([$nom, $prenom, $email, $telephone, $admin_id]);

    // Mettre à jour la session
    $_SESSION['admin_nom'] = $nom;

    header('Location: profil_admin.php?msg=modif');
    exit;
}

if ($action === 'changer_mon_mdp_admin') {
    $admin_id = $_SESSION['admin_id'];
    $nouveau_mdp = trim($_POST['nouveau_mdp'] ?? '');
    $confirmer_mdp = trim($_POST['confirmer_mdp'] ?? '');

    if (empty($nouveau_mdp)) {
        header('Location: profil_admin.php?msg=erreur');
        exit;
    }

    if ($nouveau_mdp !== $confirmer_mdp) {
        header('Location: profil_admin.php?msg=mdp_different');
        exit;
    }

    $mot_de_passe_hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET mot_de_passe = ? WHERE id = ? AND role = 'admin'");
    $stmt->execute([$mot_de_passe_hash, $admin_id]);

    header('Location: profil_admin.php?msg=mdp_modifie');
    exit;
}

// ═══════════════════════════════════════════════════════════
// GESTION DES CLIENTS
// ═══════════════════════════════════════════════════════════

if ($action === 'supprimer_client') {
    $id = (int)$_POST['id'];

    // Supprimer d'abord les demandes du client
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ? AND role = 'client'");
    $stmt->execute([$id]);
    $client = $stmt->fetch();

    if ($client) {
        // Supprimer les demandes liées à cet email
        $stmt = $pdo->prepare("DELETE FROM demandes_contact WHERE email = ?");
        $stmt->execute([$client['email']]);

        // Supprimer le client
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'client'");
        $stmt->execute([$id]);

        header('Location: gestion_clients.php?msg=suppression');
        exit;
    }

    header('Location: gestion_clients.php?msg=erreur');
    exit;
}

header('Location: dashboard_admin.php');
exit;
?>