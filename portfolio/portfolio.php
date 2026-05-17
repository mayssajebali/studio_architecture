<?php

session_start();
require_once '../db.php';

/** @var PDO $pdo */


$message_flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_quote'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $project_type = htmlspecialchars($_POST['project_type']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO quotes (name, email, project_type) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $project_type]);
        $message_flash = "✅ Merci ! Nous vous recontacterons sous 48h.";
    } catch(Exception $e) {
        $message_flash = "❌ Une erreur est survenue, veuillez réessayer.";
    }
}


$stmt = $pdo->query("SELECT * FROM portfolio_categories ORDER BY id");
$allCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
$projetsCount = count($allCategories);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Design - Thème Nude</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="portfoliostyle.css">
    <style>
        .filter-btn.active {
            background-color: #2c2c2c;
            color: white;
            border-color: #2c2c2c;
        }
        .portfolio-category {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .filtered-out {
            display: none;
        }
    </style>
</head>
<body>

<header>
    <?php include '../navbar.php'; ?>
</header>

<a href="../contact/contact.php" class="contact-page-btn fade-in" style="animation-delay: 1s">
    <i class="fas fa-envelope"></i>
    <span>Contactez-nous</span>
</a>

<section class="hero-section" id="accueil">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title fade-in">Création d'espaces uniques et inspirants</h1>
                <p class="hero-subtitle fade-in" style="animation-delay: 0.2s">
                    Nous concevons des intérieurs qui racontent votre histoire, mêlant esthétique, 
                    fonctionnalité et bien-être. Notre approche sur-mesure transforme chaque espace 
                    en un lieu de vie exceptionnel.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#portfolio" class="btn btn-dark fade-in" style="animation-delay: 0.4s">Découvrir nos réalisations</a>
                    <a href="../calendrier/calendrier.php" class="btn btn-beige fade-in" style="animation-delay: 0.5s">
                        <i class="fas fa-calendar-alt me-2"></i>Prendre rendez-vous
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-image mt-5 mt-lg-0 fade-in" style="animation-delay: 0.6s">
                    <img src="https://images.unsplash.com/photo-1616594039964-ae9021a400a0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" alt="Intérieur design">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="portfolio-section" id="portfolio">
    <div class="container">
        <h2 class="section-title fade-in">Nos Domaines d'Expertise</h2>
        <p class="text-center mb-5 fade-in" style="color: var(--gray-dark); max-width: 700px; margin: 0 auto 3rem; animation-delay: 0.2s">
            Nous créons des espaces sur-mesure adaptés à chaque univers, en respectant votre identité et vos besoins spécifiques.
            <br><strong id="projectsCount"><?= $projetsCount ?></strong> domaines d'expertise – chacun avec des projets uniques.
        </p>

        <!-- Boutons de filtre (sans rechargement) -->
        <div class="filter-buttons text-center mb-4">
            <button class="btn btn-outline-dark filter-btn active" data-filter="all">Tous</button>
            <?php foreach ($allCategories as $cat): ?>
                <button class="btn btn-outline-dark filter-btn" data-filter="<?= htmlspecialchars($cat['link_slug']) ?>">
                    <?= htmlspecialchars($cat['title']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Conteneur des cartes avec attribut data-category pour chaque carte -->
        <div class="row g-4" id="portfolioContainer">
            <?php foreach ($allCategories as $index => $cat): ?>
                <div class="col-md-6 col-lg-4 portfolio-item" data-category="<?= htmlspecialchars($cat['link_slug']) ?>">
                    <div class="portfolio-category fade-in" style="animation-delay: <?= 0.3 + ($index * 0.1) ?>s">
                        <div class="category-image">
                            <img src="<?= htmlspecialchars($cat['image_url']) ?>" alt="<?= htmlspecialchars($cat['title']) ?>">
                        </div>
                        <div class="category-content">
                            <h3 class="category-title"><?= htmlspecialchars($cat['title']) ?></h3>
                            <p class="category-description"><?= htmlspecialchars($cat['description']) ?></p>
                            <!-- Lien corrigé : tout en minuscules -->
                            <a href="../<?= htmlspecialchars($cat['link_slug']) ?>/<?= strtolower(htmlspecialchars($cat['link_slug'])) ?>.php" class="btn btn-beige">
                                <i class="fas fa-arrow-right me-2"></i>Projet similaire
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="process-section" id="processus">
    <div class="container">
        <h2 class="section-title fade-in">Notre Processus Créatif</h2>
        <p class="text-center mb-5 fade-in" style="color: var(--gray-dark); max-width: 700px; margin: 0 auto 3rem;">Un parcours clair et transparent pour transformer votre vision en réalité.</p>
        <div class="row">
            <div class="col-md-6 col-lg-3"><div class="process-step fade-in"><div class="step-number">1</div><h4>Consultation</h4><p>Nous échangeons sur vos besoins...</p></div></div>
            <div class="col-md-6 col-lg-3"><div class="process-step fade-in"><div class="step-number">2</div><h4>Conceptualisation</h4><p>Moodboards et esquisses.</p></div></div>
            <div class="col-md-6 col-lg-3"><div class="process-step fade-in"><div class="step-number">3</div><h4>Développement</h4><p>Plans détaillés, matériaux.</p></div></div>
            <div class="col-md-6 col-lg-3"><div class="process-step fade-in"><div class="step-number">4</div><h4>Réalisation</h4><p>Suivi et livraison.</p></div></div>
        </div>
    </div>
</section>

<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Demande de devis rapide</h3>
                    <?php if($message_flash): ?>
                        <div class="alert alert-info text-center"><?= $message_flash ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type de projet</label>
                            <select name="project_type" class="form-select" required>
                                <option value="">Sélectionnez...</option>
                                <?php foreach($allCategories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['title']) ?>"><?= htmlspecialchars($cat['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="quick_quote" class="btn btn-dark w-100">Envoyer la demande</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div id="div1footer" class="row justify-content-between">
            <div class="col-md-3">
                <img src="../images/logo.png" alt="Logo" height="40">
                <p class="mt-3 pfooter">Nous proposons une gamme complète de services de design d'intérieur et de conception architecturale.</p>
                <a href="https://www.facebook.com" target="_blank"><i class="bi bi-facebook me-4"></i></a>
                <a href="https://www.instagram.com" target="_blank"><i class="bi bi-instagram me-4"></i></a>
                <a href="https://www.linkedin.com" target="_blank"><i class="bi bi-linkedin me-4"></i></a>
                <a href="https://www.youtube.com" target="_blank"><i class="bi bi-youtube me-4"></i></a>
            </div>
            <div class="col-md-2">
                <p class="p_gris">Liens importants</p>
                <p class="pfooter"><a href="../accueil.php">Accueil</a></p>
                <p class="pfooter"><a href="../apropos.php">À propos</a></p>
                <p class="pfooter"><a href="../services.php">Services</a></p>
                <p class="pfooter"><a href="../portfolio/portfolio.php">Portfolio</a></p>
                <p class="pfooter"><a href="../contact/contact.php">Contact</a></p>
            </div>
            <div class="col-md-4">
                <p class="p_gris">Contact</p>
                <p class="pfooter"><i class="bi bi-geo-alt"></i> 2972 Westheimer Rd. Santa Ana, Illinois 85486</p>
                <p class="pfooter"><i class="bi bi-telephone"></i> +216 77 715 720</p>
                <p class="pfooter"><a href="mailto:info@greenhome.com" id="mail"><i class="bi bi-envelope"></i> info@greenhome.com</a></p>
            </div>
        </div>
    </div>
    <div id="div2footer">Copyright © 2025 GreenHome LLP. Tous droits réservés.</div>
</footer>

<a href="#" class="back-to-top" id="backToTop"><i class="fas fa-chevron-up"></i></a>

<!-- Script de filtrage sans AJAX (client uniquement) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');
    const projectsCountSpan = document.getElementById('projectsCount');

    function filterCategories(slug) {
        let visibleCount = 0;
        portfolioItems.forEach(item => {
            const itemCategory = item.getAttribute('data-category');
            if (slug === 'all' || itemCategory === slug) {
                item.classList.remove('filtered-out');
                visibleCount++;
            } else {
                item.classList.add('filtered-out');
            }
        });
        if (projectsCountSpan) {
            projectsCountSpan.textContent = visibleCount;
        }
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-filter');
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterCategories(filter);
        });
    });
});
</script>

<style>
    .filtered-out {
        display: none;
    }
</style>

</body>
</html>