<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: main.php');
    exit();
}

require_once '../Controller/InscriptionController.php';
require_once '../Controller/ApprenantController.php';
require_once '../Controller/FormationController.php';

$inscriptionController = new InscriptionController();
$apprenantController = new ApprenantController();
$formationController = new FormationController();

$apprenants = $apprenantController->getAllApprenants();
$formations = $formationController->getAllFormations();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $formation_id = $_POST['formation_id'];

    try {
        if ($inscriptionController->registerToFormation($user_id, $formation_id)) {
            header('Location: listeInscription.php?success=Inscription ajoutée');
            exit();
        } else {
            $message = "L'utilisateur est déjà inscrit à cette formation.";
        }
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}

ob_start();
?>

    <div class="container mt-4">
        <h2 class="mb-4">
            <i class="fas fa-clipboard-list me-2"></i>Ajouter une inscription
        </h2>

        <!-- Afficher un message de succès ou d'erreur si présent -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($message || isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($message ?: $_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire pour ajouter une inscription -->
        <form method="POST" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="user_id" class="form-label">Utilisateur :</label>
                <select class="form-select" id="user_id" name="user_id" required>
                    <option value="">-- Sélectionner un utilisateur --</option>
                    <?php foreach ($apprenants as $apprenant): ?>
                        <option value="<?php echo htmlspecialchars($apprenant['user_id']); ?>">
                            <?php echo htmlspecialchars($apprenant['nom'] . ' ' . $apprenant['prenom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="formation_id" class="form-label">Formation :</label>
                <select class="form-select" id="formation_id" name="formation_id" required>
                    <option value="">-- Sélectionner une formation --</option>
                    <?php foreach ($formations as $formation): ?>
                        <option value="<?php echo htmlspecialchars($formation['id']); ?>">
                            <?php echo htmlspecialchars($formation['titre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Ajouter
            </button>
            <a href="listeInscription.php" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Liste des inscriptions
            </a>
        </form>
    </div>

<?php
$content = ob_get_clean();
require 'template.php';
?>