<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: ../accueil.php');
    exit;
}
require_once '../db.php';
/** @var PDO $pdo */

$admin_id = $_SESSION['admin_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

if (!$admin) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$msg = $_GET['msg'] ?? '';
$initiales = strtoupper(mb_substr($admin['prenom'], 0, 1) . mb_substr($admin['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil Admin – GreenHome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin_sidebar_styles.css">
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="admin-layout">
    <?php include 'admin_sidebar.php'; ?>
    
    <main class="admin-main">
        <div class="admin-container">
            <div class="breadcrumb-gh">
                <a href="dashboard_admin.php"><i class="bi bi-house-door"></i> Dashboard</a>
                <i class="bi bi-chevron-right"></i>
                <span>Mon profil</span>
            </div>

    <?php if ($msg === 'modif'): ?>
        <div class="alert-gh">
            <i class="bi bi-check-circle"></i>
            Profil modifié avec succès.
        </div>
    <?php elseif ($msg === 'mdp_modifie'): ?>
        <div class="alert-gh">
            <i class="bi bi-check-circle"></i>
            Mot de passe modifié avec succès.
        </div>
    <?php elseif ($msg === 'mdp_different'): ?>
        <div class="alert-gh error">
            <i class="bi bi-exclamation-triangle"></i>
            Les mots de passe ne correspondent pas.
        </div>
    <?php elseif ($msg === 'erreur'): ?>
        <div class="alert-gh error">
            <i class="bi bi-exclamation-triangle"></i>
            Une erreur s'est produite. Veuillez réessayer.
        </div>
    <?php endif; ?>

    <div class="profile-header">
        <div class="profile-avatar"><?= htmlspecialchars($initiales) ?></div>
        <div class="profile-info">
            <h1 class="profile-name"><?= htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']) ?></h1>
            <div class="profile-email"><?= htmlspecialchars($admin['email']) ?></div>
            <span class="profile-badge">
                <i class="bi bi-shield-check"></i>
                Administrateur
            </span>
        </div>
    </div>

    <!-- Informations du compte -->
    <div class="form-card">
        <h2>Informations du compte</h2>
        <div class="info-row">
            <div class="info-label">Identifiant</div>
            <div class="info-value">#<?= str_pad($admin['id'], 5, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Téléphone</div>
            <div class="info-value"><?= htmlspecialchars($admin['telephone'] ?: '—') ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Membre depuis</div>
            <div class="info-value"><?= date('d/m/Y', strtotime($admin['created_at'])) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Rôle</div>
            <div class="info-value">Administrateur</div>
        </div>
    </div>

    <!-- Modifier les informations -->
    <div class="form-card">
        <h2>Modifier mes informations</h2>
        <form method="POST" action="admin_actions.php" novalidate>
            <input type="hidden" name="action" value="modifier_profil_admin">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">
                        Prénom <span class="required">*</span>
                    </label>
                    <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($admin['prenom']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Nom <span class="required">*</span>
                    </label>
                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($admin['nom']) ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label">
                        Email <span class="required">*</span>
                    </label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Téléphone
                    </label>
                    <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($admin['telephone'] ?? '') ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-lg"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <!-- Changer le mot de passe -->
    <div class="form-card">
        <h2>Changer mon mot de passe</h2>
        <div class="info-box">
            <i class="bi bi-info-circle"></i>
            Pour des raisons de sécurité, changez régulièrement votre mot de passe
        </div>
        <form method="POST" action="admin_actions.php" novalidate>
            <input type="hidden" name="action" value="changer_mon_mdp_admin">

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">
                        Nouveau mot de passe <span class="required">*</span>
                    </label>
                    <input type="password" name="nouveau_mdp" class="form-control" placeholder="••••••••" required minlength="6">
                    <div class="form-hint">Minimum 6 caractères</div>
                </div>

                <div class="col-12">
                    <label class="form-label">
                        Confirmer le mot de passe <span class="required">*</span>
                    </label>
                    <input type="password" name="confirmer_mdp" class="form-control" placeholder="••••••••" required minlength="6">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-key"></i>
                    Changer le mot de passe
                </button>
            </div>
        </form>
    </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="admin_sidebar_script.js"></script>
</body>
</html>
