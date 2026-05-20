<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: ../accueil.php');
    exit;
}
require_once '../db.php';
/** @var PDO $pdo */

// Récupérer tous les clients
$search = $_GET['search'] ?? '';
$where = ["role = 'client'"];
$params = [];

if ($search) {
    $where[] = "(nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR telephone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql = "SELECT * FROM users WHERE " . implode(" AND ", $where) . " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clients = $stmt->fetchAll();

// Statistiques
$stats = $pdo->query("
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as aujourd_hui,
        COUNT(CASE WHEN YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) THEN 1 END) as cette_semaine,
        COUNT(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN 1 END) as ce_mois
    FROM users WHERE role = 'client'
")->fetch();

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Clients – GreenHome</title>
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
                <span>Gestion des clients</span>
            </div>

    <div class="page-header">
        <h1>Gestion des Clients</h1>
        <p>Consultez et gérez les comptes clients enregistrés</p>
    </div>

    <?php if ($msg === 'suppression'): ?>
        <div class="alert-gh">
            <i class="bi bi-check-circle"></i>
            Client supprimé avec succès.
        </div>
    <?php elseif ($msg === 'erreur'): ?>
        <div class="alert-gh error">
            <i class="bi bi-exclamation-triangle"></i>
            Une erreur s'est produite. Veuillez réessayer.
        </div>
    <?php endif; ?>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-content">
                <div class="stat-num"><?= $stats['total'] ?></div>
                <div class="stat-label">Total clients</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon today">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-num"><?= $stats['aujourd_hui'] ?></div>
                <div class="stat-label">Aujourd'hui</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon week">
                <i class="bi bi-calendar-week"></i>
            </div>
            <div class="stat-content">
                <div class="stat-num"><?= $stats['cette_semaine'] ?></div>
                <div class="stat-label">Cette semaine</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon month">
                <i class="bi bi-calendar-month"></i>
            </div>
            <div class="stat-content">
                <div class="stat-num"><?= $stats['ce_mois'] ?></div>
                <div class="stat-label">Ce mois</div>
            </div>
        </div>
    </div>

    <div class="card-gh">
        <div class="card-header-gh">
            <h2>Liste des clients (<?= count($clients) ?>)</h2>
            <form method="GET" action="" class="search-box">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher un client...">
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i>
                </button>
                <?php if ($search): ?>
                <a href="gestion_clients.php" class="btn-search" style="background:#6D6D6D;">
                    <i class="bi bi-x-lg"></i>
                </a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (empty($clients)): ?>
            <div class="empty-state">
                <i class="bi bi-person-x"></i>
                <h3>Aucun client trouvé</h3>
                <p><?= $search ? 'Essayez une autre recherche.' : 'Aucun client enregistré pour le moment.' ?></p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="clients-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Téléphone</th>
                            <th>Date d'inscription</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): 
                            $initiales = strtoupper(mb_substr($client['prenom'], 0, 1) . mb_substr($client['nom'], 0, 1));
                        ?>
                        <tr>
                            <td>
                                <div class="client-info">
                                    <div class="client-avatar"><?= htmlspecialchars($initiales) ?></div>
                                    <div>
                                        <div class="client-name">
                                            <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
                                        </div>
                                        <div class="client-email">
                                            <?= htmlspecialchars($client['email']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size: 13px;">
                                <?= htmlspecialchars($client['telephone'] ?: '—') ?>
                            </td>
                            <td style="font-size: 12px; color: #6D6D6D;">
                                <?= date('d/m/Y', strtotime($client['created_at'])) ?>
                            </td>
                            <td>
                                <span class="badge-client">Actif</span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="detail_client.php?id=<?= $client['id'] ?>" class="btn-view">
                                        <i class="bi bi-eye"></i>
                                        Voir
                                    </a>
                                    <form method="POST" action="admin_actions.php" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ? Toutes ses demandes seront également supprimées.')">
                                        <input type="hidden" name="action" value="supprimer_client">
                                        <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                        <button type="submit" class="btn-del">
                                            <i class="bi bi-trash"></i>
                                            Supprimer
                                        </button>
                                    </form>
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
