<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: ../accueil.php');
    exit;
}
require_once '../db.php';
/** @var PDO $pdo */

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: gestion_admins.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
$stmt->execute([$id]);
$admin = $stmt->fetch();

if (!$admin) {
    header('Location: gestion_admins.php?msg=erreur');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Administrateur – GreenHome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="admin-container">
    <div class="breadcrumb-gh">
        <a href="dashboard_admin.php"><i class="bi bi-house-door"></i> Dashboard</a>
        <i class="bi bi-chevron-right"></i>
        <a href="gestion_admins.php">Administrateurs</a>
        <i class="bi bi-chevron-right"></i>
        <span>Modifier</span>
    </div>

    <div class="page-header">
        <h1>Modifier un Administrateur</h1>
        <p>Modifiez les informations de l'administrateur</p>
    </div>

    <!-- Informations générales -->
    <div class="form-card">
        <h2>Informations générales</h2>
        <form method="POST" action="admin_actions.php" novalidate>
            <input type="hidden" name="action" value="modifier_admin">
            <input type="hidden" name="id" value="<?= $admin['id'] ?>">

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
                    <div class="form-hint">Cet email est utilisé pour la connexion</div>
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
                <a href="gestion_admins.php" class="btn-cancel">
                    <i class="bi bi-x-lg"></i>
                    Annuler
                </a>
            </div>
        </form>
    </div>

    <!-- Changer le mot de passe -->
    <div class="form-card">
        <h2>Changer le mot de passe</h2>
        <div class="info-box">
            <i class="bi bi-info-circle"></i>
            Laissez vide si vous ne souhaitez pas modifier le mot de passe
        </div>
        <form method="POST" action="admin_actions.php" novalidate>
            <input type="hidden" name="action" value="changer_mdp_admin">
            <input type="hidden" name="id" value="<?= $admin['id'] ?>">

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">
                        Nouveau mot de passe
                    </label>
                    <input type="password" name="nouveau_mdp" class="form-control" placeholder="••••••••" minlength="6">
                    <div class="form-hint">Minimum 6 caractères</div>
                </div>

                <div class="col-12">
                    <label class="form-label">
                        Confirmer le mot de passe
                    </label>
                    <input type="password" name="confirmer_mdp" class="form-control" placeholder="••••••••" minlength="6">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
