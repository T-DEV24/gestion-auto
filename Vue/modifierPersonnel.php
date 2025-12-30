<?php
require_once '../Config/auth.php';
requireAdmin();

require_once '../Controller/PersonnelController.php';
$controller = new PersonnelController();

$id = isset($_GET['id']) ? $_GET['id'] : null;
$personnel = $id ? $controller->getPersonnelById($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $controller->updatePersonnel($id, $nom, $prenom, $role, $email);
    header('Location: listePersonnel.php?success=Personnel modifié');
    exit();
}

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-edit me-2"></i>Modifier un personnel
    </h2>

    <?php if (!$personnel): ?>
        <div class="alert alert-danger">Personnel non trouvé.</div>
    <?php else: ?>
        <form method="POST" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom :</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($personnel['nom']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom :</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($personnel['prenom']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rôle :</label>
                <input type="text" class="form-control" id="role" name="role" value="<?php echo htmlspecialchars($personnel['role']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($personnel['email']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Enregistrer
            </button>
            <a href="listePersonnel.php" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
