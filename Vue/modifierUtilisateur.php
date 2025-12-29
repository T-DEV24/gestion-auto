<?php
require_once '../Config/auth.php';
requireAdmin();

require_once '../Controller/UserController.php';
$controller = new UserController();

$id = isset($_GET['id']) ? $_GET['id'] : null;
$user = $id ? $controller->getUserById($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $controller->updateUser($id, $username, $email, $role);
    header('Location: listeUtilisateur.php?success=Utilisateur modifié');
    exit();
}

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="fas fa-edit me-2"></i>Modifier un utilisateur
    </h2>

    <?php if (!$user): ?>
        <div class="alert alert-danger">Utilisateur non trouvé.</div>
    <?php else: ?>
        <form method="POST" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur :</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rôle :</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>Utilisateur</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Enregistrer
            </button>
            <a href="listeUtilisateur.php" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
