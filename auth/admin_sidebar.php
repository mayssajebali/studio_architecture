<?php
// Get current page name for active state
$current_page = basename($_SERVER['PHP_SELF']);

// Get counts for badges
$nb_attente_nav = 0;
$nb_clients = 0;
$nb_styles = 0;
$nb_rdv_attente = 0;

try {
    $nb_attente_nav = $pdo->query("SELECT COUNT(*) FROM demandes_contact WHERE statut='en_attente'")->fetchColumn();
} catch(Exception $e) {}

try {
    $nb_clients = $pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn();
} catch(Exception $e) {}

try {
    $nb_styles = $pdo->query("SELECT COUNT(*) FROM styles")->fetchColumn();
} catch(Exception $e) {}

try {
    $nb_rdv_attente = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='en_attente'")->fetchColumn();
} catch(Exception $e) {}
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="bi bi-house-heart"></i></div>
        <span class="sidebar-brand-text">ADMIN</span>
    </div>
    <nav class="sidebar-nav">
        <div class="sidebar-section-label">GESTION</div>
        <a href="dashboard_admin.php?section=demandes" class="nav-item <?= $current_page === 'dashboard_admin.php' ? 'active' : '' ?>" data-tooltip="Demandes clients">
            <i class="bi bi-envelope"></i><span class="nav-item-label">Demandes clients</span>
            <?php if ($nb_attente_nav > 0): ?>
            <span class="nav-badge"><?= $nb_attente_nav ?></span>
            <?php endif; ?>
        </a>
        <a href="dashboard_admin.php?section=rendez-vous" class="nav-item" data-tooltip="Rendez-vous">
            <i class="bi bi-calendar-check"></i><span class="nav-item-label">Rendez-vous</span>
            <?php if ($nb_rdv_attente > 0): ?>
            <span class="nav-badge" style="background:#ff6b6b;color:#fff;"><?= $nb_rdv_attente ?></span>
            <?php endif; ?>
        </a>
        <a href="dashboard_admin.php?section=styles" class="nav-item" data-tooltip="Styles">
            <i class="bi bi-palette"></i><span class="nav-item-label">Styles</span>
            <?php if ($nb_styles > 0): ?>
            <span class="nav-badge" style="background:#f0ebe6;color:#b8a99a;"><?= $nb_styles ?></span>
            <?php endif; ?>
        </a>
        
        <div class="sidebar-section-label" style="margin-top:8px;">CONTENU</div>
        <a href="dashboard_admin.php?section=avis" class="nav-item" data-tooltip="Avis">
            <i class="bi bi-star"></i><span class="nav-item-label">Avis</span>
        </a>
        <a class="nav-item" href="gestion_portfolio.php" data-tooltip="Portfolio">
            <i class="bi bi-images"></i><span class="nav-item-label">Portfolio</span>
        </a>
        
        <div class="sidebar-section-label" style="margin-top:8px;">ADMINISTRATION</div>
        <a class="nav-item <?= $current_page === 'gestion_clients.php' ? 'active' : '' ?>" href="gestion_clients.php" data-tooltip="Clients">
            <i class="bi bi-people"></i><span class="nav-item-label">Clients</span>
            <?php if ($nb_clients > 0): ?>
            <span class="nav-badge" style="background:#d1e7dd;color:#0f5132;"><?= $nb_clients ?></span>
            <?php endif; ?>
        </a>
        <a class="nav-item <?= $current_page === 'gestion_admins.php' ? 'active' : '' ?>" href="gestion_admins.php" data-tooltip="Administrateurs">
            <i class="bi bi-shield-check"></i><span class="nav-item-label">Administrateurs</span>
        </a>
        <a class="nav-item <?= $current_page === 'profil_admin.php' ? 'active' : '' ?>" href="profil_admin.php" data-tooltip="Mon profil">
            <i class="bi bi-person-circle"></i><span class="nav-item-label">Mon profil</span>
        </a>
        <a class="nav-item" href="logout.php" data-tooltip="Déconnexion" style="color:#c0392b;">
            <i class="bi bi-box-arrow-right"></i><span class="nav-item-label">Déconnexion</span>
        </a>
    </nav>
    <div class="sidebar-toggle">
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="bi bi-chevron-left" id="toggle-chevron"></i></button>
    </div>
</aside>
