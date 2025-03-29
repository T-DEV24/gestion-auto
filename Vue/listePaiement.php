<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: main.php');
    exit();
}

require_once '../Controller/PaiementController.php';
$controller = new PaiementController();
$paiements = $controller->getAllPaiements();

ob_start();
?>

    <div class="container mt-4">
        <h2 class="mb-4">
            <i class="fas fa-file-invoice-dollar me-2"></i>Liste des paiements
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

        <!-- Tableau des paiements -->
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Formation</th>
                <th>Montant</th>
                <th>Statut</th>
                <th>Date de paiement</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($paiements)): ?>
                <tr>
                    <td colspan="7" class="text-center">Aucun paiement trouvé.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($paiements as $paiement): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($paiement['id']); ?></td>
                        <td><?php echo htmlspecialchars($paiement['username']); ?></td>
                        <td><?php echo htmlspecialchars($paiement['formation_titre']); ?></td>
                        <td><?php echo htmlspecialchars($paiement['montant']); ?> EUR</td>
                        <td><?php echo htmlspecialchars($paiement['statut']); ?></td>
                        <td><?php echo htmlspecialchars($paiement['date_paiement'] ?? 'Non spécifiée'); ?></td>
                        <td>
                            <a href="../Controller/PaiementController.php?action=delete&id=<?php echo $paiement['id']; ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?');">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <a href="formulairePaiement.php" class="btn btn-primary mt-3">
            <i class="fas fa-plus-circle me-2"></i>Ajouter un paiement
        </a>
    </div>

<?php
$content = ob_get_clean();
require 'template.php';
?>