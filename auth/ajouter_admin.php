<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: ../accueil.php');
    exit;
}
require_once '../db.php';
/** @var PDO $pdo */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Administrateur – GreenHome</title>
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
        <span>Ajouter</span>
    </div>

    <div class="page-header">
        <h1>Ajouter un Administrateur</h1>
        <p>Créez un nouveau compte administrateur</p>
    </div>

    <div class="form-card">
        <form method="POST" action="admin_actions.php" novalidate>
            <input type="hidden" name="action" value="ajouter_admin">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">
                        Prénom <span class="required">*</span>
                    </label>
                    <input type="text" name="prenom" class="form-control" placeholder="Jean" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Nom <span class="required">*</span>
                    </label>
                    <input type="text" name="nom" class="form-control" placeholder="Dupont" required>
                </div>

                <div class="col-12">
                    <label class="form-label">
                        Email <span class="required">*</span>
                    </label>
                    <input type="email" name="email" class="form-control" placeholder="admin@greenhome.com" required>
                    <div class="form-hint">Cet email sera utilisé pour la connexion</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Téléphone
                    </label>
                    <input type="tel" name="telephone" class="form-control" placeholder="+216 XX XXX XXX">
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        Mot de passe <span class="required">*</span>
                    </label>
                    <input type="password" name="mot_de_passe" class="form-control" placeholder="••••••••" required minlength="6">
                    <div class="form-hint">Minimum 6 caractères</div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-lg"></i>
                    Créer l'administrateur
                </button>
                <a href="gestion_admins.php" class="btn-cancel">
                    <i class="bi bi-x-lg"></i>
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
