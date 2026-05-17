<?php
session_start();

if (empty($_SESSION['client_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

$pdo  = getDB();
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['client_id']]);
$client = $stmt->fetch();

if (!$client) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$bienvenue = isset($_GET['bienvenue']);
$initiales = strtoupper(mb_substr($client['prenom'], 0, 1) . mb_substr($client['nom'], 0, 1));

$req = $pdo->prepare('SELECT * FROM demandes_contact WHERE email = ? ORDER BY date_envoi DESC');
$req->execute([$client['email']]);
$demandes = $req->fetchAll();

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mon Espace — GreenHome</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Inter:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../style.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --sand: #b8a99a; --sand-light: #e8ddd4;
      --dark: #1a1a1a; --ivory: #f8f6f3; --white: #ffffff;
      --border: #e6e0d8; --text-main: #1a1a1a; --text-sub: #6b6560;
      --text-hint: #a09b95; --green: #2d6a4f; --green-light: #d8f3dc;
      --blue-light: #cfe2ff; --blue: #084298;
      --yellow-light: #fff3cd; --yellow: #856404; --radius: 12px;
    }
    body { font-family: 'Inter', sans-serif; background: var(--ivory); color: var(--text-main); padding-top: 80px; }

    /* HERO */
    .profile-hero { background: var(--dark); padding: 48px 0 0; position: relative; overflow: hidden; }
    .profile-hero::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse at 80% 0%, rgba(184,169,154,.15) 0%, transparent 65%); }
    .profile-hero-inner { max-width: 1100px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 1; display: flex; align-items: flex-end; gap: 2rem; }
    .hero-avatar { width: 88px; height: 88px; border-radius: 50%; background: linear-gradient(135deg, rgba(184,169,154,.3), rgba(184,169,154,.1)); border: 2px solid rgba(184,169,154,.4); display: flex; align-items: center; justify-content: center; font-family: 'Cinzel', serif; font-size: 28px; font-weight: 500; color: var(--sand); flex-shrink: 0; margin-bottom: 14px; }
    .hero-info { padding-bottom: 24px; }
    .hero-tag { font-size: 10px; letter-spacing: .3em; text-transform: uppercase; color: var(--sand); opacity: .7; margin-bottom: 6px; }
    .hero-name { font-family: 'Cinzel', serif; font-size: 26px; font-weight: 400; color: var(--white); line-height: 1.2; }
    .hero-email { font-size: 13px; color: rgba(255,255,255,.4); margin-top: 4px; }
    .hero-tabs { margin-left: auto; display: flex; align-self: flex-end; }
    .hero-tab { padding: 12px 22px; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; font-weight: 500; color: rgba(255,255,255,.45); border: none; background: none; border-bottom: 2px solid transparent; cursor: pointer; transition: color .2s, border-color .2s; text-decoration: none; }
    .hero-tab:hover { color: rgba(255,255,255,.8); }
    .hero-tab.active { color: var(--white); border-bottom-color: var(--sand); }

    /* CONTENT */
    .page-content { max-width: 1100px; margin: 0 auto; padding: 2.5rem 2rem 4rem; }

    /* BIENVENUE */
    .banner-welcome { background: linear-gradient(135deg, #2d6a4f 0%, #1b4332 100%); border-radius: var(--radius); padding: 1.25rem 1.75rem; display: flex; align-items: center; gap: 1.25rem; margin-bottom: 2rem; border: 1px solid rgba(255,255,255,.1); animation: slideDown .4s ease; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }
    .bw-icon { font-size: 22px; }
    .bw-title { font-family: 'Cinzel', serif; font-size: 16px; color: #fff; }
    .bw-sub { font-size: 12px; color: rgba(255,255,255,.6); margin-top: 3px; }
    .bw-close { margin-left: auto; background: none; border: none; color: rgba(255,255,255,.4); font-size: 20px; cursor: pointer; transition: color .2s; }
    .bw-close:hover { color: #fff; }

    /* LAYOUT */
    .profile-grid { display: grid; grid-template-columns: 280px 1fr; gap: 1.25rem; margin-bottom: 1.25rem; }

    /* IDENTITÉ */
    .profile-identity { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); padding: 2rem 1.75rem; }
    .pi-avatar { width: 64px; height: 64px; border-radius: 50%; background: var(--dark); border: 2px solid rgba(184,169,154,.3); display: flex; align-items: center; justify-content: center; font-family: 'Cinzel', serif; font-size: 20px; color: var(--sand); margin-bottom: 1.25rem; }
    .pi-name { font-family: 'Cinzel', serif; font-size: 18px; font-weight: 400; color: var(--text-main); line-height: 1.3; }
    .pi-email { font-size: 12px; color: var(--text-hint); margin-top: 4px; }
    .pi-divider { height: 1px; background: var(--border); margin: 1.25rem 0; }
    .pi-field { margin-bottom: .875rem; }
    .pi-label { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: var(--text-hint); margin-bottom: 3px; }
    .pi-value { font-size: 13px; color: var(--text-main); }
    .pi-badge { display: inline-flex; align-items: center; gap: 5px; background: var(--green-light); color: var(--green); padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }
    .pi-badge::before { content: '●'; font-size: 7px; }

    /* STATS (droite) */
    .stats-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.75rem; display: flex; flex-direction: column; }
    .stats-card-title { font-family: 'Cinzel', serif; font-size: 14px; font-weight: 500; color: var(--text-main); letter-spacing: .04em; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border); }
    .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; flex: 1; }
    .stat-item { background: var(--ivory); border: 1px solid var(--border); border-radius: 10px; padding: 1.5rem; position: relative; overflow: hidden; transition: box-shadow .2s, transform .2s; }
    .stat-item:hover { box-shadow: 0 4px 16px rgba(0,0,0,.06); transform: translateY(-2px); }
    .stat-item::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; border-radius: 10px 10px 0 0; }
    .stat-item.c-total::before   { background: var(--sand); }
    .stat-item.c-attente::before { background: #f4b400; }
    .stat-item.c-encours::before { background: #4a90d9; }
    .stat-item.c-termine::before { background: #2d6a4f; }
    .stat-num { font-family: 'Cinzel', serif; font-size: 40px; font-weight: 500; color: var(--text-main); line-height: 1; }
    .stat-label { font-size: 11px; letter-spacing: .15em; text-transform: uppercase; color: var(--text-hint); margin-top: 8px; }
    .stat-icon { position: absolute; right: 1rem; top: 1rem; font-size: 24px; opacity: .1; }

    /* TABLE */
    .card-gh { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; margin-bottom: 1.25rem; }
    .card-gh-header { padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .card-gh-title { font-family: 'Cinzel', serif; font-size: 14px; font-weight: 500; color: var(--text-main); letter-spacing: .04em; }
    .demandes-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .demandes-table th { background: var(--ivory); padding: 10px 16px; font-size: 10px; font-weight: 600; letter-spacing: .18em; text-transform: uppercase; color: var(--text-hint); border-bottom: 2px solid var(--border); text-align: left; }
    .demandes-table td { padding: 14px 16px; border-bottom: 1px solid #f0ebe6; vertical-align: top; }
    .demandes-table tr:last-child td { border-bottom: none; }
    .demandes-table tr:hover td { background: #faf8f6; }
    .td-service { font-weight: 500; color: var(--text-main); margin-bottom: 2px; }
    .td-msg { font-size: 12px; color: var(--text-hint); line-height: 1.5; }
    .reponse-block { background: #f0f7f0; border: 1px solid #b8d4b8; border-radius: 6px; padding: 8px 12px; font-size: 12px; color: #2d5a2d; line-height: 1.6; }
    .no-reponse { font-size: 12px; color: var(--text-hint); font-style: italic; }

    /* PILLS */
    .pill { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 500; }
    .pill-attente { background: var(--yellow-light); color: var(--yellow); }
    .pill-encours { background: var(--blue-light); color: var(--blue); }
    .pill-termine { background: var(--green-light); color: var(--green); }
    .pill-archive { background: #f0ebe6; color: #6D6D6D; }
    .pill-unknown { background: rgba(26,24,20,.08); color: var(--text-main); }

    /* BTN */
    .btn-primary-gh { display: inline-flex; align-items: center; gap: 8px; background: var(--dark); color: var(--white); border: none; border-radius: 6px; padding: 10px 22px; font-family: 'Inter', sans-serif; font-size: 12px; font-weight: 500; letter-spacing: .08em; text-transform: uppercase; text-decoration: none; cursor: pointer; transition: background .2s; }
    .btn-primary-gh:hover { background: #333; color: var(--white); }

    /* EMPTY */
    .empty-state { text-align: center; padding: 60px 20px; color: var(--text-hint); }
    .empty-state i { font-size: 40px; display: block; margin-bottom: 12px; opacity: .3; }
    .empty-state p { font-size: 14px; }

    @media (max-width: 768px) {
      .profile-grid { grid-template-columns: 1fr; }
      .hero-tabs { display: none; }
      .profile-hero-inner { flex-wrap: wrap; }
      .page-content { padding: 1.5rem 1rem 3rem; }
      .stats-grid { grid-template-columns: 1fr 1fr; }
    }
  </style>
</head>
<body>

<?php include '../navbar.php'; ?>


<!-- CONTENT -->
<div class="page-content">

  <?php if ($bienvenue): ?>
  <div class="banner-welcome" id="banner">
    <div class="bw-icon">🎉</div>
    <div>
      <div class="bw-title">Bienvenue, <?= htmlspecialchars($client['prenom']) ?> !</div>
      <div class="bw-sub">Votre compte a été créé avec succès. Votre architecte vous contactera bientôt.</div>
    </div>
    <button class="bw-close" onclick="document.getElementById('banner').remove()">×</button>
  </div>
  <?php endif; ?>

  <!-- Identité + Stats -->
  <div class="profile-grid">

    <!-- Gauche : identité -->
    <div class="profile-identity">
      <div class="pi-avatar"><?= htmlspecialchars($initiales) ?></div>
      <div class="pi-name"><?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?></div>
      <div class="pi-email"><?= htmlspecialchars($client['email']) ?></div>
      <div class="pi-divider"></div>
      <?php if (!empty($client['telephone'])): ?>
      <div class="pi-field">
        <div class="pi-label">Téléphone</div>
        <div class="pi-value"><?= htmlspecialchars($client['telephone']) ?></div>
      </div>
      <?php endif; ?>
      <div class="pi-field">
        <div class="pi-label">Identifiant client</div>
        <div class="pi-value">#<?= str_pad($client['id'], 5, '0', STR_PAD_LEFT) ?></div>
      </div>
      <div class="pi-field">
        <div class="pi-label">Membre depuis</div>
        <div class="pi-value"><?= date('d/m/Y', strtotime($client['created_at'])) ?></div>
      </div>
      <div class="pi-field">
        <div class="pi-label">Statut</div>
        <div class="pi-value"><span class="pi-badge">Actif</span></div>
      </div>
      <div class="pi-divider"></div>
      <a href="../contact/contact.php" class="btn-primary-gh" style="width:100%;justify-content:center;">
        <i class="bi bi-plus-lg"></i> Nouvelle demande
      </a>
    </div>

    <!-- Droite : stats -->
    <div class="stats-card">
      <div class="stats-card-title">Suivi de mes demandes</div>
      <div class="stats-grid">
        <div class="stat-item c-total">
          <div class="stat-num"><?= $totalDemandes ?></div>
          <div class="stat-label">Total</div>
          <i class="bi bi-folder stat-icon"></i>
        </div>
        <div class="stat-item c-attente">
          <div class="stat-num"><?= $nbAttente ?></div>
          <div class="stat-label">En attente</div>
          <i class="bi bi-clock stat-icon"></i>
        </div>
        <div class="stat-item c-encours">
          <div class="stat-num"><?= $nbEncours ?></div>
          <div class="stat-label">En cours</div>
          <i class="bi bi-arrow-repeat stat-icon"></i>
        </div>
        <div class="stat-item c-termine">
          <div class="stat-num"><?= $nbTermine ?></div>
          <div class="stat-label">Terminées</div>
          <i class="bi bi-check-circle stat-icon"></i>
        </div>
      </div>
    </div>

  </div>

  <!-- Tableau demandes -->
  <div class="card-gh">
    <div class="card-gh-header">
      <div class="card-gh-title">Toutes mes demandes</div>
      <span style="background:var(--dark);color:#fff;padding:3px 12px;border-radius:20px;font-size:12px;">
        <?= $totalDemandes ?> demande<?= $totalDemandes > 1 ? 's' : '' ?>
      </span>
    </div>
    <?php if (empty($demandes)): ?>
      <div class="empty-state">
        <i class="bi bi-folder2-open"></i>
        <p>Aucune demande pour le moment.</p>
      </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
      <table class="demandes-table">
        <thead>
          <tr>
            <th>Projet</th><th>Budget</th><th>Date</th><th>Statut</th><th>Réponse</th>
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
            <td>
              <div class="td-service"><?= htmlspecialchars(ucfirst($d['service'] ?? 'Non précisé')) ?></div>
              <div class="td-msg"><?= htmlspecialchars(mb_strimwidth($d['message'] ?? '', 0, 90, '...')) ?></div>
            </td>
            <td style="font-size:13px;white-space:nowrap;"><?= htmlspecialchars($d['budget'] ?? 'N/A') ?></td>
            <td style="font-size:12px;color:var(--text-hint);white-space:nowrap;"><?= isset($d['date_envoi']) ? date('d/m/Y', strtotime($d['date_envoi'])) : '—' ?></td>
            <td><span class="pill <?= $sc ?>"><?= $sl ?></span></td>
            <td>
              <?php if (!empty($d['reponse_admin'])): ?>
                <div class="reponse-block"><?= htmlspecialchars(mb_strimwidth($d['reponse_admin'], 0, 120, '...')) ?></div>
              <?php else: ?>
                <span class="no-reponse">En attente de réponse…</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>