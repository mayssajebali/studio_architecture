<?php
session_start();


if (!empty($_SESSION['client_id'])) {
    header('Location: profil.php');
    exit;
}

require_once 'db.php';

$erreurs = [];
$valeurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $valeurs = [
        'prenom'      => trim($_POST['prenom']      ?? ''),
        'nom'         => trim($_POST['nom']         ?? ''),
        'email'       => trim($_POST['email']       ?? ''),
        'telephone'   => trim($_POST['telephone']   ?? ''),
        'password'    => $_POST['password']         ?? '',
        'confirm'     => $_POST['confirm']          ?? '',
    ];

  
    if (empty($valeurs['prenom']))
        $erreurs['prenom'] = 'Le prénom est requis.';

    if (empty($valeurs['nom']))
        $erreurs['nom'] = 'Le nom est requis.';

    if (empty($valeurs['email']) || !filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL))
        $erreurs['email'] = 'Adresse e-mail invalide.';

    if (strlen($valeurs['password']) < 8)
        $erreurs['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';

    if ($valeurs['password'] !== $valeurs['confirm'])
        $erreurs['confirm'] = 'Les mots de passe ne correspondent pas.';



    if (empty($erreurs['email'])) {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$valeurs['email']]);
        if ($stmt->fetch()) {
            $erreurs['email'] = 'Cette adresse e-mail est déjà utilisée.';
        }
    }

  
    if (empty($erreurs)) {
        $pdo  = getDB();
        $hash = password_hash($valeurs['password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare('
            INSERT INTO users
                (prenom, nom, email, telephone, mot_de_passe)
            VALUES
                (:prenom, :nom, :email, :telephone, :mot_de_passe)
        ');

        $stmt->execute([
            ':prenom'      => $valeurs['prenom'],
            ':nom'         => $valeurs['nom'],
            ':email'       => $valeurs['email'],
            ':telephone'   => $valeurs['telephone'] ?: null,
            ':mot_de_passe'=> $hash,
        ]);

       
        $_SESSION['client_id']  = $pdo->lastInsertId();
        $_SESSION['client_nom'] = $valeurs['prenom'] . ' ' . $valeurs['nom'];

        header('Location: profil.php?bienvenue=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inscription — Studio Architecture</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --sand:#c8b99a; --dark:#1a1814; --dark-mid:#2c2822;
      --ivory:#f0ebe3; --muted:#8a8078;
      --form-bg:#ffffff; --text-main:#1a1814; --text-sub:#6b6560;
      --text-hint:#a09b95; --border:#e0dbd4; --input-bg:#f9f7f4;
      --error:#c0392b; --error-bg:#fdf3f2; --radius:6px;
    }
    html,body{ height:100%; font-family:'Jost',sans-serif; background:var(--dark); }

    .page{ min-height:100vh; display:grid; grid-template-columns:1fr 1fr; }

    /* ── LEFT ── */
    .visual-panel{
      position:relative; background:var(--dark); overflow:hidden;
      display:flex; flex-direction:column; justify-content:flex-end; padding:3rem;
    }
    .arch-svg{ position:absolute; inset:0; width:100%; height:100%; opacity:.10; pointer-events:none; }
    .panel-tag{ font-size:13px; letter-spacing:.4em; text-transform:uppercase; color:var(--sand); font-weight:300; margin-bottom:2rem; opacity:.8; }
    .panel-heading{ font-family:'Cormorant Garamond',serif; font-size:clamp(34px,4vw,50px); font-weight:300; color:var(--ivory); line-height:1.1; }
    .panel-heading em{ font-style:italic; color:var(--sand); }
    .panel-rule{ width:40px; height:1px; background:var(--sand); opacity:.6; margin:1.5rem 0; }
    .panel-sub{ font-size:13px; letter-spacing:.12em; color:var(--muted); font-weight:300; }
    .steps{ margin-top:2.5rem; display:flex; flex-direction:column; gap:1rem; }
    .step{ display:flex; align-items:flex-start; gap:1rem; }
    .step-num{
      width:30px; height:30px; border:1px solid rgba(200,185,154,.4); border-radius:50%;
      display:flex; align-items:center; justify-content:center;
      font-size:17px; color:var(--sand); font-family:'Cormorant Garamond',serif; flex-shrink:0; margin-top:1px;
    }
    .step-text{ font-size:16px; color:#6a6258; letter-spacing:.04em; line-height:1.5; font-weight:300; }
    .panel-footer{ margin-top:3rem; font-size:10px; letter-spacing:.08em; color:#4a4540; font-weight:300; }

    /* ── RIGHT ── */
    .form-panel{
      background:var(--form-bg); display:flex; flex-direction:column; justify-content:center;
      padding:3rem clamp(2rem,5vw,4rem); overflow-y:auto;
    }
    .form-eyebrow{ font-size:10px; letter-spacing:.3em; text-transform:uppercase; color:var(--text-hint); font-weight:400; margin-bottom:.5rem; }
    .form-title{ font-family:'Cormorant Garamond',serif; font-size:32px; font-weight:400; color:var(--text-main); margin-bottom:.4rem; }
    .form-desc{ font-size:14px; color:var(--text-hint); letter-spacing:.02em; font-weight:300; margin-bottom:2rem; }

    .section-label{
      font-size:10px; letter-spacing:.3em; text-transform:uppercase; color:#b8a99a;
      font-weight:400; margin-bottom:1rem; margin-top:1.5rem;
      display:flex; align-items:center; gap:.75rem;
    }
    .section-label::after{ content:''; flex:1; height:1px; background:var(--border); }

    .field{ margin-bottom:1.1rem; }
    .field label{ display:block; font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:var(--text-sub); font-weight:400; margin-bottom:.45rem; }

    .field input,
    .field select{
      width:100%; background:var(--input-bg); border:1px solid var(--border);
      border-radius:var(--radius); padding:.75rem 1rem;
      font-size:15px; font-family:'Jost',sans-serif; font-weight:300; color:var(--text-main);
      outline:none; transition:border-color .2s,background .2s; -webkit-appearance:none; appearance:none;
    }
    .field input::placeholder{ color:var(--text-hint); }
    .field input:focus,.field select:focus{ border-color:var(--sand); background:#fff; }
    .field input.error,.field select.error{ border-color:var(--error); background:var(--error-bg); }

    .field select{
      background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23a09b95' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
      background-repeat:no-repeat; background-position:right 1rem center; padding-right:2.5rem; cursor:pointer;
    }

    .field-row{ display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }

    .err-msg{ font-size:11px; color:var(--error); margin-top:.35rem; letter-spacing:.02em; }

    /* Alerte globale */
    .alert{ background:var(--error-bg); border:1px solid #f5c6c6; border-radius:var(--radius); padding:.75rem 1rem; font-size:12px; color:var(--error); margin-bottom:1.5rem; }

    /* Strength */
    .pw-strength{ display:flex; gap:4px; margin-top:.4rem; }
    .pw-bar{ flex:1; height:2px; background:var(--border); border-radius:2px; transition:background .3s; }
    .pw-bar.weak{ background:#e24b4a; }
    .pw-bar.medium{ background:#ef9f27; }
    .pw-bar.strong{ background:#639922; }
    .pw-hint{ font-size:12px; color:var(--text-hint); margin-top:.35rem; letter-spacing:.03em; }

    /* Checkbox */
    .check-row{ display:flex; align-items:flex-start; gap:.65rem; margin-top:1.25rem; margin-bottom:1.5rem; }
    .check-row input[type="checkbox"]{ width:16px; height:16px; accent-color:var(--dark); flex-shrink:0; margin-top:1px; cursor:pointer; }
    .check-row label{ font-size:11px; color:var(--text-hint); line-height:1.5; letter-spacing:.02em; font-weight:300; }
    .check-row label a{ color:var(--text-sub); text-decoration:none; border-bottom:1px solid var(--border); }
    .check-row.error-field label{ color:var(--error); }

    .btn-primary{
      width:100%; padding:.9rem; background:var(--dark); color:var(--ivory);
      border:none; border-radius:var(--radius);
      font-family:'Jost',sans-serif; font-size:10px; font-weight:400;
      letter-spacing:.3em; text-transform:uppercase; cursor:pointer;
      transition:background .2s; margin-bottom:1.5rem;
    }
    .btn-primary:hover{ background:var(--dark-mid); }

    .switch-text{ text-align:center; font-size:13px; color:var(--text-hint); letter-spacing:.02em; }
    .switch-text a{ color:var(--text-main); text-decoration:none; border-bottom:1px solid var(--border); padding-bottom:1px; }
    .switch-text a:hover{ border-color:var(--text-main); }

    @media(max-width:900px){
      .page{ grid-template-columns:1fr; }
      .visual-panel{ min-height:200px; padding:2rem; }
      .steps{ display:none; }
      .form-panel{ padding:2.5rem 1.75rem; }
    }
    @media(max-width:480px){
      .field-row{ grid-template-columns:1fr; }
    }
  </style>
</head>
<body>
<div class="page">

  <!-- LEFT -->
  <div class="visual-panel">
    <svg class="arch-svg" viewBox="0 0 500 800" fill="none" xmlns="http://www.w3.org/2000/svg">
      <line x1="0" y1="160" x2="500" y2="160" stroke="white" stroke-width="0.5"/>
      <line x1="0" y1="320" x2="500" y2="320" stroke="white" stroke-width="0.5"/>
      <line x1="0" y1="480" x2="500" y2="480" stroke="white" stroke-width="0.5"/>
      <line x1="0" y1="640" x2="500" y2="640" stroke="white" stroke-width="0.5"/>
      <line x1="100" y1="0" x2="100" y2="800" stroke="white" stroke-width="0.5"/>
      <line x1="200" y1="0" x2="200" y2="800" stroke="white" stroke-width="0.5"/>
      <line x1="300" y1="0" x2="300" y2="800" stroke="white" stroke-width="0.5"/>
      <line x1="400" y1="0" x2="400" y2="800" stroke="white" stroke-width="0.5"/>
      <rect x="80" y="320" width="340" height="280" stroke="white" stroke-width="0.8" fill="none"/>
      <line x1="50" y1="320" x2="200" y2="260" stroke="white" stroke-width="0.6"/>
      <line x1="450" y1="320" x2="200" y2="260" stroke="white" stroke-width="0.6"/>
      <line x1="50" y1="320" x2="450" y2="320" stroke="white" stroke-width="1"/>
      <line x1="130" y1="320" x2="130" y2="600" stroke="white" stroke-width="0.5"/>
      <line x1="200" y1="320" x2="200" y2="600" stroke="white" stroke-width="0.5"/>
      <line x1="270" y1="320" x2="270" y2="600" stroke="white" stroke-width="0.5"/>
      <line x1="340" y1="320" x2="340" y2="600" stroke="white" stroke-width="0.5"/>
      <rect x="95" y="345" width="25" height="35" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="148" y="345" width="42" height="35" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="218" y="345" width="42" height="35" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="288" y="345" width="42" height="35" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="358" y="345" width="52" height="35" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="148" y="410" width="42" height="35" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="288" y="410" width="42" height="35" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="218" y="455" width="52" height="145" stroke="white" stroke-width="0.5" fill="none"/>
      <line x1="244" y1="455" x2="244" y2="600" stroke="white" stroke-width="0.3"/>
      <circle cx="232" cy="528" r="2.5" stroke="white" stroke-width="0.5" fill="none"/>
      <circle cx="256" cy="528" r="2.5" stroke="white" stroke-width="0.5" fill="none"/>
      <line x1="0" y1="740" x2="500" y2="740" stroke="white" stroke-width="0.8"/>
    </svg>

    <div class="panel-tag">GreenHome &nbsp;·&nbsp; Architecture &amp; Design</div>
    <div class="panel-heading">Rejoignez<br>notre<br><em>communauté</em></div>
    <div class="panel-rule"></div>
    <div class="panel-sub">Accédez à vos projets en temps réel</div>
    <div class="steps">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-text">Créez votre profil client en quelques minutes</div>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-text">Suivez l'avancement de vos projets architecturaux</div>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-text">Donner votre avis et s'inscrire au newsletter</div>
      </div>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="form-panel">
    <div class="form-eyebrow">Espace client</div>
    <div class="form-title">Créer un compte</div>
    <div class="form-desc">Remplissez les informations ci-dessous pour accéder à votre espace.</div>

    <?php if (!empty($erreurs) && !isset($erreurs['prenom']) && !isset($erreurs['nom']) && !isset($erreurs['email']) && !isset($erreurs['password']) && !isset($erreurs['confirm'])): ?>
      <div class="alert">Veuillez corriger les erreurs ci-dessous.</div>
    <?php endif; ?>

    <form method="POST" action="register.php" novalidate>

      <div class="section-label">Informations personnelles</div>

      <div class="field-row">
        <div class="field">
          <label for="prenom">Prénom</label>
          <input type="text" id="prenom" name="prenom"
            placeholder="Marie"
            value="<?= htmlspecialchars($valeurs['prenom'] ?? '') ?>"
            class="<?= isset($erreurs['prenom']) ? 'error' : '' ?>"/>
          <?php if (isset($erreurs['prenom'])): ?><div class="err-msg"><?= $erreurs['prenom'] ?></div><?php endif; ?>
        </div>
        <div class="field">
          <label for="nom">Nom</label>
          <input type="text" id="nom" name="nom"
            placeholder="Dupont"
            value="<?= htmlspecialchars($valeurs['nom'] ?? '') ?>"
            class="<?= isset($erreurs['nom']) ? 'error' : '' ?>"/>
          <?php if (isset($erreurs['nom'])): ?><div class="err-msg"><?= $erreurs['nom'] ?></div><?php endif; ?>
        </div>
      </div>

      <div class="field">
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email"
          placeholder="marie.dupont@exemple.com"
          value="<?= htmlspecialchars($valeurs['email'] ?? '') ?>"
          class="<?= isset($erreurs['email']) ? 'error' : '' ?>"/>
        <?php if (isset($erreurs['email'])): ?><div class="err-msg"><?= $erreurs['email'] ?></div><?php endif; ?>
      </div>

      <div class="field">
        <label for="telephone">Téléphone</label>
        <input type="tel" id="telephone" name="telephone"
          placeholder="+216 XX XXX XXX"
          value="<?= htmlspecialchars($valeurs['telephone'] ?? '') ?>"/>
      </div>


      <div class="section-label">Sécurité</div>

      <div class="field">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password"
          placeholder="Min. 8 caractères"
          class="<?= isset($erreurs['password']) ? 'error' : '' ?>"
          oninput="updateStrength(this.value)" />
        <div class="pw-strength">
          <div class="pw-bar" id="bar1"></div>
          <div class="pw-bar" id="bar2"></div>
          <div class="pw-bar" id="bar3"></div>
          <div class="pw-bar" id="bar4"></div>
        </div>
        <div class="pw-hint" id="pw-hint">Choisissez un mot de passe robuste</div>
        <?php if (isset($erreurs['password'])): ?><div class="err-msg"><?= $erreurs['password'] ?></div><?php endif; ?>
      </div>

      <div class="field">
        <label for="confirm">Confirmer le mot de passe</label>
        <input type="password" id="confirm" name="confirm"
          placeholder="••••••••"
          class="<?= isset($erreurs['confirm']) ? 'error' : '' ?>"/>
        <?php if (isset($erreurs['confirm'])): ?><div class="err-msg"><?= $erreurs['confirm'] ?></div><?php endif; ?>
      </div>

      <button class="btn-primary" type="submit">Créer mon compte</button>
    </form>

    <div class="switch-text">
      Déjà un compte ? <a href="login.php">Se connecter</a>
    </div>
  </div>

</div>
<script>
function updateStrength(val) {
  const bars = ['bar1','bar2','bar3','bar4'].map(id => document.getElementById(id));
  const hint = document.getElementById('pw-hint');
  bars.forEach(b => b.className = 'pw-bar');
  if (!val) { hint.textContent = 'Choisissez un mot de passe robuste'; return; }
  let score = 0;
  if (val.length >= 8)  score++;
  if (val.length >= 12) score++;
  if (/[A-Z]/.test(val) && /[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const levels = [
    {cls:'weak',  label:'Faible',  count:1},
    {cls:'weak',  label:'Faible',  count:2},
    {cls:'medium',label:'Moyen',   count:3},
    {cls:'strong', label:'Robuste', count:4}
  ];
  const level = levels[Math.min(score-1, 3)] || levels[0];
  for (let i = 0; i < level.count; i++) bars[i].classList.add(level.cls);
  hint.textContent = level.label;
}
</script>
</body>
</html>
