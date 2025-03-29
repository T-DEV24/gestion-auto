<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$file = isset($_GET['file']) ? $_GET['file'] : '';
$facturePath = '../Asset/templates/' . $file;

ob_start();
?>

    <div class="container mt-4">
        <h2 class="mb-4">
            <i class="fas fa-file-invoice me-2"></i>Facture générée
        </h2>

        <?php if (!$file || !file_exists($facturePath)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>Facture non trouvée.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <a href="listePaiement.php" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste des paiements
            </a>
        <?php else: ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>La facture a été générée avec succès. Vous pouvez la télécharger ci-dessous.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <div class="mb-3">
                <a href="../Asset/templates/<?php echo htmlspecialchars($file); ?>" download class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Télécharger la facture
                </a>
                <a href="listePaiement.php" class="btn btn-secondary ms-2">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste des paiements
                </a>
            </div>
        <?php endif; ?>
    </div>

<?php
$content = ob_get_clean();
require 'template.php';
?>