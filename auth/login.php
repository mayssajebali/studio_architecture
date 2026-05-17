<?php
session_start();
require_once '../db.php'; 
/** @var PDO $pdo */
$erreur = '';
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        session_regenerate_id(true);

        if ($user['role'] === 'admin') {
            $_SESSION['admin_id']  = $user['id'];
            $_SESSION['admin_nom'] = $user['nom'];
            $_SESSION['admin']     = true;
        } else {
            $_SESSION['client_id']  = $user['id'];
            $_SESSION['client_nom'] = $user['nom'];
        }

        header('Location: ../accueil.php');
        exit;
    } else {
        $erreur = 'Email ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Connexion — Studio Architecture</title>
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

    /* LEFT */
    .visual-panel{
      position:relative; background:var(--dark); overflow:hidden;
      display:flex; flex-direction:column; justify-content:flex-end; padding:3rem;
    }
    .arch-svg{ position:absolute; inset:0; width:100%; height:100%; opacity:.10; pointer-events:none; }
    .panel-tag{ font-size:10px; letter-spacing:.4em; text-transform:uppercase; color:var(--sand); font-weight:300; margin-bottom:1rem; opacity:.8; }
    .panel-heading{ font-family:'Cormorant Garamond',serif; font-size:clamp(36px,4vw,52px); font-weight:300; color:var(--ivory); line-height:1.1; }
    .panel-heading em{ font-style:italic; color:var(--sand); }
    .panel-rule{ width:40px; height:1px; background:var(--sand); opacity:.6; margin:1.5rem 0; }
    .panel-sub{ font-size:11px; letter-spacing:.12em; color:var(--muted); font-weight:300; }
    .panel-footer{ margin-top:3rem; font-size:10px; letter-spacing:.08em; color:#4a4540; font-weight:300; }

    /* RIGHT */
    .form-panel{
      background:var(--form-bg); display:flex; flex-direction:column; justify-content:center;
      padding:3.5rem clamp(2rem,5vw,4rem);
    }
    .form-eyebrow{ font-size:10px; letter-spacing:.3em; text-transform:uppercase; color:var(--text-hint); font-weight:400; margin-bottom:.5rem; }
    .form-title{ font-family:'Cormorant Garamond',serif; font-size:32px; font-weight:400; color:var(--text-main); margin-bottom:2.5rem; }

    .alert-error{
      background:var(--error-bg); border:1px solid #f5c6c6;
      border-radius:var(--radius); padding:.75rem 1rem;
      font-size:12px; color:var(--error); margin-bottom:1.5rem;
      display:flex; align-items:center; gap:.5rem;
    }
    .alert-error::before{ content:'⚠'; font-size:14px; }

    .field{ margin-bottom:1.25rem; }
    .field label{ display:block; font-size:11px; letter-spacing:.22em; text-transform:uppercase; color:var(--text-sub); font-weight:400; margin-bottom:.5rem; }
    .field input{
      width:100%; background:var(--input-bg); border:1px solid var(--border);
      border-radius:var(--radius); padding:.8rem 1rem;
      font-size:15px; font-family:'Jost',sans-serif; font-weight:300; color:var(--text-main);
      outline:none; transition:border-color .2s,background .2s; -webkit-appearance:none;
    }
    .field input::placeholder{ color:var(--text-hint); }
    .field input:focus{ border-color:var(--sand); background:#fff; }
    .field input.error{ border-color:var(--error); background:var(--error-bg); }

    .forgot{ text-align:right; margin-top:-.5rem; margin-bottom:1.5rem; }
    .forgot a{ font-size:11px; color:var(--text-hint); text-decoration:none; letter-spacing:.04em; transition:color .2s; }
    .forgot a:hover{ color:var(--text-sub); }

    .btn-primary{
      width:100%; padding:.9rem; background:var(--dark); color:var(--ivory);
      border:none; border-radius:var(--radius);
      font-family:'Jost',sans-serif; font-size:10px; font-weight:400;
      letter-spacing:.3em; text-transform:uppercase; cursor:pointer;
      transition:background .2s; margin-bottom:1.75rem;
    }
    .btn-primary:hover{ background:var(--dark-mid); }

    .sep{ display:flex; align-items:center; gap:1rem; margin-bottom:1.75rem; }
    .sep-line{ flex:1; height:1px; background:var(--border); }
    .sep span{ font-size:10px; letter-spacing:.15em; text-transform:uppercase; color:var(--text-hint); }

    .switch-text{ text-align:center; font-size:13px; color:var(--text-hint); letter-spacing:.02em; }
    .switch-text a{ color:var(--text-main); text-decoration:none; border-bottom:1px solid var(--border); padding-bottom:1px; }
    .switch-text a:hover{ border-color:var(--text-main); }

    @media(max-width:768px){
      .page{ grid-template-columns:1fr; }
      .visual-panel{ min-height:240px; padding:2rem; }
      .form-panel{ padding:2.5rem 1.75rem; }
    }
  </style>
</head>
<body>
<div class="page">

  <!-- LEFT -->
  <div class="visual-panel">
    <svg class="arch-svg" viewBox="0 0 500 700" fill="none" xmlns="http://www.w3.org/2000/svg">
      <line x1="0" y1="140" x2="500" y2="140" stroke="white" stroke-width="0.5"/>
      <line x1="0" y1="280" x2="500" y2="280" stroke="white" stroke-width="0.5"/>
      <line x1="0" y1="420" x2="500" y2="420" stroke="white" stroke-width="0.5"/>
      <line x1="0" y1="560" x2="500" y2="560" stroke="white" stroke-width="0.5"/>
      <line x1="100" y1="0" x2="100" y2="700" stroke="white" stroke-width="0.5"/>
      <line x1="200" y1="0" x2="200" y2="700" stroke="white" stroke-width="0.5"/>
      <line x1="300" y1="0" x2="300" y2="700" stroke="white" stroke-width="0.5"/>
      <line x1="400" y1="0" x2="400" y2="700" stroke="white" stroke-width="0.5"/>
      <rect x="100" y="280" width="200" height="360" stroke="white" stroke-width="0.8" fill="none"/>
      <line x1="70" y1="280" x2="200" y2="230" stroke="white" stroke-width="0.5"/>
      <line x1="330" y1="280" x2="200" y2="230" stroke="white" stroke-width="0.5"/>
      <line x1="70" y1="280" x2="330" y2="280" stroke="white" stroke-width="1"/>
      <rect x="115" y="305" width="38" height="50" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="163" y="305" width="38" height="50" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="211" y="305" width="38" height="50" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="115" y="370" width="38" height="50" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="211" y="370" width="38" height="50" stroke="white" stroke-width="0.5" fill="none"/>
      <rect x="158" y="480" width="84" height="160" stroke="white" stroke-width="0.5" fill="none"/>
      <circle cx="236" cy="560" r="3" stroke="white" stroke-width="0.5" fill="none"/>
      <line x1="0" y1="640" x2="500" y2="640" stroke="white" stroke-width="0.8"/>
    </svg>

    <div class="panel-tag">GreenHome &nbsp;·&nbsp; Architecture &amp; Design</div>
    <div class="panel-heading">Concevoir<br>des espaces<br><em>qui inspirent</em></div>
    <div class="panel-rule"></div>
    <div class="panel-sub">Tunis &nbsp;·&nbsp; Architectes &amp; Urbanistes</div>
  </div>

  <!-- RIGHT -->
  <div class="form-panel">
    <div class="form-eyebrow">Espace client</div>
    <div class="form-title">Bon retour</div>

    <?php if ($erreur): ?>
      <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" novalidate>
      <div class="field">
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email"
          placeholder="vous@exemple.com"
          value="<?= htmlspecialchars($email) ?>"
          autocomplete="email"
          class="<?= $erreur ? 'error' : '' ?>"/>
      </div>

      <div class="field">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password"
          placeholder="••••••••"
          autocomplete="current-password"
          class="<?= $erreur ? 'error' : '' ?>"/>
      </div>

      <button class="btn-primary" type="submit">Entrer</button>
    </form>

    <div class="sep">
      <span class="sep-line"></span><span>ou</span><span class="sep-line"></span>
    </div>

    <div class="switch-text">
      Pas encore de compte ? <a href="register.php">Créer un compte</a>
    </div>
  </div>

</div>
</body>
</html>


