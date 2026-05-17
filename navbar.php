<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$projectBase = basename(__DIR__);
$prefix = '';

$scriptParts = array_filter(explode('/', trim($scriptName, '/')));
if (!empty($scriptParts) && $scriptParts[0] === $projectBase) {
    array_shift($scriptParts);
}
$directoryDepth = max(0, count($scriptParts) - 1);
if ($directoryDepth > 0) {
    $prefix = str_repeat('../', $directoryDepth);
}

$currentPage = basename($scriptName);

$est_admin = !empty($_SESSION['admin']);
$admin_nom = $est_admin ? htmlspecialchars($_SESSION['admin_nom']) : '';
$admin_init = $est_admin ? mb_strtoupper(mb_substr($_SESSION['admin_nom'], 0, 1)) : '';

$connecte = !$est_admin && !empty($_SESSION['client_id']);
$prenom   = $connecte ? htmlspecialchars($_SESSION['client_nom']) : '';
$initiale = $connecte ? mb_strtoupper(mb_substr($_SESSION['client_nom'], 0, 1)) : '';
?>
<header>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand ms-5" href="<?= $prefix ?>accueil.php">
                <img src="<?= $prefix ?>images/logo.png" alt="Logo" height="30">
            </a>
           
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-4 align-items-center">
                    <li class="nav-item me-4">
                        <a class="nav-link <?= $currentPage === 'accueil.php' ? 'active' : '' ?>" href="<?= $prefix ?>accueil.php">ACCUEIL</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link <?= $currentPage === 'apropos.php' ? 'active' : '' ?>" href="<?= $prefix ?>apropos.php">A PROPOS</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link <?= $currentPage === 'services.php' ? 'active' : '' ?>" href="<?= $prefix ?>services.php">SERVICES</a>
                    </li>
                    <li class="nav-item me-4">
                        <a class="nav-link <?= $currentPage === 'portfolio.php' ? 'active' : '' ?>" href="<?= $prefix ?>portfolio/portfolio.php">PORTFOLIO</a>
                    </li>
                    <li class="nav-item me-5">
                        <a class="nav-link <?= $currentPage === 'contact.php' ? 'active' : '' ?>" href="<?= $prefix ?>contact/contact.php">CONTACT</a>
                    </li>

                    <?php if ($est_admin): ?>
                    <!-- ── Admin connecté ── -->
                    <li class="nav-item dropdown">
                        <a href="<?= $prefix ?>auth/dashboard_admin.php"
                           class="nav-link dropdown-toggle d-flex align-items-center gap-2 p-0"
                           id="adminDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar-icon avatar-admin" title="Admin">
                                <?= $admin_init ?>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end profil-dropdown" aria-labelledby="adminDropdown">
                            <li class="dropdown-header-custom">
                                <div class="dp-name"><?= $admin_nom ?></div>
                                <div class="dp-role" style="color:#b8a99a;">Administrateur</div>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item" href="<?= $prefix ?>auth/dashboard_admin.php">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                    Dashboard
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= $prefix ?>auth/logout.php">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                                    Déconnexion
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php elseif ($connecte): ?>
                    <!-- ── Client connecté (votre code original intact) ── -->
                    <li class="nav-item dropdown">
                        <a href="<?= $prefix ?>auth/profil.php" class="nav-link dropdown-toggle d-flex align-items-center gap-2 p-0"
                           id="profilDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar-icon" title="<?= $prenom ?>">
                                <?= $initiale ?>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end profil-dropdown" aria-labelledby="profilDropdown">
                            <li class="dropdown-header-custom">
                                <div class="dp-name"><?= $prenom ?></div>
                                <div class="dp-role">Client</div>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item" href="<?= $prefix ?>auth/profil.php">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                    Mon profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= $prefix ?>auth/profil.php#projet">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                                    Mon projet
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= $prefix ?>auth/logout.php">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                                    Déconnexion
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php else: ?>
                    <!-- ── Non connecté ── -->
                    <li class="nav-item">
                        <a href="<?= $prefix ?>auth/login.php" class="btn-inscrire">Se connecter</a>
                    </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>
</header>

<style>
.btn-inscrire {
    font-family: "Inter", sans-serif;
    font-size: 0.8rem;
    font-weight: 500;
    letter-spacing: 0.5px;
    color: #ffffff;
    background-color: #b8a99a;
    border: 1.5px solid #b8a99a;
    padding: .42rem 1.2rem;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color .25s ease, color .25s ease, border-color .25s ease;
    white-space: nowrap;
    line-height: 1;
}
.btn-inscrire:hover {
    background-color: #a8998a;
    border-color: #a8998a;
    color: #ffffff;
}
.avatar-icon {
    width: 36px; height: 36px;
    border-radius: 50%;
    background-color: #f5f1ee;
    border: 1.5px solid #b8a99a;
    display: flex; align-items: center; justify-content: center;
    font-family: "Inter", sans-serif;
    font-size: 13px; font-weight: 600;
    color: #b8a99a;
    cursor: pointer;
    transition: background-color .2s ease;
    letter-spacing: 0;
    user-select: none;
}
.avatar-icon:hover { background-color: #ede7e2; }

/* Avatar admin : fond sombre pour le distinguer du client */
.avatar-admin {
    background-color: #1a1a1a !important;
    color: #b8a99a !important;
    border-color: #1a1a1a !important;
    font-weight: 700;
}
.avatar-admin:hover { background-color: #333 !important; }

#profilDropdown::after { display: none; }
#adminDropdown::after  { display: none; }
.nav-item.dropdown > a.nav-link::after { display: none !important; }

.profil-dropdown {
    min-width: 210px;
    border: 1px solid #E6E6E6;
    border-radius: 6px;
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
    padding: .5rem 0;
    margin-top: .6rem;
    font-family: "Inter", sans-serif;
}
.dropdown-header-custom { padding: .65rem 1rem .5rem; }
.dp-name { font-size: 13.5px; font-weight: 600; color: #333333; letter-spacing: .01em; }
.dp-role { font-size: 11px; color: #a09b95; letter-spacing: .05em; text-transform: uppercase; margin-top: 2px; font-weight: 400; }
.profil-dropdown .dropdown-item {
    font-family: "Inter", sans-serif;
    font-size: 13px; font-weight: 400; color: #555555;
    padding: .5rem 1rem;
    display: flex; align-items: center; gap: .6rem;
    transition: background .15s, color .15s;
    letter-spacing: .01em;
}
.profil-dropdown .dropdown-item:hover { background-color: #f9f7f5; color: #333333; }
.profil-dropdown .dropdown-item.text-danger { color: #c0392b !important; }
.profil-dropdown .dropdown-item.text-danger:hover { background-color: #fdf3f2; }
.dropdown-divider { border-color: #E6E6E6; }
</style>