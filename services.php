<?php
session_start();
require_once 'db.php';
$styles = $pdo->query("SELECT * FROM styles ORDER BY ordre ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenHome</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<title>Nos Services</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Contact - Architecture d'Intérieur</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Services Design</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<title>Filtres Styles Design</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">




</head>
<body>
    <header>
           <?php include 'navbar.php'; ?>
    </header>

<section class="services-hero">
    <div class="services-overlay"></div>
    <h1 class="hero-title">NOS SERVICES</h1>
</section>



<!-- BARRE DE FILTRE DYNAMIQUE -->
<div class="filter-bar-container">
    <div class="container-fluid">
        <div class="filter-wrapper">
            <span class="filter-label">Styles</span>
            <?php foreach ($styles as $i => $s):
                $slug = 'style-' . $s['id'];
            ?>
            <a href="#<?= $slug ?>" class="filter-btn" data-filter="<?= $slug ?>">
                <?= htmlspecialchars($s['titre']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- SECTIONS DYNAMIQUES -->
<?php foreach ($styles as $i => $s):
    $slug   = 'style-' . $s['id'];
    $numero = $i + 1;
    $gauche = ($i % 2 === 0); 
?>
<section class="services-details-section" id="<?= $slug ?>">
    <div class="container">
        <div class="service-item row align-items-center">

            <?php if ($gauche): ?>
            <!-- Texte à gauche -->
            <div class="col-lg-6 order-lg-1 order-2">
            <?php else: ?>
            <!-- Texte à droite (image déjà placée avant) -->
            <?php endif; ?>

            <?php if (!$gauche): ?>
            <!-- Image à gauche -->
            <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?= htmlspecialchars($s['image_path']) ?>" class="carousel-image" alt="<?= htmlspecialchars($s['titre']) ?>">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
            <?php endif; ?>

                <h2 class="service-title"><?= $numero ?>. <?= htmlspecialchars($s['titre']) ?></h2>
                <div class="title-line"></div>
                <ul class="service-list">
                    <?php if ($s['materiau']): ?>
                    <li><strong>Matériaux principaux :</strong> <?= htmlspecialchars($s['materiau']) ?></li>
                    <?php endif; ?>
                    <?php if ($s['palette']): ?>
                    <li><strong>Palette de couleurs :</strong> <?= htmlspecialchars($s['palette']) ?></li>
                    <?php endif; ?>
                    <?php if ($s['mobilier']): ?>
                    <li><strong>Style de mobilier :</strong> <?= htmlspecialchars($s['mobilier']) ?></li>
                    <?php endif; ?>
                    <?php if ($s['caracteristiques']): ?>
                    <li><strong>Caractéristiques distinctives :</strong> <?= htmlspecialchars($s['caracteristiques']) ?></li>
                    <?php endif; ?>
                </ul>
                <p class="service-description"><?= htmlspecialchars($s['description']) ?></p>
            </div>

            <?php if ($gauche): ?>
            <!-- Image à droite -->
            <div class="col-lg-6 order-lg-2 order-1 mb-lg-0 mb-4">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?= htmlspecialchars($s['image_path']) ?>" class="carousel-image" alt="<?= htmlspecialchars($s['titre']) ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</section>
<?php endforeach; ?>


    <!-- Section Contact avec Background -->
<section class="contact-hero-section">
    <div class="overlay-dark"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-xl-7">
                <div class="contact-content">
                    <p class="overline-text">Besoin d'un architecte d'intérieur</p>
                    <h1 class="hero-title1">Nous Sommes À Votre Écoute</h1>
                    <button class="btn-contact">
                      
                        <a href="contact/contact.html" id="btnA"><span class="btn-text">Nous Contacter</span></a>
                        <span class="btn-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4 10H16M16 10L10 4M16 10L10 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    
                    </button>
                </div>
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
                <p class="pfooter"><i class="bi bi-geo-alt"></i> 2972 Westheimer Rd. Santa Ana, Illinois 85486</p>
                <p class="pfooter"><i class="bi bi-telephone"></i> +216 77 715 720</p>
                <p class="pfooter"><a href="mailto:info@greenhome.com" id="mail"><i class="bi bi-envelope"></i> info@greenhome.com</a></p>
            </div>

        </div>

    </div>
    <div id="div2footer">
        Copyright © 2025 GreenHome LLP. Tous droits réservés.
    </div>
</footer>
    
</body>
</html>