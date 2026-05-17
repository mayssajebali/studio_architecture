<?php
session_start();
require_once '../db.php';
/** @var PDO $pdo */

$success = '';
$erreur  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom        = trim($_POST['nom'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $telephone  = trim($_POST['telephone'] ?? '');
    $entreprise = trim($_POST['entreprise'] ?? '');
    $service    = trim($_POST['service'] ?? '');
    $budget     = trim($_POST['budget'] ?? '');
    $message    = trim($_POST['message'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;

    if ($nom && $email && $telephone && $service && $budget && $message) {
        $stmt = $pdo->prepare("
            INSERT INTO demandes_contact 
            (nom, email, telephone, entreprise, service, budget, message, newsletter)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nom, $email, $telephone, $entreprise, $service, $budget, $message, $newsletter]);
        $success = 'Votre demande a bien été envoyée. Nous vous répondrons dans les plus brefs délais.';
    } else {
        $erreur = 'Veuillez remplir tous les champs obligatoires.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contactez-nous - GreenHome Interiors LLP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styleContact.css">
</head>
<body>

<header>
  <?php include '../navbar.php'; ?>
</header>

<section class="hero-section">
  <div class="container py-5">
    <div class="row justify-content-center text-center">
      <div class="col-lg-8">
        <h1 class="display-4 fw-bold text-white mb-4">CONTACTEZ-NOUS</h1>
        <p class="lead text-white mb-0">Nous serons ravis d'en savoir plus sur votre projet et de vous accompagner dans la création de votre espace idéal.</p>
      </div>
    </div>
  </div>
</section>

<section class="contact-form-section">
  <div class="container py-4">
    <div class="row">

      <div class="col-lg-7 mb-5 mb-lg-0">
        <div class="contact-form fade-in">
          <h2 class="section-title mb-3">Formulaire de contact</h2>
          <p class="text-muted mb-4">Remplissez ce formulaire</p>

          <?php if ($success): ?>
          <div class="alert-gh-success mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
          </div>
          <?php endif; ?>

          <?php if ($erreur): ?>
          <div class="alert-gh-error mb-4">
            <i class="bi bi-exclamation-circle-fill me-2"></i><?= htmlspecialchars($erreur) ?>
          </div>
          <?php endif; ?>

          <form method="POST" action="" class="needs-validation" novalidate>
            <div class="row mb-3">
              <div class="col-md-6 mb-3">
                <label for="nom" class="form-label fw-semibold">Nom complet <span class="text-dark">*</span></label>
                <input type="text" class="form-control form-control-lg" id="nom" name="nom"
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                <div class="invalid-feedback">Veuillez saisir votre nom complet.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label fw-semibold">Adresse e-mail <span class="text-dark">*</span></label>
                <input type="email" class="form-control form-control-lg" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                <div class="invalid-feedback">Veuillez saisir une adresse e-mail valide.</div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6 mb-3">
                <label for="telephone" class="form-label fw-semibold">Numéro de téléphone <span class="text-dark">*</span></label>
                <input type="tel" class="form-control form-control-lg" id="telephone" name="telephone"
                       value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" required>
                <div class="invalid-feedback">Veuillez saisir votre numéro de téléphone.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="entreprise" class="form-label fw-semibold">Entreprise (optionnel)</label>
                <input type="text" class="form-control form-control-lg" id="entreprise" name="entreprise"
                       value="<?= htmlspecialchars($_POST['entreprise'] ?? '') ?>">
              </div>
            </div>

            <div class="mb-3">
              <label for="service" class="form-label fw-semibold">Type de service recherché <span class="text-dark">*</span></label>
              <select class="form-select form-select-lg" id="service" name="service" required>
                <option value="" selected disabled>Sélectionnez un service</option>
                <option value="design"        <?= ($_POST['service'] ?? '') === 'design'        ? 'selected' : '' ?>>Design d'intérieur résidentiel</option>
                <option value="commercial"    <?= ($_POST['service'] ?? '') === 'commercial'    ? 'selected' : '' ?>>Design d'intérieur commercial</option>
                <option value="consultation"  <?= ($_POST['service'] ?? '') === 'consultation'  ? 'selected' : '' ?>>Consultation en décoration</option>
                <option value="architecture"  <?= ($_POST['service'] ?? '') === 'architecture'  ? 'selected' : '' ?>>Services d'architecture</option>
                <option value="autre"         <?= ($_POST['service'] ?? '') === 'autre'         ? 'selected' : '' ?>>Autre</option>
              </select>
              <div class="invalid-feedback">Veuillez sélectionner un type de service.</div>
            </div>

            <div class="mb-3">
              <label for="budget" class="form-label fw-semibold">Budget estimé <span class="text-dark">*</span></label>
              <select class="form-select form-select-lg" id="budget" name="budget" required>
                <option value="" selected disabled>Sélectionnez une fourchette de budget</option>
                <option value="moins-5k"  <?= ($_POST['budget'] ?? '') === 'moins-5k'  ? 'selected' : '' ?>>Moins de 5 000 €</option>
                <option value="5k-15k"    <?= ($_POST['budget'] ?? '') === '5k-15k'    ? 'selected' : '' ?>>5 000 € - 15 000 €</option>
                <option value="15k-30k"   <?= ($_POST['budget'] ?? '') === '15k-30k'   ? 'selected' : '' ?>>15 000 € - 30 000 €</option>
                <option value="30k-50k"   <?= ($_POST['budget'] ?? '') === '30k-50k'   ? 'selected' : '' ?>>30 000 € - 50 000 €</option>
                <option value="plus-50k"  <?= ($_POST['budget'] ?? '') === 'plus-50k'  ? 'selected' : '' ?>>Plus de 50 000 €</option>
              </select>
              <div class="invalid-feedback">Veuillez sélectionner une fourchette de budget.</div>
            </div>

            <div class="mb-3">
              <label for="message" class="form-label fw-semibold">Message <span class="text-dark">*</span></label>
              <textarea class="form-control form-control-lg" id="message" name="message" rows="5" required
                        placeholder="Décrivez votre projet en détail..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
              <div class="invalid-feedback">Veuillez saisir votre message.</div>
            </div>

            <div class="mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter"
                       <?= isset($_POST['newsletter']) ? 'checked' : '' ?>>
                <label class="form-check-label text-muted" for="newsletter">
                  Je souhaite m'abonner à la newsletter pour recevoir des conseils et inspirations
                </label>
              </div>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-dark btn-lg">
                <i class="fas fa-paper-plane me-2"></i>Envoyer la demande
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Colonne droite : infos contact — INCHANGÉE -->
      <div class="col-lg-5">
        <div class="contact-info h-100 fade-in" style="animation-delay: 0.2s">
          <h2 class="section-title mb-4">Localisation et informations</h2>
          <div class="mb-4">
            <div class="contact-image rounded overflow-hidden mb-3">
              <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?ixlib=rb-4.0.3&auto=format&fit=crop&w=1472&q=80"
                   alt="Intérieur design" class="img-fluid w-100" style="height:200px;object-fit:cover;">
            </div>
          </div>
          <div class="mb-4">
            <div class="map-container rounded-4 overflow-hidden shadow position-relative">
              <a href="https://www.google.com/maps/place/TEK-UP+University/@36.8978205,10.1900872,17z"
                 target="_blank" class="map-overlay" aria-label="Ouvrir dans Google Maps">
                <span class="map-btn"><i class="bi bi-geo-alt-fill"></i> Ouvrir dans Google Maps</span>
              </a>
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3189.057522223865!2d10.1875123!3d36.8978205!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12fd34cc25bd5aff%3A0x495e852ae57f3ff5!2sTEK-UP%20University!5e0!3m2!1sfr!2stn!4v20251209"
                      width="100%" height="280" style="border:0;" allowfullscreen loading="lazy"></iframe>
            </div>
          </div>
          <div class="mb-4">
            <h3 class="h5 fw-semibold text-dark mb-3"><i class="fas fa-map-marker-alt text-beige me-2"></i>Nos coordonnées</h3>
            <address class="mb-0">
              <strong class="text-dark">GreenHome Interiors LLP</strong><br>
              Immeuble Tekup, Rue du Lac Turkana<br>Les Berges du Lac, 1053 Tunis<br>Tunisie<br><br>
              <i class="fas fa-phone text-beige me-2"></i>+216 77 715 720<br>
              <i class="fas fa-envelope text-beige me-2"></i>info@greenhome.com
            </address>
          </div>
          <div class="mb-4">
            <h3 class="h5 fw-semibold text-dark mb-3"><i class="fas fa-clock text-beige me-2"></i>Horaires d'ouverture</h3>
            <table class="table table-borderless hours-table">
              <tbody>
                <tr><td class="fw-medium text-dark">Lundi - Vendredi</td><td class="text-end">08h00 - 18h00</td></tr>
                <tr><td class="fw-medium text-dark">Samedi</td><td class="text-end">10h00 - 16h00</td></tr>
                <tr><td class="fw-medium text-dark">Dimanche</td><td class="text-end text-muted">Fermé</td></tr>
              </tbody>
            </table>
          </div>
          <div>
            <h3 class="h5 fw-semibold text-dark mb-3"><i class="fas fa-share-alt text-beige me-2"></i>Suivez-nous</h3>
            <div class="social-icons d-flex gap-3">
              <a href="#" class="social-icon d-flex align-items-center justify-content-center"><i class="fab fa-facebook-f"></i></a>
              <a href="#" class="social-icon d-flex align-items-center justify-content-center"><i class="fab fa-instagram"></i></a>
              <a href="#" class="social-icon d-flex align-items-center justify-content-center"><i class="fab fa-linkedin-in"></i></a>
              <a href="#" class="social-icon d-flex align-items-center justify-content-center"><i class="fab fa-pinterest-p"></i></a>
              <a href="#" class="social-icon d-flex align-items-center justify-content-center"><i class="fab fa-youtube"></i></a>
            </div>
          </div>
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
        <a href="https://www.facebook.com" target="_blank"><i class="bi bi-facebook me-3"></i></a>
        <a href="https://www.instagram.com" target="_blank"><i class="bi bi-instagram me-3"></i></a>
        <a href="https://www.linkedin.com" target="_blank"><i class="bi bi-linkedin me-3"></i></a>
        <a href="https://www.youtube.com" target="_blank"><i class="bi bi-youtube me-3"></i></a>
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
        <p class="pfooter"><i class="bi bi-geo-alt"></i> Immeuble Tekup, Rue du Lac Turkana, Tunis</p>
        <p class="pfooter"><i class="bi bi-telephone"></i> +216 77 715 720</p>
        <p class="pfooter"><a href="mailto:info@greenhome.com" id="mail"><i class="bi bi-envelope"></i> info@greenhome.com</a></p>
      </div>
    </div>
  </div>
  <div id="div2footer">© 2025 GreenHome LLP. Tous droits réservés.</div>
</footer>

<a href="#" class="back-to-top"><i class="fas fa-chevron-up"></i></a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
  'use strict';
  document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', e => {
      if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
      form.classList.add('was-validated');
    });
  });
})();
</script>
</body>
</html>