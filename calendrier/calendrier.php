<?php
session_start();
require_once '../db.php';
/** @var PDO $pdo */

$message = '';
$message_type = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $firstName   = trim($_POST['firstName']);
    $lastName    = trim($_POST['lastName']);
    $email       = trim($_POST['email']);
    $phone       = trim($_POST['phone']);
    $service     = $_POST['service'] ?? '';
    $messageText = trim($_POST['message'] ?? '');
    $meetingType = $_POST['meetingType'] ?? 'presentiel';
    $budget      = $_POST['projectBudget'] ?? '';
    $elements    = isset($_POST['elements']) ? implode(', ', $_POST['elements']) : '';
    $newsletter  = isset($_POST['newsletter']) ? 1 : 0;
    $reminder    = isset($_POST['reminder']) ? 1 : 0;
    $date        = $_POST['appointmentDate'];
    $time        = $_POST['appointmentTime'];
    $conditions  = isset($_POST['conditions']);

   
    $errors = [];
    if (empty($firstName)) $errors[] = 'Prénom requis.';
    if (empty($lastName)) $errors[] = 'Nom requis.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
    if (empty($phone)) $errors[] = 'Téléphone requis.';
    if (empty($service)) $errors[] = 'Type de projet requis.';
    if (empty($date)) $errors[] = 'Date requise.';
    if (empty($time)) $errors[] = 'Horaire requis.';
    if (!$conditions) $errors[] = 'Vous devez accepter les conditions.';


    if ($date < date('Y-m-d')) $errors[] = 'La date ne peut pas être dans le passé.';
    
  
    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM appointments WHERE appointment_date = ? AND appointment_time = ?");
        $check->execute([$date, $time]);
        if ($check->fetch()) {
            $errors[] = 'Ce créneau horaire est déjà réservé. Veuillez en choisir un autre.';
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO appointments 
                (first_name, last_name, email, phone, service, message, meeting_type, budget_range, elements, newsletter, reminder, appointment_date, appointment_time, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')
            ");
            $stmt->execute([
                $firstName, $lastName, $email, $phone, $service, $messageText,
                $meetingType, $budget, $elements, $newsletter, $reminder, $date, $time
            ]);
            $appointmentId = $pdo->lastInsertId();

          
            $to = $email;
            $subject = "Confirmation de rendez-vous - GreenHome";
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: GreenHome <rdv@greenhome.com>\r\n";
            $body = "
                <html>
                <body>
                    <h2>Bonjour $firstName $lastName,</h2>
                    <p>Votre rendez-vous a bien été enregistré.</p>
                    <p><strong>Date :</strong> " . date('d/m/Y', strtotime($date)) . "<br>
                    <strong>Horaire :</strong> $time<br>
                    <strong>Type :</strong> $meetingType</p>
                    <p>Nous vous confirmerons par téléphone sous 24h.</p>
                    <p>À bientôt,<br>L'équipe GreenHome</p>
                </body>
                </html>
            ";
            @mail($to, $subject, $body, $headers);

            $message = "✅ Rendez-vous enregistré avec succès ! Vous recevrez un email de confirmation.";
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = "❌ Erreur technique : " . $e->getMessage();
            $message_type = 'error';
        }
    } else {
        $message = implode('<br>', $errors);
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous | Studio d'Architecture d'Intérieur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stylecalen.css">
    <style>
        .alert-custom { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d1e7dd; color: #0f5132; border-left: 5px solid #0f5132; }
        .alert-error { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }
        .spinner-border-sm { width: 1rem; height: 1rem; margin-left: 8px; }
        .time-slot:disabled { background: #f0f0f0; cursor: not-allowed; }
        .info-box { background: #f8f6f3; border-radius: 12px; padding: 20px; transition: 0.2s; }
        .info-box:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .bg-light-beige { background-color: #f8f6f3; }
    </style>
</head>
<body>

<header>
    <?php include '../navbar.php'; ?>
</header>

<section class="hero-section">
    <div class="container py-5">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold text-white mb-4">PRENEZ RENDEZ-VOUS</h1>
                <p class="lead text-white mb-0">Réservez une consultation avec nos experts en architecture d'intérieur pour discuter de votre projet.</p>
            </div>
        </div>
    </div>
</section>

<main class="container main-content">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="appointment-card">
                <?php if ($message): ?>
                    <div class="alert-custom alert-<?= $message_type === 'success' ? 'success' : 'error' ?>">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <article class="mb-5">
                    <h3 class="section-title"><i class="bi bi-info-circle me-2"></i>Pourquoi choisir notre studio ?</h3>
                    <p>Notre studio d'architecture d'intérieur vous accompagne dans tous vos projets de <strong>rénovation</strong>, <em>décoration</em> et <u>aménagement</u>. Nous mettons à votre disposition des experts certifiés pour réaliser vos rêves.</p>
                    <p>Chaque projet est unique : <mark>nous créons des espaces sur mesure</mark> qui reflètent votre personnalité tout en optimisant l'espace disponible.</p>
                    <hr>
                    <p>Nos tarifs sont <b>transparents</b> et notre première consultation est <strong>gratuite</strong>.<br>N'hésitez pas à nous contacter pour plus d'informations.</p>
                </article>

                <section class="mb-5">
                    <h4 class="mb-3">Glossaire des services</h4>
                    <dl class="row">
                        <dt class="col-sm-3">Design résidentiel</dt>
                        <dd class="col-sm-9">Aménagement et décoration d'espaces habitables (maisons, appartements)</dd>
                        <dt class="col-sm-3">Design commercial</dt>
                        <dd class="col-sm-9">Aménagement d'espaces professionnels (bureaux, boutiques, restaurants)</dd>
                        <dt class="col-sm-3">Rénovation complète</dt>
                        <dd class="col-sm-9">Transformation totale d'un espace incluant travaux structurels</dd>
                    </dl>
                </section>

                <form id="appointmentForm" method="POST" action="">
                    <!-- Informations personnelles -->
                    <section class="mb-5">
                        <h3 class="section-title">Informations personnelles</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="firstName" class="form-label fw-semibold">Prénom <span class="text-dark">*</span></label>
                                <input type="text" class="form-control form-control-lg" id="firstName" name="firstName" value="<?= htmlspecialchars($_POST['firstName'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lastName" class="form-label fw-semibold">Nom <span class="text-dark">*</span></label>
                                <input type="text" class="form-control form-control-lg" id="lastName" name="lastName" value="<?= htmlspecialchars($_POST['lastName'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email <span class="text-dark">*</span></label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Téléphone <span class="text-dark">*</span></label>
                                <input type="tel" class="form-control form-control-lg" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="service" class="form-label fw-semibold">Type de projet <span class="text-dark">*</span></label>
                                <select class="form-select form-select-lg" id="service" name="service" required>
                                    <option value="">Sélectionnez votre projet</option>
                                    <?php
                                    $options = ['Consultation initiale','Design résidentiel','Design commercial','Rénovation complète','Décoration intérieure','Aménagement d\'espace','Autre'];
                                    foreach ($options as $opt) {
                                        $selected = ($_POST['service'] ?? '') === $opt ? 'selected' : '';
                                        echo "<option value=\"$opt\" $selected>$opt</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="message" class="form-label fw-semibold">Description du projet</label>
                                <textarea class="form-control form-control-lg" id="message" name="message" rows="4"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- Tarifs -->
                    <section class="mb-5">
                        <h3 class="section-title">Tarifs des services</h3>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <caption>Tarifs TTC pour une surface de 50m²</caption>
                                <thead class="table-dark">
                                    <tr><th>Service</th><th>Durée</th><th>Prix</th><th>Garantie</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>Consultation initiale</td><td>1 heure</td><td>Gratuit</td><td>-</td></tr>
                                    <tr><td>Design résidentiel</td><td>2-4 semaines</td><td>À partir de 1 500€</td><td>1 an</td></tr>
                                    <tr><td>Rénovation complète</td><td>3-6 mois</td><td>À partir de 15 000€</td><td>5 ans</td></tr>
                                </tbody>
                                <tfoot><tr><td colspan="4" class="text-end small">* Les prix sont donnés à titre indicatif</td></tr></tfoot>
                            </table>
                        </div>
                    </section>

                    <!-- Infos pratiques -->
                    <div class="row mb-5">
                        <div class="col-md-4"><div class="info-box text-center"><i class="bi bi-clock-history fs-3"></i><h5>Durée</h5><p>Consultation de 60 minutes</p></div></div>
                        <div class="col-md-4"><div class="info-box text-center"><i class="bi bi-cash fs-3"></i><h5>Tarif</h5><p>Première consultation gratuite</p></div></div>
                        <div class="col-md-4"><div class="info-box text-center"><i class="bi bi-geo-alt fs-3"></i><h5>Lieu</h5><p>En présentiel ou visioconférence</p></div></div>
                    </div>

                    <!-- Date et heure -->
                    <section class="mb-5">
                        <h3 class="section-title">Date et heure du rendez-vous</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="appointmentDate" class="form-label fw-semibold">Date <span class="text-dark">*</span></label>
                                <input type="date" class="form-control form-control-lg" id="appointmentDate" name="appointmentDate" value="<?= htmlspecialchars($_POST['appointmentDate'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="appointmentTime" class="form-label fw-semibold">Heure <span class="text-dark">*</span></label>
                                <select class="form-select form-select-lg" id="appointmentTime" name="appointmentTime" required>
                                    <option value="">Sélectionnez un créneau</option>
                                    <option>09:00</option><option>10:00</option><option>11:00</option>
                                    <option>14:00</option><option>15:00</option><option>16:00</option>
                                    <option>17:00</option><option>18:00</option>
                                </select>
                                <div class="form-text">Les créneaux déjà réservés seront désactivés automatiquement.</div>
                            </div>
                        </div>
                    </section>

                    <!-- Préférences supplémentaires -->
                    <section class="mb-5">
                        <h3 class="section-title">Préférences supplémentaires</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold mb-3">Type de rendez-vous <span class="text-dark">*</span></label>
                                <?php $meeting = $_POST['meetingType'] ?? 'presentiel'; ?>
                                <div class="radio-option"><input type="radio" name="meetingType" id="presentiel" value="presentiel" <?= $meeting === 'presentiel' ? 'checked' : '' ?>><label for="presentiel" class="radio-label">Présentiel</label></div>
                                <div class="radio-option"><input type="radio" name="meetingType" id="visio" value="visio" <?= $meeting === 'visio' ? 'checked' : '' ?>><label for="visio" class="radio-label">Visio</label></div>
                                <div class="radio-option"><input type="radio" name="meetingType" id="domicile" value="domicile" <?= $meeting === 'domicile' ? 'checked' : '' ?>><label for="domicile" class="radio-label">Domicile</label></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="projectBudget" class="form-label fw-semibold">Budget estimé</label>
                                <select class="form-select form-select-lg" id="projectBudget" name="projectBudget">
                                    <option value="">Non spécifié</option>
                                    <option value="1">Moins de 5 000 dt</option>
                                    <option value="2">5 000 dt - 15 000 dt</option>
                                    <option value="3">15 000 dt - 30 000 dt</option>
                                    <option value="4">30 000 dt - 50 000 dt</option>
                                    <option value="5">Plus de 50 000 dt</option>
                                </select>
                            </div>
                        </div>
                        <div class="p-4 bg-light-beige rounded-3 mb-4">
                            <label class="form-label fw-semibold mb-3">Éléments souhaités :</label>
                            <div class="row">
                                <?php $selectedElements = $_POST['elements'] ?? []; ?>
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" id="check1" name="elements[]" value="Plans 3D" <?= in_array('Plans 3D', $selectedElements) ? 'checked' : '' ?>><label class="form-check-label" for="check1">Plans 3D</label></div></div>
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" id="check2" name="elements[]" value="Liste fournisseurs" <?= in_array('Liste fournisseurs', $selectedElements) ? 'checked' : '' ?>><label class="form-check-label" for="check2">Liste fournisseurs</label></div></div>
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" id="check3" name="elements[]" value="Suivi travaux" <?= in_array('Suivi travaux', $selectedElements) ? 'checked' : '' ?>><label class="form-check-label" for="check3">Suivi travaux</label></div></div>
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" id="check4" name="elements[]" value="Déco luminaires" <?= in_array('Déco luminaires', $selectedElements) ? 'checked' : '' ?>><label class="form-check-label" for="check4">Déco luminaires</label></div></div>
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" id="check5" name="elements[]" value="Plans électriques" <?= in_array('Plans électriques', $selectedElements) ? 'checked' : '' ?>><label class="form-check-label" for="check5">Plans électriques</label></div></div>
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" id="check6" name="elements[]" value="Meubles sur mesure" <?= in_array('Meubles sur mesure', $selectedElements) ? 'checked' : '' ?>><label class="form-check-label" for="check6">Meubles sur mesure</label></div></div>
                            </div>
                        </div>
                    </section>

                    <!-- Vidéo -->
                    <section class="mb-5 text-center">
                        <h3 class="section-title">Découvrez notre studio</h3>
                        <div class="row justify-content-center">
                            <div class="col-md-6 mb-4">
                                <div class="ratio ratio-16x9 mx-auto">
                                    <video controls class="rounded-3 w-100"><source src="../video/MM.mp4" type="video/mp4">Votre navigateur ne supporte pas la lecture de vidéos.</video>
                                </div>
                                <p class="small text-muted mt-2">Vidéo de présentation de nos services</p>
                            </div>
                        </div>
                    </section>

                    <!-- Audio témoignage -->
                    <aside class="mb-5 p-4 bg-light-beige rounded-3 text-center">
                        <h4><i class="bi bi-mic me-2"></i>Témoignage client</h4>
                        <audio controls class="w-100 mt-3"><source src="../AA.mp3" type="audio/mpeg">Votre navigateur ne supporte pas l'élément audio.</audio>
                        <p class="small mt-2">Écoutez le témoignage de M. Dupont sur notre travail</p>
                    </aside>

                    <!-- Consentements -->
                    <fieldset class="mb-4 p-4 border rounded-3">
                        <legend class="h5 fw-semibold">Confirmation et consentements</legend>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="conditions" name="conditions" required <?= isset($_POST['conditions']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="conditions">J'accepte les <a href="#" class="text-decoration-none text-dark">conditions générales</a> et la politique de confidentialité <span class="text-dark">*</span></label>
                        </div>
                        <div class="row">
                            <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" <?= isset($_POST['newsletter']) ? 'checked' : '' ?>><label class="form-check-label" for="newsletter">Recevoir la newsletter</label></div></div>
                            <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" id="reminder" name="reminder" <?= isset($_POST['reminder']) ? 'checked' : '' ?>><label class="form-check-label" for="reminder">Rappel par SMS</label></div></div>
                        </div>
                    </fieldset>

                    <!-- Boutons -->
                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-dark btn-lg px-5 me-3" id="submitBtn"><i class="bi bi-calendar-plus me-2"></i>Confirmer le rendez-vous</button>
                        <button type="reset" class="btn btn-outline-dark btn-lg"><i class="bi bi-arrow-clockwise me-2"></i>Réinitialiser</button>
                        <p class="text-muted small mt-3">Vous recevrez une confirmation par email dans les 24 heures</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

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
            <div class="col-md-2"><p class="p_gris">Liens importants</p><p class="pfooter"><a href="../accueil.html">Accueil</a></p><p class="pfooter"><a href="../apropos.html">À propos</a></p><p class="pfooter"><a href="../services.html">Services</a></p><p class="pfooter"><a href="../portfolio/portfolio.html">Portfolio</a></p><p class="pfooter"><a href="../contact/contact.html">Contact</a></p></div>
            <div class="col-md-4"><p class="p_gris">Contact</p><p class="pfooter"><i class="bi bi-geo-alt"></i> 2972 Westheimer Rd. Santa Ana, Illinois 85486</p><p class="pfooter"><i class="bi bi-telephone"></i> +216 77 715 720</p><p class="pfooter"><a href="mailto:info@greenhome.com" id="mail"><i class="bi bi-envelope"></i> info@greenhome.com</a></p></div>
        </div>
    </div>
    <div id="div2footer">© 2025 GreenHome LLP. Tous droits réservés.</div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('appointmentDate');
    const timeSelect = document.getElementById('appointmentTime');
    const form = document.getElementById('appointmentForm');
    const submitBtn = document.getElementById('submitBtn');

  
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);

    
    async function loadAvailableSlots() {
        const date = dateInput.value;
        if (!date) return;
        timeSelect.disabled = true;
        timeSelect.innerHTML = '<option>Chargement des créneaux...</option>';
        try {
            const response = await fetch(`check_slots.php?date=${encodeURIComponent(date)}`);
            const takenSlots = await response.json(); 
            const allOptions = ['09:00','10:00','11:00','14:00','15:00','16:00','17:00','18:00'];
            let optionsHtml = '<option value="">Sélectionnez un créneau</option>';
            for (let slot of allOptions) {
                const disabled = takenSlots.includes(slot) ? 'disabled' : '';
                optionsHtml += `<option value="${slot}" ${disabled}>${slot} ${disabled ? '❌ (indisponible)' : ''}</option>`;
            }
            timeSelect.innerHTML = optionsHtml;
            timeSelect.disabled = false;
        } catch(err) {
            console.error(err);
            timeSelect.innerHTML = '<option>Erreur de chargement</option>';
        }
    }

    dateInput.addEventListener('change', loadAvailableSlots);
    if (dateInput.value) loadAvailableSlots();

   
    form.addEventListener('submit', function(e) {
        const conditions = document.getElementById('conditions');
        if (!conditions.checked) {
            e.preventDefault();
            alert('Vous devez accepter les conditions générales.');
            return false;
        }
        if (!dateInput.value || !timeSelect.value) {
            e.preventDefault();
            alert('Veuillez choisir une date et un horaire.');
            return false;
        }
      
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-calendar-plus me-2"></i>Envoi en cours... <span class="spinner-border spinner-border-sm"></span>';
        
        setTimeout(() => { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-calendar-plus me-2"></i>Confirmer le rendez-vous'; }, 5000);
    });
});
</script>
</body>
</html>