<?php
require_once '../Config/auth.php';
requireAdmin('login.php');

require_once '../Controller/FormationController.php';
$controller = new FormationController();

$id = isset($_GET['id']) ? $_GET['id'] : null;
$formation = $id ? $controller->getFormationById($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $controller->updateFormation($id, $titre, $description, $prix);
    header('Location: listeFormation.php?success=Formation modifiée');
    exit();
}

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-edit me-2"></i>Modifier une formation
    </h2>

    <?php if (!$formation): ?>
        <div class="alert alert-danger">Formation non trouvée.</div>
    <?php else: ?>
        <form method="POST" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre :</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?php echo htmlspecialchars($formation['titre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description :</label>
                <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($formation['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="prix" class="form-label">Prix (EUR) :</label>
                <input type="number" step="0.01" class="form-control" id="prix" name="prix" value="<?php echo htmlspecialchars($formation['prix']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Enregistrer
            </button>
            <a href="listeFormation.php" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
