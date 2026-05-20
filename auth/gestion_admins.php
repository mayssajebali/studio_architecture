<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: ../accueil.php');
    exit;
}
require_once '../db.php';
/** @var PDO $pdo */

// Récupérer tous les administrateurs
$stmt = $pdo->query("SELECT id, nom, prenom, email, telephone, created_at FROM users WHERE role = 'admin' ORDER BY created_at DESC");
$admins = $stmt->fetchAll();

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Administrateurs – GreenHome</title>
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
                <span>Gestion des administrateurs</span>
            </div>

    <div class="page-header">
        <h1>Gestion des Administrateurs</h1>
        <p>Gérez les comptes administrateurs du système</p>
    </div>

    <?php if ($msg === 'ajout'): ?>
        <div class="alert-gh success">
            <i class="bi bi-check-circle"></i>
            Administrateur ajouté avec succès.
        </div>
    <?php elseif ($msg === 'modif'): ?>
        <div class="alert-gh success">
            <i class="bi bi-check-circle"></i>
            Administrateur modifié avec succès.
        </div>
    <?php elseif ($msg === 'suppression'): ?>
        <div class="alert-gh success">
            <i class="bi bi-check-circle"></i>
            Administrateur supprimé avec succès.
        </div>
    <?php elseif ($msg === 'erreur'): ?>
        <div class="alert-gh error">
            <i class="bi bi-exclamation-triangle"></i>
            Une erreur s'est produite. Veuillez réessayer.
        </div>
    <?php elseif ($msg === 'email_existe'): ?>
        <div class="alert-gh error">
            <i class="bi bi-exclamation-triangle"></i>
            Cet email est déjà utilisé.
        </div>
    <?php elseif ($msg === 'dernier_admin'): ?>
        <div class="alert-gh error">
            <i class="bi bi-exclamation-triangle"></i>
            Impossible de supprimer le dernier administrateur.
        </div>
    <?php endif; ?>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-content">
                <div class="stat-num"><?= count($admins) ?></div>
                <div class="stat-label">Administrateur<?= count($admins) > 1 ? 's' : '' ?></div>
            </div>
        </div>
    </div>

    <div class="card-gh">
        <div class="card-header-gh">
            <h2>Liste des administrateurs</h2>
            <a href="ajouter_admin.php" class="btn-primary-gh">
                <i class="bi bi-plus-lg"></i>
                Ajouter un administrateur
            </a>
        </div>

        <?php if (empty($admins)): ?>
            <div class="empty-state">
                <i class="bi bi-person-x"></i>
                <h3>Aucun administrateur</h3>
                <p>Commencez par ajouter un administrateur.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admins-table">
                    <thead>
                        <tr>
                            <th>Administrateur</th>
                            <th>Téléphone</th>
                            <th>Date de création</th>
                            <th>Rôle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td>
                                <div class="admin-name">
                                    <?= htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']) ?>
                                </div>
                                <div class="admin-email">
                                    <?= htmlspecialchars($admin['email']) ?>
                                </div>
                            </td>
                            <td style="font-size: 13px;">
                                <?= htmlspecialchars($admin['telephone'] ?: '—') ?>
                            </td>
                            <td style="font-size: 12px; color: #6D6D6D;">
                                <?= date('d/m/Y', strtotime($admin['created_at'])) ?>
                            </td>
                            <td>
                                <span class="badge-admin">Administrateur</span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="modifier_admin.php?id=<?= $admin['id'] ?>" class="btn-edit">
                                        <i class="bi bi-pencil"></i>
                                        Modifier
                                    </a>
                                    <?php if (count($admins) > 1): ?>
                                    <form method="POST" action="admin_actions.php" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ?')">
                                        <input type="hidden" name="action" value="supprimer_admin">
                                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                        <button type="submit" class="btn-del">
                                            <i class="bi bi-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="admin_sidebar_script.js"></script>
</body>
</html>
