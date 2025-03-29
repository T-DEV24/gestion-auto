<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: dashboard.php');
        exit();
    } else {
        header('Location: main.php');
        exit();
    }
}

ob_start();
?>

    <div class="container mt-4">
        <h2 class="mb-4">
            <i class="fas fa-user-plus me-2"></i>Inscription
        </h2>

        <!-- Afficher un message d'erreur si présent -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>Erreur : <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'inscription -->
        <form method="POST" action="../Controller/AuthController.php?action=register" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur :</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe :</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rôle :</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="user">Utilisateur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>S'inscrire
            </button>
            <a href="login.php" class="btn btn-secondary ms-2">
                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
            </a>
        </form>
    </div>

<?php
$content = ob_get_clean();
require 'template.php';
?>