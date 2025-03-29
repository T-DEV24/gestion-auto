<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: main.php');
    exit();
}

require_once '../Controller/FormationController.php';
$controller = new FormationController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $controller->addFormation($titre, $description, $prix, $date_debut, $date_fin);
    header('Location: listeFormation.php?success=Formation ajoutée');
    exit();
}

ob_start();
?>

    <div class="container mt-4">
        <h2 class="mb-4">
            <i class="fas fa-chalkboard-teacher me-2"></i>Ajouter une formation
        </h2>

        <!-- Afficher un message de succès ou d'erreur si présent -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire pour ajouter une formation -->
        <form method="POST" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre :</label>
                <input type="text" class="form-control" id="titre" name="titre" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description :</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>
            <div class="mb-3">
                <label for="prix" class="form-label">Prix (en EUR) :</label>
                <input type="number" step="0.01" class="form-control" id="prix" name="prix" required>
            </div>
            <div class="mb-3">
                <label for="date_debut" class="form-label">Date de début :</label>
                <input type="date" class="form-control" id="date_debut" name="date_debut" required>
            </div>
            <div class="mb-3">
                <label for="date_fin" class="form-label">Date de fin :</label>
                <input type="date" class="form-control" id="date_fin" name="date_fin" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Ajouter
            </button>
            <a href="listeFormation.php" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Liste des formations
            </a>
        </form>
    </div>

<?php
$content = ob_get_clean();
require 'template.php';
?>