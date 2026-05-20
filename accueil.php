<?php 
session_start();  
require_once 'auth/db.php';

$success_msg = '';
$error_msg   = '';

if (!isset($_SESSION['client_id'])) {
    $error_msg = "Vous devez être connecté pour laisser un avis.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['client_id'])) {

   $pdo = getDB();

    $stmtClient = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmtClient->execute([$_SESSION['client_id']]);

    $client = $stmtClient->fetch();

    $nom = $client['nom'];
    $prenom = $client['prenom'];

    $note = intval($_POST['note'] ?? 0);
    $avis = trim(htmlspecialchars($_POST['avis'] ?? ''));

   
    if (!$avis) {

        $error_msg = "Veuillez écrire votre avis.";

    } elseif ($note < 1 || $note > 5) {

        $error_msg = "Veuillez sélectionner une note.";

    } else {

        try {

            $pdo  = getDB();

            $stmt = $pdo->prepare("
                INSERT INTO temoignages (nom, prenom, note, avis)
                VALUES (:nom, :prenom, :note, :avis)
            ");

            $stmt->execute([
                ':nom'     => $nom,
                ':prenom'  => $prenom,
                ':note'    => $note,
                ':avis'    => $avis,
            ]);

            $success_msg = "Merci $prenom ! Votre avis a bien été enregistré.";

        } catch (PDOException $e) {

            $error_msg = "Une erreur est survenue.";

        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Home</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet"></head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<style>
    
.review-form {
    background: #ffffff;
    border: 0.5px solid rgba(193, 193, 193, 0.4);
    border-radius: 10px;
    padding: 32px;
    margin: 40px auto;
    width: 600px;
}

.review-form h3 {
    font-size: 20px;
    font-weight: 600;
    color: #000000;
    margin-bottom: 20px;
    letter-spacing: 0.5px;
}

.review-form .form-control {
    background: #ffffff;
    border: 0.5px solid rgba(193, 193, 193, 0.4);
    border-radius: 6px;
    color: #000000;
    font-size: 14px;
    padding: 10px 14px;
    transition: border-color 0.2s;
}

.review-form .form-control:focus {
    border-color: rgba(193, 193, 193, 0.4);
    border-width: 1px;
    box-shadow: none;
    outline: none;
    background: #ffffff;
}

.review-form .form-control::placeholder {
    color: #999999;
}

.review-form textarea.form-control {
    resize: none;
    min-height: 110px;
}

/* Label note */
.review-form .note-label {
    font-size: 13px;
    color: #666666;
    margin-bottom: 6px;
}

/* Étoiles */
.stars span {
    font-size: 28px;
    cursor: pointer;
    color: #cccccc;
    transition: color 0.15s;
}
.stars span.active,
.stars span:hover {
    color: #f5a623;
}

/* Bouton */
.btn-submit {
    background: #b8a99a;
    color: #ffffff;
    border: none;
    border-radius: 6px;
    padding: 11px 28px;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 0.3px;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
}

.btn-submit:hover {
    background: #a6968a;
}

.btn-submit:active {
    transform: scale(0.98);
}

/* Messages */
.alert-success {
    background: #ffffff;
    border: 0.5px solid #000000;
    border-left: 3px solid #b8a99a;
    color: #000000;
    border-radius: 6px;
    padding: 10px 16px;
    font-size: 14px;
}

.alert-danger {
    background: #ffffff;
    border: 0.5px solid #000000;
    border-left: 3px solid #cc0000;
    color: #000000;
    border-radius: 6px;
    padding: 10px 16px;
    font-size: 14px;
}
.p_gris{
    font-family: "Inter", sans-serif;
    font-size: 22px;
    color: #333333;
}
.review-stars{
    margin-bottom: 12px;
}

.star-filled{
    color: #f5a623;
    font-size: 20px;
}

.star-empty{
    color: #d6d6d6;
    font-size: 20px;
}
.grid-c{
    display: flex;
    gap: 0px;
    flex-wrap: wrap;
    margin-top: 40px;
    margin-left: 140px;
}

.c1{
    width:280px;
    padding: 25px;
    font-family: "Inter", sans-serif;
    font-size: 15px;
    margin-left:100px;
}
</style>

<body>

<header>
        <?php include 'navbar.php'; ?>
    </header> 

    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p id="titre">DESIGN D'intérieur</p>
                    <p class="p_gris ms-2">Créez l'espace qui vous ressemble</p>
                    <img src="images/ligne.png" height="3" class="ms-2">
                </div>
                <div class="col-md-4 text-end ">
                    <img src="images/lampe.png" height="320" alt="Lampe" style="margin-top: -48px;">
                </div>
            </div>
        </div>
    </section>
    <section class="position-relative">
        <img src="images/home.png" class="w-100" height="650">
        <div class="container position-absolute" id="div2">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <p class="p_gris" style="margin-top: -15px;">A propos de nous</p>
                    <p id="p1">Qui somme <br><span style="font-family: 'Inter',sans-serif;">Nous ?</span> </p>
                </div>
                <div class="col-md-6 text-end me-1">
                    <p class="p_inter" align="left">Notre philosophie consiste à considérer chaque projet comme le reflet personnel de 
                        la vision et de la personnalité de nos clients, ce qui nous a permis de dépasser leurs 
                        attentes. Grâce à notre approche unique et personnalisée, nous créons des expériences qui 
                        établissent de nouvelles références dans le domaine de l'architecture et du design
                        d'intérieur. Notre engagement et notre sincérité font de nous un partenaire de confiance 
                        pour nos clients. Nous aspirons à atteindre de nouveaux sommets en matière de design et 
                        d'architecture, et nos efforts constants pour nous surpasser demeurent notre force. 
                        Nous poursuivons avec passion notre mission, souhaitant que le monde entier puisse profiter 
                        de l'empreinte créative de nos réalisations.</p>
                </div>

            </div>
        </div>
        <div class="text-center mt-4" id="btn-overlay">
            <a href="apropos.html"><img src="images/read_more.png" height="130"></a>
        </div>
    </section>
    <section>
        <div id="div3">
            <div class="marquee">
                <p id="p2">
                    NOS PROJETS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NOS PROJETS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NOS PROJETS
                </p>
                <p id="p2">
                    NOS PROJETS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NOS PROJETS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NOS PROJETS
                </p>
            </div>
        <ul style="list-style-type: none;">
            <li>
                <div class="item-row">
                <p class="p_li">
                    01&nbsp;&nbsp;&nbsp; <span style="font-family: 'Cinzel',sans-serif;font-size: 32px;">DESIGN</span>  
                    <span style="font-size: 32px;">RESIDENTIEL</span>
                </p>
                <a href="residential/residentiall.html">
                    <img src="images/fleche.png" height="50">
                </a>                
                </div>
                <div class="images item-row">
                    <img src="images/home1.png" width="200">
                    <img src="images/home2.png" width="200">
                    <img src="images/home3.png" width="200">
                </div>
            </li>
            <li>
                <div class="item-row">
                <p class="p_li">
                    02&nbsp;&nbsp;&nbsp; <span style="font-family: 'Cinzel',sans-serif;font-size: 32px;">DESIGN</span>  
                    <span style="font-size: 32px;">HOTELIER</span>
                </p>
                <a href="hotelier/hotelier.html">
                    <img src="images/fleche.png" height="50">
                </a>                </div>
                <div class="images item-row">
                    <img src="images/hotel1.jpg" width="200">
                    <img src="images/hotel2.webp" width="200">
                    <img src="images/hotel3.jpg" width="200">
                </div>
            </li>
            <li>
                <div class="item-row">
                <p class="p_li">
                    03&nbsp;&nbsp;&nbsp; <span style="font-family: 'Cinzel',sans-serif;font-size: 32px;">DESIGN</span>  
                    <span style="font-size: 32px;">COMMERCIAL</span>
                </p>
                <a href="commercial/Commercial.html">
                    <img src="images/fleche.png" height="50">
                </a>                </div>
                <div class="images item-row">
                    <img src="images/com.jpg" width="200">
                    <img src="images/com22.jpg" width="200">
                    <img src="images/com33.jpg" width="200">
                </div>
            </li>
            <li>
                <div class="item-row">
                <p class="p_li">
                    04&nbsp;&nbsp;&nbsp; <span style="font-family: 'Cinzel',sans-serif;font-size: 32px;">DESIGN</span>  
                    <span style="font-size: 32px;">BUREAUX</span>
                </p>
                <a href="Bureau/Bureau.html">
                    <img src="images/fleche.png" height="50">
                </a>
                </div>
                <div class="images item-row">
                    <img src="images/bur1.webp" width="200">
                    <img src="images/bur2.jpg" width="200">
                    <img src="images/bur3.jpg" width="200">
                </div>
            </li>
            <li>
                <div class="item-row">
                <p class="p_li">
                    05&nbsp;&nbsp;&nbsp; <span style="font-family: 'Cinzel',sans-serif;font-size: 32px;">DESIGN</span>  
                    <span style="font-size: 32px;">RESTAURANT</span> 
                </p>
                <a href="restau/Restau.html">
                    <img src="images/fleche.png" height="50">
                </a>
                </div>
                <div class="images item-row">
                    <img src="images/restau1.jpeg" width="200">
                    <img src="images/restau2.jpg" width="200">
                    <img src="images/restau3.jpg" width="200">
                </div>
            </li>
        </ul>
        </div>
    
    </section>
<section class="stats-section">
  <div class="grid">

    <div class="stat stat-left">
      <span class="number">500+</span>
      <span class="label">Clients</span>
    </div>

    <div class="stat stat-top">
      <span class="number">04+</span>
      <span class="label">Récompenses remportées</span>
    </div>

    <div class="image-box">
      <img src="images/lampe2.png" alt="Lampe" class="lamp">
      <img src="images/tableau.png" alt="Artwork" class="art">
    </div>

    <div class="stat stat-bottom">
      <span class="number">10+</span>
      <span class="label">Ans <br>d'expérience</span>
    </div>

    <div class="stat stat-right">
      <span class="number">03+</span>
      <span class="label">Offices dans le<br> monde</span>
    </div>

  </div>
</section>
<section class="services">
    <div class="d-flex s3 justify-content-between">
    <div class="col-md-7 ms-5 p-5">
        <p class="p_gris">Nos Services</p>
        <p class="p_titre">Confortables & <br> transparents 
        <span style="font-family:'Inter', sans-serif;font-weight: 500;">SERVICES</span>
    </p>
    </div>
    <div class="col-md-3 p-5">
        <img src="images/fleche_droite.png" height="50" class=" mt-5 arrow-right">
        <br>
        <img src="images/fleche_gauche.png" height="50" class="mt-4 arrow-left">
    </div>
    </div>
    <div class="cadre-wrapper">
    <div class="cadre-container">
        <div class="cadre">
        <img src="images/arch1.png" height="80" width="80" class="ms-2 mt-5">
        <div class="card-body mt-5">
            <p class="p_gris">01</p>
            <h5 class="card-title">Architecture</h5>
            <p class="card-text mt-2">
                Les services d'architecture comprennent deux activités interconnectées mais distinctes.
            </p>    
        </div>
        </div>

    <div class="cadre">
    <img src="images/arch2.png" height="80" width="80" class="ms-2 mt-5">
    <div class="card-body mt-5">
        <p class="p_gris">02</p>
        <h5 class="card-title">Design d'interieur</h5>
        <p class="card-text mt-2">
            Un design collaboratif pour donner vie à la vision parfaite du client.        
        </p>
    </div>
    </div>
    <div class="cadre">
    <img src="images/arch3.png" height="80" width="80" class="ms-2 mt-5">
    <div class="card-body mt-5">
        <p class="p_gris">03</p>
        <h5 class="card-title">Projets clés en main</h5>
        <p class="card-text mt-2">
            Modèle complet de design d'intérieur de bout en bout, également appelé projet clé en main.  
        </p> 
    </div>
    </div>

    <div class="cadre">
    <img src="images/arch4.png" height="80" width="80" class="ms-2 mt-5">
    <div class="card-body mt-5">
        <p class="p_gris">04</p>
        <h5 class="card-title">Design de mobilier</h5>
        <p class="card-text mt-2">
        Le mobilier est un design industriel ou artisanal conçu pour accompagner les activités humaines.        </p> 
    </div>
    </div>

    <div class="cadre">
    <img src="images/communications.png" height="80" width="80" class="ms-2 mt-5">
    <div class="card-body mt-5">
        <p class="p_gris">05</p>
        <h5 class="card-title">Accompagnement & suivi</h5>
        <p class="card-text mt-2">
        Séances de consultation personnalisées pour guider nos clients dans leurs 
        choix esthétiques, techniques et budgétaires.</p> 
        </div>
    </div>

    <div class="cadre">
    <img src="images/conception-3d.png" height="80" width="80" class="ms-2 mt-5">
    <div class="card-body mt-5">
        <p class="p_gris">06</p>
        <h5 class="card-title">Conception 3D</h5>
        <p class="card-text mt-2">
       Maquettes 3D réalistes et rendus professionnels permettant au client de visualiser son projet avant le lancement des travaux.</p> 
        </div>
    </div>

    </div>
    </div>
</section>

<section class="stats-section">
    <div class="d-flex comments">
        <div class="col-md-8 ms-5">
        <p class="p_gris">Témoignages</p>
        <p class="p_titre">Une relation de <span style="font-family:'Inter', sans-serif;font-weight: 500;font-size: 32px;">CONFIANCE</span></p>
        </div>
        <div class="col-md-4">
            <img src="images/temoignages.png" height="80">
        </div>
    </div>
<div class="grid-c">
<?php
$pdo = getDB();

$stmt = $pdo->query("
    SELECT * FROM temoignages
    WHERE statut = 'approuve'
    ORDER BY id DESC
    LIMIT 3
");

$temoignages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($temoignages as $row):
?>

<div class="c1">
    <div class="review-stars">
        <?php for($i = 1; $i <= 5; $i++): ?>

            <?php if($i <= $row['note']): ?>
                <span class="star-filled">★</span>
            <?php else: ?>
                <span class="star-empty">★</span>
            <?php endif; ?>

        <?php endfor; ?>
    </div>

    <!-- avis -->
    <p>
        <?= htmlspecialchars($row['avis']) ?>
    </p>

    <!-- nom -->
    <p>
        <span style="font-weight: bold;">
            <?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?>
        </span>
        <br>
        Client
    </p>

</div>

<?php endforeach; ?>

</div>

</div>
    <img src="images/ligne.png" height="3" style="margin-left: 250px;">
</section>
<!-- Formulaire d'avis -->
<div class="review-form">
    <p class="p_gris">Laissez votre avis</p>
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success" id="success-alert">
            <?= $success_msg ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="stars mb-3" id="stars">
            <span data-v="1">★</span>
            <span data-v="2">★</span>
            <span data-v="3">★</span>
            <span data-v="4">★</span>
            <span data-v="5">★</span>
        </div>
        <input type="hidden" name="note" id="note_value">

        <textarea name="avis" class="form-control mb-3" rows="4"
            placeholder="Partagez votre expérience avec GreenHome..." required></textarea>

        <button type="submit" class="btn btn-submit">Envoyer mon avis</button>
        <?php if(isset($_SESSION['user'])): ?>

        <p style="font-size:14px;color:#666;">
            Connecté en tant que 
            <strong>
                <?= $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom'] ?>
            </strong>
        </p>

        <?php endif; ?>
    </form>
</div>
<section class="newsletter">
    <div class="d-flex newsletter-div">
    <div class="col-md-6">
        <p class="p_titre">S'abonner à <br>la <span style="font-family:'Inter', sans-serif;font-weight: 500;font-size: 32px;">
            NEWSLETTER</span></p>
    </div>
    <div class="col-md-6 mt-4">
        <div class="input-wrapper">
        <input placeholder="Entrer votre e-mail">
        <img src="images/fleche3.png" height="50" class="input-icon">
        </div>
    </div>
    </div>

</section>

<footer>
    <div class="container">
        <div id="div1footer" class="row justify-content-between">
            <div class="col-md-3">
                <img src="images/logo.png" alt="Logo" height="40">
                <p class="mt-3" class="pfooter">
                    Nous proposons une gamme complète de services de design d'intérieur 
                    et de conception architecturale.
                </p>
                <a href="https://www.facebook.com" target="_blank"><i class="bi bi-facebook me-4"></i></a>
                <a href="https://www.instagram.com" target="_blank"><i class="bi bi-instagram me-4"></i></a>
                <a href="https://www.linkedin.com" target="_blank"><i class="bi bi-linkedin me-4"></i></a>
                <a href="https://www.youtube.com" target="_blank"><i class="bi bi-youtube me-4"></i></a>
            </div>

            <div class="col-md-2">
                <p class="p_gris" style="font-size: 16px;">Liens importants</p>
                <p class="pfooter"><a href="accueil.html" style="font-style: none;">Accueil</a></p>
                <p class="pfooter"><a href="apropos.html">À propos</a></p>
                <p class="pfooter"><a href="services.html">Services</a></p>
                <p class="pfooter"><a href="portfolio/portfolio.html">Portfolio</a></p>
                <p class="pfooter"><a href="contact/contact.html">Contact</a></p>
            </div>

            <div class="col-md-4">
                <p class="p_gris" style="font-size: 16px;">Contact</p>
                <p class="pfooter"><i class="bi bi-geo-alt"></i>&nbsp;&nbsp;2972 Westheimer Rd. Santa Ana, Illinois 85486</p>
                <p class="pfooter"><i class="bi bi-telephone"></i>&nbsp;&nbsp;+216 77 715 720</p>
                <p class="pfooter"><a href="mailto:info@greenhome.com" id="mail"><i class="bi bi-envelope"></i>&nbsp;&nbsp;info@greenhome.com</a></p>
            </div>

        </div>

    </div>
    <div id="div2footer">
        Copyright © 2025 GreenHome LLP. Tous droits réservés.
    </div>
</footer>
<script>
  const container = document.querySelector('.cadre-container');
  const rightArrow = document.querySelector('.arrow-right');
  const leftArrow = document.querySelector('.arrow-left');

  let offset = 0;
  const step = 480; 

  const maxOffset = -900;

  rightArrow.addEventListener('click', () => {
    offset -= step;
    if (offset < maxOffset) offset = maxOffset;
    container.style.transform = `translateX(${offset}px)`;
  });

  leftArrow.addEventListener('click', () => {
    offset += step;
    if (offset > 0) offset = 0;
    container.style.transform = `translateX(${offset}px)`;
  });
</script>
<script>
  let rating = 0;
  const stars = document.querySelectorAll('#stars span');

  stars.forEach((s, i) => {
    s.addEventListener('mouseover', () => {
      stars.forEach((x, j) => x.style.color = j <= i ? '#f5a623' : '#ccc');
    });
    s.addEventListener('mouseout', () => {
      stars.forEach((x, j) => x.style.color = j < rating ? '#f5a623' : '#ccc');
    });
    s.addEventListener('click', () => {
      rating = i + 1;
      document.getElementById('note_value').value = rating;
      stars.forEach((x, j) => x.style.color = j < rating ? '#f5a623' : '#ccc');
    });
  });
</script>
<script>
    setTimeout(() => {
        const alertBox = document.getElementById('success-alert');

        if(alertBox){
            alertBox.style.transition = "0.5s";
            alertBox.style.opacity = "0";

            setTimeout(() => {
                alertBox.remove();
            }, 500);
        }
    }, 3000);
</script>
</body>
</html>