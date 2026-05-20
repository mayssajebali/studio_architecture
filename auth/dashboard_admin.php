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
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
      <style>
        * { box-sizing: border-box; }
        body {
            padding-top: 80px;
            font-family: "Inter", sans-serif;
            background: #f8f6f3;
            margin: 0;
        }

        /* ── LAYOUT ── */
        .admin-layout {
            display: flex;
            min-height: calc(100vh - 80px);
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 240px;
            min-width: 240px;
            background: #fff;
            border-right: 1px solid #E6E6E6;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 80px;
            height: calc(100vh - 80px);
            overflow-y: auto;
            transition: width .25s ease, min-width .25s ease;
            z-index: 100;
        }
        .sidebar.collapsed {
            width: 64px;
            min-width: 64px;
        }

        .sidebar-brand {
            padding: 24px 20px 16px;
            border-bottom: 1px solid #E6E6E6;
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
            white-space: nowrap;
        }
        .sidebar-brand-icon {
            width: 32px; height: 32px; min-width: 32px;
            background: #1a1a1a;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 15px;
        }
        .sidebar-brand-text {
            font-family: "Cinzel", serif;
            font-size: 13px;
            font-weight: 600;
            color: #1a1a1a;
            letter-spacing: .5px;
            transition: opacity .2s;
        }
        .sidebar.collapsed .sidebar-brand-text { opacity: 0; pointer-events: none; }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
        }

        .sidebar-section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #b8a99a;
            padding: 8px 20px 4px;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity .2s;
        }
        .sidebar.collapsed .sidebar-section-label { opacity: 0; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            text-decoration: none;
            color: #555;
            font-size: 14px;
            border-left: 3px solid transparent;
            transition: all .15s;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            position: relative;
        }
        .nav-item:hover {
            background: #f8f6f3;
            color: #1a1a1a;
        }
        .nav-item.active {
            background: #f8f6f3;
            color: #1a1a1a;
            border-left-color: #b8a99a;
            font-weight: 500;
        }
        .nav-item i {
            font-size: 18px;
            min-width: 20px;
            text-align: center;
            flex-shrink: 0;
        }
        .nav-item-label {
            transition: opacity .2s;
        }
        .sidebar.collapsed .nav-item-label { opacity: 0; }

        .nav-badge {
            margin-left: auto;
            background: #1a1a1a;
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 10px;
            transition: opacity .2s;
        }
        .sidebar.collapsed .nav-badge { opacity: 0; }

        /* Tooltip for collapsed sidebar */
        .sidebar.collapsed .nav-item:hover::after {
            content: attr(data-tooltip);
            position: fixed;
            left: 68px;
            background: #1a1a1a;
            color: #fff;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 4px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 9999;
        }

        /* Toggle button */
        .sidebar-toggle {
            padding: 12px 20px;
            border-top: 1px solid #E6E6E6;
            display: flex;
            justify-content: flex-end;
        }
        .sidebar.collapsed .sidebar-toggle { justify-content: center; }
        .toggle-btn {
            background: none;
            border: 1px solid #E6E6E6;
            border-radius: 4px;
            width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: #6D6D6D;
            font-size: 14px;
            transition: all .2s;
        }
        .toggle-btn:hover { background: #f8f6f3; color: #1a1a1a; }

        /* ── MAIN CONTENT ── */
        .admin-main {
            flex: 1;
            min-width: 0;
            padding: 40px;
            overflow-x: hidden;
        }

        .admin-page-header {
            margin-bottom: 32px;
        }
        .admin-page-header h1 {
            font-family: "Cinzel", serif;
            font-size: 28px;
            color: #1a1a1a;
            margin: 0 0 4px;
        }
        .admin-page-header p { color: #6D6D6D; font-size: 13px; margin: 0; }

        /* ── SECTIONS ── */
        .admin-section { display: none; }
        .admin-section.active { display: block; }

        /* Cards styles */
        .style-card {
            background: #fff;
            border: 1px solid #E6E6E6;
            border-radius: 8px;
            overflow: hidden;
            transition: box-shadow .25s ease, transform .25s ease;
            margin-bottom: 24px;
        }
        .style-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,.08);
            transform: translateY(-3px);
        }
        .style-card img { width: 100%; height: 200px; object-fit: cover; }
        .style-card .card-body { padding: 20px; }
        .style-card h5 {
            font-family: "Cinzel", serif;
            font-size: 18px;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        .style-card p { font-size: 13px; color: #6D6D6D; margin: 0; }

        .btn-edit {
            background: #b8a99a; color: #fff; border: none;
            padding: 6px 14px; border-radius: 4px; font-size: 13px;
            cursor: pointer; transition: background .2s;
        }
        .btn-edit:hover { background: #a8998a; }
        .btn-del {
            background: #fff; color: #c0392b; border: 1px solid #c0392b;
            padding: 6px 14px; border-radius: 4px; font-size: 13px;
            cursor: pointer; transition: all .2s;
        }
        .btn-del:hover { background: #c0392b; color: #fff; }

        /* Form section */
        .form-section {
            background: #fff;
            border: 1px solid #E6E6E6;
            border-radius: 8px;
            padding: 36px;
            margin-bottom: 40px;
        }
        .form-section h2 {
            font-family: "Cinzel", serif;
            font-size: 22px;
            margin-bottom: 24px;
            color: #1a1a1a;
            padding-bottom: 12px;
            border-bottom: 2px solid #b8a99a;
            display: inline-block;
        }
        .form-label { font-size: 13px; font-weight: 500; color: #555; margin-bottom: 4px; }
        .form-control, .form-select {
            border: 1px solid #E6E6E6; border-radius: 4px;
            font-size: 14px; padding: 10px 14px;
            font-family: "Inter", sans-serif;
        }
        .form-control:focus, .form-select:focus {
            border-color: #b8a99a;
            box-shadow: 0 0 0 3px rgba(184,169,154,.15);
            outline: none;
        }
        .btn-submit {
            background: #1a1a1a; color: #fff; border: none;
            padding: 12px 32px; border-radius: 4px;
            font-family: "Inter", sans-serif; font-size: 14px;
            font-weight: 500; letter-spacing: .5px; cursor: pointer;
            transition: background .2s;
        }
        .btn-submit:hover { background: #333; }

        /* Alert */
        .alert-gh {
            background: #f0ebe6; border: 1px solid #b8a99a;
            color: #5a4a3a; border-radius: 4px; padding: 12px 20px;
            font-size: 14px; margin-bottom: 24px;
        }

        #preview-img {
            max-width: 200px; max-height: 140px;
            border-radius: 4px; border: 1px solid #E6E6E6;
            margin-top: 10px; display: none;
        }

        /* Demandes table */
        .demandes-table { width: 100%; border-collapse: collapse; font-family: "Inter", sans-serif; }
        .demandes-table th {
            background: #f8f6f3; padding: 12px 16px;
            font-size: 12px; font-weight: 600; color: #6D6D6D;
            text-transform: uppercase; letter-spacing: .5px;
            border-bottom: 2px solid #E6E6E6; text-align: left;
        }
        .demandes-table td { padding: 14px 16px; border-bottom: 1px solid #f0ebe6; vertical-align: middle; }
        .demandes-table tr:hover td { background: #faf8f6; }

        /* Status pills */
        .statut-pill {
            display: inline-block; padding: 4px 12px;
            border-radius: 20px; font-size: 11px; font-weight: 600; letter-spacing: .3px;
        }
        .badge-attente, .statut-pill.badge-attente { background:#fff3cd; color:#856404; }
        .badge-encours, .statut-pill.badge-encours { background:#cfe2ff; color:#084298; }
        .badge-termine, .statut-pill.badge-termine { background:#d1e7dd; color:#0f5132; }
        .badge-archive, .statut-pill.badge-archive { background:#f0ebe6; color:#6D6D6D; }

        /* Stat badges */
        .stat-badge {
            border-radius: 8px; padding: 16px 20px;
            display: flex; flex-direction: column; align-items: center;
            border: 1px solid #E6E6E6;
        }
        .stat-num  { font-family:"Cinzel",serif; font-size:28px; font-weight:500; }
        .stat-label{ font-size:12px; color:#6D6D6D; margin-top:4px; }

        /* Filter tabs */
        .filter-tab {
            padding: 6px 16px; border-radius: 20px; font-size: 13px;
            text-decoration: none; color: #555; border: 1px solid #E6E6E6;
            background: #fff; transition: all .2s; white-space: nowrap;
        }
        .filter-tab:hover { border-color: #b8a99a; color: #b8a99a; }
        .filter-tab.tab-active { background:#1a1a1a; color:#fff; border-color:#1a1a1a; }

        /* Modal */
        .modal-field-label { font-size: 11px; font-weight:600; color:#6D6D6D; text-transform:uppercase; letter-spacing:.5px; }
        .modal-field-value { font-size: 14px; color:#1a1a1a; margin-top:2px; }

        /* Placeholder sections */
        .empty-section {
            text-align: center; padding: 80px 40px;
            color: #6D6D6D; font-size: 14px;
        }
        .empty-section i { font-size: 48px; display: block; margin-bottom: 16px; opacity: .3; }
        .empty-section h3 { font-family: "Cinzel", serif; font-size: 18px; color: #1a1a1a; margin-bottom: 8px; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #b8a99a; border-radius: 10px; }

        /* Mobile */
        @media (max-width: 768px) {
            .admin-main { padding: 20px 16px; }
            .sidebar { position: fixed; left: 0; top: 80px; height: calc(100vh - 80px); transform: translateX(-100%); transition: transform .25s ease; }
            .sidebar.mobile-open { transform: translateX(0); box-shadow: 4px 0 20px rgba(0,0,0,.15); }
            .mobile-toggle { display: flex !important; }
        }
        .mobile-toggle {
            display: none;
            position: fixed;
            bottom: 24px; right: 24px;
            background: #1a1a1a; color: #fff;
            border: none; border-radius: 50%;
            width: 48px; height: 48px;
            align-items: center; justify-content: center;
            font-size: 20px; cursor: pointer; z-index: 200;
            box-shadow: 0 4px 16px rgba(0,0,0,.2);
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
            <a href="?section=demandes" class="nav-item <?= $active_section === 'demandes' ? 'active' : '' ?>" data-section="demandes" onclick="showSection('demandes'); return false;" data-tooltip="Demandes clients">
                <i class="bi bi-envelope"></i><span class="nav-item-label">Demandes clients</span>
                <?php 
                $nb_attente_nav = 0;
                try { $nb_attente_nav = $pdo->query("SELECT COUNT(*) FROM demandes_contact WHERE statut='en_attente'")->fetchColumn(); } catch(Exception $e) {}
                if ($nb_attente_nav > 0): ?>
                <span class="nav-badge"><?= $nb_attente_nav ?></span>
                <?php endif; ?>
            </a>
            <a href="?section=rendez-vous" class="nav-item <?= $active_section === 'rendez-vous' ? 'active' : '' ?>" data-section="rendez-vous" onclick="showSection('rendez-vous'); return false;" data-tooltip="Rendez-vous">
                <i class="bi bi-calendar-check"></i><span class="nav-item-label">Rendez-vous</span>
                <?php 
                $nb_rdv_attente_nav = 0;
                try { $nb_rdv_attente_nav = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='en_attente'")->fetchColumn(); } catch(Exception $e) {}
                if ($nb_rdv_attente_nav > 0): ?>
                <span class="nav-badge" style="background:#ff6b6b;color:#fff;"><?= $nb_rdv_attente_nav ?></span>
                <?php endif; ?>
            </a>
            <a href="?section=styles" class="nav-item <?= $active_section === 'styles' ? 'active' : '' ?>" data-section="styles" onclick="showSection('styles'); return false;" data-tooltip="Styles">
                <i class="bi bi-palette"></i><span class="nav-item-label">Styles</span>
                <span class="nav-badge" style="background:#f0ebe6;color:#b8a99a;"><?= count($styles) ?></span>
            </a>
            <div class="sidebar-section-label" style="margin-top:8px;">Contenu</div>
            <a href="?section=avis" class="nav-item <?= $active_section === 'avis' ? 'active' : '' ?>" data-section="avis" onclick="showSection('avis'); return false;" data-tooltip="Avis">
                <i class="bi bi-star"></i><span class="nav-item-label">Avis</span>
            </a>
           <a class="nav-item" href="gestion_portfolio.php" data-tooltip="Portfolio">
    <i class="bi bi-images"></i><span class="nav-item-label">Portfolio</span>
</a>
            <div class="sidebar-section-label" style="margin-top:8px;">Administration</div>
            <a class="nav-item" href="gestion_clients.php" data-tooltip="Clients">
                <i class="bi bi-people"></i><span class="nav-item-label">Clients</span>
                <?php 
                $nb_clients = 0;
                try { $nb_clients = $pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn(); } catch(Exception $e) {}
                if ($nb_clients > 0): ?>
                <span class="nav-badge" style="background:#d1e7dd;color:#0f5132;"><?= $nb_clients ?></span>
                <?php endif; ?>
            </a>
            <a class="nav-item" href="gestion_admins.php" data-tooltip="Administrateurs">
                <i class="bi bi-shield-check"></i><span class="nav-item-label">Administrateurs</span>
            </a>
            <a class="nav-item" href="profil_admin.php" data-tooltip="Mon profil">
                <i class="bi bi-person-circle"></i><span class="nav-item-label">Mon profil</span>
            </a>
        </nav>
        <div class="sidebar-toggle">
            <button class="toggle-btn" onclick="toggleSidebar()"><i class="bi bi-chevron-left" id="toggle-chevron"></i></button>
        </div>
    </aside>

     <!-- ── MAIN ── -->
    <main class="admin-main">

        <?php if ($msg === 'ajout'): ?>
            <div class="alert-gh"><i class="bi bi-check-circle me-2"></i>Style ajouté avec succès.</div>
        <?php elseif ($msg === 'modif'): ?>
            <div class="alert-gh"><i class="bi bi-check-circle me-2"></i>Style modifié avec succès.</div>
        <?php elseif ($msg === 'suppression'): ?>
            <div class="alert-gh"><i class="bi bi-check-circle me-2"></i>Style supprimé avec succès.</div>
        <?php elseif ($msg === 'confirme'): ?>
            <div class="alert-gh"><i class="bi bi-check-circle me-2"></i>Rendez-vous confirmé avec succès.</div>
        <?php elseif ($msg === 'annule'): ?>
            <div class="alert-gh"><i class="bi bi-check-circle me-2"></i>Rendez-vous annulé.</div>
        <?php elseif ($msg === 'termine'): ?>
            <div class="alert-gh"><i class="bi bi-check-circle me-2"></i>Rendez-vous marqué comme terminé.</div>
        <?php elseif ($msg === 'notes_ajoutees'): ?>
            <div class="alert-gh"><i class="bi bi-check-circle me-2"></i>Notes ajoutées avec succès.</div>
        <?php endif; ?>

        <?php
        $filtre_statut = $_GET['statut'] ?? 'tous';
        $search = $_GET['search'] ?? '';
        $where = []; $params = [];
        if ($filtre_statut !== 'tous') { $where[] = "statut = ?"; $params[] = $filtre_statut; }
        if ($search) { $where[] = "(nom LIKE ? OR email LIKE ? OR service LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
        $sql = "SELECT * FROM demandes_contact";
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY date_envoi DESC";
        $stmt = $pdo->prepare($sql); $stmt->execute($params);
        $demandes = $stmt->fetchAll();
        $counts = $pdo->query("SELECT statut, COUNT(*) as nb FROM demandes_contact GROUP BY statut")->fetchAll(PDO::FETCH_KEY_PAIR);
        $total = array_sum($counts);
        $nb_attente = $counts['en_attente'] ?? 0;
        $nb_encours = $counts['en_cours'] ?? 0;
        $nb_termine = $counts['termine'] ?? 0;
        $nb_archive = $counts['archive'] ?? 0;
        ?>

     <!-- ── SECTION: DEMANDES ── -->
        <div id="section-demandes" class="admin-section <?= $active_section === 'demandes' ? 'active' : '' ?>">
            <div class="admin-page-header">
                <h1>Demandes clients</h1>
                <p>Consultez et traitez les demandes reçues via le formulaire de contact</p>
            </div>

            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 style="margin:0;border:none;padding:0;font-size:18px;">Toutes les demandes</h2>
                    <span style="background:#1a1a1a;color:#fff;padding:4px 14px;border-radius:20px;font-size:13px;">
                        <?= $total ?> demande<?= $total > 1 ? 's' : '' ?>
                    </span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3"><div class="stat-badge badge-attente"><span class="stat-num"><?= $nb_attente ?></span><span class="stat-label">En attente</span></div></div>
                    <div class="col-6 col-md-3"><div class="stat-badge badge-encours"><span class="stat-num"><?= $nb_encours ?></span><span class="stat-label">En cours</span></div></div>
                    <div class="col-6 col-md-3"><div class="stat-badge badge-termine"><span class="stat-num"><?= $nb_termine ?></span><span class="stat-label">Terminé</span></div></div>
                    <div class="col-6 col-md-3"><div class="stat-badge badge-archive"><span class="stat-num"><?= $nb_archive ?></span><span class="stat-label">Archivé</span></div></div>
                </div>

                <form method="GET" action="" class="d-flex gap-3 flex-wrap mb-4 align-items-center">
                    <input type="hidden" name="section" value="demandes">
                    <div class="d-flex gap-2 flex-wrap">
                        <?php $tabs = ['tous'=>'Tous','en_attente'=>'En attente','en_cours'=>'En cours','termine'=>'Terminé','archive'=>'Archivé'];
                        foreach ($tabs as $val => $label): $active = ($filtre_statut === $val) ? 'tab-active' : ''; ?>
                        <a href="?section=demandes&statut=<?= $val ?>&search=<?= urlencode($search) ?>" class="filter-tab <?= $active ?>"><?= $label ?></a>
                        <?php endforeach; ?>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <input type="hidden" name="statut" value="<?= htmlspecialchars($filtre_statut) ?>">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" style="width:220px;font-size:13px;" placeholder="Rechercher...">
                        <button type="submit" class="btn-edit">Chercher</button>
                    </div>
                </form>

                <?php if (empty($demandes)): ?>
                <div style="text-align:center;padding:40px;color:#6D6D6D;font-size:14px;">
                    <i class="bi bi-inbox" style="font-size:40px;display:block;margin-bottom:12px;opacity:.4;"></i>
                    Aucune demande trouvée.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="demandes-table">
                        <thead><tr><th>Client</th><th>Service</th><th>Budget</th><th>Date</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($demandes as $d): ?>
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:13px;color:#1a1a1a;"><?= htmlspecialchars($d['nom']) ?></div>
                                    <div style="font-size:12px;color:#6D6D6D;"><?= htmlspecialchars($d['email']) ?></div>
                                    <div style="font-size:12px;color:#6D6D6D;"><?= htmlspecialchars($d['telephone']) ?></div>
                                </td>
                                <td style="font-size:13px;"><?= htmlspecialchars(ucfirst($d['service'])) ?></td>
                                <td style="font-size:13px;"><?= htmlspecialchars($d['budget']) ?></td>
                                <td style="font-size:12px;color:#6D6D6D;"><?= date('d/m/Y H:i', strtotime($d['date_envoi'])) ?></td>
                                <td>
                                    <?php $badges = ['en_attente'=>['label'=>'En attente','class'=>'badge-attente'],'en_cours'=>['label'=>'En cours','class'=>'badge-encours'],'termine'=>['label'=>'Terminé','class'=>'badge-termine'],'archive'=>['label'=>'Archivé','class'=>'badge-archive']];
                                    $b = $badges[$d['statut']] ?? ['label'=>$d['statut'],'class'=>'']; ?>
                                    <span class="statut-pill <?= $b['class'] ?>"><?= $b['label'] ?></span>
                                </td>
                                <td>
                                    <button class="btn-edit" style="padding:5px 10px;font-size:12px;" onclick="ouvrirDetail(<?= htmlspecialchars(json_encode($d)) ?>)">
                                        <i class="bi bi-eye me-1"></i>Voir
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

       <!-- ── SECTION: STYLES ── -->
        <div id="section-styles" class="admin-section <?= $active_section === 'styles' ? 'active' : '' ?>">
            <div class="admin-page-header">
                <h1>Styles</h1>
                <p>Gérez les styles de design d'intérieur affichés sur le site</p>
            </div>

            <div class="form-section">
                <h2>Styles existants</h2>
                <div class="row">
                    <?php foreach ($styles as $s): ?>
                    <div class="col-md-4">
                        <div class="style-card">
                            <?php if ($s['image_path']): ?>
                                <img src="../<?= htmlspecialchars($s['image_path']) ?>" alt="<?= htmlspecialchars($s['titre']) ?>">
                            <?php else: ?>
                                <div style="height:200px;background:#f0ebe6;display:flex;align-items:center;justify-content:center;color:#b8a99a;font-size:13px;">Aucune image</div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5><?= htmlspecialchars($s['titre']) ?></h5>
                                <p><?= mb_substr(htmlspecialchars($s['description']), 0, 80) ?>...</p>
                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn-edit" onclick="chargerModif(<?= htmlspecialchars(json_encode($s)) ?>)">
                                        <i class="bi bi-pencil me-1"></i>Modifier
                                    </button>
                                    <form method="POST" action="admin_actions.php" onsubmit="return confirm('Supprimer ce style ?')">
                                        <input type="hidden" name="action" value="supprimer">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn-del"><i class="bi bi-trash me-1"></i>Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-section" id="form-section">
                <h2 id="form-titre">Ajouter un style</h2>
                <form method="POST" action="admin_actions.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="form-action" value="ajouter">
                    <input type="hidden" name="id" id="form-id" value="">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Titre du style *</label><input type="text" name="titre" id="f-titre" class="form-control" placeholder="Ex: Style Scandinave" required></div>
                        <div class="col-md-6"><label class="form-label">Ordre d'affichage</label><input type="number" name="ordre" id="f-ordre" class="form-control" value="<?= count($styles) + 1 ?>" min="1"></div>
                        <div class="col-12"><label class="form-label">Description</label><textarea name="description" id="f-description" class="form-control" rows="3" placeholder="Description générale du style..."></textarea></div>
                        <div class="col-md-6"><label class="form-label">Matériaux principaux</label><input type="text" name="materiau" id="f-materiau" class="form-control" placeholder="bois, métal, verre..."></div>
                        <div class="col-md-6"><label class="form-label">Palette de couleurs</label><input type="text" name="palette" id="f-palette" class="form-control" placeholder="blanc, noir, gris..."></div>
                        <div class="col-md-6"><label class="form-label">Style de mobilier</label><input type="text" name="mobilier" id="f-mobilier" class="form-control" placeholder="formes épurées, fonctionnel..."></div>
                        <div class="col-md-6"><label class="form-label">Caractéristiques distinctives</label><input type="text" name="caracteristiques" id="f-caracteristiques" class="form-control" placeholder="lignes épurées, sans encombrement..."></div>
                        <div class="col-12">
                            <label class="form-label">Image principale</label>
                            <input type="file" name="image" id="f-image" class="form-control" accept="image/*" onchange="previewImage(this)">
                            <img id="preview-img" src="" alt="Aperçu">
                            <input type="hidden" name="image_actuelle" id="f-image-actuelle" value="">
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn-submit" id="btn-submit-label"><i class="bi bi-plus-lg me-2"></i>Ajouter le style</button>
                        <button type="button" class="btn-edit" onclick="resetForm()"><i class="bi bi-x-lg me-1"></i>Annuler</button>
                    </div>
                </form>
            </div>
        </div>

<div id="section-avis" class="admin-section <?= $active_section === 'avis' ? 'active' : '' ?>">
    <div class="admin-page-header">
        <h1>Avis clients</h1>
        <p>Modérez les avis soumis par les clients</p>
    </div>

    <?php
    $avis_all    = $pdo->query("SELECT * FROM temoignages ORDER BY date_creation DESC")->fetchAll();
    $nb_total    = count($avis_all);
    $nb_attente  = count(array_filter($avis_all, fn($a) => $a['statut'] === 'en_attente'));
    $nb_approuve = count(array_filter($avis_all, fn($a) => $a['statut'] === 'approuve'));
    $nb_masque   = count(array_filter($avis_all, fn($a) => $a['statut'] === 'masque'));
    $notes       = array_column($avis_all, 'note');
    $moy_note    = $nb_total ? round(array_sum($notes) / $nb_total, 1) : 0;
    ?>

    <div class="form-section">
        <h2>Tous les avis</h2>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-badge">
                    <span class="stat-num"><?= $nb_total ?></span>
                    <span class="stat-label">Total</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-badge badge-attente">
                    <span class="stat-num"><?= $nb_attente ?></span>
                    <span class="stat-label">En attente</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-badge badge-termine">
                    <span class="stat-num"><?= $nb_approuve ?></span>
                    <span class="stat-label">Approuvés</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-badge" style="border-color:#b8a99a;">
                    <span class="stat-num" style="color:#b8a99a;">★ <?= $moy_note ?></span>
                    <span class="stat-label">Note moyenne</span>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <?php if (empty($avis_all)): ?>
            <div style="text-align:center;padding:40px;color:#6D6D6D;font-size:14px;">
                <i class="bi bi-star" style="font-size:40px;display:block;margin-bottom:12px;opacity:.4;"></i>
                Aucun avis pour le moment.
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="demandes-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Note</th>
                        <th>Avis</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($avis_all as $a): ?>
                    <?php
                        $etoiles = str_repeat('★', $a['note']) . str_repeat('☆', 5 - $a['note']);
                        $statut_badges = [
                            'en_attente' => ['label' => 'En attente', 'class' => 'badge-attente'],
                            'approuve'   => ['label' => 'Approuvé',   'class' => 'badge-termine'],
                            'masque'     => ['label' => 'Masqué',     'class' => 'badge-archive'],
                        ];
                        $b = $statut_badges[$a['statut']] ?? ['label' => $a['statut'], 'class' => ''];
                    ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:13px;color:#1a1a1a;">
                                <?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?>
                            </div>
                        </td>
                        <td>
                            <span style="color:#f4b400;font-size:15px;letter-spacing:1px;"><?= $etoiles ?></span>
                            <span style="font-size:12px;color:#6D6D6D;margin-left:4px;">(<?= $a['note'] ?>/5)</span>
                        </td>
                        <td style="max-width:260px;font-size:13px;color:#555;">
                            <?= htmlspecialchars(mb_substr($a['avis'], 0, 100)) ?><?= mb_strlen($a['avis']) > 100 ? '…' : '' ?>
                        </td>
                        <td style="font-size:12px;color:#6D6D6D;">
                            <?= isset($a['date_creation']) ? date('d/m/Y', strtotime($a['date_creation'])) : '—' ?>
                        </td>
                        <td>
                            <span class="statut-pill <?= $b['class'] ?>"><?= $b['label'] ?></span>
                        </td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php if ($a['statut'] !== 'approuve'): ?>
                                <form method="POST" action="admin_actions.php">
                                    <input type="hidden" name="action" value="moderer_avis">
                                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                    <input type="hidden" name="statut" value="approuve">
                                    <button type="submit" class="btn-edit" style="padding:5px 10px;font-size:12px;background:#d1e7dd;color:#0f5132;">
                                        <i class="bi bi-check-lg"></i> Approuver
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php if ($a['statut'] !== 'masque'): ?>
                                <form method="POST" action="admin_actions.php">
                                    <input type="hidden" name="action" value="moderer_avis">
                                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                    <input type="hidden" name="statut" value="masque">
                                    <button type="submit" class="btn-edit" style="padding:5px 10px;font-size:12px;">
                                        <i class="bi bi-eye-slash"></i> Masquer
                                    </button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" action="admin_actions.php" onsubmit="return confirm('Supprimer cet avis ?')">
                                    <input type="hidden" name="action" value="supprimer_avis">
                                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                    <button type="submit" class="btn-del" style="padding:5px 10px;font-size:12px;">
                                        <i class="bi bi-trash"></i>
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

<!-- ── SECTION: RENDEZ-VOUS ── -->
<div id="section-rendez-vous" class="admin-section <?= $active_section === 'rendez-vous' ? 'active' : '' ?>">
    <div class="admin-page-header">
        <h1>Gestion des Rendez-vous</h1>
        <p>Consultez et gérez les rendez-vous pris par les clients</p>
    </div>

    <?php
    // Récupérer tous les rendez-vous
    $filtre_rdv = $_GET['statut_rdv'] ?? 'tous';
    $search_rdv = $_GET['search_rdv'] ?? '';
    $where_rdv = [];
    $params_rdv = [];
    
    if ($filtre_rdv !== 'tous') {
        $where_rdv[] = "status = ?";
        $params_rdv[] = $filtre_rdv;
    }
    
    if ($search_rdv) {
        $where_rdv[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
        $params_rdv[] = "%$search_rdv%";
        $params_rdv[] = "%$search_rdv%";
        $params_rdv[] = "%$search_rdv%";
        $params_rdv[] = "%$search_rdv%";
    }
    
    $sql_rdv = "SELECT * FROM appointments";
    if ($where_rdv) $sql_rdv .= " WHERE " . implode(" AND ", $where_rdv);
    $sql_rdv .= " ORDER BY appointment_date DESC, appointment_time DESC";
    
    $stmt_rdv = $pdo->prepare($sql_rdv);
    $stmt_rdv->execute($params_rdv);
    $rendez_vous = $stmt_rdv->fetchAll();
    
    // Statistiques
    $counts_rdv = $pdo->query("SELECT status, COUNT(*) as nb FROM appointments GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
    $total_rdv = array_sum($counts_rdv);
    $nb_rdv_attente = $counts_rdv['en_attente'] ?? 0;
    $nb_rdv_confirme = $counts_rdv['confirme'] ?? 0;
    $nb_rdv_termine = $counts_rdv['termine'] ?? 0;
    $nb_rdv_annule = $counts_rdv['annule'] ?? 0;
    ?>

    <div class="form-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="margin:0;border:none;padding:0;font-size:18px;">Tous les rendez-vous</h2>
            <span style="background:#1a1a1a;color:#fff;padding:4px 14px;border-radius:20px;font-size:13px;">
                <?= $total_rdv ?> rendez-vous
            </span>
        </div>

        <!-- Statistiques -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-badge badge-attente">
                    <span class="stat-num"><?= $nb_rdv_attente ?></span>
                    <span class="stat-label">En attente</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-badge badge-encours">
                    <span class="stat-num"><?= $nb_rdv_confirme ?></span>
                    <span class="stat-label">Confirmé</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-badge badge-termine">
                    <span class="stat-num"><?= $nb_rdv_termine ?></span>
                    <span class="stat-label">Terminé</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-badge badge-archive">
                    <span class="stat-num"><?= $nb_rdv_annule ?></span>
                    <span class="stat-label">Annulé</span>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <form method="GET" action="" class="d-flex gap-3 flex-wrap mb-4 align-items-center">
            <input type="hidden" name="section" value="rendez-vous">
            <div class="d-flex gap-2 flex-wrap">
                <?php 
                $tabs_rdv = [
                    'tous' => 'Tous',
                    'en_attente' => 'En attente',
                    'confirme' => 'Confirmé',
                    'termine' => 'Terminé',
                    'annule' => 'Annulé'
                ];
                foreach ($tabs_rdv as $val => $label): 
                    $active = ($filtre_rdv === $val) ? 'tab-active' : '';
                ?>
                <a href="?section=rendez-vous&statut_rdv=<?= $val ?>&search_rdv=<?= urlencode($search_rdv) ?>" 
                   class="filter-tab <?= $active ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </div>
            <div class="ms-auto d-flex gap-2">
                <input type="hidden" name="statut_rdv" value="<?= htmlspecialchars($filtre_rdv) ?>">
                <input type="text" name="search_rdv" value="<?= htmlspecialchars($search_rdv) ?>" 
                       class="form-control" style="width:220px;font-size:13px;" placeholder="Rechercher...">
                <button type="submit" class="btn-edit">Chercher</button>
            </div>
        </form>

        <!-- Tableau des rendez-vous -->
        <?php if (empty($rendez_vous)): ?>
        <div style="text-align:center;padding:40px;color:#6D6D6D;font-size:14px;">
            <i class="bi bi-calendar-x" style="font-size:40px;display:block;margin-bottom:12px;opacity:.4;"></i>
            Aucun rendez-vous trouvé.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="demandes-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Date & Heure</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rendez_vous as $rdv): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:13px;color:#1a1a1a;">
                                <?= htmlspecialchars($rdv['first_name'] . ' ' . $rdv['last_name']) ?>
                            </div>
                            <div style="font-size:12px;color:#6D6D6D;">
                                <?= htmlspecialchars($rdv['email']) ?>
                            </div>
                            <div style="font-size:12px;color:#6D6D6D;">
                                <?= htmlspecialchars($rdv['phone']) ?>
                            </div>
                        </td>
                        <td style="font-size:13px;">
                            <?= htmlspecialchars($rdv['service']) ?>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13px;color:#1a1a1a;">
                                <?= date('d/m/Y', strtotime($rdv['appointment_date'])) ?>
                            </div>
                            <div style="font-size:12px;color:#6D6D6D;">
                                <?= htmlspecialchars($rdv['appointment_time']) ?>
                            </div>
                        </td>
                        <td style="font-size:13px;">
                            <?php
                            $types = [
                                'presentiel' => '🏢 Présentiel',
                                'visio' => '💻 Visio',
                                'domicile' => '🏠 Domicile'
                            ];
                            echo $types[$rdv['meeting_type']] ?? htmlspecialchars($rdv['meeting_type']);
                            ?>
                        </td>
                        <td>
                            <?php
                            $badges_rdv = [
                                'en_attente' => ['label' => 'En attente', 'class' => 'badge-attente'],
                                'confirme' => ['label' => 'Confirmé', 'class' => 'badge-encours'],
                                'termine' => ['label' => 'Terminé', 'class' => 'badge-termine'],
                                'annule' => ['label' => 'Annulé', 'class' => 'badge-archive']
                            ];
                            $b_rdv = $badges_rdv[$rdv['status']] ?? ['label' => $rdv['status'], 'class' => ''];
                            ?>
                            <span class="statut-pill <?= $b_rdv['class'] ?>"><?= $b_rdv['label'] ?></span>
                        </td>
                        <td>
                            <button class="btn-edit" style="padding:5px 10px;font-size:12px;" 
                                    onclick="ouvrirDetailRdv(<?= htmlspecialchars(json_encode($rdv)) ?>)">
                                <i class="bi bi-eye me-1"></i>Voir
                            </button>
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

<!-- MODAL DÉTAIL RENDEZ-VOUS -->
<div id="modal-rdv-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:10px;width:90%;max-width:700px;max-height:90vh;overflow-y:auto;padding:36px;position:relative;">
        <button onclick="fermerModalRdv()" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
        
        <h3 style="font-family:'Cinzel',serif;margin-bottom:20px;">
            <i class="bi bi-calendar-check me-2"></i>Détails du rendez-vous
        </h3>
        
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="modal-field-label">Client</div>
                <div class="modal-field-value" id="modal-rdv-client"></div>
            </div>
            <div class="col-md-6">
                <div class="modal-field-label">Email</div>
                <div class="modal-field-value" id="modal-rdv-email"></div>
            </div>
            <div class="col-md-6">
                <div class="modal-field-label">Téléphone</div>
                <div class="modal-field-value" id="modal-rdv-phone"></div>
            </div>
            <div class="col-md-6">
                <div class="modal-field-label">Service</div>
                <div class="modal-field-value" id="modal-rdv-service"></div>
            </div>
            <div class="col-md-6">
                <div class="modal-field-label">Date</div>
                <div class="modal-field-value" id="modal-rdv-date"></div>
            </div>
            <div class="col-md-6">
                <div class="modal-field-label">Heure</div>
                <div class="modal-field-value" id="modal-rdv-time"></div>
            </div>
            <div class="col-md-6">
                <div class="modal-field-label">Type de rendez-vous</div>
                <div class="modal-field-value" id="modal-rdv-type"></div>
            </div>
            <div class="col-md-6">
                <div class="modal-field-label">Budget estimé</div>
                <div class="modal-field-value" id="modal-rdv-budget"></div>
            </div>
            <div class="col-12" id="modal-rdv-message-container" style="display:none;">
                <div class="modal-field-label">Message du client</div>
                <div class="modal-field-value" id="modal-rdv-message" style="white-space:pre-wrap;"></div>
            </div>
            <div class="col-12" id="modal-rdv-elements-container" style="display:none;">
                <div class="modal-field-label">Éléments souhaités</div>
                <div class="modal-field-value" id="modal-rdv-elements"></div>
            </div>
            <div class="col-12" id="modal-rdv-notes-container" style="display:none;">
                <div class="modal-field-label">Notes admin</div>
                <div class="modal-field-value" id="modal-rdv-notes" style="white-space:pre-wrap;background:#f8f6f3;padding:12px;border-radius:6px;"></div>
            </div>
        </div>

        <div style="border-top:1px solid #E6E6E6;padding-top:20px;margin-top:20px;">
            <h4 style="font-size:16px;margin-bottom:16px;">Actions</h4>
            <input type="hidden" id="modal-rdv-id" value="">
            <input type="hidden" id="modal-rdv-status" value="">
            
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn-edit" id="btn-confirmer-rdv" style="background:#d1e7dd;color:#0f5132;" onclick="confirmerRdv()">
                    <i class="bi bi-check-circle me-1"></i>Confirmer
                </button>
                <button class="btn-edit" id="btn-terminer-rdv" style="background:#cfe2ff;color:#084298;" onclick="terminerRdv()">
                    <i class="bi bi-check-all me-1"></i>Marquer terminé
                </button>
                <button class="btn-del" id="btn-annuler-rdv" onclick="annulerRdv()">
                    <i class="bi bi-x-circle me-1"></i>Annuler
                </button>
                <button class="btn-edit" onclick="ajouterNotesRdv()">
                    <i class="bi bi-pencil me-1"></i>Ajouter notes
                </button>
            </div>
        </div>
    </div>
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
    const target = document.getElementById('section-'+name);
    if (!target) return;
    document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    target.classList.add('active');
    document.querySelectorAll('.nav-item[data-section]').forEach(n => {
        if (n.dataset.section === name) n.classList.add('active');
    });
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

// ═══════════════════════════════════════════════════════════
// GESTION DES RENDEZ-VOUS
// ═══════════════════════════════════════════════════════════

function ouvrirDetailRdv(rdv) {
    document.getElementById('modal-rdv-id').value = rdv.id;
    document.getElementById('modal-rdv-status').value = rdv.status;
    document.getElementById('modal-rdv-client').textContent = rdv.first_name + ' ' + rdv.last_name;
    document.getElementById('modal-rdv-email').textContent = rdv.email;
    document.getElementById('modal-rdv-phone').textContent = rdv.phone;
    document.getElementById('modal-rdv-service').textContent = rdv.service;
    
    const date = new Date(rdv.appointment_date);
    document.getElementById('modal-rdv-date').textContent = date.toLocaleDateString('fr-FR');
    document.getElementById('modal-rdv-time').textContent = rdv.appointment_time;
    
    const types = {
        'presentiel': '🏢 Présentiel',
        'visio': '💻 Visioconférence',
        'domicile': '🏠 À domicile'
    };
    document.getElementById('modal-rdv-type').textContent = types[rdv.meeting_type] || rdv.meeting_type;
    
    const budgets = {
        '1': 'Moins de 5 000 dt',
        '2': '5 000 dt - 15 000 dt',
        '3': '15 000 dt - 30 000 dt',
        '4': '30 000 dt - 50 000 dt',
        '5': 'Plus de 50 000 dt'
    };
    document.getElementById('modal-rdv-budget').textContent = budgets[rdv.budget_range] || 'Non spécifié';
    
    // Message
    const msgContainer = document.getElementById('modal-rdv-message-container');
    if (rdv.message && rdv.message.trim()) {
        msgContainer.style.display = 'block';
        document.getElementById('modal-rdv-message').textContent = rdv.message;
    } else {
        msgContainer.style.display = 'none';
    }
    
    // Éléments souhaités
    const elemContainer = document.getElementById('modal-rdv-elements-container');
    if (rdv.elements && rdv.elements.trim()) {
        elemContainer.style.display = 'block';
        document.getElementById('modal-rdv-elements').textContent = rdv.elements;
    } else {
        elemContainer.style.display = 'none';
    }
    
    // Notes admin
    const notesContainer = document.getElementById('modal-rdv-notes-container');
    if (rdv.admin_notes && rdv.admin_notes.trim()) {
        notesContainer.style.display = 'block';
        document.getElementById('modal-rdv-notes').textContent = rdv.admin_notes;
    } else {
        notesContainer.style.display = 'none';
    }
    
    // Gérer l'affichage des boutons selon le statut
    const btnConfirmer = document.getElementById('btn-confirmer-rdv');
    const btnTerminer = document.getElementById('btn-terminer-rdv');
    const btnAnnuler = document.getElementById('btn-annuler-rdv');
    
    if (rdv.status === 'en_attente') {
        btnConfirmer.style.display = 'inline-flex';
        btnTerminer.style.display = 'none';
        btnAnnuler.style.display = 'inline-flex';
    } else if (rdv.status === 'confirme') {
        btnConfirmer.style.display = 'none';
        btnTerminer.style.display = 'inline-flex';
        btnAnnuler.style.display = 'inline-flex';
    } else {
        btnConfirmer.style.display = 'none';
        btnTerminer.style.display = 'none';
        btnAnnuler.style.display = 'none';
    }
    
    document.getElementById('modal-rdv-overlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function fermerModalRdv() {
    document.getElementById('modal-rdv-overlay').style.display = 'none';
    document.body.style.overflow = '';
}

function confirmerRdv() {
    const id = document.getElementById('modal-rdv-id').value;
    const notes = prompt('Notes pour ce rendez-vous (optionnel):');
    
    if (notes !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'admin_actions.php';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'confirmer_rdv';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        const notesInput = document.createElement('input');
        notesInput.type = 'hidden';
        notesInput.name = 'notes';
        notesInput.value = notes;
        
        form.appendChild(actionInput);
        form.appendChild(idInput);
        form.appendChild(notesInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function terminerRdv() {
    const id = document.getElementById('modal-rdv-id').value;
    const notes = prompt('Notes finales pour ce rendez-vous (optionnel):');
    
    if (notes !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'admin_actions.php';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'terminer_rdv';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        const notesInput = document.createElement('input');
        notesInput.type = 'hidden';
        notesInput.name = 'notes';
        notesInput.value = notes;
        
        form.appendChild(actionInput);
        form.appendChild(idInput);
        form.appendChild(notesInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function annulerRdv() {
    const id = document.getElementById('modal-rdv-id').value;
    const raison = prompt('Raison de l\'annulation:');
    
    if (raison && raison.trim()) {
        if (confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin_actions.php';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'annuler_rdv';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = id;
            
            const raisonInput = document.createElement('input');
            raisonInput.type = 'hidden';
            raisonInput.name = 'raison';
            raisonInput.value = raison;
            
            form.appendChild(actionInput);
            form.appendChild(idInput);
            form.appendChild(raisonInput);
            document.body.appendChild(form);
            form.submit();
        }
    } else if (raison !== null) {
        alert('Veuillez indiquer une raison pour l\'annulation.');
    }
}

function ajouterNotesRdv() {
    const id = document.getElementById('modal-rdv-id').value;
    const currentNotes = document.getElementById('modal-rdv-notes').textContent;
    const notes = prompt('Ajouter/Modifier les notes:', currentNotes);
    
    if (notes !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'admin_actions.php';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'ajouter_notes_rdv';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        const notesInput = document.createElement('input');
        notesInput.type = 'hidden';
        notesInput.name = 'notes';
        notesInput.value = notes;
        
        form.appendChild(actionInput);
        form.appendChild(idInput);
        form.appendChild(notesInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('modal-rdv-overlay')?.addEventListener('click', function(e) {
    if (e.target === this) fermerModalRdv();
});
</script>
</body>
</html>