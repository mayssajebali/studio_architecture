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
    header('Location: gestion_clients.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'client'");
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) {
    header('Location: gestion_clients.php?msg=erreur');
    exit;
}

// Récupérer les demandes du client
$stmt = $pdo->prepare("SELECT * FROM demandes_contact WHERE email = ? ORDER BY date_envoi DESC");
$stmt->execute([$client['email']]);
$demandes = $stmt->fetchAll();

$totalDemandes = count($demandes);
$nbAttente = $nbEncours = $nbTermine = $nbArchive = 0;
foreach ($demandes as $row) {
    switch ($row['statut'] ?? '') {
        case 'en_attente': $nbAttente++; break;
        case 'en_cours':   $nbEncours++; break;
        case 'termine':    $nbTermine++; break;
        case 'archive':    $nbArchive++; break;
    }
}

$initiales = strtoupper(mb_substr($client['prenom'], 0, 1) . mb_substr($client['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Client – GreenHome</title>
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
        <a href="gestion_clients.php">Clients</a>
        <i class="bi bi-chevron-right"></i>
        <span><?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?></span>
    </div>

    <div class="client-header">
        <div class="client-avatar"><?= htmlspecialchars($initiales) ?></div>
        <div class="client-info">
            <h1 class="client-name"><?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?></h1>
            <div class="client-email"><?= htmlspecialchars($client['email']) ?></div>
            <span class="client-badge">
                <i class="bi bi-person-check"></i>
                Client actif
            </span>
        </div>
        <form method="POST" action="admin_actions.php" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ? Toutes ses demandes seront également supprimées.')">
            <input type="hidden" name="action" value="supprimer_client">
            <input type="hidden" name="id" value="<?= $client['id'] ?>">
            <button type="submit" class="btn-delete-client">
                <i class="bi bi-trash"></i>
                Supprimer le client
            </button>
        </form>
    </div>

    <div class="content-grid">
        <!-- Informations -->
        <div>
            <div class="card-gh" style="margin-bottom: 20px;">
                <div class="card-header-gh">
                    <h2>Informations personnelles</h2>
                </div>
                <div class="card-body-gh">
                    <div class="info-row">
                        <div class="info-label">Prénom</div>
                        <div class="info-value"><?= htmlspecialchars($client['prenom']) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nom</div>
                        <div class="info-value"><?= htmlspecialchars($client['nom']) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($client['email']) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value"><?= htmlspecialchars($client['telephone'] ?: '—') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ID Client</div>
                        <div class="info-value">#<?= str_pad($client['id'], 5, '0', STR_PAD_LEFT) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Inscrit le</div>
                        <div class="info-value"><?= date('d/m/Y', strtotime($client['created_at'])) ?></div>
                    </div>
                </div>
            </div>

            <div class="card-gh">
                <div class="card-header-gh">
                    <h2>Statistiques</h2>
                </div>
                <div class="card-body-gh">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-num"><?= $totalDemandes ?></div>
                            <div class="stat-label">Total</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-num"><?= $nbAttente ?></div>
                            <div class="stat-label">En attente</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-num"><?= $nbEncours ?></div>
                            <div class="stat-label">En cours</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-num"><?= $nbTermine ?></div>
                            <div class="stat-label">Terminées</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demandes -->
        <div class="card-gh">
            <div class="card-header-gh">
                <h2>Demandes du client (<?= $totalDemandes ?>)</h2>
            </div>
            <?php if (empty($demandes)): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Aucune demande pour ce client.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="demandes-table">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Budget</th>
                                <th>Date</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($demandes as $d): 
                                $sl = 'Inconnu'; $sc = 'pill-unknown';
                                switch ($d['statut'] ?? '') {
                                    case 'en_attente': $sl = 'En attente'; $sc = 'pill-attente'; break;
                                    case 'en_cours':   $sl = 'En cours';   $sc = 'pill-encours'; break;
                                    case 'termine':    $sl = 'Terminé';    $sc = 'pill-termine'; break;
                                    case 'archive':    $sl = 'Archivé';    $sc = 'pill-archive'; break;
                                }
                            ?>
                            <tr>
                                <td style="font-weight: 500; font-size: 13px;">
                                    <?= htmlspecialchars(ucfirst($d['service'] ?? 'Non précisé')) ?>
                                </td>
                                <td style="font-size: 13px;">
                                    <?= htmlspecialchars($d['budget'] ?? 'N/A') ?>
                                </td>
                                <td style="font-size: 12px; color: #6D6D6D;">
                                    <?= isset($d['date_envoi']) ? date('d/m/Y', strtotime($d['date_envoi'])) : '—' ?>
                                </td>
                                <td>
                                    <span class="pill <?= $sc ?>"><?= $sl ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
