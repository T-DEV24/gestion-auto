<?php
require_once '../Config/auth.php';
requireAdmin();

require_once '../Controller/ApprenantController.php';
require_once '../Controller/UserController.php';

$apprenantController = new ApprenantController();
$userController = new UserController();

$id = isset($_GET['id']) ? $_GET['id'] : null;
$apprenant = $id ? $apprenantController->getApprenantById($id) : null;

// Récupérer tous les utilisateurs pour le menu déroulant
$users = $userController->getAllUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $user_id = $_POST['user_id'] ?: null;

    try {
        $apprenantController->updateApprenant($id, $nom, $prenom, $user_id);
        header('Location: listeApprenant.php?success=Apprenant modifié');
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-edit me-2"></i>Modifier un apprenant
    </h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!$apprenant): ?>
        <div class="alert alert-danger">Apprenant non trouvé.</div>
    <?php else: ?>
        <form method="POST" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom :</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($apprenant['nom']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom :</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($apprenant['prenom']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="user_id" class="form-label">Utilisateur lié :</label>
                <select class="form-select" id="user_id" name="user_id">
                    <option value="">Aucun utilisateur</option>
                    <?php foreach ($users as $user): ?>
                        <?php if (!$userController->isUserLinkedToApprenant($user['id']) || $user['id'] == $apprenant['user_id']): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo $apprenant['user_id'] == $user['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($user['username']); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Enregistrer
            </button>
            <a href="listeApprenant.php" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
