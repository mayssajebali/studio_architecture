<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: ../accueil.php');
    exit;
}
require_once '../db.php';
/** @var PDO $pdo */

$message = '';
$categorie_filtre = $_GET['categorie'] ?? 'all';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'ajouter') {
        $titre = trim($_POST['titre']);
        $categorie = $_POST['categorie'];
        $type_media = $_POST['type_media'];
        $media_path = trim($_POST['media_path']);
        $description = trim($_POST['description_longue']);
        $ville = trim($_POST['ville']);
        $annee = (int)$_POST['annee'];
        $superficie = trim($_POST['superficie']);
        $concept = trim($_POST['concept']);
        $materiaux = trim($_POST['materiaux']);
        $architecte = trim($_POST['architecte']);
        $designer = trim($_POST['designer']);
        $photographe = trim($_POST['photographe']);
        $duree = trim($_POST['duree']);
        $budget = trim($_POST['budget']);

        $stmt = $pdo->prepare("
            INSERT INTO projets_portfolio 
            (titre, categorie, type_media, media_path, description_longue, ville, annee, superficie,
             concept, materiaux, architecte, designer, photographe, duree, budget)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$titre, $categorie, $type_media, $media_path, $description, $ville, $annee, $superficie,
                       $concept, $materiaux, $architecte, $designer, $photographe, $duree, $budget]);
        $message = "Projet ajouté.";
    }
    elseif ($action === 'modifier') {
        $id = (int)$_POST['id'];
        $titre = trim($_POST['titre']);
        $categorie = $_POST['categorie'];
        $type_media = $_POST['type_media'];
        $media_path = trim($_POST['media_path']);
        $description = trim($_POST['description_longue']);
        $ville = trim($_POST['ville']);
        $annee = (int)$_POST['annee'];
        $superficie = trim($_POST['superficie']);
        $concept = trim($_POST['concept']);
        $materiaux = trim($_POST['materiaux']);
        $architecte = trim($_POST['architecte']);
        $designer = trim($_POST['designer']);
        $photographe = trim($_POST['photographe']);
        $duree = trim($_POST['duree']);
        $budget = trim($_POST['budget']);

        $stmt = $pdo->prepare("
            UPDATE projets_portfolio 
            SET titre=?, categorie=?, type_media=?, media_path=?, description_longue=?, ville=?, annee=?, superficie=?,
                concept=?, materiaux=?, architecte=?, designer=?, photographe=?, duree=?, budget=?
            WHERE id=?
        ");
        $stmt->execute([$titre, $categorie, $type_media, $media_path, $description, $ville, $annee, $superficie,
                       $concept, $materiaux, $architecte, $designer, $photographe, $duree, $budget, $id]);
        $message = "Projet modifié.";
    }
    elseif ($action === 'supprimer') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM projets_portfolio WHERE id = ?")->execute([$id]);
        $message = "Projet supprimé.";
    }
}

$sql = "SELECT * FROM projets_portfolio";
$params = [];
if ($categorie_filtre !== 'all') {
    $sql .= " WHERE categorie = ?";
    $params[] = $categorie_filtre;
}
$sql .= " ORDER BY date_creation DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projets = $stmt->fetchAll();


