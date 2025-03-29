<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: main.php');
    exit();
}

require_once '../Controller/PersonnelController.php';
$controller = new PersonnelController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $controller->addPersonnel($nom, $prenom, $role, $email);
    header('Location: listePersonnel.php?success=Personnel ajouté');
    exit();
}

ob_start();
?>

    <div class="container mt-4">
        <h2 class="mb-4">
            <i class="fas fa-user-tie me-2"></i>Ajouter un personnel
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

        <!-- Formulaire pour ajouter un personnel -->
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
                <label for="role" class="form-label">Rôle :</label>
                <input type="text" class="form-control" id="role" name="role" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Ajouter
            </button>
            <a href="listePersonnel.php" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Liste des personnels
            </a>
        </form>
    </div>

<?php
$content = ob_get_clean();
require 'template.php';
?>