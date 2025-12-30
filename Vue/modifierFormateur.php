<?php
require_once '../Config/auth.php';
requireAdmin();

require_once '../Controller/FormateurController.php';
$controller = new FormateurController();
$error = null;

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$formateur = $id ? $controller->getFormateurById($id) : null;

if (!$formateur) {
    header('Location: listeFormateur.php?error=Formateur introuvable');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            throw new Exception("Jeton CSRF invalide.");
        }
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $specialite = trim($_POST['specialite']);
        $user_id = $_POST['user_id'] !== '' ? (int) $_POST['user_id'] : null;
        $controller->updateFormateur($id, $nom, $prenom, $email, $specialite, $user_id);
        header('Location: listeFormateur.php?success=Formateur mis à jour');
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

ob_start();
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-user-edit me-2"></i>Modifier un formateur</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(getCsrfToken()); ?>">
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($formateur['nom']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($formateur['prenom']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($formateur['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Spécialité</label>
            <input type="text" name="specialite" class="form-control" value="<?php echo htmlspecialchars($formateur['specialite'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Utilisateur associé (ID)</label>
            <input type="number" name="user_id" class="form-control" value="<?php echo htmlspecialchars($formateur['user_id'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Enregistrer
        </button>
    </form>
</div>

<?php
$content = ob_get_clean();
require 'template.php';
?>