$categories = ['residential', 'hotelier', 'commercial', 'bureau', 'restau', 'surMesure'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Portfolio - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f6f3; font-family: 'Inter', sans-serif; padding-top: 80px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table img { width: 80px; height: 60px; object-fit: cover; border-radius: 6px; }
        .btn-sm { margin: 2px; }
        .filter-bar { background: white; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion du Portfolio</h1>
        <a href="dashboard_admin.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= $message ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    
    <!-- Filtre par catégorie -->
    <div class="filter-bar">
        <label class="fw-semibold">Filtrer par catégorie :</label>
        <div class="d-flex gap-2">
            <a href="?categorie=all" class="btn btn-sm <?= $categorie_filtre === 'all' ? 'btn-dark' : 'btn-outline-secondary' ?>">Tous</a>
            <?php foreach ($categories as $cat): ?>
                <a href="?categorie=<?= $cat ?>" class="btn btn-sm <?= $categorie_filtre === $cat ? 'btn-dark' : 'btn-outline-secondary' ?>">
                    <?= ucfirst($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>
       <button class="btn btn-beige-clair ms-auto" data-bs-toggle="modal" data-bs-target="#modalProjet" onclick="resetForm()">
    <i class="fas fa-plus"></i> Nouveau projet
</button>
    </div>
    
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>ID</th><th>Média</th><th>Titre</th><th>Catégorie</th><th>Ville</th><th>Année</th><th>Vues</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projets as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td>
                                <?php if ($p['type_media'] === 'image'): ?>
                                    <img src="<?= htmlspecialchars($p['media_path']) ?>" alt="aperçu">
                                <?php else: ?>
                                    <i class="fas fa-video fa-2x text-secondary"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($p['titre']) ?></td>
                            <td><?= htmlspecialchars($p['categorie']) ?></td>
                            <td><?= htmlspecialchars($p['ville']) ?></td>
                            <td><?= $p['annee'] ?></td>
                            <td><?= $p['vues'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editProjet(<?= htmlspecialchars(json_encode($p)) ?>)"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="if(confirm('Supprimer ?')) document.getElementById('deleteForm<?= $p['id'] ?>').submit();"><i class="fas fa-trash"></i></button>
                                <form id="deleteForm<?= $p['id'] ?>" method="POST" style="display:none;"><input type="hidden" name="action" value="supprimer"><input type="hidden" name="id" value="<?= $p['id'] ?>"></form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($projets)): ?>
                            <tr><td colspan="8" class="text-center py-4">Aucun projet trouvé. Cliquez sur "Nouveau projet" pour ajouter.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout / Modification -->
<div class="modal fade" id="modalProjet" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" id="formAction" value="ajouter">
                <input type="hidden" name="id" id="projetId">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Ajouter un projet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Titre *</label><input type="text" name="titre" id="titre" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Catégorie *</label>
                            <select name="categorie" id="categorie" class="form-select" required>
                                <option value="">Sélectionner</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat ?>"><?= ucfirst($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label">Type média *</label><select name="type_media" id="type_media" class="form-select"><option value="image">Image</option><option value="video">Vidéo</option></select></div>
                        <div class="col-md-6"><label class="form-label">URL média *</label><input type="text" name="media_path" id="media_path" class="form-control" placeholder="https://... ou ../video/..." required></div>
                        <div class="col-12"><label class="form-label">Description longue</label><textarea name="description_longue" id="description_longue" class="form-control" rows="3"></textarea></div>
                        <div class="col-md-4"><label class="form-label">Ville</label><input type="text" name="ville" id="ville" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Année</label><input type="number" name="annee" id="annee" class="form-control" min="1900" max="2030"></div>
                        <div class="col-md-4"><label class="form-label">Superficie</label><input type="text" name="superficie" id="superficie" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Concept</label><textarea name="concept" id="concept" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Matériaux</label><textarea name="materiaux" id="materiaux" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-4"><label class="form-label">Architecte</label><input type="text" name="architecte" id="architecte" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Designer</label><input type="text" name="designer" id="designer" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Photographe</label><input type="text" name="photographe" id="photographe" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Durée</label><input type="text" name="duree" id="duree" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Budget</label><input type="text" name="budget" id="budget" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
function resetForm() {
    document.getElementById('formAction').value = 'ajouter';
    document.getElementById('projetId').value = '';
    document.getElementById('modalTitle').innerText = 'Ajouter un projet';
    document.getElementById('titre').value = '';
    document.getElementById('categorie').value = '';
    document.getElementById('type_media').value = 'image';
    document.getElementById('media_path').value = '';
    document.getElementById('description_longue').value = '';
    document.getElementById('ville').value = '';
    document.getElementById('annee').value = '';
    document.getElementById('superficie').value = '';
    document.getElementById('concept').value = '';
    document.getElementById('materiaux').value = '';
    document.getElementById('architecte').value = '';
    document.getElementById('designer').value = '';
    document.getElementById('photographe').value = '';
    document.getElementById('duree').value = '';
    document.getElementById('budget').value = '';
}
function editProjet(p) {
    document.getElementById('formAction').value = 'modifier';
    document.getElementById('projetId').value = p.id;
    document.getElementById('modalTitle').innerText = 'Modifier le projet';
    document.getElementById('titre').value = p.titre || '';
    document.getElementById('categorie').value = p.categorie || '';
    document.getElementById('type_media').value = p.type_media || 'image';
    document.getElementById('media_path').value = p.media_path || '';
    document.getElementById('description_longue').value = p.description_longue || '';
    document.getElementById('ville').value = p.ville || '';
    document.getElementById('annee').value = p.annee || '';
    document.getElementById('superficie').value = p.superficie || '';
    document.getElementById('concept').value = p.concept || '';
    document.getElementById('materiaux').value = p.materiaux || '';
    document.getElementById('architecte').value = p.architecte || '';
    document.getElementById('designer').value = p.designer || '';
    document.getElementById('photographe').value = p.photographe || '';
    document.getElementById('duree').value = p.duree || '';
    document.getElementById('budget').value = p.budget || '';
    new bootstrap.Modal(document.getElementById('modalProjet')).show();
}
</script>
</body>
</html>