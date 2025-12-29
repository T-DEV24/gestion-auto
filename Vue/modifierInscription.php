<?php
require_once '../Config/auth.php';
requireAdmin('login.php');

require_once '../Controller/InscriptionController.php';
require_once '../Controller/ApprenantController.php';
require_once '../Controller/FormationController.php';

$controller = new InscriptionController();
$apprenantController = new ApprenantController();
$formationController = new FormationController();

$id = isset($_GET['id']) ? $_GET['id'] : null;
$inscription = $id ? $controller->getInscriptionById($id) : null;

$apprenants = $apprenantController->getAllApprenants();
$formations = $formationController->getAllFormations();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $formation_id = $_POST['formation_id'];
    $controller->updateInscription($id, $user_id, $formation_id);
    header('Location: listeInscription.php?success=Inscription modifiée');
    exit();
}

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-edit me-2"></i>Modifier une inscription
    </h2>

    <?php if (!$inscription): ?>
        <div class="alert alert-danger">Inscription non trouvée.</div>
    <?php else: ?>
        <form method="POST" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="user_id" class="form-label">Apprenant :</label>
                <select class="form-select" id="user_id" name="user_id" required>
                    <?php foreach ($apprenants as $apprenant): ?>
                        <option value="<?php echo $apprenant['user_id']; ?>" 
                                <?php echo $apprenant['user_id'] == $inscription['user_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($apprenant['nom'] . ' ' . $apprenant['prenom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="formation_id" class="form-label">Formation :</label>
                <select class="form-select" id="formation_id" name="formation_id" required>
                    <?php foreach ($formations as $formation): ?>
                        <option value="<?php echo $formation['id']; ?>" 
                                <?php echo $formation['id'] == $inscription['formation_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($formation['titre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Enregistrer
            </button>
            <a href="listeInscription.php" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
