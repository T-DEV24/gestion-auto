<?php
require_once '../Config/auth.php';
requireAdmin();

require_once '../Controller/FormateurController.php';
require_once '../Controller/ApprenantController.php';

$formateurController = new FormateurController();
$apprenantController = new ApprenantController();
$formateurs = $formateurController->getAllFormateurs();
$apprenants = $apprenantController->getAllApprenants();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            throw new Exception("Jeton CSRF invalide.");
        }
        $formateur_id = (int) $_POST['formateur_id'];
        $apprenant_id = (int) $_POST['apprenant_id'];
        $formateurController->assignApprenant($formateur_id, $apprenant_id);
        header('Location: listeFormateur.php?success=Association créée');
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-link me-2"></i>Associer un formateur à un apprenant</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(getCsrfToken()); ?>">
        <div class="mb-3">
            <label class="form-label">Formateur</label>
            <select name="formateur_id" class="form-select" required>
                <option value="">Sélectionner</option>
                <?php foreach ($formateurs as $formateur): ?>
                    <option value="<?php echo $formateur['id']; ?>">
                        <?php echo htmlspecialchars($formateur['prenom'] . ' ' . $formateur['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Apprenant</label>
            <select name="apprenant_id" class="form-select" required>
                <option value="">Sélectionner</option>
                <?php foreach ($apprenants as $apprenant): ?>
                    <option value="<?php echo $apprenant['id']; ?>">
                        <?php echo htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Associer
        </button>
    </form>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
