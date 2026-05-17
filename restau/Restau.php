<?php
require_once '../db.php';
/** @var PDO $pdo */
session_start();


if (isset($_GET['view_id'])) {
    $id = (int)$_GET['view_id'];
    $pdo->prepare("UPDATE projets_portfolio SET vues = vues + 1 WHERE id = ?")->execute([$id]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM projets_portfolio WHERE categorie = 'restau' ORDER BY date_creation DESC");
$stmt->execute();
$projets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Design Restaurant - Réalisations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Cormorant+Garamond:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="restau.css">
    <style>
        .filter-bar { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; }
        .gallery-item { transition: transform 0.2s; }
        .view-count { margin-left: 5px; }
    </style>
</head>
<body>
<header><?php include '../navbar.php'; ?></header>

<section class="hero-section">
    <div class="container">
        <h1 class="display-4 mb-4">Design Restaurant d'Exception</h1>
        <p class="lead">Créez une atmosphère unique qui met en valeur votre cuisine et offre une expérience gastronomique complète à vos clients</p>
    </div>
</section>

<section class="gallery-container">
    <div class="container">
        <!-- Barre de recherche et tri -->
        <div class="filter-bar">
            <div class="d-flex gap-2">
                <input type="text" id="searchInput" class="form-control" placeholder="Rechercher..." style="width: 250px;">
                <select id="sortSelect" class="form-select" style="width: 150px;">
                    <option value="date">Récent</option>
                    <option value="vues">Populaires</option>
                </select>
            </div>
        </div>

        <div class="row" id="projectsGrid">
            <?php if (empty($projets)): ?>
                <div class="col-12 text-center py-5">
                    <p>Aucune réalisation de restaurant pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($projets as $p): ?>
                    <div class="col-md-4 col-lg-3 gallery-item project-card"
                         data-date="<?= strtotime($p['date_creation']) ?>"
                         data-vues="<?= $p['vues'] ?>"
                         data-title="<?= htmlspecialchars($p['titre']) ?>">
                        <?php if ($p['type_media'] === 'image'): ?>
                            <img src="<?= htmlspecialchars($p['media_path']) ?>" alt="<?= htmlspecialchars($p['titre']) ?>">
                        <?php else: ?>
                            <div style="position: relative; height: 100%;">
                                <video controls loop style="width:100%; height:100%; object-fit:cover;">
                                    <source src="<?= htmlspecialchars($p['media_path']) ?>" type="video/mp4">
                                </video>
                            </div>
                        <?php endif; ?>
                        <div class="item-type"><?= strtoupper($p['type_media']) ?></div>
                        <div class="item-overlay">
                            <h5><?= htmlspecialchars($p['titre']) ?></h5>
                            <p class="small mb-2"><?= htmlspecialchars($p['categorie']) ?></p>
                            <div class="d-flex justify-content-between">
                                <span><i class="far fa-eye"></i> <span class="view-count"><?= $p['vues'] ?></span></span>
                            </div>
                            <label for="modal-<?= $p['id'] ?>" class="btn btn-sm btn-outline-light mt-2">Voir détails</label>
                        </div>
                    </div>

                    <!-- Modal dynamique -->
                    <input type="checkbox" id="modal-<?= $p['id'] ?>" class="modal-toggle">
                    <div class="modal">
                        <div class="modal-content">
                            <label for="modal-<?= $p['id'] ?>" class="modal-close">&times;</label>
                            <div class="project-media">
                                <?php if ($p['type_media'] === 'image'): ?>
                                    <img src="<?= htmlspecialchars($p['media_path']) ?>" alt="<?= htmlspecialchars($p['titre']) ?>">
                                <?php else: ?>
                                    <video controls style="width:100%; height:100%; object-fit:cover;">
                                        <source src="<?= htmlspecialchars($p['media_path']) ?>" type="video/mp4">
                                    </video>
                                <?php endif; ?>
                            </div>
                            <div class="project-info">
                                <h2><?= htmlspecialchars($p['titre']) ?></h2>
                                <div class="project-meta">
                                    <span><?= htmlspecialchars($p['categorie']) ?></span>
                                    <span><?= ucfirst($p['type_media']) ?></span>
                                    <span><?= htmlspecialchars($p['ville']) ?></span>
                                    <span><?= $p['annee'] ?></span>
                                </div>
                                <p class="project-description"><?= nl2br(htmlspecialchars($p['description_longue'])) ?></p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Concept</h5>
                                        <p><?= nl2br(htmlspecialchars($p['concept'])) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Matériaux / Équipements</h5>
                                        <p><?= nl2br(htmlspecialchars($p['materiaux'])) ?></p>
                                    </div>
                                </div>
                                <div class="project-details">
                                    <h5>Détails du projet</h5>
                                    <div class="row">
                                        <div class="col-md-4 detail-item"><strong>Superficie:</strong> <?= htmlspecialchars($p['superficie']) ?></div>
                                        <div class="col-md-4 detail-item"><strong>Durée:</strong> <?= htmlspecialchars($p['duree']) ?></div>
                                        <div class="col-md-4 detail-item"><strong>Budget:</strong> <?= htmlspecialchars($p['budget']) ?></div>
                                        <div class="col-md-4 detail-item"><strong>Architecte:</strong> <?= htmlspecialchars($p['architecte']) ?></div>
                                        <div class="col-md-4 detail-item"><strong>Designer:</strong> <?= htmlspecialchars($p['designer']) ?></div>
                                        <div class="col-md-4 detail-item"><strong>Photographe:</strong> <?= htmlspecialchars($p['photographe']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div id="div1footer" class="row justify-content-between">
            <div class="col-md-3">
                <img src="../images/logo.png" alt="Logo" height="40">
                <p class="mt-3 pfooter">Nous proposons une gamme complète de services de design d'intérieur et de conception architecturale.</p>
                <div class="social-icons">
                    <a href="https://www.facebook.com" target="_blank"><i class="bi bi-facebook me-3"></i></a>
                    <a href="https://www.instagram.com" target="_blank"><i class="bi bi-instagram me-3"></i></a>
                    <a href="https://www.linkedin.com" target="_blank"><i class="bi bi-linkedin me-3"></i></a>
                    <a href="https://www.youtube.com" target="_blank"><i class="bi bi-youtube me-3"></i></a>
                </div>
            </div>
            <div class="col-md-2">
                <p class="p_gris">Liens importants</p>
                <p class="pfooter"><a href="../accueil.html">Accueil</a></p>
                <p class="pfooter"><a href="../apropos.html">À propos</a></p>
                <p class="pfooter"><a href="../services.html">Services</a></p>
                <p class="pfooter"><a href="../portfolio/portfolio.html">Portfolio</a></p>
                <p class="pfooter"><a href="../contact/contact.html">Contact</a></p>
            </div>
            <div class="col-md-4">
                <p class="p_gris">Contact</p>
                <p class="pfooter"><i class="bi bi-geo-alt"></i> 2972 Westheimer Rd. Santa Ana, Illinois 85486</p>
                <p class="pfooter"><i class="bi bi-telephone"></i> +216 77 715 720</p>
                <p class="pfooter"><a href="mailto:info@greenhome.com" id="mail"><i class="bi bi-envelope"></i> info@greenhome.com</a></p>
            </div>
        </div>
    </div>
    <div id="div2footer">© 2025 GreenHome LLP. Tous droits réservés.</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const grid = document.getElementById('projectsGrid');
    const items = Array.from(document.querySelectorAll('.project-card'));
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');

    function filterAndSort() {
        const searchTerm = searchInput.value.toLowerCase();
        const sortBy = sortSelect.value;
        let filtered = items.filter(card => card.dataset.title.toLowerCase().includes(searchTerm));
        filtered.sort((a,b) => sortBy === 'vues' ? b.dataset.vues - a.dataset.vues : b.dataset.date - a.dataset.date);
        filtered.forEach(card => grid.appendChild(card));
        items.forEach(card => card.style.display = filtered.includes(card) ? '' : 'none');
    }
    searchInput.addEventListener('input', filterAndSort);
    sortSelect.addEventListener('change', filterAndSort);

   
    document.querySelectorAll('label[for^="modal-"]').forEach(label => {
        label.addEventListener('click', function() {
            const modalId = this.getAttribute('for').replace('modal-', '');
            const viewSpan = this.closest('.project-card').querySelector('.view-count');
            fetch(`?view_id=${modalId}`).then(() => {
                if (viewSpan) viewSpan.innerText = parseInt(viewSpan.innerText) + 1;
            });
        });
    });
});
</script>
</body>
</html>