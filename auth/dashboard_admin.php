<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: ../accueil.php');
    exit;
}
require_once '../db.php';
/** @var PDO $pdo */

$styles = [];
try {
    $styles = $pdo->query("SELECT * FROM styles ORDER BY ordre ASC")->fetchAll();
} catch (PDOException $e) {
    $styles = [];
}

$msg = $_GET['msg'] ?? '';
$active_section = $_GET['section'] ?? 'demandes';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin – GreenHome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { padding-top: 80px; font-family: "Inter", sans-serif; background: #f8f6f3; margin: 0; }
        .admin-layout { display: flex; min-height: calc(100vh - 80px); }
        .sidebar { width: 240px; min-width: 240px; background: #fff; border-right: 1px solid #E6E6E6; display: flex; flex-direction: column; position: sticky; top: 80px; height: calc(100vh - 80px); overflow-y: auto; transition: width .25s; z-index: 100; }
        .sidebar.collapsed { width: 64px; min-width: 64px; }
        .sidebar-brand { padding: 24px 20px 16px; border-bottom: 1px solid #E6E6E6; display: flex; align-items: center; gap: 10px; overflow: hidden; white-space: nowrap; }
        .sidebar-brand-icon { width: 32px; height: 32px; min-width: 32px; background: #1a1a1a; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 15px; }
        .sidebar-brand-text { font-family: "Cinzel", serif; font-size: 13px; font-weight: 600; color: #1a1a1a; letter-spacing: .5px; transition: opacity .2s; }
        .sidebar.collapsed .sidebar-brand-text { opacity: 0; pointer-events: none; }
        .sidebar-nav { flex: 1; padding: 16px 0; }
        .sidebar-section-label { font-size: 10px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: #b8a99a; padding: 8px 20px 4px; white-space: nowrap; overflow: hidden; transition: opacity .2s; }
        .sidebar.collapsed .sidebar-section-label { opacity: 0; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 10px 20px; text-decoration: none; color: #555; font-size: 14px; border-left: 3px solid transparent; transition: all .15s; cursor: pointer; white-space: nowrap; overflow: hidden; }
        .nav-item:hover { background: #f8f6f3; color: #1a1a1a; }
        .nav-item.active { background: #f8f6f3; color: #1a1a1a; border-left-color: #b8a99a; font-weight: 500; }
        .nav-item i { font-size: 18px; min-width: 20px; text-align: center; flex-shrink: 0; }
        .nav-item-label { transition: opacity .2s; }
        .sidebar.collapsed .nav-item-label { opacity: 0; }
        .nav-badge { margin-left: auto; background: #1a1a1a; color: #fff; font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 10px; }
        .sidebar-toggle { padding: 12px 20px; border-top: 1px solid #E6E6E6; display: flex; justify-content: flex-end; }
        .sidebar.collapsed .sidebar-toggle { justify-content: center; }
        .toggle-btn { background: none; border: 1px solid #E6E6E6; border-radius: 4px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6D6D6D; font-size: 14px; }
        .admin-main { flex: 1; min-width: 0; padding: 40px; overflow-x: hidden; }
        .admin-page-header { margin-bottom: 32px; }
        .admin-page-header h1 { font-family: "Cinzel", serif; font-size: 28px; color: #1a1a1a; margin: 0 0 4px; }
        .admin-page-header p { color: #6D6D6D; font-size: 13px; margin: 0; }
        .admin-section { display: none; }
        .admin-section.active { display: block; }
        .style-card { background: #fff; border: 1px solid #E6E6E6; border-radius: 8px; overflow: hidden; margin-bottom: 24px; transition: 0.2s; }
        .style-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,.08); }
        .style-card img { width: 100%; height: 200px; object-fit: cover; }
        .style-card .card-body { padding: 20px; }
        .style-card h5 { font-family: "Cinzel", serif; font-size: 18px; margin-bottom: 8px; }
        .btn-edit { background: #b8a99a; color: #fff; border: none; padding: 6px 14px; border-radius: 4px; font-size: 13px; cursor: pointer; }
        .btn-edit:hover { background: #a8998a; }
        .btn-del { background: #fff; color: #c0392b; border: 1px solid #c0392b; padding: 6px 14px; border-radius: 4px; cursor: pointer; }
        .btn-del:hover { background: #c0392b; color: #fff; }
        .form-section { background: #fff; border: 1px solid #E6E6E6; border-radius: 8px; padding: 36px; margin-bottom: 40px; }
        .form-section h2 { font-family: "Cinzel", serif; font-size: 22px; border-bottom: 2px solid #b8a99a; display: inline-block; padding-bottom: 12px; margin-bottom: 24px; }
        .form-label { font-size: 13px; font-weight: 500; color: #555; }
        .form-control, .form-select { border: 1px solid #E6E6E6; border-radius: 4px; padding: 10px 14px; font-size: 14px; }
        .btn-submit { background: #1a1a1a; color: #fff; border: none; padding: 12px 32px; border-radius: 4px; cursor: pointer; }
        .alert-gh { background: #f0ebe6; border: 1px solid #b8a99a; color: #5a4a3a; border-radius: 4px; padding: 12px 20px; margin-bottom: 24px; }
        .demandes-table { width: 100%; border-collapse: collapse; }
        .demandes-table th { background: #f8f6f3; padding: 12px 16px; font-size: 12px; text-transform: uppercase; text-align: left; }
        .demandes-table td { padding: 14px 16px; border-bottom: 1px solid #f0ebe6; }
        .statut-pill { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-attente { background:#fff3cd; color:#856404; }
        .badge-encours { background:#cfe2ff; color:#084298; }
        .badge-termine { background:#d1e7dd; color:#0f5132; }
        .badge-archive { background:#f0ebe6; color:#6D6D6D; }
        .stat-badge { border: 1px solid #E6E6E6; border-radius: 8px; padding: 16px 20px; text-align: center; }
        .stat-num { font-family: "Cinzel", serif; font-size: 28px; font-weight: 500; }
        .filter-tab { padding: 6px 16px; border-radius: 20px; font-size: 13px; text-decoration: none; color: #555; border: 1px solid #E6E6E6; background: #fff; display: inline-block; }
        .filter-tab.tab-active { background:#1a1a1a; color:#fff; border-color:#1a1a1a; }
        .empty-section { text-align: center; padding: 80px 40px; color: #6D6D6D; }
        .empty-section i { font-size: 48px; opacity: 0.3; display: block; margin-bottom: 16px; }
        .mobile-toggle { display: none; position: fixed; bottom: 24px; right: 24px; background: #1a1a1a; color: #fff; border: none; border-radius: 50%; width: 48px; height: 48px; align-items: center; justify-content: center; font-size: 20px; cursor: pointer; z-index: 200; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .25s; position: fixed; }
            .sidebar.mobile-open { transform: translateX(0); }
            .mobile-toggle { display: flex; }
            .admin-main { padding: 20px; }
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="admin-layout">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon"><i class="bi bi-house-heart"></i></div>
            <span class="sidebar-brand-text">Admin</span>
        </div>
        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Gestion</div>
            <a class="nav-item <?= $active_section === 'demandes' ? 'active' : '' ?>" onclick="showSection('demandes')" data-tooltip="Demandes clients">
                <i class="bi bi-envelope"></i><span class="nav-item-label">Demandes clients</span>
                <?php 
                $nb_attente_nav = 0;
                try { $nb_attente_nav = $pdo->query("SELECT COUNT(*) FROM contact_requests WHERE statut='en_attente'")->fetchColumn(); } catch(Exception $e) {}
                if ($nb_attente_nav > 0): ?>
                <span class="nav-badge"><?= $nb_attente_nav ?></span>
                <?php endif; ?>
            </a>
            <a class="nav-item <?= $active_section === 'styles' ? 'active' : '' ?>" onclick="showSection('styles')" data-tooltip="Styles">
                <i class="bi bi-palette"></i><span class="nav-item-label">Styles</span>
                <span class="nav-badge" style="background:#f0ebe6;color:#b8a99a;"><?= count($styles) ?></span>
            </a>
            <div class="sidebar-section-label" style="margin-top:8px;">Contenu</div>
            <a class="nav-item <?= $active_section === 'avis' ? 'active' : '' ?>" onclick="showSection('avis')" data-tooltip="Avis">
                <i class="bi bi-star"></i><span class="nav-item-label">Avis</span>
            </a>
           <a class="nav-item" href="gestion_portfolio.php" data-tooltip="Portfolio">
    <i class="bi bi-images"></i><span class="nav-item-label">Portfolio</span>
</a>
        </nav>
        <div class="sidebar-toggle">
            <button class="toggle-btn" onclick="toggleSidebar()"><i class="bi bi-chevron-left" id="toggle-chevron"></i></button>
        </div>
    </aside>

    <main class="admin-main">
        <?php if ($msg === 'ajout'): ?>
            <div class="alert-gh"><i class="bi bi-check-circle"></i> Style ajouté.</div>
        <?php elseif ($msg === 'modif'): ?>
            <div class="alert-gh"><i class="bi bi-check-circle"></i> Style modifié.</div>
        <?php elseif ($msg === 'suppression'): ?>
            <div class="alert-gh"><i class="bi bi-check-circle"></i> Style supprimé.</div>
        <?php elseif ($msg === 'avis_ajoute'): ?>
            <div class="alert-gh"><i class="bi bi-check-circle"></i> Avis ajouté avec succès.</div>
        <?php endif; ?>

        <?php
       
        $filtre_statut = $_GET['statut'] ?? 'tous';
        $search = $_GET['search'] ?? '';
        $where = []; $params = [];
        if ($filtre_statut !== 'tous') { $where[] = "statut = ?"; $params[] = $filtre_statut; }
        if ($search) { $where[] = "(name LIKE ? OR email LIKE ? OR service LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
        $sql = "SELECT *, name as nom FROM contact_requests";
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY created_at DESC";
        $demandes = [];
        try {
            $stmt = $pdo->prepare($sql); $stmt->execute($params); $demandes = $stmt->fetchAll();
        } catch (PDOException $e) { /* table peut ne pas exister */ }
        $counts = [];
        try { $counts = $pdo->query("SELECT statut, COUNT(*) as nb FROM contact_requests GROUP BY statut")->fetchAll(PDO::FETCH_KEY_PAIR); } catch(Exception $e) {}
        $total = array_sum($counts);
        $nb_attente = $counts['en_attente'] ?? 0;
        $nb_encours = $counts['en_cours'] ?? 0;
        $nb_termine = $counts['termine'] ?? 0;
        $nb_archive = $counts['archive'] ?? 0;
        ?>

        <!-- SECTION DEMANDES -->
        <div id="section-demandes" class="admin-section <?= $active_section === 'demandes' ? 'active' : '' ?>">
            <div class="admin-page-header"><h1>Demandes clients</h1><p>Consultez et traitez les demandes de contact</p></div>
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 style="margin:0; font-size:18px;">Toutes les demandes</h2>
                    <span style="background:#1a1a1a;color:#fff;padding:4px 14px;border-radius:20px;"><?= $total ?> demande(s)</span>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3"><div class="stat-badge badge-attente"><span class="stat-num"><?= $nb_attente ?></span><div>En attente</div></div></div>
                    <div class="col-6 col-md-3"><div class="stat-badge badge-encours"><span class="stat-num"><?= $nb_encours ?></span><div>En cours</div></div></div>
                    <div class="col-6 col-md-3"><div class="stat-badge badge-termine"><span class="stat-num"><?= $nb_termine ?></span><div>Terminé</div></div></div>
                    <div class="col-6 col-md-3"><div class="stat-badge badge-archive"><span class="stat-num"><?= $nb_archive ?></span><div>Archivé</div></div></div>
                </div>
                <form method="GET" class="d-flex gap-3 flex-wrap mb-4">
                    <input type="hidden" name="section" value="demandes">
                    <div class="d-flex gap-2">
                        <?php $tabs = ['tous'=>'Tous','en_attente'=>'En attente','en_cours'=>'En cours','termine'=>'Terminé','archive'=>'Archivé'];
                        foreach ($tabs as $val => $label): $active = ($filtre_statut === $val) ? 'tab-active' : ''; ?>
                        <a href="?section=demandes&statut=<?= $val ?>&search=<?= urlencode($search) ?>" class="filter-tab <?= $active ?>"><?= $label ?></a>
                        <?php endforeach; ?>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" style="width:200px;" placeholder="Rechercher...">
                        <button type="submit" class="btn-edit">Chercher</button>
                    </div>
                </form>
                <?php if (empty($demandes)): ?>
                    <div class="empty-section"><i class="bi bi-inbox"></i><p>Aucune demande trouvée.</p></div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="demandes-table">
                        <thead><tr><th>Client</th><th>Service</th><th>Budget</th><th>Date</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($demandes as $d): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($d['nom']) ?></strong><br><small><?= htmlspecialchars($d['email']) ?></small><br><small><?= htmlspecialchars($d['telephone'] ?? '') ?></small></td>
                                <td><?= htmlspecialchars(ucfirst($d['service'] ?? 'Non précisé')) ?></td>
                                <td><?= htmlspecialchars($d['budget'] ?? '—') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($d['created_at'])) ?></td>
                                <td><span class="statut-pill badge-<?= $d['statut'] ?>"><?= $d['statut'] ?></span></td>
                                <td><button class="btn-edit" onclick='ouvrirDetail(<?= json_encode($d, JSON_HEX_TAG) ?>)'><i class="bi bi-eye"></i> Voir</button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SECTION STYLES -->
        <div id="section-styles" class="admin-section <?= $active_section === 'styles' ? 'active' : '' ?>">
            <div class="admin-page-header"><h1>Styles d'intérieur</h1><p>Ajoutez, modifiez ou supprimez des styles</p></div>
            <div class="form-section">
                <h2>Styles existants</h2>
                <?php if (empty($styles)): ?>
                    <div class="empty-section"><i class="bi bi-palette"></i><p>Aucun style. Ajoutez-en un ci-dessous.</p></div>
                <?php else: ?>
                <div class="row">
                    <?php foreach ($styles as $s): ?>
                    <div class="col-md-4">
                        <div class="style-card">
                            <?php if (!empty($s['image_path'])): ?>
                                <img src="../<?= htmlspecialchars($s['image_path']) ?>" alt="<?= htmlspecialchars($s['titre']) ?>">
                            <?php else: ?>
                                <div style="height:200px;background:#f0ebe6;display:flex;align-items:center;justify-content:center;">Aucune image</div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5><?= htmlspecialchars($s['titre']) ?></h5>
                                <p><?= mb_substr(htmlspecialchars($s['description'] ?? ''), 0, 80) ?>...</p>
                                <div class="d-flex gap-2">
                                    <button class="btn-edit" onclick='chargerModif(<?= json_encode($s, JSON_HEX_TAG) ?>)'><i class="bi bi-pencil"></i> Modifier</button>
                                    <form method="POST" action="admin_actions.php" onsubmit="return confirm('Supprimer ce style ?')">
                                        <input type="hidden" name="action" value="supprimer"><input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn-del"><i class="bi bi-trash"></i> Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="form-section" id="form-section">
                <h2 id="form-titre">Ajouter un style</h2>
                <form method="POST" action="admin_actions.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="form-action" value="ajouter">
                    <input type="hidden" name="id" id="form-id">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Titre *</label><input type="text" name="titre" id="f-titre" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Ordre</label><input type="number" name="ordre" id="f-ordre" class="form-control" value="<?= count($styles)+1 ?>"></div>
                        <div class="col-12"><label class="form-label">Description</label><textarea name="description" id="f-description" class="form-control" rows="3"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Matériaux</label><input type="text" name="materiau" id="f-materiau" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Palette</label><input type="text" name="palette" id="f-palette" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Mobilier</label><input type="text" name="mobilier" id="f-mobilier" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Caractéristiques</label><input type="text" name="caracteristiques" id="f-caracteristiques" class="form-control"></div>
                        <div class="col-12">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" id="f-image" class="form-control" accept="image/*" onchange="previewImage(this)">
                            <img id="preview-img" src="" alt="Aperçu" style="max-width:200px;margin-top:10px;display:none;">
                            <input type="hidden" name="image_actuelle" id="f-image-actuelle">
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-3">
                        <button type="submit" class="btn-submit" id="btn-submit-label"><i class="bi bi-plus-lg"></i> Ajouter</button>
                        <button type="button" class="btn-edit" onclick="resetForm()">Annuler</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SECTION AVIS (avec formulaire d'ajout) -->
        <div id="section-avis" class="admin-section <?= $active_section === 'avis' ? 'active' : '' ?>">
            <div class="admin-page-header"><h1>Avis clients</h1><p>Modérez les avis ou ajoutez-en manuellement</p></div>
            <?php
            $avis_all = [];
            try {
                $avis_all = $pdo->query("SELECT *, client_name as nom, content as avis, rating as note, date as date_creation, statut FROM testimonials ORDER BY date_creation DESC")->fetchAll();
            } catch (PDOException $e) {}
            $nb_total = count($avis_all);
            $nb_attente = count(array_filter($avis_all, fn($a) => ($a['statut'] ?? '') === 'en_attente'));
            $nb_approuve = count(array_filter($avis_all, fn($a) => ($a['statut'] ?? '') === 'approuve'));
            $nb_masque = count(array_filter($avis_all, fn($a) => ($a['statut'] ?? '') === 'masque'));
            $moy_note = $nb_total ? round(array_sum(array_column($avis_all, 'note')) / $nb_total, 1) : 0;
            ?>
            <div class="form-section">
                <h2>Statistiques</h2>
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3"><div class="stat-badge"><span class="stat-num"><?= $nb_total ?></span><div>Total</div></div></div>
                    <div class="col-6 col-md-3"><div class="stat-badge badge-attente"><span class="stat-num"><?= $nb_attente ?></span><div>En attente</div></div></div>
                    <div class="col-6 col-md-3"><div class="stat-badge badge-termine"><span class="stat-num"><?= $nb_approuve ?></span><div>Approuvés</div></div></div>
                    <div class="col-6 col-md-3"><div class="stat-badge"><span class="stat-num">★ <?= $moy_note ?></span><div>Note moyenne</div></div></div>
                </div>

                <!-- Formulaire d'ajout manuel d'avis -->
                <h3 class="mt-4">Ajouter un avis</h3>
                <form method="POST" action="admin_actions.php" class="row g-3 mb-5">
                    <input type="hidden" name="action" value="ajouter_avis">
                    <div class="col-md-4"><label class="form-label">Nom complet</label><input type="text" name="client_name" class="form-control" required></div>
                    <div class="col-md-2"><label class="form-label">Note (1-5)</label><input type="number" name="rating" class="form-control" min="1" max="5" required></div>
                    <div class="col-md-3"><label class="form-label">Date</label><input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                    <div class="col-12"><label class="form-label">Avis</label><textarea name="content" class="form-control" rows="3" required></textarea></div>
                    <div class="col-12"><button type="submit" class="btn-submit"><i class="bi bi-plus-lg"></i> Ajouter l’avis</button></div>
                </form>

                <hr>
                <h3>Tous les avis</h3>
                <?php if (empty($avis_all)): ?>
                    <div class="empty-section"><i class="bi bi-star"></i><p>Aucun avis pour le moment.</p></div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="demandes-table">
                        <thead><tr><th>Client</th><th>Note</th><th>Avis</th><th>Date</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($avis_all as $a): $etoiles = str_repeat('★', $a['note']) . str_repeat('☆', 5 - $a['note']); ?>
                            <tr>
                                <td><?= htmlspecialchars($a['nom']) ?></td>
                                <td><span style="color:#f4b400;"><?= $etoiles ?></span> (<?= $a['note'] ?>/5)</td>
                                <td style="max-width:260px;"><?= htmlspecialchars(mb_substr($a['avis'], 0, 100)) ?>…</td>
                                <td><?= date('d/m/Y', strtotime($a['date_creation'])) ?></td>
                                <td><span class="statut-pill badge-<?= $a['statut'] ?>"><?= $a['statut'] ?></span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <?php if ($a['statut'] !== 'approuve'): ?>
                                        <form method="POST" action="admin_actions.php"><input type="hidden" name="action" value="moderer_avis"><input type="hidden" name="id" value="<?= $a['id'] ?>"><input type="hidden" name="statut" value="approuve"><button class="btn-edit" style="background:#d1e7dd;color:#0f5132;">Approuver</button></form>
                                        <?php endif; ?>
                                        <?php if ($a['statut'] !== 'masque'): ?>
                                        <form method="POST" action="admin_actions.php"><input type="hidden" name="action" value="moderer_avis"><input type="hidden" name="id" value="<?= $a['id'] ?>"><input type="hidden" name="statut" value="masque"><button class="btn-edit">Masquer</button></form>
                                        <?php endif; ?>
                                        <form method="POST" action="admin_actions.php" onsubmit="return confirm('Supprimer cet avis ?')"><input type="hidden" name="action" value="supprimer_avis"><input type="hidden" name="id" value="<?= $a['id'] ?>"><button class="btn-del">Supprimer</button></form>
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

        <!-- SECTION PORTFOLIO (placeholder) -->
        <a class="nav-item" href="gestion_portfolio.php" data-tooltip="Portfolio">
    <i class="bi bi-images"></i><span class="nav-item-label">Portfolio</span>
</a>
    </main>
</div>

<!-- MODAL DÉTAIL DEMANDE -->
<div id="modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div id="modal-box" style="background:#fff;border-radius:10px;width:90%;max-width:620px;max-height:90vh;overflow-y:auto;padding:36px;position:relative;">
        <button onclick="fermerModal()" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:20px;">✕</button>
        <h3 id="modal-nom" style="font-family:'Cinzel',serif;"></h3><p id="modal-email" style="font-size:13px;"></p>
        <div class="row g-3 mb-3">
            <div class="col-6"><div class="modal-field-label">Téléphone</div><div id="modal-tel"></div></div>
            <div class="col-6"><div class="modal-field-label">Entreprise</div><div id="modal-entreprise"></div></div>
            <div class="col-6"><div class="modal-field-label">Service</div><div id="modal-service"></div></div>
            <div class="col-6"><div class="modal-field-label">Budget</div><div id="modal-budget"></div></div>
        </div>
        <div class="modal-field-label">Message</div><div id="modal-message" style="background:#f8f6f3;border:1px solid #E6E6E6;border-radius:6px;padding:14px;margin-bottom:20px;"></div>
        <div id="modal-reponse-existante" style="display:none;"><div class="modal-field-label">Réponse envoyée</div><div id="modal-reponse-texte" style="background:#f0f7f0;border:1px solid #b8d4b8;padding:14px;margin-bottom:20px;"></div></div>
        <form method="POST" action="admin_actions.php">
            <input type="hidden" name="action" value="traiter_demande"><input type="hidden" name="id" id="modal-id">
            <div class="mb-3"><label class="form-label">Statut</label><select name="statut" id="modal-statut" class="form-control"><option value="en_attente">En attente</option><option value="en_cours">En cours</option><option value="termine">Terminé</option><option value="archive">Archivé</option></select></div>
            <div class="mb-3"><label class="form-label">Réponse</label><textarea name="reponse" id="modal-reponse-input" class="form-control" rows="4"></textarea></div>
            <div class="d-flex gap-3"><button type="submit" class="btn-submit">Enregistrer</button><button type="button" class="btn-edit" onclick="fermerModal()">Annuler</button></div>
        </form>
    </div>
</div>

<button class="mobile-toggle" onclick="toggleMobileSidebar()"><i class="bi bi-list"></i></button>

<script>
function showSection(name) {
    document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('section-'+name).classList.add('active');
    document.querySelectorAll('.nav-item').forEach(n => { if(n.getAttribute('onclick') === "showSection('"+name+"')") n.classList.add('active'); });
    history.replaceState(null, '', '?section='+name);
}
let collapsed = false;
function toggleSidebar() {
    collapsed = !collapsed;
    document.getElementById('sidebar').classList.toggle('collapsed', collapsed);
    document.getElementById('toggle-chevron').className = collapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
}
function toggleMobileSidebar() {
    document.getElementById('sidebar').classList.toggle('mobile-open');
}
function ouvrirDetail(d) {
    document.getElementById('modal-id').value = d.id;
    document.getElementById('modal-nom').textContent = d.nom;
    document.getElementById('modal-email').innerHTML = d.email + ' · ' + (d.telephone || '');
    document.getElementById('modal-tel').textContent = d.telephone || '—';
    document.getElementById('modal-entreprise').textContent = d.entreprise || '—';
    document.getElementById('modal-service').textContent = d.service || 'Non précisé';
    document.getElementById('modal-budget').textContent = d.budget || '—';
    document.getElementById('modal-message').textContent = d.message;
    document.getElementById('modal-statut').value = d.statut;
    document.getElementById('modal-reponse-input').value = d.reponse_admin || '';
    const repExist = document.getElementById('modal-reponse-existante');
    if (d.reponse_admin) { repExist.style.display = 'block'; document.getElementById('modal-reponse-texte').textContent = d.reponse_admin; }
    else { repExist.style.display = 'none'; }
    document.getElementById('modal-overlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function fermerModal() { document.getElementById('modal-overlay').style.display = 'none'; document.body.style.overflow = ''; }
document.getElementById('modal-overlay').addEventListener('click', function(e) { if (e.target === this) fermerModal(); });
function previewImage(input) {
    const img = document.getElementById('preview-img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; img.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
function chargerModif(s) {
    showSection('styles');
    document.getElementById('form-titre').textContent = 'Modifier un style';
    document.getElementById('form-action').value = 'modifier';
    document.getElementById('form-id').value = s.id;
    document.getElementById('f-titre').value = s.titre || '';
    document.getElementById('f-ordre').value = s.ordre || '';
    document.getElementById('f-description').value = s.description || '';
    document.getElementById('f-materiau').value = s.materiau || '';
    document.getElementById('f-palette').value = s.palette || '';
    document.getElementById('f-mobilier').value = s.mobilier || '';
    document.getElementById('f-caracteristiques').value = s.caracteristiques || '';
    const oldImage = s.image_path || '';
    document.getElementById('f-image-actuelle').value = oldImage;
    const previewImg = document.getElementById('preview-img');
    if (oldImage) {
        previewImg.src = '../' + oldImage;
        previewImg.style.display = 'block';
    } else {
        previewImg.style.display = 'none';
        previewImg.src = '';
    }
    document.getElementById('f-image').value = '';
    document.getElementById('btn-submit-label').innerHTML = '<i class="bi bi-check-lg"></i> Enregistrer';
    document.getElementById('form-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
}
function resetForm() {
    document.getElementById('form-titre').textContent = 'Ajouter un style';
    document.getElementById('form-action').value = 'ajouter';
    document.getElementById('form-id').value = '';
    document.getElementById('btn-submit-label').innerHTML = '<i class="bi bi-plus-lg"></i> Ajouter';
    document.querySelector('#form-section form').reset();
    document.getElementById('preview-img').style.display = 'none';
    document.getElementById('preview-img').src = '';
    document.getElementById('f-image-actuelle').value = '';
}
</script>
</body>
</html>