<?php
require_once '../Config/auth.php';
requireAdmin();

require_once '../Controller/ApprenantController.php';
require_once '../Controller/UserController.php';

$apprenantController = new ApprenantController();
$userController = new UserController();

// Récupérer tous les utilisateurs pour le menu déroulant
$users = $userController->getAllUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $user_id = $_POST['user_id'] ?: null;

    try {
        $apprenantController->addApprenant($nom, $prenom, $email, $user_id);
        header('Location: listeApprenant.php?success=Apprenant ajouté');
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-user-graduate me-2"></i>Ajouter un apprenant
    </h2>

    <!-- Afficher un message de succès ou d'erreur si présent -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) || isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_GET['error'] ?? $error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Formulaire pour ajouter un apprenant -->
    <form method="POST" class="bg-light p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom :</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom :</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email :</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="user_id" class="form-label">Utilisateur lié :</label>
            <select class="form-select" id="user_id" name="user_id">
                <option value="">Aucun utilisateur</option>
                <?php foreach ($users as $user): ?>
                    <?php if (!$userController->isUserLinkedToApprenant($user['id'])): ?>
                        <option value="<?php echo $user['id']; ?>">
                            <?php echo htmlspecialchars($user['username']); ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Ajouter
        </button>
        <a href="listeApprenant.php" class="btn btn-secondary ms-2">
            <i class="fas fa-arrow-left me-2"></i>Liste des apprenants
        </a>
    </form>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
